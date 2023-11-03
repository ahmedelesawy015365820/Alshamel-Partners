<template>
  <!--  create   -->
   <div>
       <Country :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'country-create-governate'" @created="getCategory" />
       <b-modal
           :id="id"
           :title="type != 'edit'?getCompanyKey('governorate_create_form'):getCompanyKey('governorate_create_form')"
           title-class="font-18"
           size="lg"
           body-class="p-4 "
           :hide-footer="true"
           @show="resetModal"
           @hidden="resetModalHidden"
       >
           <form>
               <loader size="large" v-if="isCustom && !isPage" />
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
                           type="submit"
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
                   <b-button @click.prevent="resetModalHidden" variant="danger" type="button">
                       {{ $t("general.Cancel") }}
                   </b-button>
               </div>
               <div class="row">
                   <div class="col-md-6" v-if="isVisible('country_id') && !country_id">
                       <div class="form-group position-relative">
                           <label class="control-label">
                               {{ getCompanyKey("country") }}
                               <span v-if="isRequired('country_id')" class="text-danger">*</span>
                           </label>
                           <multiselect
                               @input="showCountryModal"
                               v-model="create.country_id"
                               :options="countries.map((type) => type.id)"
                               :custom-label="(opt) => countries.find((x) => x.id == opt)?
                                 $i18n.locale == 'ar' ? countries.find((x) => x.id == opt).name:countries.find((x) => x.id == opt).name_e:
                                  null"
                           >
                           </multiselect>
                           <div
                               v-if="$v.create.country_id.$error || errors.country_id"
                               class="text-danger"
                           >
                               {{ $t("general.fieldIsRequired") }}
                           </div>
                           <template v-if="errors.country_id">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.country_id"
                                   :key="index"
                               >{{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
                   <div class="col-md-6" v-if="isVisible('phone_key')">
                       <div class="form-group">
                           <label for="field-5" class="control-label">
                               {{ getCompanyKey("governorate_phone_key") }}
                               <span v-if="isRequired('phone_key')" class="text-danger">*</span>
                           </label>
                           <input
                               type="number"
                               class="form-control"
                               data-create="4"
                               v-model="$v.create.phone_key.$model"
                               :class="{
                'is-invalid': $v.create.phone_key.$error || errors.phone_key,
                'is-valid': !$v.create.phone_key.$invalid && !errors.phone_key,
              }"
                               id="field-5"
                           />
                           <div v-if="!$v.create.phone_key.minLength" class="invalid-feedback">
                               {{ $t("general.Itmustbeatleast") }}
                               {{ $v.create.phone_key.$params.minLength.min }}
                               {{ $t("general.letters") }}
                           </div>
                           <div v-if="!$v.create.phone_key.maxLength" class="invalid-feedback">
                               {{ $t("general.Itmustbeatmost") }}
                               {{ $v.create.phone_key.$params.maxLength.max }}
                               {{ $t("general.letters") }}
                           </div>
                           <template v-if="errors.phone_key">
                               <ErrorMessage v-for="(errorMessage, index) in errors.phone_key" :key="index"
                               >{{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
                   <div class="col-md-6 direction" v-if="isVisible('name')" dir="rtl">
                       <div class="form-group">
                           <label for="field-1" class="control-label">
                               {{ getCompanyKey("governorate_name_ar") }}
                               <span v-if="isRequired('name')" class="text-danger">*</span>
                           </label>
                           <input
                               @keyup="arabicValue(create.name)"
                               type="text"
                               class="form-control"
                               data-create="1"
                               v-model="$v.create.name.$model"
                               :class="{
                'is-invalid': $v.create.name.$error || errors.name,
                'is-valid': !$v.create.name.$invalid && !errors.name,
              }"
                               id="field-1"
                           />
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
                               <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index"
                               >{{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
                   <div class="col-md-6 direction-ltr" v-if="isVisible('name_e')" dir="ltr">
                       <div class="form-group">
                           <label for="field-2" class="control-label">
                               {{ getCompanyKey("governorate_name_en") }}
                               <span v-if="isRequired('name_e')" class="text-danger">*</span>
                           </label>
                           <input
                               @keyup="englishValue(create.name_e)"
                               type="text"
                               class="form-control"
                               data-create="2"
                               v-model="$v.create.name_e.$model"
                               :class="{
                'is-invalid': $v.create.name_e.$error || errors.name_e,
                'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
              }"
                               id="field-2"
                           />
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
                               <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index"
                               >{{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
                   <div class="col-md-6" v-if="isVisible('is_default')">
                       <div class="form-group">
                           <label class="mr-2" for="field-11">
                               {{ getCompanyKey("governorate_default") }}
                               <span v-if="isRequired('is_default')" class="text-danger">*</span>
                           </label>
                           <select
                               class="custom-select mr-sm-2"
                               id="field-11"
                               data-create="5"
                               v-model="$v.create.is_default.$model"
                               :class="{
                'is-invalid': $v.create.is_default.$error || errors.is_default,
                'is-valid': !$v.create.is_default.$invalid && !errors.is_default,
              }"
                           >
                               <option value="" selected>{{ $t("general.Choose") }}...</option>
                               <option value="1">{{ $t("general.Yes") }}</option>
                               <option value="0">{{ $t("general.No") }}</option>
                           </select>
                           <template v-if="errors.is_default">
                               <ErrorMessage
                                   v-for="(errorMessage, index) in errors.is_default"
                                   :key="index"
                               >{{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
                   <div class="col-md-12" v-if="isVisible('is_active')">
                       <div class="form-group">
                           <label class="mr-2">
                               {{ getCompanyKey("governorate_status") }}
                               <span v-if="isRequired('is_active')" class="text-danger">*</span>
                           </label>
                           <b-form-group
                               :class="{
                'is-invalid': $v.create.is_active.$error || errors.is_active,
                'is-valid': !$v.create.is_active.$invalid && !errors.is_active,
              }"
                           >
                               <b-form-radio
                                   class="d-inline-block"
                                   v-model="$v.create.is_active.$model"
                                   name="some-radios"
                                   value="active"
                               >{{ $t("general.Active") }}</b-form-radio
                               >
                               <b-form-radio
                                   class="d-inline-block m-1"
                                   v-model="$v.create.is_active.$model"
                                   name="some-radios"
                                   value="inactive"
                               >{{ $t("general.Inactive") }}</b-form-radio
                               >
                           </b-form-group>
                           <template v-if="errors.is_active">
                               <ErrorMessage v-for="(errorMessage, index) in errors.is_active" :key="index"
                               >{{ errorMessage }}
                               </ErrorMessage>
                           </template>
                       </div>
                   </div>
               </div>
           </form>
       </b-modal>
   </div>
  <!--  /create   -->
</template>

<script>
import adminApi from "../../../api/adminAxios";
import {
  minLength,
  maxLength,
  integer,
  requiredIf,
} from "vuelidate/lib/validators";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";
import Country from './country'

export default {
  components: {
    ErrorMessage,
    loader,
    Multiselect,
    Country
  },
  props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},url: {default: '/governorates'},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},country_id: {default: null}
  },
  mixins: [transMixinComp,successError],
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
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
      phone_key: {
        required: requiredIf(function (model) {
          return this.isRequired("phone_key");
        }),
        integer,
        minLength: minLength(1),
        maxLength: maxLength(10),
      },
      is_default: {
        required: requiredIf(function (model) {
          return this.isRequired("is_default");
        }),
        integer,
      },
      country_id: {
        required: requiredIf(function (model) {
          return this.isRequired("country_id");
        }),
      },
      is_active: {
        required: requiredIf(function (model) {
          return this.isRequired("is_active");
        }),
      },
    },
  },
  data() {
    return {
      fields: [],
      isLoader: false,
      is_disabled: false,
      create: {
        name: "",
        name_e: "",
        phone_key: "",
        country_id: null,
        is_default: 0,
        is_active: "active",
      },
      errors: {},
      isCustom: false,
      countries: [],
    };
  },
  methods: {
      async getCustomTableFields() {
      this.isCustom = true;
       await adminApi
        .get(`/customTable/table-columns/general_governorates`)
        .then((res) => {
          this.fields = res.data;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
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
      defaultData(edit = null){
          this.create = {
              name: "",
              name_e: "",
              phone_key: "",
              country_id: null,
              is_default: 0,
              is_active: "active",
          };
          this.$nextTick(() => {
              this.$v.$reset();
          });
          this.is_disabled = false;
          this.errors = {};
      },
      getCategory() {
      this.isLoader = true;

       adminApi
        .get(`/countries/get-drop-down?is_active=active`)
        .then((res) => {
          let l = res.data.data;
            if (this.isPermission("create Country")) {
                l.unshift({ id: 0, name: "اضافة دولة", name_e: "Add Country" });
            }
          this.countries = l;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        })
        .finally(() => {
          this.isLoader = false;
        });
    },
      resetModal() {
          this.defaultData();
          setTimeout( async () => {
              if(this.type != 'edit'){
                  if(!this.isPage)  await this.getCustomTableFields();
                  this.$nextTick(() => {this.$v.$reset();});
                  if (this.isVisible("country_id")) this.getCategory();
              }else {
                  if(this.idObjEdit.dataObj){
                      let governorate = this.idObjEdit.dataObj;
                      this.errors = {};
                      this.create.name = governorate.name;
                      this.create.name_e = governorate.name_e;
                      this.create.is_active = governorate.is_active;
                      this.create.is_default = governorate.is_default;
                      if (this.isVisible("country_id"))  this.getCategory();
                      if (governorate.country) this.create.country_id = governorate.country.id;
                      this.create.phone_key = governorate.phone_key;
                  }
              }
          },50);
      },
    resetModalHidden() {
      this.defaultData();
      this.$bvModal.hide(this.id);
    },
    AddSubmit() {
      if (this.create.name || this.create.name_e) {
        this.create.name = this.create.name ? this.create.name : this.create.name_e;
        this.create.name_e = this.create.name_e ? this.create.name_e : this.create.name;
      }

      if( this.isVisible('country_id') && this.country_id ) this.create.country_id = this.country_id;

      this.$v.create.$touch();
      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;
          if(this.type != 'edit') {
              adminApi
                  .post(this.url, {...this.create, company_id: this.company_id})
                  .then((res) => {
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
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
    },
    showCountryModal() {
          if (this.create.country_id == 0) {
              this.$bvModal.show("country-create-governate");
              this.create.country_id = null;
          }
      }
  },
};
</script>

<style>
form {
    position: relative;
}
</style>
