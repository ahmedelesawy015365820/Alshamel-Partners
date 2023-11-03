<script>
import adminApi from "../../../api/adminAxios";
import {
  required,
  minLength,
  maxLength,
  integer,
  url,
  email,
  requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import employee from "../general/employee";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import img from "../../../assets/images/img-1.png";
import DatePicker from "vue2-datepicker";
/**
 * Advanced Table component
 */

export default {
    name: "BookingCompany",
  props: {
        id: {default: "company-create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/general-customer'}
  },
  mixins: [transMixinComp,successError],
  components: {
    ErrorMessage,
    loader,
    Multiselect,
    employee,
      DatePicker
  },
  data() {
    return {
        img,
      watsApp : { phone: "" },
      fields: [],
      isCustom:false,
      employees: [],
      isLoader: false,
      isVaildPhone: false,
      typeModel: '',
      create: {
          name: "",
          email: "",
          employee_id: null,
          name_e: "",
          phone: "",
          contact_person: "",
          contact_phone: "",
          tax_record: "",
          address: "",
          code_country: "",
          type: 0,
          is_supplier: 0,
        media: [],
        old_media: []
      },
      codeCountry: "",
      customer_id: null,
      errors: {},
      images: [],
      media: {},
      titleFile: "",
      saveImageName: [],
      showPhoto: img,
      image: "",
      is_disabled: false,
    };
  },
  validations: {
    create: {
        name: {
            required: requiredIf(function (model) {
                return this.isRequired("name");
            }),
            minLength: minLength(2),
            maxLength: maxLength(100),
        },
        name_e: {
            required: requiredIf(function (model) {
                return this.isRequired("name_e");
            }),
            minLength: minLength(2),
            maxLength: maxLength(100),
        },
        phone: {
            required: requiredIf(function (model) {
                return this.isRequired("phone");
            }),
            maxLength: maxLength(20),
        },
        employee_id: {
            required: requiredIf(function (model) {
                return this.isRequired("employee_id");
            }),
        },

        email: {
            required: requiredIf(function (model) {
                return this.isRequired("email");
            }),
            email,
        },

        tax_record: {
            required: requiredIf(function (model) {
                return this.isRequired("tax_record");
            }),
            integer,
        },
        address: {
            required: requiredIf(function (model) {
                return this.isRequired("address");
            }),
            maxLength: maxLength(1000),
        },
        contact_person: {
            required: requiredIf(function (model) {
                return this.isRequired("contact_person");
            }),
            maxLength: maxLength(100),
        },
        contact_phone: {
            required: requiredIf(function (model) {
                return this.isRequired("contact_phone");
            }),
            maxLength: maxLength(100),
        },
        media: {},
    },
    titleFile: { required, minLength: minLength(2), maxLength: maxLength(100) },
    watsApp: {
        phone: {required}
    }
  },
  methods: {
      // start whats app
    resetWhatsApp(index) {
          this.watsApp = { phone: "" };
          this.$nextTick(() => {
              this.$v.$reset();
          });
          this.$bvModal.hide("show-whatup-"+index);
      },
    getLinkWhastapp(photo) {
          this.$v.watsApp.$touch();
          if (this.$v.watsApp.$invalid) {
              return;
          } else {
              var url =
                  "https://api.whatsapp.com/send?phone=" +
                  this.watsApp.phone +
                  "&text=" +
                  encodeURIComponent(
                      photo
                  );

              window.open(url);
          }
      },
    async getCustomTableFields() {
          this.isCustom = true;
     await  adminApi
        .get(`/customTable/table-columns/general_customers`)
        .then((res) => {
          this.fields = res.data;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isCustom = false;
        });
    },
    isVisible(fieldName) {
          if(!this.isPage){
              let res = this.fields.filter((field) => {
                  return field.column_name == fieldName;
              });
              return res.length > 0 && res[0].is_visible == 1 ? true : false;
          }else {
              return this.isVisiblePage(fieldName);
          }
      },
    isRequired(fieldName) {
          if(!this.isPage) {
              let res = this.fields.filter((field) => {
                  return field.column_name == fieldName;
              });
              return res.length > 0 && res[0].is_required == 1 ? true : false;
          }else {
              return this.isRequiredPage(fieldName);
          }
      },
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
    },
    defaultData(edit = null){
        this.create = {
            name: "",
            email: "",
            employee_id: null,
            name_e: "",
            phone: "",
            contact_person: "",
            contact_phone: "",
            tax_record: "",
            address: "",
            type: 0,
            is_supplier: 0,
            code_country: "",
            media: [],
            old_media: []
        };
        this.$nextTick(() => {
            this.$v.$reset();
        });
        this.errors = {};
        this.images = [];
        this.showPhoto = img;
        this.media = {};
        this.codeCountry = this.$store.getters["locationIp/countryCode"];
        this.is_disabled = false;
      },
    resetModalHidden() {
      this.defaultData();
      this.$bvModal.hide(this.id);
    },
    resetModal() {
      this.defaultData();
      setTimeout(async () => {
            if(this.type != 'edit'){
                if(!this.isPage) await this.getCustomTableFields();
                if (this.isVisible("employee_id"))  this.getEmployees();
            }else {
                if(this.idObjEdit.dataObj){
                    let build = this.idObjEdit.dataObj;
                    this.errors = {};
                    this.customer_id = this.idObjEdit.idEdit;
                    this.create.name = build.name;
                    this.create.name_e = build.name_e;
                    this.create.is_supplier = build.is_supplier;
                    if (this.isVisible("employee_id"))  this.getEmployees();
                    this.create.employee_id = build.employee_id ?? null;
                    this.create.phone = build.phone;
                    this.create.contact_person = build.contact_person;
                    this.create.contact_phone = build.contact_phone;
                    this.create.email = build.email;
                    this.create.tax_record = build.tax_record;
                    this.create.address = build.address;
                    this.create.type = build.type;
                    this.codeCountry = build.code_country;

                    this.images = build.media ?? [];
                    if (this.images && this.images.length > 0) {
                        this.showPhoto = this.images[this.images.length - 1].webp;
                    } else {
                        this.showPhoto = img;
                    }

                    this.errors = {};
                }
            }
        },50);
    },
    resetForm() {
      this.defaultData();
    },
    AddSubmit() {
      this.create.code_country = this.codeCountry;
      if (!this.create.name) {
        this.create.name = this.create.name_e;
      }
      if (!this.create.name_e) {
        this.create.name_e = this.create.name;
      }
      this.create.company_id = JSON.parse(localStorage.getItem("company_id"));
      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};

          if(this.type != 'edit'){
              adminApi
                  .post(this.url, {
                      ...this.create,
                      company_id: this.$store.getters["auth/company_id"],
                  })
                  .then((res) => {
                      this.customer_id = res.data.data.id;
                      this.is_disabled = true;
                      if(!this.isPage)
                          this.$emit("created");
                      else
                          this.$emit("getDataTable");

                      setTimeout(() => {
                          this.successFun('Addedsuccessfully');
                      }, 500);
                  })
                  .catch((err) => {
                      if (err.response.data) {
                          this.errors = err.response.data.errors;
                      } else {
                          this.errorFun('Error','Thereisanerrorinthesystem');
                      }
                  })
                  .finally(() => {
                      this.isLoader = false;
                  });
          }else {
              this.images.forEach((e) => {
                  this.create.old_media.push(e.id);
              });
              adminApi
                  .put(`general-customer/${this.idObjEdit.idEdit}`, {
                      ...this.create,
                      company_id: this.$store.getters["auth/company_id"],
                  })
                  .then((res) => {
                      this.$bvModal.hide(this.id);
                      this.$emit("getDataTable");
                      setTimeout(() => {
                          this.successFun('Editsuccessfully');
                      }, 500);
                  })
                  .catch((err) => {
                      if (err.response.data) {
                          this.errors = err.response.data.errors;
                      } else {
                          this.errorFun('Error','Thereisanerrorinthesystem');
                      }
                  })
                  .finally(() => {
                      this.isLoader = false;
                  });
          }

      }
    },
    uploadPhotoTitle() {
      this.titleFile = "";
      this.$bvModal.show(`uploadPhotoTitleCreate`);
      this.errors = {};
    },
    uploadPhotoTitleHidden() {
      this.titleFile = "";
      this.$bvModal.hide(`uploadPhotoTitleCreate`);
      this.errors = {};
    },
    changePhoto() {
      document.getElementById("uploadImageCreate").click();
    },
    changePhotoEdit() {
      document.getElementById("uploadImageEdit").click();
    },
    onImageChanged(e) {
      const file = e.target.files[0];
      this.addImage(file);
    },
    addImage(file) {
      this.media = file; //upload
      if (file) {
        this.idDelete = null;
        let is_media = this.images.find(
          (e) => e.name == file.name.slice(0, file.name.indexOf("."))
        );
        this.idDelete = is_media ? is_media.id : null;
        if (!this.idDelete) {
          this.isLoader = true;
          let formDate = new FormData();
          formDate.append("media[][media]", this.media);
          formDate.append("media[][title]", this.titleFile);
          adminApi
            .post(`/media-name`, formDate)
            .then((res) => {
              let old_media = [];
              this.images.forEach((e) => old_media.push(e.id));
              let new_media = [];
              res.data.data.forEach((e) => new_media.push(e.id));

              adminApi
                .put(`/general-customer/${this.customer_id}`, {
                  old_media,
                  media: new_media,
                })
                .then((res) => {
                  this.images = res.data.data.media ?? [];
                  if (this.images && this.images.length > 0) {
                    this.showPhoto = this.images[this.images.length - 1].webp;
                  } else {
                    this.showPhoto = img;
                  }
                  this.getData();
                  this.uploadPhotoTitleHidden();
                })
                .catch((err) => {
                  Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
                });
            })
            .catch((err) => {
              if (err.response.data) {
                this.errors = err.response.data.errors;
              } else {
                Swal.fire({
                  icon: "error",
                  title: `${this.$t("general.Error")}`,
                  text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                });
              }
            })
            .finally(() => {
              this.isLoader = false;
            });
        } else {
          Swal.fire({
            title: `${this.$t("general.Thisfilehasalreadybeenuploaded")}`,
            type: "warning",
            showCancelButton: true,
            confirmButtonText: `${this.$t("general.Replace")}`,
            cancelButtonText: `${this.$t("general.Nocancel")}`,
            confirmButtonClass: "btn btn-success mt-2",
            cancelButtonClass: "btn btn-danger ml-2 mt-2",
            buttonsStyling: false,
          }).then((result) => {
            if (result.value) {
              this.isLoader = true;
              let formDate = new FormData();
              formDate.append("media[0]", this.media);
              adminApi
                .post(`/media`, formDate)
                .then((res) => {
                  let old_media = [];
                  this.images.forEach((e) => old_media.push(e.id));
                  old_media.splice(old_media.indexOf(this.idDelete), 1);
                  let new_media = [];
                  res.data.data.forEach((e) => new_media.push(e.id));

                  adminApi
                    .put(`/general-customer/${this.country_id}`, {
                      old_media,
                      media: new_media,
                    })
                    .then((res) => {
                      this.images = res.data.data.media ?? [];
                      if (this.images && this.images.length > 0) {
                        this.showPhoto =
                          this.images[this.images.length - 1].webp;
                      } else {
                        this.showPhoto = img;
                      }
                      this.getData();
                    })
                    .catch((err) => {
                      Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                      });
                    });
                })
                .catch((err) => {
                  if (err.response.data) {
                    this.errors = err.response.data.errors;
                  } else {
                    Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                  }
                })
                .finally(() => {
                  this.isLoader = false;
                });
            }
          });
        }
      }
    },
    deleteImageCreate(id, index) {
      let old_media = [];
      this.images.forEach((e) => {
        if (e.id != id) {
          old_media.push(e.id);
        }
      });
      adminApi
        .put(`/general-customer/${this.customer_id}`, { old_media })
        .then((res) => {
          this.customers[index] = res.data.data;
          this.images = res.data.data.media ?? [];
          if (this.images && this.images.length > 0) {
            this.showPhoto = this.images[this.images.length - 1].webp;
          } else {
            this.showPhoto = img;
          }
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    getCurrentYear() {
      return new Date().getFullYear();
    },
     getEmployees() {
       adminApi
        .get(`/employees?customer_handel=1`)
        .then((res) => {
          let l = res.data.data;
          // if (this.isPermission("create Employee")) {
          //   l.unshift({ id: 0, name: "اضاف موظف", name_e: "Add Employee" });
          // }
          this.employees = l;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        });
    },
    showEmployeeModal() {
      if (this.create.employee_id == 0) {
        this.$bvModal.show("employee-create-customer");
        this.create.employee_id = null;
      }
    },
    updatePhone(e) {
      this.create.phone = e.phoneNumber;
      this.codeCountry = e.countryCode;
      // this.isVaildPhone = e.isValid;
    },
      updateContract(e) {
          this.create.contact_phone = e.phoneNumber;
      },
  },
};
</script>

<template>
  <div>
<!--    <employee-->
<!--        :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"-->
<!--        :isPage="false" type="create" :isPermission="isPermission"-->
<!--        :id="'employee-create-customer'" @created="getEmployees"-->
<!--    />-->
    <!--  create   -->
    <b-modal
      :id="id"
      :title="type != 'edit'?getCompanyKey('company_create_form'):getCompanyKey('company_edit_form')"
      title-class="font-18"
      dialog-class="modal-full-width"
      body-class="p-4 "
      :hide-footer="true"
      @show="resetModal"
      @hidden="resetModalHidden"
    >
      <form>
        <loader size="large" v-if="isCustom && !isPage" />
        <div class="card">
          <div class="card-body">
            <div class="mb-3 d-flex justify-content-end">
              <b-button
                v-if="type != 'edit'"
                variant="success"
                :disabled="!is_disabled"
                @click.prevent="resetForm"
                type="button"
                :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
              >
                {{ $t("general.AddNewRecord") }}
              </b-button>
              <template v-if="!is_disabled">
                <b-button
                  variant="success"
                  type="button"
                  class="mx-1"
                  v-if="!isLoader"
                  @click.prevent="AddSubmit"
                >
                    {{ type != 'edit'? $t("general.Add"): $t("general.edit") }}
                </b-button>

                <b-button variant="success" class="mx-1" disabled v-else>
                  <b-spinner small></b-spinner>
                  <span class="sr-only">{{ $t("login.Loading") }}...</span>
                </b-button>
              </template>
              <!-- Emulate built in modal footer ok and cancel button actions -->

              <b-button
                variant="danger"
                type="button"
                @click.prevent="resetModalHidden"
              >
                {{ $t("general.Cancel") }}
              </b-button>
            </div>
          </div>
          <b-tabs nav-class="nav-tabs nav-bordered">
              <b-tab :title="$t('general.DataEntry')" active>
                  <div class="row">
                      <div class="col-md-4" v-if="isVisible('name')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{ getCompanyKey("guest_name_ar") }}
                                  <span v-if="isRequired('name')" class="text-danger">*</span>
                              </label>
                              <div dir="rtl">
                                  <input
                                      @keyup="arabicValue(create.name)"
                                      type="text"
                                      class="form-control"
                                      data-create="1"
                                      v-model="$v.create.name.$model"
                                      :class="{
                                          'is-invalid':
                                            $v.create.name.$error || errors.name,
                                          'is-valid':
                                            !$v.create.name.$invalid && !errors.name,
                                        }"
                                  />
                              </div>
                              <div v-if="!$v.create.name.minLength" class="invalid-feedback">
                                  {{ $t("general.Itmustbeatleast") }}
                                  {{ $v.create.name.$params.minLength.min }}
                                  {{ $t("general.letters") }}
                              </div>
                              <div v-if="!$v.create.name.maxLength" class="invalid-feedback">
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.create.name.$params.maxLength.max }}
                                  {{ $t("general.letters") }}
                              </div>
                              <template v-if="errors.name">
                                  <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">
                                      {{ errorMessage }}
                                  </ErrorMessage>
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('name_e')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{ getCompanyKey("guest_name_en") }}
                                  <span v-if="isRequired('guest_name_en')" class="text-danger">*</span>
                              </label>
                              <div dir="ltr">
                                  <input
                                      @keyup="englishValue(create.name_e)"
                                      type="text"
                                      class="form-control"
                                      data-create="2"
                                      v-model="$v.create.name_e.$model"
                                      :class="{
                                          'is-invalid':
                                            $v.create.name_e.$error || errors.name_e,
                                          'is-valid':
                                            !$v.create.name_e.$invalid &&
                                            !errors.name_e,
                                        }"
                                  />
                              </div>
                              <div v-if="!$v.create.name_e.minLength" class="invalid-feedback">
                                  {{ $t("general.Itmustbeatleast") }}
                                  {{ $v.create.name_e.$params.minLength.min }}
                                  {{ $t("general.letters") }}
                              </div>
                              <div v-if="!$v.create.name_e.maxLength" class="invalid-feedback">
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.create.name_e.$params.maxLength.max }}
                                  {{ $t("general.letters") }}
                              </div>
                              <template v-if="errors.name_e">
                                  <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index">
                                      {{ errorMessage }}
                                  </ErrorMessage>
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('employee_id')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{ getCompanyKey("customer_employee")}}
                                  <span v-if="isRequired('employee_id')" class="text-danger">*</span>
                              </label>
                              <multiselect
                                  @input="showEmployeeModal"
                                  v-model="create.employee_id"
                                  :options="employees.map((type) => type.id)"
                                  :custom-label="
                                (opt) =>
                                  employees.find((x) => x.id == opt)?$i18n.locale == 'ar'
                                    ? employees.find((x) => x.id == opt).name
                                    : employees.find((x) => x.id == opt).name_e:null
                              "
                                  :class="{
                                'is-invalid':
                                  $v.create.employee_id.$error ||
                                  errors.employee_id,
                                'is-valid':
                                  !$v.create.employee_id.$invalid &&
                                  !errors.employee_id,
                              }"
                              >
                              </multiselect>
                              <template v-if="errors.employee_id">
                                  <ErrorMessage v-for="(errorMessage, index) in errors.employee_id" :key="index">
                                      {{ errorMessage }}
                                  </ErrorMessage>
                              </template>
                          </div>
                      </div>
                  </div>
                  <hr
                      v-if="
                          isVisible('phone') ||
                          isVisible('email') ||
                          isVisible('tax_record') ||
                          isVisible('address')
                        "
                      style="
                          margin: 10px 0 !important;
                          border-top: 1px solid rgb(141 163 159 / 42%);
                        "
                  />
                  <div class="row">
                      <div class="col-md-3" v-if="isVisible('phone')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{ getCompanyKey("company_phone") }}
                                  <span v-if="isRequired('phone')" class="text-danger">*</span>
                              </label>
                              <VuePhoneNumberInput
                                  v-model="$v.create.phone.$model"
                                  :default-country-code="codeCountry"
                                  valid-color="#28a745"
                                  error-color="#dc3545"
                                  :preferred-countries="['FR', 'EG', 'DE']"
                                  @update="updatePhone"
                              />
                              <template v-if="errors.phone">
                                  <ErrorMessage v-for="(errorMessage, index) in errors.phone" :key="index">{{ errorMessage }}</ErrorMessage>
                              </template>
                          </div>
                      </div>
                      <div class="col-md-3" v-if="isVisible('email')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{ getCompanyKey("company_email") }}
                                  <span v-if="isRequired('email')" class="text-danger">*</span>
                              </label>
                              <input
                                  type="text"
                                  class="form-control"
                                  data-create="9"
                                  v-model="$v.create.email.$model"
                                  :class="{
                                    'is-invalid': $v.create.email.$error || errors.email,
                                    'is-valid':!$v.create.email.$invalid && !errors.email,
                                  }"
                              />
                              <template v-if="errors.email">
                                  <ErrorMessage v-for="(errorMessage, index) in errors.email" :key="index">{{ errorMessage }}</ErrorMessage>
                              </template>
                          </div>
                      </div>
                      <div class="col-md-3" v-if="isVisible('tax_record')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{ getCompanyKey("company_tax_record") }}
                                  <span v-if="isRequired('tax_record')" class="text-danger">*</span>
                              </label>
                              <input
                                  type="text"
                                  class="form-control"
                                  data-create="9"
                                  v-model="$v.create.tax_record.$model"
                                  :class="{
                                    'is-invalid': $v.create.tax_record.$error || errors.tax_record,
                                    'is-valid': !$v.create.tax_record.$invalid && !errors.tax_record,
                                  }"
                              />
                              <template v-if="errors.tax_record">
                                  <ErrorMessage v-for="( errorMessage, index) in errors.tax_record" :key="index">{{ errorMessage }}</ErrorMessage>
                              </template>
                          </div>
                      </div>
                      <div class="col-md-3" v-if="isVisible('address')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{ getCompanyKey("company_address") }}
                                  <span v-if="isRequired('address')" class="text-danger">*</span>
                              </label>
                              <input
                                  type="text"
                                  class="form-control"
                                  data-create="9"
                                  v-model="$v.create.address.$model"
                                  :class="{
                                    'is-invalid': $v.create.address.$error || errors.address,
                                    'is-valid': !$v.create.address.$invalid && !errors.address,
                                  }"
                              />
                              <template v-if="errors.address">
                                  <ErrorMessage v-for="(errorMessage, index) in errors.address" :key="index">{{ errorMessage }}</ErrorMessage>
                              </template>
                          </div>
                      </div>
                  </div>
                  <hr v-if="
                          isVisible('contact_person') ||
                          isVisible('contact_phone')
                        "
                      style="
                          margin: 10px 0 !important;
                          border-top: 1px solid rgb(141 163 159 / 42%);
                        "
                  />
                  <div class="row">
                      <div class="col-md-4" v-if="isVisible('contact_person')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{ getCompanyKey("comapny_contact_person") }}
                                  <span v-if="isRequired('contact_person')" class="text-danger">*</span>
                              </label>
                              <input
                                  type="text"
                                  class="form-control"
                                  data-create="9"
                                  v-model="$v.create.contact_person.$model"
                                  :class="{
                                     'is-invalid': $v.create.contact_person.$error || errors.contact_person,
                                    'is-valid': !$v.create.contact_person.$invalid && !errors.contact_person,
                                  }"
                              />
                              <template v-if="errors.contact_person">
                                  <ErrorMessage v-for="( errorMessage, index ) in errors.contact_person" :key="index">{{ errorMessage }}</ErrorMessage>
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('contact_phone')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{ getCompanyKey("comapny_contact_phones") }}
                                  <span v-if="isRequired('contact_phone')" class="text-danger">*</span>
                              </label>
                              <VuePhoneNumberInput
                                  v-model="$v.create.contact_phone.$model"
                                  :default-country-code="codeCountry"
                                  valid-color="#28a745"
                                  error-color="#dc3545"
                                  :preferred-countries="['FR', 'EG', 'DE']"
                                  @update="updateContract"
                              />
                              <template v-if="errors.contact_phone">
                                  <ErrorMessage v-for="( errorMessage, index ) in errors.contact_phone" :key="index">{{ errorMessage }}</ErrorMessage>
                              </template>
                          </div>
                      </div>
                  </div>
              </b-tab>
              <b-tab
                  :disabled="!customer_id"
                  :title="$t('general.ImageUploads')"
              >
                  <div class="row">
                      <b-modal
                          id="uploadPhotoTitleCreate"
                          :title="$t('general.ImageUploads')"
                          title-class="font-18"
                          body-class="p-4 "
                          :hide-footer="true"
                          @show="uploadPhotoTitle"
                          @hidden="uploadPhotoTitleHidden"
                      >
                          <div class="form-group">
                              <label class="control-label">
                                  {{ $t("general.titleFile") }}
                                  <span class="text-danger">*</span>
                              </label>
                              <div dir="rtl">
                                  <input
                                      type="text"
                                      class="form-control"
                                      data-create="1"
                                      v-model="$v.titleFile.$model"
                                      :class="{
                                  'is-invalid':
                                    $v.titleFile.$error || errors.title,
                                  'is-valid':
                                    !$v.titleFile.$invalid && !errors.title,
                                }"
                                  />
                              </div>
                              <div
                                  v-if="!$v.titleFile.minLength"
                                  class="invalid-feedback"
                              >
                                  {{ $t("general.Itmustbeatleast") }}
                                  {{ $v.titleFile.$params.minLength.min }}
                                  {{ $t("general.letters") }}
                              </div>
                              <div
                                  v-if="!$v.titleFile.maxLength"
                                  class="invalid-feedback"
                              >
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.titleFile.$params.maxLength.max }}
                                  {{ $t("general.letters") }}
                              </div>
                              <template v-if="errors.title">
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.title"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>

                          <input
                              accept="image/png, image/gif, image/jpeg, image/jpg"
                              type="file"
                              id="uploadImageCreate"
                              @change.prevent="onImageChanged"
                              class="input-file-upload position-absolute"
                              :class="[
                              'd-none',
                              {
                                'is-invalid':
                                  $v.create.media.$error || errors.media,
                                'is-valid':
                                  !$v.create.media.$invalid && !errors.media,
                              },
                            ]"
                          />

                          <b-button
                              :disabled="!titleFile && $v.titleFile.$error"
                              @click="changePhoto"
                              variant="success"
                              type="button"
                              class="mx-1 font-weight-bold px-3"
                              v-if="!isLoader"
                          >
                              {{ $t("general.Add") }}
                          </b-button>
                          <b-button
                              variant="success"
                              class="mx-1"
                              disabled
                              v-else
                          >
                              <b-spinner small></b-spinner>
                              <span class="sr-only"
                              >{{ $t("login.Loading") }}...</span
                              >
                          </b-button>
                      </b-modal>

                      <div class="col-md-8 my-1">
                          <!-- file upload -->
                          <div
                              class="row align-content-between"
                              style="width: 100%; height: 100%"
                          >
                              <div class="col-12">
                                  <div class="d-flex flex-wrap">
                                      <div class="col-4" v-for="(photo, index) in images" :key="photo.id">
                                          <!-- whatup -->
                                          <b-modal
                                              :id="`show-whatup-${index}`"
                                              title="Send Whatsapp"
                                              title-class="font-18"
                                              body-class="p-4 "
                                              :hide-footer="true"
                                              @hidden="resetWhatsApp"
                                          >
                                              <form>
                                                  <div class="d-flex justify-content-end">
                                                      <button @click.prevent="getLinkWhastapp(photo.webp)" type="button" class="btn btn-info">
                                                          {{ $t("general.send") }}
                                                      </button>
                                                  </div>
                                                  <div class="card" style="background: none !important">
                                                      <div class="row">
                                                          <div class="col-md-12">
                                                              <div class="form-group">
                                                                  <label class="control-label">
                                                                      {{ $t("general.phone") }}
                                                                  </label>
                                                                  <input
                                                                      v-model="$v.watsApp.phone.$model"
                                                                      type="number"
                                                                      class="form-control"
                                                                      :class="{
                      'is-invalid': $v.watsApp.phone.$error,
                      'is-valid': !$v.watsApp.phone.$invalid,
                    }"
                                                                      data-create="9"
                                                                  />
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </form>
                                          </b-modal>
                                          <!-- whatup -->
                                          <div :class="['dropzone-previews position-relative mb-2',]">
                                              <div :class="['card mb-0 shadow-none border',images.length - 1 == index? 'bg-primary': '',]">
                                                  <div class="p-2">
                                                      <div class="row align-items-center">
                                                          <div
                                                              class="col-auto"
                                                              @click="showPhoto = photo.webp"
                                                          >
                                                              <img
                                                                  data-dz-thumbnail
                                                                  :src="photo.webp"
                                                                  class="avatar-sm rounded bg-light"
                                                                  @error="
                                                              src = img

                                                            "
                                                              />
                                                          </div>
                                                          <div class="col pl-0">
                                                              <a
                                                                  href="javascript:void(0);"
                                                                  :class="[
                                              'font-weight-bold',
                                              images.length - 1 == index
                                                ? 'text-white'
                                                : 'text-muted',
                                            ]"
                                                                  data-dz-name
                                                              >
                                                                  {{ photo.title }}
                                                              </a>
                                                          </div>
                                                          <!-- Button -->
                                                          <a
                                                              href="javascript:void(0);"
                                                              :class="[
                                            'btn-danger dropzone-close',
                                            $i18n.locale == 'ar'
                                              ? 'dropzone-close-rtl'
                                              : '',
                                          ]"
                                                              data-dz-remove
                                                              @click.prevent="
                                            deleteImageCreate(photo.id, index)
                                          "
                                                          >
                                                              <i class="fe-x"></i>
                                                          </a>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="text-center">
                                              <a
                                                  :href="photo.webp"
                                                  download
                                                  class="btn-sm mx-1 font-weight-bold btn-info"
                                              >
                                                  {{ $t("general.download") }}
                                                  <i class="fas fa-file-download"></i>
                                              </a>
                                              <a
                                                  @click.prevent="$bvModal.show(`show-whatup-${index}`)"
                                                  href="#"
                                                  class="btn-sm mx-1 font-weight-bold btn-secondary"
                                              >
                                                  {{ $t("general.whatsapp") }}
                                                  <i class="fab fa-whatsapp"></i>
                                              </a>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <div class="footer-image col-12 mt-3">
                                  <b-button
                                      @click="uploadPhotoTitle"
                                      variant="success"
                                      type="button"
                                      class="mx-1 font-weight-bold px-3"
                                      v-if="!isLoader"
                                  >
                                      {{ $t("general.Add") }}
                                  </b-button>
                              </div>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="show-dropzone">
                              <img
                                  :src="showPhoto"
                                  class="img-thumbnail"
                                  @error="src = img"
                              />
                          </div>
                      </div>
                  </div>
              </b-tab>
          </b-tabs>
        </div>
      </form>
    </b-modal>
  </div>
</template>
<style>
.dropdown-menu-custom-company.dropdown .dropdown-menu {
  padding: 5px 10px !important;
  overflow-y: scroll;
  height: 300px;
}
.modal-body .card .tabs .tab-content {
  padding: 20px 60px 40px !important;
}
form {
    position: relative;
}
</style>
