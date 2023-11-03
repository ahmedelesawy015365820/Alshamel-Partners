<script>
import adminApi from "../../../api/adminAxios";
import {required, minLength, maxLength, integer, requiredIf} from "vuelidate/lib/validators";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../general/loader";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";
/**
 * Advanced Table component
 */
export default {
  mixins: [transMixinComp,successError],
  components: {
    ErrorMessage,
    loader,
  },
  data() {
    return {
      isCustom: false,
      isLoader: false,
      isCustom: false,
      create: {
        name: "",
        name_e: "",
        description: "",
        description_e: "",
        is_default: 1,
        is_active: "active",
      },
      fields: [],
      errors: {},
      is_disabled: false,
      company_id: null
    };
  },
  props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/recievable-payable/rp_installment_p_plan'}
    },
  mounted(){
        this.company_id = this.$store.getters["auth/company_id"];
  },
  validations: {
    create: {
        name: { required: requiredIf(function (model) {
                return this.isRequired("name");
            }), minLength: minLength(3), maxLength: maxLength(100) },
        name_e: { required: requiredIf(function (model) {
                return this.isRequired("name_e");
            }), minLength: minLength(3), maxLength: maxLength(100) },
        description: {required: requiredIf(function (model) {
                return this.isRequired("description");
            }), maxLength: maxLength(1000) },
        description_e: { required: requiredIf(function (model) {
                return this.isRequired("description_e");
            }),maxLength: maxLength(1000) },
        is_default: { required: requiredIf(function (model) {
                return this.isRequired("is_default");
            }) },
        is_active: { required: requiredIf(function (model) {
                return this.isRequired("is_active");
            }) },
    },
  },
  methods: {
      getCustomTableFields() {
          this.isCustom = true;
          adminApi
              .get(`/customTable/table-columns/rp_installment_payment_plans`)
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
              description: "",
              description_e: "",
              is_default: 1,
              is_active: "active",
          };
          this.$nextTick(() => {
              this.$v.$reset();
          });
          this.errors = {};
          this.is_disabled = false;
      },
      resetModalHidden() {
          this.defaultData();
          this.$bvModal.hide(this.id);
      },
      resetModal() {
          this.defaultData();
          setTimeout( () => {
              if(this.type != 'edit'){
                  if(!this.isPage)  this.getCustomTableFields();
              }else {
                  if(this.idObjEdit.dataObj){
                      let color = this.idObjEdit.dataObj;
                      this.errors = {};
                      this.create.name = color.name;
                      this.create.name_e = color.name_e;
                      this.create.description = color.description;
                      this.create.description_e = color.description_e;
                      this.create.is_active = color.is_active == 1 ? 'active' : 'inactive';
                      this.create.is_default = color.is_default;
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
          if (!this.create.description) {
              this.create.description = this.create.description_e;
          }
          if (!this.create.description_e) {
              this.create.description_e = this.create.description;
          }
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
                          is_active: this.create.is_active == "active" ? 1 : 0,
                          company_id: this.company_id
                      })
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
                          is_active: this.create.is_active == "active" ? 1 : 0,
                          company_id: this.company_id
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
      arabicValueDescription(txt){
          this.create.description = arabicValue(txt);
      },
      englishValueDescription(txt){
          this.create.description_e = englishValue(txt);
      },
      arabicValueName(txt) {
          this.create.name = arabicValue(txt);
      },
      englishValueName(txt) {
          this.create.name_e = englishValue(txt);
      },
  },
};
</script>

<template>
  <div>
      <!--  create   -->
      <b-modal
          :id="id"
          :title="type != 'edit'?getCompanyKey('installment_payment_plan_create'):getCompanyKey('installment_payment_edit_form')"
          title-class="font-18"
          body-class="p-4 "
          size="lg"
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
             <b-button
        @click.prevent="resetModalHidden"
        variant="danger"
        type="button"
      >
        {{ $t("general.Cancel") }}
      </b-button>
          </div>
          <div class="row">
              <div v-if="isVisible('name')" class="col-md-6">
                  <div class="form-group">
                      <label for="field-1" class="control-label">
                          {{ getCompanyKey("installment_payment_name_ar") }}
                          <span v-if="isRequired('name')" class="text-danger">*</span>
                      </label>
                      <div dir="rtl">
                          <input
                              type="text"
                              class="form-control"
                              data-create="1"
                              @keyup="arabicValueName(create.name)"
                              v-model="$v.create.name.$model"
                              :class="{
                                    'is-invalid': $v.create.name.$error || errors.name,
                                    'is-valid': !$v.create.name.$invalid && !errors.name,
                                  }"
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
              <div v-if="isVisible('name_e')" class="col-md-6">
                  <div class="form-group">
                      <label for="field-2" class="control-label">
                          {{ getCompanyKey("installment_payment_name_en") }}
                          <span v-if="isRequired('name_e')" class="text-danger">*</span>
                      </label>
                      <div>
                          <input
                              type="text"
                              class="form-control englishInput"
                              data-create="2"
                              @keyup="englishValueName(create.name_e)"
                              v-model="$v.create.name_e.$model"
                              :class="{
                                    'is-invalid': $v.create.name_e.$error || errors.name_e,
                                    'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                                  }"
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
              <div v-if="isVisible('is_default')" class="col-md-6">
                  <div class="form-group">
                      <label class="mr-2">
                          {{ getCompanyKey("is_default") }}
                          <span v-if="isRequired('is_default')" class="text-danger">*</span>
                      </label>
                      <b-form-group
                          :class="{
                          'is-invalid': $v.create.is_default.$error || errors.is_default,
                          'is-valid':
                            !$v.create.is_default.$invalid && !errors.is_default,
                        }"
                      >
                          <b-form-radio
                              class="d-inline-block"
                              v-model="$v.create.is_default.$model"
                              name="some-radios__create"
                              value="1"
                          >{{ $t("general.Yes") }}</b-form-radio
                          >
                          <b-form-radio
                              class="d-inline-block m-1"
                              v-model="$v.create.is_default.$model"
                              name="some-radios__create"
                              value="0"
                          >{{ $t("general.No") }}</b-form-radio
                          >
                      </b-form-group>
                      <template v-if="errors.is_default">
                          <ErrorMessage
                              v-for="(errorMessage, index) in errors.is_default"
                              :key="index"
                          >{{ errorMessage }}</ErrorMessage
                          >
                      </template>
                  </div>
              </div>
              <div v-if="isVisible('is_active')" class="col-md-6">
                  <div class="form-group">
                      <label class="mr-2">
                          {{ getCompanyKey("is_active") }}
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
                          <ErrorMessage
                              v-for="(errorMessage, index) in errors.is_active"
                              :key="index"
                          >{{ errorMessage }}
                          </ErrorMessage>
                      </template>
                  </div>
              </div>
              <div v-if="isVisible('description')" class="col-md-6">
                  <div class="form-group">
                      <label class="mr-2">
                          {{ getCompanyKey("installment_payment_description_ar") }}
                          <span v-if="isRequired('description')" class="text-danger">*</span>
                      </label>
                      <textarea @input="arabicValueDescription(create.description)" v-model="$v.create.description.$model" class="form-control" :maxlength="1000" rows="5"></textarea>
                      <template v-if="errors.description">
                          <ErrorMessage
                              v-for="(errorMessage, index) in errors.description"
                              :key="index"
                          >{{ errorMessage }}</ErrorMessage
                          >
                      </template>
                  </div>
              </div>
              <div v-if="isVisible('description_e')" class="col-md-6">
                  <div class="form-group">
                      <label class="mr-2">
                          {{ getCompanyKey("installment_payment_description_en") }}
                          <span v-if="isRequired('description_e')" class="text-danger">*</span>
                      </label>
                      <textarea  @input="englishValueDescription(create.description_e)" v-model="$v.create.description_e.$model" class="form-control" :maxlength="1000" rows="5"></textarea>
                      <template v-if="errors.description_e">
                          <ErrorMessage
                              v-for="(errorMessage, index) in errors.description_e"
                              :key="index"
                          >{{ errorMessage }}</ErrorMessage
                          >
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
