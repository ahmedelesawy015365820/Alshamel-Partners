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
import Country from "./country";
import CustomerResource from "./customer_resource";
import CustomerCategory from "./customer_category";
import City from "./city";
import Salesman from "./saleman";
import bankAccount from "./bankAccount";
import Multiselect from "vue-multiselect";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import employee from "./employee";
import customerGroup from "./customer_group";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import img from "../../../assets/images/img-1.png";
/**
 * Advanced Table component
 */

export default {
  props: {
        id: {default: "avenue-create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/general-customer'}
  },
  mixins: [transMixinComp,successError],
  components: {
    customerGroup,
    ErrorMessage,
    loader,
    Country,CustomerResource,
    City,CustomerCategory,
    Multiselect,
    bankAccount,
    employee,
    Salesman,
  },
  data() {
    return {
        img,
      watsApp : { phone: "" },
      fields: [],
      isCustom:false,
      cities: [],
      salesmen: [],
      countries: [],
      employees: [],
      sectors: [],
      main_customer_categories: [],
      sub_customer_categories: [],
      customer_sources: [],
      bank_accounts: [],
      isLoader: false,
      isVaildPhone: false,
      typeModel: '',
      groups: [],
      create: {
        name: "",
        name_e: "",
        phone: "",
        email: "",
        rp_code: "",
        contact_person: "",
        contact_phone: "",
        national_id: null,
        nationality: null,
        bank_account_id: null,
        country_id: null,
        city_id: null,
        customer_group_id: null,
        is_supplier: 0,
        whatsapp: "",
        passport_no: null,
        employee_id: null,
        salesman_id: null,
        customer_main_category_id: null,
        customer_sub_category_id: null,
        sector_id: null,
        customer_source_id: null,
        note: "",
        facebook: "",
        instagram: "",
        linkedin: "",
        snapchat: "",
        twitter: "",
        website: "",
        media: [],
        code_country: "",
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
      email: {
        required: requiredIf(function (model) {
          return this.isRequired("email");
        }),
        maxLength: maxLength(100),
        email,
      },
      customer_group_id: {
        required: requiredIf(function (model) {
          return this.isRequired("customer_group_id");
        }),
      },
      is_supplier: {
        required: requiredIf(function (model) {
          return this.isRequired("is_supplier");
        }),
      },
      rp_code: {
        required: requiredIf(function (model) {
          return this.isRequired("rp_code");
        }),
        maxLength: maxLength(9),
      },
      nationality: {
        required: requiredIf(function (model) {
          return this.isRequired("nationality");
        }),
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
      national_id: {
        required: requiredIf(function (model) {
          return this.isRequired("national_id");
        }),
        integer,
        maxLength: maxLength(20),
      },
      country_id: {
        required: requiredIf(function (model) {
          return this.isRequired("country_id");
        }),
      },
      city_id: {
        required: requiredIf(function (model) {
          return this.isRequired("city_id");
        }),
      },
      bank_account_id: {
        required: requiredIf(function (model) {
          return this.isRequired("bank_account_id");
        }),
      },
      employee_id: {
        required: requiredIf(function (model) {
          return this.isRequired("employee_id");
        }),
      },
      whatsapp: {
        required: requiredIf(function (model) {
          return this.isRequired("whatsapp");
        }),
        maxLength: maxLength(20),
      },
      passport_no: {
        required: requiredIf(function (model) {
          return this.isRequired("passport_no");
        }),
        integer,
        maxLength: maxLength(20),
      },
      media: {},
      salesman_id: {
        required: requiredIf(function (model) {
          return this.isRequired("salesman_id");
        }),
        integer,
      },
      note: {
        required: requiredIf(function (model) {
          return this.isRequired("note");
        }),
        maxLength: maxLength(1000),
      },
      facebook: {
        required: requiredIf(function (model) {
          return this.isRequired("facebook");
        }),
        url,
      },
      instagram: {
        required: requiredIf(function (model) {
          return this.isRequired("instagram");
        }),
        url,
      },
      linkedin: {
        required: requiredIf(function (model) {
          return this.isRequired("linkedin");
        }),
        url,
      },
      snapchat: {
        required: requiredIf(function (model) {
          return this.isRequired("snapchat");
        }),
        url,
      },
      twitter: {
        required: requiredIf(function (model) {
          return this.isRequired("twitter");
        }),
        url,
      },
      website: {
        required: requiredIf(function (model) {
          return this.isRequired("website");
        }),
        url,
      },
      customer_main_category_id: {
        required: requiredIf(function (model) {
          return this.isRequired("customer_main_category_id");
        }),
      },
      customer_sub_category_id: {
        required: requiredIf(function (model) {
          return this.isRequired("customer_sub_category_id");
        }),
      },
      sector_id: {
        required: requiredIf(function (model) {
          return this.isRequired("sector_id");
        }),
      },
      customer_source_id: {
        required: requiredIf(function (model) {
          return this.isRequired("customer_source_id");
        }),
      },
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
    checkSupplierCreate() {
      if (!this.create.name) return;
      this.isLoader = true;
      adminApi
        .get(
          `check-supplier?name=${this.create.name}&is_supplier=${this.create.is_supplier}&supplier=create`
        )
        .then((res) => {})
        .catch((err) => {
          if (err.response.status == 400) {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.SupplierExistBefore")}`,
            });
            return;
          }
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        })
        .finally(() => {
          this.isLoader = false;
        });
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
            name_e: "",
            customer_group_id: null,
            is_supplier: 0,
            phone: "",
            email: "",
            rp_code: "",
            contact_person: "",
            contact_phone: "",
            national_id: null,
            nationality: null,
            bank_account_id: null,
            country_id: null,
            city_id: null,
            whatsapp: "",
            passport_no: null,
            employee_id: null,
            salesman_id: null,
            customer_main_category_id: null,
            customer_sub_category_id: null,
            sector_id: null,
            customer_source_id: null,
            note: "",
            facebook: "",
            instagram: "",
            linkedin: "",
            snapchat: "",
            twitter: "",
            website: "",
            media: [],
            code_country: "",
            old_media: []
        };
        this.countries = [];
        this.cities = [];
        this.bank_accounts = [];
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
                if (this.isVisible("country_id"))  this.getCategory();
                if (this.isVisible("bank_account_id"))  this.getBankAcount();
                if (this.isVisible("employee_id"))  this.getEmployees();
                if (this.isVisible("salesman_id"))  this.getSalesmen();
                if (this.isVisible("sector_id"))  this.getSectors();
                if (this.isVisible("customer_group_id"))  this.getGroups();
                if (this.isVisible("customer_source_id")) this.getCustomerResourse();
                if (this.isVisible("customer_main_category_id")) this.getCustomerCategory();
            }else {
                if(this.idObjEdit.dataObj){
                    let build = this.idObjEdit.dataObj;
                    this.errors = {};
                    this.countries = [];
                    this.cities = [];
                    this.customer_id = this.idObjEdit.idEdit;
                    this.bank_accounts = [];
                    this.create.name = build.name;
                    this.create.name_e = build.name_e;
                    this.create.is_supplier = build.is_supplier;
                    if (this.isVisible("bank_account_id"))  this.getBankAcount();
                    this.create.bank_account_id = build.bank_account_id ?? null;
                    if (this.isVisible("employee_id"))  this.getEmployees();
                    this.create.employee_id = build.employee_id ?? null;
                    if (this.isVisible("country_id"))  this.getCategory();
                    this.create.country_id = build.country ? build.country.id : null;
                    if (this.isVisible("city_id"))  this.getCity(build.country_id);
                    this.create.city_id = build.city ? build.city.id : null;
                    if (this.isVisible("salesman_id"))  this.getSalesmen();
                    this.create.salesman_id = build.salesman ? build.salesman.id : null;
                    if (this.isVisible("sector_id"))  this.getSectors();
                    this.create.sector_id = build.sector_id ? build.sector_id : null;
                    if (this.isVisible("customer_source_id"))
                        this.getCustomerResourse();
                    if (this.isVisible("customer_group_id"))  this.getGroups();
                    this.create.customer_group_id = build.customer_group_id;

                    this.create.customer_source_id = build.customer_source_id
                        ? build.customer_source_id
                        : null;
                    if (this.isVisible("customer_main_category_id"))
                        this.getCustomerCategory();
                    this.create.customer_main_category_id = build.customer_main_category
                        ? build.customer_main_category.id
                        : null;
                    if (this.isVisible("customer_sub_category_id"))
                        this.getCustomerCategorySub(
                            build.customer_main_category ? build.customer_main_category.id : null
                        );
                    this.create.customer_sub_category_id = build.customer_sub_category
                        ? build.customer_sub_category.id
                        : null;
                    this.create.contact_person = build.contact_person;
                    this.create.contact_phone = build.contact_phone;
                    this.create.national_id = build.national_id;
                    this.create.email = build.email;
                    this.create.code_country = build.code_country;
                    this.create.nationality = build.nationality ?? null;
                    this.create.passport_no = build.passport_no;
                    this.create.phone = build.phone;
                    this.create.rp_code = build.rp_code;
                    this.create.whatsapp = build.whatsapp;
                    this.create.note = build.whatsapp;
                    this.create.facebook = build.facebook;
                    this.create.instagram = build.instagram;
                    this.create.linkedin = build.linkedin;
                    this.create.snapchat = build.snapchat;
                    this.create.twitter = build.twitter;
                    this.create.website = build.website;
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
                  .put(`${this.url}/${this.idObjEdit.idEdit}`, {
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
     getSectors() {
       adminApi
        .get(`/sectors`)
        .then((res) => {
          let l = res.data.data;
          this.sectors = l;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        });
    },
     getCategory() {
      this.create.city_id = null;
      this.cities = [];
       adminApi
        .get(`/countries?is_active=active`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create Country")) {
            l.unshift({ id: 0, name: "اضافة دولة", name_e: "Add Country" });
          }
          this.countries = l;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        });
    },
     getGroups() {
       adminApi
        .get(`/customer-groups`)
        .then((res) => {
          let l = res.data.data;
            if (this.isPermission("create Group")) {
                l.unshift({ id: 0, title: "اضف مجموعة", title_e: "Add Group" });
            }
          this.groups = l;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        });
    },
     getBankAcount() {
      this.bank_accounts = [];
       adminApi
        .get(`/bank-accounts?`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create Bank Account")) {
            l.unshift({
              id: 0,
              name: "اضافة حساب بنكي",
              name_e: "Add Bank Account",
              account_number: "Add Bank Account",
            });
          }
          this.bank_accounts = l;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
     getCity(id = null) {
      if (this.create.city_id == 0) {
        this.$bvModal.show("city-create-customer");
        this.create.city_id = null;
      }
      if (id) {
        this.create.city_id = null;
        this.cities = [];
        if (this.isVisible("country_id")) {
           adminApi
            .get(`/cities?country_id=${id}`)
            .then((res) => {
              let l = res.data.data;
              if (this.isPermission("create City")) {
                l.unshift({ id: 0, name: "اضافة مدينة", name_e: "Add City" });
              }
              this.cities = l;
            })
            .catch((err) => {
                this.errorFun('Error','Thereisanerrorinthesystem');
            });
        }
      }
    },
     getEmployees() {
       adminApi
        .get(`/employees?customer_handel=1`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create Employee")) {
            l.unshift({ id: 0, name: "اضاف موظف", name_e: "Add Employee" });
          }
          this.employees = l;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        });
    },
     getSalesmen() {
       adminApi
        .get(`/salesmen`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create Salesman")) {
            l.unshift({ id: 0, name: "اضف بائع", name_e: "Add Salesman" });
          }
          this.salesmen = l;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');

        });
    },
     getCustomerResourse() {
       adminApi
        .get(`/customer-sources/get-drop-down`)
        .then((res) => {
          let l = res.data.data;
            if (this.isPermission("create Customer Resource")) {
                l.unshift({ id: 0, name: "اضف موارد العملاء", name_e: "Add Customer Resource" });
            }
          this.customer_sources = l;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        });
    },
     getCustomerCategory() {
      if (this.create.customer_main_category_id == 0) {
             this.typeModel = 'main';
             this.$bvModal.show("CustomerCategory-create-customer");
             this.create.customer_main_category_id = null;
             return;
      }
      if(this.create.customer_main_category_id > 0 && this.create.customer_main_category_id){
          this.getCustomerCategorySub(this.create.customer_main_category_id );
          return;
      }
      this.sub_customer_categories = [];
      this.create.customer_sub_category_id = null;
       adminApi
        .get(`/customers-categories?parent_id=null`)
        .then((res) => {
          let l = res.data.data;
            if (this.isPermission("create Customer Category")) {
                l.unshift({ id: 0, name: "اضف تصنيف الزبون", name_e: "Add Customer Category" });
            }
          this.main_customer_categories = l;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        });
    },
     getCustomerCategorySub(id) {
       adminApi
        .get(`/customers-categories?parent_id=${id}`)
        .then((res) => {
          let l = res.data.data;
            if (this.isPermission("create Customer Category")) {
                l.unshift({ id: 0, name: "اضف تصنيف الزبون", name_e: "Add Customer Category" });
            }
          this.sub_customer_categories = l;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        });
    },
    showCountryModal() {
      if (this.create.country_id == 0) {
        this.$bvModal.show("country-create-customer");
        this.create.country_id = null;
      } else if (this.create.country_id > 0 && this.create.country_id) {
        this.getCity(this.create.country_id);
      }
    },
    showGroupModal() {
      if (this.create.customer_group_id == 0) {
        this.$bvModal.show("group-create-customer");
        this.create.customer_group_id = null;
      }
    },
    showBankAccountModal() {
      if (this.create.bank_account_id == 0) {
        this.$bvModal.show("bankAccount-create-customer");
        this.create.bank_account_id = null;
      }
    },
    showEmployeeModal() {
      if (this.create.employee_id == 0) {
        this.$bvModal.show("employee-create-customer");
        this.create.employee_id = null;
      }
    },
    showSalesmanModal() {
      if (this.create.salesman_id == 0) {
        this.$bvModal.show("salesman-create-customer");
        this.create.salesman_id = null;
      }
    },
    showCustomerResouModal() {
          if (this.create.customer_source_id == 0) {
              this.$bvModal.show("customerResource-create-customer");
              this.create.customer_source_id = null;
          }
      },
    showCustomerCategoryModal() {
          if (this.create.customer_sub_category_id == 0) {
              this.typeModel = 'sub';
              this.$bvModal.show("CustomerCategory-create-customer");
              this.create.customer_sub_category_id = null;
          }
      },
    updatePhone(e) {
      this.create.phone = e.phoneNumber;
      // this.create.phone_code = e.countryCallingCode;
      // this.isVaildPhone = e.isValid;
    },
    updateWhatsapp(e) {
      this.create.whatsapp = e.phoneNumber;
    },
    updateContract(e) {
      this.create.contact_phone = e.phoneNumber;
    },
  },
};
</script>

