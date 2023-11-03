<template>
  <div>
    <country :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'country-create-city'" @created="getCategory" />
    <governate
      :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'governorate-create-city'"
      :isPage="false" type="create" :isPermission="isPermission" :country_id="create.country_id"
      @created="getGovernorate(create.country_id)"
    />
    <!--  create   -->
    <b-modal
          :id="id"
          :title="type != 'edit'?getCompanyKey('city_create_form'): getCompanyKey('city_edit_form')"
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
                  <b-button
                      @click.prevent="resetModalHidden"
                      variant="danger"
                      type="button"
                  >
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
                              @input="getGovernorate(create.country_id)"
                              v-model="create.country_id"
                              :options="countries.map((type) => type.id)"
                              :custom-label="
                                  (opt) => countries.find((x) => x.id == opt)?
                                     $i18n.locale == 'ar'  ?countries.find((x) => x.id == opt).name:countries.find((x) => x.id == opt).name_e
                                    : null
                                "
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
                              >{{ errorMessage }}</ErrorMessage
                              >
                          </template>
                      </div>
                  </div>
                  <div class="col-md-6" v-if="isVisible('governorate_id') && !governorate_id">
                      <div class="form-group position-relative">
                          <label class="control-label">
                              {{ getCompanyKey("governorate") }}
                              <span v-if="isRequired('governorate_id')" class="text-danger">*</span>
                          </label>
                          <multiselect
                              @input="showGovernorateModal"
                              v-model="create.governorate_id"
                              :options="governorates.map((type) => type.id)"
                              :custom-label="
                                  (opt) => governorates.find((x) => x.id == opt)?
                                     $i18n.locale == 'ar'  ?governorates.find((x) => x.id == opt).name:governorates.find((x) => x.id == opt).name_e
                                    : null
                                "
                          >
                          </multiselect>
                          <div
                              v-if="$v.create.governorate_id.$error || errors.governorate_id"
                              class="text-danger"
                          >
                              {{ $t("general.fieldIsRequired") }}
                          </div>
                          <template v-if="errors.governorate_id">
                              <ErrorMessage
                                  v-for="(errorMessage, index) in errors.governorate_id"
                                  :key="index"
                              >{{ errorMessage }}</ErrorMessage
                              >
                          </template>
                      </div>
                  </div>
                  <div class="col-md-6 direction" dir="rtl" v-if="isVisible('name')">
                      <div class="form-group">
                          <label class="control-label">
                              {{ getCompanyKey("city_name_ar") }}
                              <span v-if="isRequired('name')" class="text-danger">*</span>
                          </label>
                          <input
                              @keyup="arabicValue(create.name)"
                              type="text"
                              class="form-control"
                              data-create="1"
                              @keypress.enter="moveInput('input', 'create', 2)"
                              v-model="$v.create.name.$model"
                              :class="{
                          'is-invalid': $v.create.name.$error || errors.name,
                          'is-valid': !$v.create.name.$invalid && !errors.name,
                        }"
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
                              <ErrorMessage
                                  v-for="(errorMessage, index) in errors.name"
                                  :key="index"
                              >{{ errorMessage }}</ErrorMessage
                              >
                          </template>
                      </div>
                  </div>
                  <div class="col-md-6 direction-ltr" dir="ltr" v-if="isVisible('name_e')">
                      <div class="form-group">
                          <label class="control-label">
                              {{ getCompanyKey("city_name_en") }}
                              <span v-if="isRequired('name_e')" class="text-danger">*</span>
                          </label>
                          <input
                              @keyup="englishValue(create.name_e)"

                              type="text"
                              class="form-control"
                              data-create="2"
                              @keypress.enter="moveInput('select', 'create', 5)"
                              v-model="$v.create.name_e.$model"
                              :class="{
                          'is-invalid': $v.create.name_e.$error || errors.name_e,
                          'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                        }"
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
                              <ErrorMessage
                                  v-for="(errorMessage, index) in errors.name_e"
                                  :key="index"
                              >{{ errorMessage }}</ErrorMessage
                              >
                          </template>
                      </div>
                  </div>
                  <div class="col-md-12" v-if="isVisible('is_active')">
                    <div class="form-group">
                      <label class="mr-2">
                        {{ getCompanyKey("city_status") }}
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
                          value="1"
                          >{{ $t("general.Active") }}</b-form-radio
                        >
                        <b-form-radio
                          class="d-inline-block m-1"
                          v-model="$v.create.is_active.$model"
                          name="some-radios"
                          value="0"
                          >{{ $t("general.Inactive") }}</b-form-radio
                        >
                      </b-form-group>
                      <template v-if="errors.is_active">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.is_active"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
              </div>
          </form>
      </b-modal>
    <!--  /create   -->
  </div>
