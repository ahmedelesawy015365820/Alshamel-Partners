<template>
  <div>
    <Bank
        :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'bank_account_bank'"
        @created="getBank" :isPage="false" type="create" :isPermission="isPermission"
    />
    <Employee
          :id="'employee-create-bankAccount'"
          :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getEmployees"
          :isPage="false" type="create" :isPermission="isPermission"
    />
    <b-modal
          :id="id"
          :title="type != 'edit'?getCompanyKey('bank_account_create_form'):getCompanyKey('bank_account_edit_form')"
          title-class="font-18"
          dialog-class="modal-full-width"
          :hide-footer="true"
          body-class="bankAccount"
          @show="resetModal"
          @hidden="resetModalHidden"
      >
          <form>
              <loader size="large" v-if="isCustom && !isPage" />
              <div class="card">
                  <div class="card-body">
                      <div class="mt-1 d-flex justify-content-end">
                          <!-- Emulate built in modal footer ok and cancel button actions -->
                          <b-button
                              v-if="type != 'edit'"
                              variant="success"
                              :disabled="!bankAccount_id"
                              @click.prevent="resetForm"
                              type="button"
                              :class="['font-weight-bold px-2', bankAccount_id ? 'mx-2' : '']"
                          >
                              {{ $t("general.AddNewRecord") }}
                          </b-button>

                          <template v-if="!is_disabled">
                              <b-button
                                  variant="success"
                                  type="button"
                                  class="mx-1 font-weight-bold px-3"
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

                          <b-button
                              variant="danger"
                              class="font-weight-bold"
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
                              <div class="row">
                                  <div class="col-md-6" v-if="isVisible('bank_id')">
                                      <div class="form-group position-relative">
                                          <label class="control-label">
                                              {{ getCompanyKey("bank") }}
                                              <span v-if="isRequired('bank_id')" class="text-danger">*</span>
                                          </label>
                                          <multiselect
                                              @input="showBankModal"
                                              v-model="create.bank_id"
                                              :options="banks.map((type) => type.id)"
                                              :custom-label="
                                              (opt) => banks.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'? banks.find((x) => x.id == opt).name:
                                                    banks.find((x) => x.id == opt).name_e:
                                                    null
                                              "
                                          >
                                          </multiselect>
                                          <div
                                              v-if="$v.create.bank_id.$error || errors.bank_id"
                                              class="text-danger"
                                          >
                                              {{ $t("general.fieldIsRequired") }}
                                          </div>
                                          <template v-if="errors.bank_id">
                                              <ErrorMessage
                                                  v-for="(errorMessage, index) in errors.bank_id"
                                                  :key="index"
                                              >{{ errorMessage }}
                                              </ErrorMessage>
                                          </template>
                                      </div>
                                  </div>
                                  <div class="col-md-6" v-if="isVisible('bank_id')"></div>
                                  <div class="col-md-6" v-if="isVisible('account_number')" >
                                      <div class="form-group">
                                          <label for="field-1" class="control-label">
                                              {{ getCompanyKey("bank_account_number") }}
                                              <span v-if="isRequired('account_number')" class="text-danger">*</span>
                                          </label>
                                          <input
                                              type="number"
                                              class="form-control"
                                              data-create="1"
                                              @keypress.enter="moveInput('input', 'create', 2)"
                                              v-model="$v.create.account_number.$model"
                                              :class="{
                                  'is-invalid':
                                    $v.create.account_number.$error ||
                                    errors.account_number,
                                  'is-valid':
                                    !$v.create.account_number.$invalid &&
                                    !errors.account_number,
                                }"
                                              id="field-1"
                                          />
                                          <div
                                              v-if="!$v.create.account_number.minLength"
                                              class="invalid-feedback"
                                          >
                                              {{ $t("general.Itmustbeatleast") }}
                                              {{ $v.create.account_number.$params.minLength.min }}
                                              {{ $t("general.letters") }}
                                          </div>
                                          <div
                                              v-if="!$v.create.account_number.maxLength"
                                              class="invalid-feedback"
                                          >
                                              {{ $t("general.Itmustbeatmost") }}
                                              {{ $v.create.account_number.$params.maxLength.max }}
                                              {{ $t("general.letters") }}
                                          </div>

                                          <template v-if="errors.account_number">
                                              <ErrorMessage
                                                  v-for="(errorMessage, index) in errors.account_number"
                                                  :key="index"
                                              >{{ errorMessage }}
                                              </ErrorMessage>
                                          </template>
                                      </div>
                                  </div>
                                  <div class="col-md-6" v-if="isVisible('phone')">
                                      <div class="form-group">
                                          <label for="field-3" class="control-label">
                                              {{ getCompanyKey("bank_account_phone") }}
                                              <span v-if="isRequired('phone')" class="text-danger">*</span>
                                          </label>

                                          <input
                                              type="text"
                                              class="form-control"
                                              data-create="3"
                                              @keypress.enter="moveInput('input', 'create', 4)"
                                              v-model="$v.create.phone.$model"
                                              :class="{
                                  'is-invalid': $v.create.phone.$error || errors.phone,
                                  'is-valid': !$v.create.phone.$invalid && !errors.phone,
                                }"
                                              id="field-3"
                                          />

                                          <div
                                              v-if="!$v.create.phone.minLength"
                                              class="invalid-feedback"
                                          >
                                              {{ $t("general.Itmustbeatleast") }}
                                              {{ $v.create.phone.$params.minLength.min }}
                                              {{ $t("general.letters") }}
                                          </div>
                                          <div
                                              v-if="!$v.create.phone.maxLength"
                                              class="invalid-feedback"
                                          >
                                              {{ $t("general.Itmustbeatmost") }}
                                              {{ $v.create.phone.$params.maxLength.max }}
                                              {{ $t("general.letters") }}
                                          </div>
                                          <template v-if="errors.phone">
                                              <ErrorMessage
                                                  v-for="(errorMessage, index) in errors.phone"
                                                  :key="index"
                                              >{{ errorMessage }}
                                              </ErrorMessage>
                                          </template>
                                      </div>
                                  </div>
                                  <div class="col-md-6" v-if="isVisible('address')">
                                      <div class="form-group">
                                          <label for="field-2" class="control-label">
                                              {{ getCompanyKey("bank_account_address") }}
                                              <span v-if="isRequired('address')" class="text-danger">*</span>
                                          </label>
                                          <div dir="ltr">
                                              <input
                                                  type="text"
                                                  class="form-control"
                                                  data-create="2"
                                                  @keypress.enter="moveInput('input', 'create', 3)"
                                                  v-model="$v.create.address.$model"
                                                  :class="{
                                    'is-invalid':
                                      $v.create.address.$error || errors.address,
                                    'is-valid':
                                      !$v.create.address.$invalid && !errors.address,
                                  }"
                                                  id="field-2"
                                              />
                                          </div>
                                          <div
                                              v-if="!$v.create.address.minLength"
                                              class="invalid-feedback"
                                          >
                                              {{ $t("general.Itmustbeatleast") }}
                                              {{ $v.create.address.$params.minLength.min }}
                                              {{ $t("general.letters") }}
                                          </div>
                                          <div
                                              v-if="!$v.create.address.maxLength"
                                              class="invalid-feedback"
                                          >
                                              {{ $t("general.Itmustbeatmost") }}
                                              {{ $v.create.address.$params.maxLength.max }}
                                              {{ $t("general.letters") }}
                                          </div>
                                          <template v-if="errors.address">
                                              <ErrorMessage
                                                  v-for="(errorMessage, index) in errors.address"
                                                  :key="index"
                                              >{{ errorMessage }}
                                              </ErrorMessage>
                                          </template>
                                      </div>
                                  </div>
                                  <div class="col-md-6" v-if="isVisible('email')">
                                      <div class="form-group">
                                          <label for="field-4" class="control-label">
                                              {{ getCompanyKey("bank_account_email") }}
                                              <span v-if="isRequired('email')" class="text-danger">*</span>
                                          </label>
                                          <input
                                              type="text"
                                              class="form-control"
                                              data-create="4"
                                              @keypress.enter="moveInput('input', 'create', 5)"
                                              v-model="$v.create.email.$model"
                                              :class="{
                                  'is-invalid': $v.create.email.$error || errors.email,
                                  'is-valid': !$v.create.email.$invalid && !errors.email,
                                }"
                                              id="field-4"
                                          />
                                          <div v-if="!$v.create.email.email" class="invalid-feedback">
                                              {{ $t("general.notValidEmail") }}
                                          </div>
                                          <template v-if="errors.email">
                                              <ErrorMessage
                                                  v-for="(errorMessage, index) in errors.email"
                                                  :key="index"
                                              >{{ errorMessage }}
                                              </ErrorMessage>
                                          </template>
                                      </div>
                                  </div>
                                  <div class="col-md-6" v-if="isVisible('emp_id')">
                                      <div class="form-group">
                                          <label for="field-3" class="control-label">
                                              {{ getCompanyKey("bank_account_employee") }}
                                              <span v-if="isRequired('emp_id')" class="text-danger">*</span>
                                          </label>

                                          <multiselect
                                              @input="showEmployeeModal"
                                              v-model="create.emp_id"
                                              :options="employees.map((type) => type.id)"
                                              :custom-label="
                                              (opt) => employees.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'? employees.find((x) => x.id == opt).name:
                                                    employees.find((x) => x.id == opt).name_e:
                                                    null
                                              "
                                          >
                                          </multiselect>
                                          <div
                                              v-if="$v.create.emp_id.$error || errors.emp_id"
                                              class="text-danger"
                                          >
                                              {{ $t("general.fieldIsRequired") }}
                                          </div>
                                          <template v-if="errors.emp_id">
                                              <ErrorMessage
                                                  v-for="(errorMessage, index) in errors.emp_id"
                                                  :key="index"
                                              >{{ errorMessage }}
                                              </ErrorMessage>
                                          </template>
                                      </div>
                                  </div>
                                  <div class="col-md-6"  v-if="isVisible('rp_code')">
                                      <div class="form-group">
                                          <label for="field-3" class="control-label">
                                              {{ getCompanyKey("bank_account_rpcode") }}
                                              <span  v-if="isRequired('rp_code')" class="text-danger">*</span>
                                          </label>
                                          <input
                                              type="text"
                                              class="form-control"
                                              data-create="3"
                                              @keypress.enter="moveInput('input', 'create', 4)"
                                              v-model="$v.create.rp_code.$model"
                                              :class="{
                                  'is-invalid':
                                    $v.create.rp_code.$error || errors.rp_code,
                                  'is-valid':
                                    !$v.create.rp_code.$invalid && !errors.rp_code,
                                }"
                                          />

                                          <div
                                              v-if="!$v.create.rp_code.minLength"
                                              class="invalid-feedback"
                                          >
                                              {{ $t("general.Itmustbeatleast") }}
                                              {{ $v.create.rp_code.$params.minLength.min }}
                                              {{ $t("general.letters") }}
                                          </div>
                                          <div
                                              v-if="!$v.create.rp_code.maxLength"
                                              class="invalid-feedback"
                                          >
                                              {{ $t("general.Itmustbeatmost") }}
                                              {{ $v.create.rp_code.$params.maxLength.max }}
                                              {{ $t("general.letters") }}
                                          </div>
                                          <template v-if="errors.rp_code">
                                              <ErrorMessage
                                                  v-for="(errorMessage, index) in errors.rp_code"
                                                  :key="index"
                                              >{{ errorMessage }}
                                              </ErrorMessage>
                                          </template>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </b-tab>
                      <b-tab
                          :disabled="!bankAccount_id"
                          :title="$t('general.ImageUploads')"
                      >
                          <div class="row">
                              <input
                                  accept="image/png, image/gif, image/jpeg, image/jpg"
                                  type="file"
                                  id="uploadImageCreate"
                                  @change.prevent="onImageChanged"
                                  class="input-file-upload position-absolute"
                                  :class="[
                            'd-none',
                            {
                              'is-invalid': $v.create.media.$error || errors.media,
                              'is-valid': !$v.create.media.$invalid && !errors.media,
                            },
                          ]"
                              />
                              <div class="col-md-8 my-1">
                                  <!-- file upload -->
                                  <div
                                      class="row align-content-between"
                                      style="width: 100%; height: 100%"
                                  >
                                      <div class="col-12">
                                          <div class="d-flex flex-wrap">
                                              <div
                                                  :class="[
                                    'dropzone-previews col-4 position-relative mb-2',
                                  ]"
                                                  v-for="(photo, index) in images"
                                                  :key="photo.id"
                                              >
                                                  <div
                                                      :class="[
                                      'card mb-0 shadow-none border',
                                      images.length - 1 == index ? 'bg-primary' : '',
                                    ]"
                                                  >
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
                                                                      @error="src = img"
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
                                                                      {{ photo.name }}
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
                                          </div>
                                      </div>
                                      <div class="footer-image col-12">
                                          <b-button
                                              @click="changePhoto"
                                              variant="success"
                                              type="button"
                                              class="mx-1 font-weight-bold px-3"
                                              v-if="!isLoader"
                                          >
                                              {{ $t("general.Add") }}
                                          </b-button>
                                          <b-button variant="success" class="mx-1" disabled v-else>
                                              <b-spinner small></b-spinner>
                                              <span class="sr-only">{{ $t("login.Loading") }}...</span>
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