<template>
  <div>
    <employee
        :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
        :isPage="false" type="create" :isPermission="isPermission"
        :id="'employee-create-customer'" @created="getEmployees"
    />
    <customerGroup
      :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
      :isPage="false" type="create" :isPermission="isPermission"
      @created="getGroups" :id="'group-create-customer'"
    />
    <CustomerResource
          :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
          :isPage="false" type="create" :isPermission="isPermission"
          @created="getCustomerResourse" :id="'customerResource-create-customer'"
      />
    <CustomerCategory
          :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
          :isPage="false" type="create" :isPermission="isPermission"
          @created="getCustomerCategory" :id="'CustomerCategory-create-customer'"
          :typeModel="typeModel" :parentModel="create.customer_main_category_id"
    />
    <bankAccount
        :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
        :isPage="false" type="create" :isPermission="isPermission"
        @created="getBankAcount" :id="'bankAccount-create-customer'"
    />
    <Country
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      :id="'country-create-customer'"
      @created="getCategory"
    />
    <City
        :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
        :isPage="false" type="create" :isPermission="isPermission"
        :id="'city-create-customer'" :country_id="create.country_id"
        @created="getCity(create.country_id)"
    />
    <Salesman
        :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
        :isPage="false" type="create" :isPermission="isPermission"
        :id="'salesman-create-customer'" @created="getSalesmen"
    />
    <!--  create   -->
    <b-modal
      :id="id"
      :title="type != 'edit'?getCompanyKey('general_customer_create_form'):getCompanyKey('general_customer_edit_form')"
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
                              <label for="field-1" class="control-label">
                                  {{ getCompanyKey("general_customer_name_ar") }}
                                  <span
                                      v-if="isRequired('name')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <div dir="rtl">
                                  <input
                                      @blur="checkSupplierCreate"
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
                                      id="field-1"
                                  />
                              </div>
                              <div
                                  v-if="!$v.create.name.minLength"
                                  class="invalid-feedback"
                              >
                                  {{ $t("general.Itmustbeatleast") }}
                                  {{ $v.create.name.$params.minLength.min }}
                                  {{ $t("general.letters") }}
                              </div>
                              <div
                                  v-if="!$v.create.name.maxLength"
                                  class="invalid-feedback"
                              >
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.create.name.$params.maxLength.max }}
                                  {{ $t("general.letters") }}
                              </div>
                              <template v-if="errors.name">
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.name"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('name_e')">
                          <div class="form-group">
                              <label for="field-2" class="control-label">
                                  {{ getCompanyKey("general_customer_name_en") }}
                                  <span
                                      v-if="isRequired('name_e')"
                                      class="text-danger"
                                  >*</span
                                  >
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
                                      id="field-2"
                                  />
                              </div>
                              <div
                                  v-if="!$v.create.name_e.minLength"
                                  class="invalid-feedback"
                              >
                                  {{ $t("general.Itmustbeatleast") }}
                                  {{ $v.create.name_e.$params.minLength.min }}
                                  {{ $t("general.letters") }}
                              </div>
                              <div
                                  v-if="!$v.create.name_e.maxLength"
                                  class="invalid-feedback"
                              >
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.create.name_e.$params.maxLength.max }}
                                  {{ $t("general.letters") }}
                              </div>
                              <template v-if="errors.name_e">
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.name_e"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                  </div>
                  <hr
                      v-if="
                          isVisible('nationality') ||
                          isVisible('country_id') ||
                          isVisible('city_id') ||
                          isVisible('national_id') ||
                          isVisible('passport_no')
                        "
                      style="
                          margin: 10px 0 !important;
                          border-top: 1px solid rgb(141 163 159 / 42%);
                        "
                  />
                  <div class="row">
                      <div class="col-md-4" v-if="isVisible('nationality')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{
                                      getCompanyKey("general_customer_nationality")
                                  }}
                                  <span
                                      v-if="isRequired('nationality')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <input
                                  type="text"
                                  class="form-control"
                                  data-create="9"
                                  v-model="$v.create.nationality.$model"
                                  :class="{
                                'is-invalid':
                                  $v.create.nationality.$error ||
                                  errors.nationality,
                                'is-valid':
                                  !$v.create.nationality.$invalid &&
                                  !errors.nationality,
                              }"
                              />
                              <template v-if="errors.nationality">
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.nationality"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('country_id')">
                          <div class="form-group position-relative">
                              <label class="control-label">
                                  {{ getCompanyKey("general_customer_country") }}
                                  <span
                                      v-if="isRequired('country_id')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <multiselect
                                  @input="showCountryModal"
                                  v-model="$v.create.country_id.$model"
                                  :options="countries.map((type) => type.id)"
                                  :custom-label="
                                (opt) => countries.find((x) => x.id == opt)? countries.find((x) => x.id == opt).name: null
                              "
                              >
                              </multiselect>
                              <div
                                  v-if="
                                $v.create.country_id.$error || errors.country_id
                              "
                                  class="text-danger"
                              >
                                  {{ $t("general.fieldIsRequired") }}
                              </div>
                              <template v-if="errors.country_id">
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.country_id"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('city_id')">
                          <div class="form-group position-relative">
                              <label class="control-label">
                                  {{ getCompanyKey("general_customer_city") }}
                                  <span
                                      v-if="isRequired('city_id')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <multiselect
                                  @input="getCity()"
                                  v-model="$v.create.city_id.$model"
                                  :options="cities.map((type) => type.id)"
                                  :custom-label="
                                (opt) => cities.find((x) => x.id == opt) ?cities.find((x) => x.id == opt).name: null
                              "
                              >
                              </multiselect>
                              <div
                                  v-if="$v.create.city_id.$error || errors.city_id"
                                  class="text-danger"
                              >
                                  {{ $t("general.fieldIsRequired") }}
                              </div>
                              <template v-if="errors.city_id">
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.city_id"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('national_id')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{
                                      getCompanyKey("general_customer_national_id")
                                  }}
                                  <span
                                      v-if="isRequired('national_id')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <input
                                  type="number"
                                  class="form-control"
                                  step="0.1"
                                  v-model="$v.create.national_id.$model"
                                  :class="{
                                'is-invalid':
                                  $v.create.national_id.$error ||
                                  errors.national_id,
                                'is-valid':
                                  !$v.create.national_id.$invalid &&
                                  !errors.national_id,
                              }"
                              />
                              <template v-if="errors.national_id">
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.national_id"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('passport_no')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{
                                      getCompanyKey(
                                          "general_customer_passport_number"
                                      )
                                  }}
                                  <span
                                      v-if="isRequired('passport_no')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <input
                                  type="number"
                                  class="form-control"
                                  step="0.1"
                                  v-model="$v.create.passport_no.$model"
                                  :class="{
                                'is-invalid':
                                  $v.create.passport_no.$error ||
                                  errors.passport_no,
                                'is-valid':
                                  !$v.create.passport_no.$invalid &&
                                  !errors.passport_no,
                              }"
                              />
                              <template v-if="errors.passport_no">
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.passport_no"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                  </div>
                  <hr
                      v-if="
                          isVisible('bank_account_id') ||
                          isVisible('rp_code') ||
                          isVisible('phone') ||
                          isVisible('whatsapp') ||
                          isVisible('email')
                        "
                      style="
                          margin: 10px 0 !important;
                          border-top: 1px solid rgb(141 163 159 / 42%);
                        "
                  />
                  <div class="row">
                      <div
                          class="col-md-4"
                          v-if="isVisible('bank_account_id')"
                      >
                          <div class="form-group position-relative">
                              <label class="control-label">
                                  {{ getCompanyKey("bank_account") }}
                                  <span
                                      v-if="isRequired('bank_account_id')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <multiselect
                                  @input="showBankAccountModal"
                                  v-model="$v.create.bank_account_id.$model"
                                  :options="bank_accounts.map((type) => type.id)"
                                  :custom-label="
                                (opt) =>
                                  bank_accounts.find((x) => x.id == opt)  ?bank_accounts.find((x) => x.id == opt)
                                    .account_number: null
                              "
                              >
                              </multiselect>
                              <div
                                  v-if="
                                $v.create.bank_account_id.$error ||
                                errors.bank_account_id
                              "
                                  class="text-danger"
                              >
                                  {{ $t("general.fieldIsRequired") }}
                              </div>
                              <template v-if="errors.bank_account_id">
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.bank_account_id"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('rp_code')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{ getCompanyKey("general_customer_code") }}
                                  <span
                                      v-if="isRequired('rp_code')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <input
                                  type="text"
                                  class="form-control"
                                  data-create="9"
                                  v-model="$v.create.rp_code.$model"
                                  :class="{
                                'is-invalid':
                                  $v.create.rp_code.$error || errors.rp_code,
                                'is-valid':
                                  !$v.create.rp_code.$invalid &&
                                  !errors.rp_code,
                              }"
                              />
                              <template v-if="errors.rp_code">
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.rp_code"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('phone')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{ getCompanyKey("general_customer_phone") }}
                                  <span
                                      v-if="isRequired('phone')"
                                      class="text-danger"
                                  >*</span
                                  >
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
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.phone"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('whatsapp')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{ getCompanyKey("general_customer_whatsapp") }}
                                  <span
                                      v-if="isRequired('whatsapp')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <VuePhoneNumberInput
                                  v-model="$v.create.whatsapp.$model"
                                  :default-country-code="codeCountry"
                                  valid-color="#28a745"
                                  error-color="#dc3545"
                                  :preferred-countries="['FR', 'EG', 'DE']"
                                  @update="updateWhatsapp"
                              />
                              <template v-if="errors.whatsapp">
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.whatsapp"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('email')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{ getCompanyKey("general_customer_email") }}
                                  <span
                                      v-if="isRequired('email')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <input
                                  type="text"
                                  class="form-control"
                                  data-create="9"
                                  v-model="$v.create.email.$model"
                                  :class="{
                                'is-invalid':
                                  $v.create.email.$error || errors.email,
                                'is-valid':
                                  !$v.create.email.$invalid && !errors.email,
                              }"
                              />
                              <template v-if="errors.email">
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.email"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                  </div>
                  <hr
                      v-if="
                          isVisible('contact_person') ||
                          isVisible('sector_id') ||
                          isVisible('customer_source_id') ||
                          isVisible('customer_sub_category_id') ||
                          isVisible('customer_main_category_id') ||
                          isVisible('contact_phone') ||
                          isVisible('employee_id') ||
                          isVisible('salesman_id')
                        "
                      style="
                          margin: 10px 0 !important;
                          border-top: 1px solid rgb(141 163 159 / 42%);
                        "
                  />
                  <div class="row">
                      <div
                          class="col-md-4"
                          v-if="isVisible('contact_person')"
                      >
                          <div class="form-group">
                              <label class="control-label">
                                  {{
                                      getCompanyKey("general_customer_contact_person")
                                  }}
                                  <span
                                      v-if="isRequired('contact_person')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <input
                                  type="text"
                                  class="form-control"
                                  data-create="9"
                                  v-model="$v.create.contact_person.$model"
                                  :class="{
                                'is-invalid':
                                  $v.create.contact_person.$error ||
                                  errors.contact_person,
                                'is-valid':
                                  !$v.create.contact_person.$invalid &&
                                  !errors.contact_person,
                              }"
                              />
                              <template v-if="errors.contact_person">
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.contact_person"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('contact_phone')">
                          <div class="form-group">
                              <label class="control-label">
                                  {{
                                      getCompanyKey("general_customer_contact_phones")
                                  }}
                                  <span
                                      v-if="isRequired('contact_phone')"
                                      class="text-danger"
                                  >*</span
                                  >
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
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.contact_phone"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('employee_id')">
                          <div class="form-group">
                              <label
                              >{{ getCompanyKey("customer_employee")
                                  }}<span
                                      v-if="isRequired('employee_id')"
                                      class="text-danger"
                                  >*</span
                                  ></label
                              >
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
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.employee_id"
                                      :key="index"
                                  >{{ errorMessage }}
                                  </ErrorMessage>
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('salesman_id')">
                          <div class="form-group">
                              <label
                              >{{ getCompanyKey("customer_salesman")
                                  }}<span
                                      v-if="isRequired('salesman_id')"
                                      class="text-danger"
                                  >*</span
                                  ></label
                              >
                              <multiselect
                                  @input="showSalesmanModal"
                                  v-model="create.salesman_id"
                                  :options="salesmen.map((type) => type.id)"
                                  :custom-label="
                                (opt) =>
                                  salesmen.find((x) => x.id == opt) ?$i18n.locale == 'ar'
                                    ? salesmen.find((x) => x.id == opt).name
                                    : salesmen.find((x) => x.id == opt).name_e: null
                              "
                                  :class="{
                                'is-invalid':
                                  $v.create.salesman_id.$error ||
                                  errors.salesman_id,
                                'is-valid':
                                  !$v.create.salesman_id.$invalid &&
                                  !errors.salesman_id,
                              }"
                              >
                              </multiselect>
                              <template v-if="errors.salesman_id">
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.salesman_id"
                                      :key="index"
                                  >{{ errorMessage }}
                                  </ErrorMessage>
                              </template>
                          </div>
                      </div>
                      <div
                          class="col-md-4"
                          v-if="isVisible('customer_main_category_id')"
                      >
                          <div class="form-group">
                              <label
                              >{{ getCompanyKey("customer_main_category")
                                  }}<span
                                      v-if="isRequired('customer_main_category_id')"
                                      class="text-danger"
                                  >*</span
                                  ></label
                              >
                              <multiselect
                                  @input="
                                getCustomerCategorySub(
                                  create.customer_main_category_id
                                )
                              "
                                  v-model="create.customer_main_category_id"
                                  :options="
                                main_customer_categories.map((type) => type.id)
                              "
                                  :custom-label="
                                (opt) =>
                                  main_customer_categories.find((x) => x.id == opt) ?$i18n.locale == 'ar'
                                    ? main_customer_categories.find(
                                        (x) => x.id == opt
                                      ).name
                                    : main_customer_categories.find(
                                        (x) => x.id == opt
                                      ).name_e: null
                              "
                                  :class="{
                                'is-invalid':
                                  $v.create.customer_main_category_id.$error ||
                                  errors.customer_main_category_id,
                                'is-valid':
                                  !$v.create.customer_main_category_id
                                    .$invalid &&
                                  !errors.customer_main_category_id,
                              }"
                              >
                              </multiselect>
                              <template v-if="errors.customer_main_category_id">
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.customer_main_category_id"
                                      :key="index"
                                  >{{ errorMessage }}
                                  </ErrorMessage>
                              </template>
                          </div>
                      </div>
                      <div
                          class="col-md-4"
                          v-if="isVisible('customer_sub_category_id')"
                      >
                          <div class="form-group">
                              <label
                              >{{ getCompanyKey("customer_sub_category")
                                  }}<span
                                      v-if="isRequired('customer_sub_category_id')"
                                      class="text-danger"
                                  >*</span
                                  ></label
                              >
                              <multiselect
                                  @input="showCustomerCategoryModal"
                                  v-model="create.customer_sub_category_id"
                                  :options="
                                sub_customer_categories.map((type) => type.id)
                              "
                                  :custom-label="
                                (opt) =>
                                  sub_customer_categories.find((x) => x.id == opt) ?$i18n.locale == 'ar'
                                    ? sub_customer_categories.find(
                                        (x) => x.id == opt
                                      ).name
                                    : sub_customer_categories.find(
                                        (x) => x.id == opt
                                      ).name_e : null
                              "
                                  :class="{
                                'is-invalid':
                                  $v.create.customer_sub_category_id.$error ||
                                  errors.customer_sub_category_id,
                                'is-valid':
                                  !$v.create.customer_sub_category_id
                                    .$invalid &&
                                  !errors.customer_sub_category_id,
                              }"
                              >
                              </multiselect>
                              <template v-if="errors.customer_sub_category_id">
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.customer_sub_category_id"
                                      :key="index"
                                  >{{ errorMessage }}
                                  </ErrorMessage>
                              </template>
                          </div>
                      </div>
                      <div
                          class="col-md-4"
                          v-if="isVisible('customer_source_id')"
                      >
                          <div class="form-group">
                              <label
                              >{{ getCompanyKey("customer_source")
                                  }}<span
                                      v-if="isRequired('customer_source_id')"
                                      class="text-danger"
                                  >*</span
                                  ></label
                              >
                              <multiselect
                                  @input="showCustomerResouModal"
                                  v-model="create.customer_source_id"
                                  :options="customer_sources.map((type) => type.id)"
                                  :custom-label="
                                (opt) =>
                                  customer_sources.find((x) => x.id == opt)? $i18n.locale == 'ar'
                                    ? customer_sources.find((x) => x.id == opt)
                                        .name
                                    : customer_sources.find((x) => x.id == opt)
                                        .name_e : null
                              "
                                  :class="{
                                'is-invalid':
                                  $v.create.customer_source_id.$error ||
                                  errors.customer_source_id,
                                'is-valid':
                                  !$v.create.customer_source_id.$invalid &&
                                  !errors.customer_source_id,
                              }"
                              >
                              </multiselect>
                              <template v-if="errors.customer_source_id">
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.customer_source_id"
                                      :key="index"
                                  >{{ errorMessage }}
                                  </ErrorMessage>
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('sector_id')">
                          <div class="form-group">
                              <label
                              >{{ getCompanyKey("customer_sector")
                                  }}<span
                                      v-if="isRequired('sector_id')"
                                      class="text-danger"
                                  >*</span
                                  ></label
                              >
                              <multiselect
                                  v-model="create.sector_id"
                                  :options="sectors.map((type) => type.id)"
                                  :custom-label="
                                (opt) =>
                                   sectors.find((x) => x.id == opt)?$i18n.locale == 'ar'
                                    ? sectors.find((x) => x.id == opt).name
                                    : sectors.find((x) => x.id == opt).name_e: null
                              "
                                  :class="{
                                'is-invalid':
                                  $v.create.sector_id.$error ||
                                  errors.sector_id,
                                'is-valid':
                                  !$v.create.sector_id.$invalid &&
                                  !errors.sector_id,
                              }"
                              >
                              </multiselect>
                              <template v-if="errors.sector_id">
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.sector_id"
                                      :key="index"
                                  >{{ errorMessage }}
                                  </ErrorMessage>
                              </template>
                          </div>
                      </div>
                  </div>
                  <hr
                      v-if="
                          isVisible('facebook') ||
                          isVisible('instagram') ||
                          isVisible('linkedin') ||
                          isVisible('snapchat') ||
                          isVisible('customer_group_id') ||
                          isVisible('is_supplier') ||
                          isVisible('twitter') ||
                          isVisible('website')
                        "
                      style="
                          margin: 10px 0 !important;
                          border-top: 1px solid rgb(141 163 159 / 42%);
                        "
                  />
                  <div class="row xyz">
                      <div class="col-md-4" v-if="isVisible('facebook')">
                          <div class="form-group">
                              <label for="field-1" class="control-label">
                                  {{ getCompanyKey("general_customer_facebook") }}
                                  <span
                                      v-if="isRequired('facebook')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <input
                                  type="url"
                                  class="form-control"
                                  data-create="1"
                                  v-model="$v.create.facebook.$model"
                                  :class="{
                                'is-invalid':
                                  $v.create.facebook.$error || errors.facebook,
                                'is-valid':
                                  !$v.create.facebook.$invalid &&
                                  !errors.facebook,
                              }"
                              />
                              <template v-if="errors.facebook">
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.facebook"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('instagram')">
                          <div class="form-group">
                              <label for="field-1" class="control-label">
                                  {{ getCompanyKey("general_customer_instagram") }}
                                  <span
                                      v-if="isRequired('instagram')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <input
                                  type="url"
                                  class="form-control"
                                  data-create="1"
                                  v-model="$v.create.instagram.$model"
                                  :class="{
                                'is-invalid':
                                  $v.create.instagram.$error ||
                                  errors.instagram,
                                'is-valid':
                                  !$v.create.instagram.$invalid &&
                                  !errors.instagram,
                              }"
                              />
                              <template v-if="errors.instagram">
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.instagram"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('linkedin')">
                          <div class="form-group">
                              <label for="field-1" class="control-label">
                                  {{ getCompanyKey("general_customer_linkedin") }}
                                  <span
                                      v-if="isRequired('linkedin')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <input
                                  type="url"
                                  class="form-control"
                                  data-create="1"
                                  v-model="$v.create.linkedin.$model"
                                  :class="{
                                'is-invalid':
                                  $v.create.linkedin.$error || errors.linkedin,
                                'is-valid':
                                  !$v.create.linkedin.$invalid &&
                                  !errors.linkedin,
                              }"
                              />
                              <template v-if="errors.linkedin">
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.linkedin"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('snapchat')">
                          <div class="form-group">
                              <label for="field-1" class="control-label">
                                  {{ getCompanyKey("general_customer_snapchat") }}
                                  <span
                                      v-if="isRequired('snapchat')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <input
                                  type="url"
                                  class="form-control"
                                  data-create="1"
                                  v-model="$v.create.snapchat.$model"
                                  :class="{
                                'is-invalid':
                                  $v.create.snapchat.$error || errors.snapchat,
                                'is-valid':
                                  !$v.create.snapchat.$invalid &&
                                  !errors.snapchat,
                              }"
                              />
                              <template v-if="errors.snapchat">
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.snapchat"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('twitter')">
                          <div class="form-group">
                              <label for="field-1" class="control-label">
                                  {{ getCompanyKey("general_customer_twitter") }}
                                  <span
                                      v-if="isRequired('twitter')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <input
                                  type="url"
                                  class="form-control"
                                  data-create="1"
                                  v-model="$v.create.twitter.$model"
                                  :class="{
                                'is-invalid':
                                  $v.create.twitter.$error || errors.twitter,
                                'is-valid':
                                  !$v.create.twitter.$invalid &&
                                  !errors.twitter,
                              }"
                              />
                              <template v-if="errors.twitter">
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.twitter"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('website')">
                          <div class="form-group">
                              <label for="field-1" class="control-label">
                                  {{ getCompanyKey("general_customer_website") }}
                                  <span
                                      v-if="isRequired('facebook')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <input
                                  type="url"
                                  class="form-control"
                                  data-create="1"
                                  v-model="$v.create.website.$model"
                                  :class="{
                                'is-invalid':
                                  $v.create.website.$error || errors.website,
                                'is-valid':
                                  !$v.create.website.$invalid &&
                                  !errors.website,
                              }"
                              />
                              <template v-if="errors.website">
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.website"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div
                          class="col-md-4"
                          v-if="isVisible('customer_group_id')"
                      >
                          <div class="form-group position-relative">
                              <label class="control-label">
                                  {{ getCompanyKey("general_customer_group") }}
                                  <span
                                      v-if="isRequired('customer_group_id')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <multiselect
                                  @input="showGroupModal"
                                  v-model="$v.create.customer_group_id.$model"
                                  :options="groups.map((type) => type.id)"
                                  :custom-label="
                                (opt) => groups.find((x) => x.id == opt) ?groups.find((x) => x.id == opt).title_e : null
                              "
                              >
                              </multiselect>
                              <div
                                  v-if="
                                $v.create.customer_group_id.$error ||
                                errors.customer_group_id
                              "
                                  class="text-danger"
                              >
                                  {{ $t("general.fieldIsRequired") }}
                              </div>
                              <template v-if="errors.customer_group_id">
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.customer_group_id"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                      <div class="col-md-4" v-if="isVisible('is_supplier')">
                          <div class="form-group">
                              <label class="mr-2">
                                  {{
                                      getCompanyKey("general_customer_is_supplier")
                                  }}
                                  <span
                                      v-if="isRequired('is_supplier')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <b-form-group
                                  :class="{
                                'is-invalid':
                                  $v.create.is_supplier.$error ||
                                  errors.is_supplier,
                                'is-valid':
                                  !$v.create.is_supplier.$invalid &&
                                  !errors.is_supplier,
                              }"
                              >
                                  <b-form-radio
                                      @change="checkSupplierCreate"
                                      class="d-inline-block"
                                      v-model="$v.create.is_supplier.$model"
                                      name="some-radios"
                                      :value="1"
                                  >{{ $t("general.Yes") }}</b-form-radio
                                  >
                                  <b-form-radio
                                      @change="checkSupplierCreate"
                                      class="d-inline-block m-1"
                                      v-model="$v.create.is_supplier.$model"
                                      name="some-radios"
                                      :value="0"
                                  >{{ $t("general.No") }}</b-form-radio
                                  >
                              </b-form-group>
                              <template v-if="errors.is_supplier">
                                  <ErrorMessage
                                      v-for="(
                                  errorMessage, index
                                ) in errors.is_supplier"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
                              </template>
                          </div>
                      </div>
                  </div>
                  <hr
                      v-if="isVisible('note')"
                      style="
                          margin: 10px 0 !important;
                          border-top: 1px solid rgb(141 163 159 / 42%);
                        "
                  />
                  <div class="row">
                      <div class="col-md-6" v-if="isVisible('note')">
                          <div class="form-group">
                              <label for="field-1" class="control-label">
                                  {{ getCompanyKey("general_customer_note") }}
                                  <span
                                      v-if="isRequired('note')"
                                      class="text-danger"
                                  >*</span
                                  >
                              </label>
                              <textarea
                                  v-model="$v.create.note.$model"
                                  :class="{
                                'is-invalid':
                                  $v.create.note.$error || errors.note,
                                'is-valid':
                                  !$v.create.note.$invalid && !errors.note,
                              }"
                                  rows="4"
                              ></textarea>
                              <template v-if="errors.note">
                                  <ErrorMessage
                                      v-for="(errorMessage, index) in errors.note"
                                      :key="index"
                                  >{{ errorMessage }}</ErrorMessage
                                  >
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