</template>

<script>
import adminApi from "../../../api/adminAxios";
import {required, minLength, maxLength, integer, alpha, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import governate from "./governate";
import country from "./country";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import {arabicValue,englishValue} from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";

export default {
  components: {
    Multiselect,
    ErrorMessage,
    loader,
    governate,
    country,
  },
  mixins: [transMixinComp,successError],
  props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {}, governorate_id: {default: null},
        country_id: {default: null},url: {default: '/cities'}
    },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
  },
  validations: {
      create: {
          name: { required: requiredIf(function (model) {
                  return this.isRequired("name");
              }), minLength: minLength(2), maxLength: maxLength(100) },
          name_e: {
              required: requiredIf(function (model) {
                  return this.isRequired("name_e");
              }), minLength: minLength(2), maxLength: maxLength(100),
          },
          country_id: { required: requiredIf(function (model) {
                  return this.isRequired("country_id");
              }) },
          governorate_id: { required: requiredIf(function (model) {
                  return this.isRequired("governorate_id");
              }) },
          is_active: { required: requiredIf(function (model) {
                  return this.isRequired("is_active");
              }), integer },
      }
  },
  data() {
    return {
      isCustom: false,
      fields: [],
      isLoader: false,
      is_disabled: false,
      errors: {},
      create: {
        name: "",
        name_e: "",
        country_id: null,
        governorate_id: null,
        is_active: 1,
      },
      countries: [],
      governorates: [],
    };
  },
  methods: {
    async getCustomTableFields() {
          this.isCustom = true;
           await adminApi
              .get(`/customTable/table-columns/general_cities`)
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
            country_id: null,
            governorate_id: null,
            is_active: 1,
        };
        this.is_disabled = false;

        this.$nextTick(() => {
            this.$v.$reset();
        });
      },
    resetForm() {
      this.defaultData();
    },
    resetModal() {
      this.defaultData();
      setTimeout( async () => {
            if(this.type != 'edit'){
                if(!this.isPage) await this.getCustomTableFields();
                if (this.isVisible("country_id")) this.getCategory();
            }else {
                if(this.idObjEdit.dataObj){
                    let city = this.idObjEdit.dataObj;
                    this.errors = {};
                    if (this.isVisible("country_id")) this.getCategory();
                    if (this.isVisible("governorate_id")) this.getGovernorate(city.country.id);
                    this.create.name = city.name;
                    this.create.name_e = city.name_e;
                    this.create.is_active = city.is_active;
                    if (this.isVisible("country_id")) this.create.country_id = city.country?city.country.id:null;
                    if (this.isVisible("governorate_id")) this.create.governorate_id = city.governorate?city.governorate.id:null;
                }
            }
        },50);
    },
    showCountryModal() {
      if (this.create.country_id == 0) {
        this.$bvModal.show("country-create-city");
        this.create.country_id = null;
      } else {
        this.getGovernorate(this.create.country_id);
      }
    },
    showGovernorateModal() {
      if (this.create.governorate_id == 0) {
        this.$bvModal.show("governorate-create-city");
        this.create.governorate_id = null;
      }
    },
    getCategory() {
      this.countries = [];
      this.governorates = [];
      this.create.country_id = null;
      this.create.governorate_id = null;
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
        });
    },
    getGovernorate(id) {
        if (id == 0)
        {
            this.showCountryModal();
        }
      if (!id) return;
      this.governorates = [];
      this.create.governorate_id = null;
       adminApi
        .get(`/governorates/get-drop-down?country_id=${id}`)
        .then((res) => {
          let l = res.data.data;
            if (this.isPermission("create Governorate")) {
                l.unshift({ id: 0, name: "اضف محافظة جديدة", name_e: "Add new governorate" });
            }
          this.governorates = l;
        })
        .catch((err) => {
            this.errorFun('Error','Thereisanerrorinthesystem');
        });
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
      if( this.isVisible('governorate_id') && this.governorate_id ) this.create.governorate_id = this.governorate_id;

      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;

        if(this.type != 'edit'){
              adminApi
                  .post(this.url, { ...this.create, company_id: this.company_id })
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
    arabicValue(txt){
          this.create.name = arabicValue(txt);
    } ,
    englishValue(txt){
          this.create.name_e = englishValue(txt);
    }
  },
};
</script>

<style>
form {
   position: relative;
}
</style>