<script>
import {email, integer, maxLength, minLength, required, requiredIf} from "vuelidate/lib/validators";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import Bank from "./bank";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import img from "../../../assets/images/img-1.png";
import Employee from "./employee";
export default {
  name: "bankAccount",
  components: {
    ErrorMessage,
    loader,
    Multiselect,
    Bank,Employee
  },
  mixins: [transMixinComp,successError],
  props: {
      id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
      isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
      type: {default: 'create'}, idObjEdit: {default: null},isPermission:{},url: {default: '/bank-accounts'}
    },
  data() {
    return {
      img,
      fields: [],
      bankAccounts: [],
      isLoader: false,
      create: {
        bank_id: null,
        account_number: "",
        phone: "",
        address: "",
        email: "",
        emp_id: "",
        rp_code: "",
      },
      errors: {},
      images: [],
      media: {},
      is_disabled: false,
      isCustom: false,
      bankAccount_id: null,
      current_page: 1,
      showPhoto: img,
      idDelete: null,
      banks: [],
      employees: []
    }
  },
  validations: {
      create: {
          bank_id: { required: requiredIf(function (model) {
                  return this.isRequired("bank_id");
              }), integer },
          account_number: {
              required: requiredIf(function (model) {
                  return this.isRequired("account_number");
              }), integer, minLength: minLength(2), maxLength: maxLength(100),},
          phone: { required: requiredIf(function (model) {
                  return this.isRequired("phone");
              }), minLength: minLength(2), maxLength: maxLength(100) },
          address: { required: requiredIf(function (model) {
                  return this.isRequired("address");
              }), minLength: minLength(2), maxLength: maxLength(100) },
          email: { required: requiredIf(function (model) {
                  return this.isRequired("email");
              }), email },
          emp_id: { required: requiredIf(function (model) {
                  return this.isRequired("emp_id");
              })},
          rp_code: { required: requiredIf(function (model) {
                  return this.isRequired("rp_code");
              }), minLength: minLength(2), maxLength: maxLength(100) },
          media: {},
      }
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
  },
  methods: {
      async getCustomTableFields() {
          this.isCustom = true;
           await adminApi
              .get(`/customTable/table-columns/general_bank_accounts`)
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
    showBankModal() {
      if (this.create.bank_id == 0) {
        this.$bvModal.show("bank_account_bank");
        this.create.bank_id = null;
      }
    },
    showEmployeeModal() {
          if (this.create.emp_id == 0) {
              this.$bvModal.show("employee-create-bankAccount");
              this.create.emp_id = null;
          }
      },
    defaultData(edit = null){
        this.create = {
            bank_id: null,
            account_number: "",
            phone: "",
            address: "",
            email: "",
            emp_id: "",
            rp_code: "",
            media: null,
        };
        this.images = [];
        this.bankAccount_id = null;
        this.showPhoto = img;
        this.is_disabled = false;
        this.errors = {};
        this.media = {};
        this.$nextTick(() => {
            this.$v.$reset();
        });
    },
    resetModalHidden() {
      this.defaultData();
      this.$bvModal.hide(this.id);
    },
    resetModal() {
      this.defaultData();
      setTimeout( async () => {
            if(this.type != 'edit'){
                if(!this.isPage)  await this.getCustomTableFields();
                if (this.isVisible("bank_id")) this.getBank();
                if (this.isVisible("emp_id")) this.getEmployees();
            }else {
                if(this.idObjEdit.dataObj){
                    let bankAccount = this.idObjEdit.dataObj;
                    this.bankAccount_id = bankAccount.id;
                    if (this.isVisible("bank_id"))  this.getBank();
                    this.create.bank_id = bankAccount.bank_id ?? null;
                    this.create.account_number = bankAccount.account_number;
                    this.create.phone = bankAccount.phone;
                    this.create.address = bankAccount.address;
                    this.create.email = bankAccount.email;
                    if (this.isVisible("emp_id"))  this.getEmployees();
                    this.create.emp_id = bankAccount.emp_id;
                    this.create.rp_code = bankAccount.rp_code;
                    this.images = bankAccount.media ?? [];
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
      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;
        if(this.type != 'edit' && !this.bankAccount_id){
            adminApi
                .post(this.url, { ...this.create, company_id: this.company_id })
                .then((res) => {
                    this.is_disabled = true;
                    this.bankAccount_id = res.data.data.id;
                    if(!this.isPage)
                        this.$emit("created");
                    else
                        this.$emit("getDataTable");
                    setTimeout(() => {
                        this.successFun('Addedsuccessfully');
                    }, 200);

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
                .put(`${this.url}/${this.bankAccount_id}`, {
                    ...this.create,
                    company_id: this.$store.getters["auth/company_id"],
                })
                .then((res) => {
                    this.$bvModal.hide(this.id);
                    this.$emit("getDataTable");
                    setTimeout(() => {
                        Swal.fire({
                            icon: "success",
                            text: `${this.$t("general.Editsuccessfully")}`,
                            showConfirmButton: false,
                            timer: 1500,
                        });
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
          formDate.append("media[0]", this.media);
          adminApi
            .post(`/media`, formDate)
            .then((res) => {
              let old_media = [];
              this.images.forEach((e) => old_media.push(e.id));
              let new_media = [];
              res.data.data.forEach((e) => new_media.push(e.id));

              adminApi
                .put(`/bank-accounts/${this.bankAccount_id}`, {
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
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                });
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
                    .put(`/bank-accounts/${this.bankAccount_id}`, {
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
                    })
                    .catch((err) => {
                        this.errorFun('Error','Thereisanerrorinthesystem');
                    });
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
        .put(`/bank-accounts/${this.bankAccount_id}`, { old_media })
        .then((res) => {
          this.bankAccounts[index] = res.data.data;
          this.images = res.data.data.media ?? [];
          if (this.images && this.images.length > 0) {
            this.showPhoto = this.images[this.images.length - 1].webp;
          } else {
            this.showPhoto = img;
          }
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        });
    },
    getBank() {
      this.isLoader = true;

       adminApi
        .get(`/banks/get-drop-down`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create Bank")) {
                l.unshift({ id: 0, name: "اضف بنك", name_e: "Add Bank" });
          }
          this.banks = l;
        })
        .catch((err) => {
          this.errorFun('Error','Thereisanerrorinthesystem');
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
    getEmployees() {
          adminApi
              .get(`/employees/get-drop-down`)
              .then((res) => {
                  let l = res.data.data;
                  if (this.isPermission("create Employee")) {
                      l.unshift({ id: 0, name: "اضف موظف", name_e: "Add Employee" });
                  }
                  this.employees = l;
              })
              .catch((err) => {
                  this.errorFun('Error','Thereisanerrorinthesystem');
              });
      }
  },
};
</script>
<style>
form {
    position: relative;
}
</style>
