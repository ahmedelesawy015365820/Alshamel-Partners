<script>
import adminApi from "../../../api/adminAxios";
import {required, minLength, maxLength, integer, requiredIf} from "vuelidate/lib/validators";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import salesManType from "./salesManType";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";

/**
 * Advanced Table component
 */
export default {
  components: {
    ErrorMessage,
    loader,
    Multiselect,
    salesManType
  },
  mixins: [transMixinComp,successError],
  data() {
    return {
        is_disabled:false,
        isLoader:false,
        isCustom:false,
      create: {
        name: "",
        name_e: "",
        salesman_type_id: null,
      },
      errors: {},
      salesmenTypes: [],
        fields: [],
    };
  },
  validations: {
      create: {
          name: { required: requiredIf(function (model) {
                  return this.isRequired("name");
              }), minLength: minLength(3), maxLength: maxLength(100) },
          name_e: { required: requiredIf(function (model) {
                  return this.isRequired("name_e");
              }), minLength: minLength(3), maxLength: maxLength(100) },
          salesman_type_id: { required: requiredIf(function (model) {
                  return this.isRequired("salesman_type_id");
              }) },
      },
  },
  mounted(){
    this.company_id=this.$store.getters["auth/company_id"];
  },
  props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/salesmen'}
    },
  methods: {
      async getCustomTableFields() {
          this.isCustom = true;
           await adminApi
              .get(`/customTable/table-columns/general_salesmen`)
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
              salesman_type_id: null,
          };
          this.is_disabled = false;
          this.$nextTick(() => {
              this.$v.$reset();
          });
          this.errors = {};
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
                  if (this.isVisible("salesman_type_id")) this.getSaleMenType();
              }else {
                  if(this.idObjEdit.dataObj){
                      let module = this.idObjEdit.dataObj;
                      this.errors = {};
                      if(this.isVisible('salesman_type_id'))  this.getSaleMenType();
                      this.create.name = module.name;
                      this.create.name_e = module.name_e;
                      if(module.salesmanType) this.create.salesman_type_id = module.salesmanType.id;
                  }
              }
          },50);
      },
      resetForm() {
          this.defaultData();
      },
      AddSubmit() {
      if (!this.create.name) {
        this.create.name = this.create.name_e;
      }
      if (!this.create.name_e) {
        this.create.name_e = this.create.name;
      }
      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
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
      getSaleMenType() {
          this.isLoader = true;

           adminApi
              .get(`/salesmen-types?is_empolyee=1`)
              .then((res) => {
                  let l = res.data.data;
                  if (this.isPermission("create Sales Man Type")) {
                      l.unshift({ id: 0, name: "اضف نوع رجل مبيعات", name_e: "Add Sales Man Type" });
                  }
                  this.salesmenTypes = l;
              })
              .catch((err) => {
                  this.errorFun('Error','Thereisanerrorinthesystem');
              })
              .finally(() => {
                  this.isLoader = false;
              });
      },
      showSaleManModal() {
          if (this.create.salesman_type_id == 0) {
              this.$bvModal.show("salesmanType-create-salesman");
              this.create.salesman_type_id = null;
          }
      },
      arabicValue(txt){
          this.create.name = arabicValue(txt);
      },
      englishValue(txt){
          this.create.name_e = englishValue(txt);
      }
  },
};
</script>

<template>
  <div>
      <salesManType
          :id="'salesmanType-create-salesman'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
          :isPage="false" type="create" :isPermission="isPermission" @created="getSaleMenType"
      />
      <!--  create   -->
      <b-modal
          :id="id"
          :title="type != 'edit'?getCompanyKey('sale_man_create_form'):getCompanyKey('sale_man_create_form')"
          title-class="font-18"
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
                      :class="[
                      'font-weight-bold px-2',
                      is_disabled ? 'mx-2' : '',
                    ]"
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
              <div class="row">
                  <div class="col-md-12" v-if="isVisible('salesman_type_id')">
                      <div class="form-group">
                          <label class="my-1 mr-2">
                              {{ getCompanyKey("sale_man_type") }}
                              <span v-if="isRequired('salesman_type_id')" class="text-danger">*</span>
                          </label>
                          <multiselect
                              @input="showSaleManModal"
                              v-model="create.salesman_type_id"
                              :options="salesmenTypes.map((type) => type.id)"
                              :custom-label="
                                (opt) => salesmenTypes.find((x) => x.id == opt)?
                                      $i18n.locale == 'ar' ? salesmenTypes.find((x) => x.id == opt).name:salesmenTypes.find((x) => x.id == opt).name_e
                                      : null
                                "
                          >
                          </multiselect>
                          <div
                              v-if="
                          $v.create.salesman_type_id.$error || errors.salesman_type_id
                        "
                              class="text-danger"
                          >
                              {{ $t("general.fieldIsRequired") }}
                          </div>
                          <template v-if="errors.salesman_type_id">
                              <ErrorMessage
                                  v-for="(errorMessage, index) in errors.salesman_type_id"
                                  :key="index"
                              >{{ errorMessage }}
                              </ErrorMessage>
                          </template>
                      </div>
                  </div>
                  <div class="col-md-12" v-if="isVisible('name')">
                      <div class="form-group">
                          <label for="field-1" class="control-label">
                              {{ getCompanyKey("sale_man_name_ar") }}
                              <span v-if="isRequired('name')" class="text-danger">*</span>
                          </label>
                          <div dir="rtl">
                              <input
                                  type="text"
                                  class="form-control arabicInput"
                                  data-create="1"
                                  @keypress.enter="moveInput('input', 'create', 2)"
                                  v-model="$v.create.name.$model"
                                  :class="{
                            'is-invalid': $v.create.name.$error || errors.name,
                            'is-valid': !$v.create.name.$invalid && !errors.name,
                          }"
                                  @keyup="arabicValue(create.name)"
                                  id="field-1"
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
                              <ErrorMessage
                                  v-for="(errorMessage, index) in errors.name"
                                  :key="index"
                              >
                                  {{ errorMessage }}
                              </ErrorMessage>
                          </template>
                      </div>
                  </div>
                  <div class="col-md-12" v-if="isVisible('name_e')">
                      <div class="form-group">
                          <label for="field-2" class="control-label">
                              {{ getCompanyKey("sale_man_name_en") }}
                              <span v-if="isRequired('name_e')" class="text-danger">*</span>
                          </label>
                          <div dir="ltr">
                              <input
                                  type="text"
                                  class="form-control englishInput"
                                  data-create="2"
                                  @keypress.enter="moveInput('select', 'create', 3)"
                                  v-model="$v.create.name_e.$model"
                                  :class="{
                            'is-invalid': $v.create.name_e.$error || errors.name_e,
                            'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                          }"
                                  @keyup="englishValue(create.name_e)"
                                  id="field-2"
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
                              <ErrorMessage
                                  v-for="(errorMessage, index) in errors.name_e"
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

<style>
form {
    position: relative;
}
</style>
