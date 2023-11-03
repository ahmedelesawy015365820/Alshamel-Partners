<script>
import adminApi from "../../../api/adminAxios";
import { required, requiredIf, minLength, maxLength, minValue, integer } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../general/loader";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import Multiselect from "vue-multiselect";
import InstallmentCond from "./installmentCondition";
import successError from "../../../helper/mixin/success&error";

/**
 * Advanced Table component
 */
export default {
  mixins: [transMixinComp,successError],
  components: {
    ErrorMessage,
    loader,
    Multiselect,
    InstallmentCond,
  },
  data() {
    return {
      isCustom: false,
      isLoader: false,
      conditions: [],
      fields: [],
      create: {
        name: "",
        name_e: "",
        auto_freq: 0,
        is_partially: 0,
        is_passed: 0,
        is_passed_all: 0,
        Freq_period: 0,
        is_conditional: 0,
        Condition_id: null,
        installmentPaymentType_freq: 0,
        step: 'D',
        is_passed_contract_plan: 0,
      },
      errors: {},
      is_disabled: false
    };
  },
  validations: {
    create: {
          name: { required: requiredIf(function (model) {
                  return this.isRequired("name");
              }), minLength: minLength(3), maxLength: maxLength(100) },
          name_e: { required: requiredIf(function (model) {
                  return this.isRequired("name");
              }), minLength: minLength(3), maxLength: maxLength(100) },
          is_partially: { required: requiredIf(function (model) {
                  return this.isRequired("is_partially");
              }) },
          is_passed: { required: requiredIf(function (model) {
                  return this.isRequired("is_passed");
              }) },
          is_passed_all: { required: requiredIf(function (model) {
                  return this.isRequired("is_passed_all");
              }) },
          Freq_period: { required: requiredIf(function (model) {
                  return this.isRequired("Freq_period");
              }) },
          is_conditional: { required: requiredIf(function (model) {
                  return this.isRequired("is_conditional");
              }) },
          installmentPaymentType_freq: {required: requiredIf(function (model) {
                  return this.isRequired("installmentPaymentType_freq");
              }) },
          step: {required: requiredIf(function (model) {
                  return this.isRequired("step");
              }) },
          is_passed_contract_plan: { required: requiredIf(function (model) {
                  return this.isRequired("is_passed_contract_plan");
              }) },
          Condition_id: {
            required: requiredIf(function (model) {
              return this.create.is_conditional == 1 && this.isRequired("Condition_id");
            }),
          },
          auto_freq: { required: requiredIf(function (model) {
                  return this.isRequired("auto_freq");
          }) },
    },
  },
  props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/recievable-payable/rp_installment_payment_types'}
    },
  methods: {
      async getCustomTableFields() {
          this.isCustom = true;
          await adminApi
              .get(`/customTable/table-columns/rp_installment_payment_types`)
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
      showConditionModal() {
          if (this.create.Condition_id == 0) {
              this.$bvModal.show("install_condition_create-payment-type");
              this.create.Condition_id = null;
          }
      },
      getConditions() {
      adminApi
        .get(`/recievable-payable/rp_installment_condation`)
        .then((res) => {
            let l = res.data.data;
            l.unshift({
                id: 0,
                name: "اضف شرط",
                name_e: "Add condition",
            });
            this.conditions = l;
        })
        .catch((err) => {
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
      defaultData(edit = null){
          this.create = {
              name: "",
              name_e: "",
              auto_freq: 0,
              is_partially: 0,
              is_passed: 0,
              is_passed_all: 0,
              Freq_period: 0,
              is_conditional: 0,
              Condition_id: null,
              installmentPaymentType_freq: 0,
              step: 'D',
              is_passed_contract_plan: 0,
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
          setTimeout( async () => {
              if(this.type != 'edit'){
                  if(!this.isPage) await  this.getCustomTableFields();
                  this.getConditions();
              }else {
                  if(this.idObjEdit.dataObj){
                      let module = this.idObjEdit.dataObj;
                      this.errors = {};
                      this.create.name = module.name;
                      this.create.name_e = module.name_e;
                      this.create.auto_freq = module.auto_freq;
                      this.create.is_partially = module.is_partially;
                      this.create.is_passed = module.is_passed;
                      this.create.is_passed_all = module.is_passed_all;
                      this.create.Freq_period = module.freq_period;
                      this.create.installmentPaymentType_freq = module.installment_payment_type_freq;
                      this.create.step = module.step;
                      this.create.is_passed_contract_plan = module.is_passed_contract_plan;
                      this.create.is_conditional = module.is_conditional;
                      this.create.Condition_id = module.installment_condation_id;
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
                      .post(this.url, {
                          ...this.create,
                          company_id: this.company_id,
                          installment_payment_type_freq: this.create.installmentPaymentType_freq,
                          freq_period: this.create.Freq_period,
                          installment_condation_id: this.create.Condition_id,
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
                          company_id: this.$store.getters["auth/company_id"],
                          installment_payment_type_freq: this.create.installmentPaymentType_freq,
                          freq_period: this.create.Freq_period,
                          installment_condation_id: this.create.Condition_id,
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
      arabicValueName(txt) {
          this.create.name = arabicValue(txt);
      },
      englishValueName(txt) {
          this.create.name_e = englishValue(txt);
      },
  },
};
</script>
<template><!--  create   -->
    <div>
      <InstallmentCond
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'install_condition_create-payment-type'"
            :isPage="false" type="create" :isPermission="isPermission" @created="getConditions"
        />
      <b-modal
          :id="id"
          size="lg"
          :title="type != 'edit'? getCompanyKey('installment_payment_type_create_form'):getCompanyKey('installment_payment_type_edit_form')"
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
              <div class="col-md-6" v-if="isVisible('name')">
                  <div class="form-group">
                      <label class="control-label">
                          {{ getCompanyKey("installment_payment_type_name") }}
                          <span v-if="isRequired('name')" class="text-danger">*</span>
                      </label>
                      <div dir="rtl">
                          <input type="text" class="form-control" data-create="1" @keyup="arabicValueName(create.name)"
                                 v-model="$v.create.name.$model" :class="{
                                'is-invalid': $v.create.name.$error || errors.name,
                                'is-valid': !$v.create.name.$invalid && !errors.name,
                              }"  />
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
              <div class="col-md-6" v-if="isVisible('name_e')">
                  <div class="form-group">
                      <label class="control-label">
                          {{ getCompanyKey("installment_payment_type_name_e") }}
                          <span v-if="isRequired('name_e')" class="text-danger">*</span>
                      </label>
                      <div>
                          <input type="text" class="form-control englishInput" data-create="2"
                                 @keyup="englishValueName(create.name_e)" v-model="$v.create.name_e.$model" :class="{
                                'is-invalid': $v.create.name_e.$error || errors.name_e,
                                'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                              }" />
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
                          <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index">{{ errorMessage }}
                          </ErrorMessage>
                      </template>
                  </div>
              </div>
              <div class="col-md-6" v-if="isVisible('is_conditional')">
                  <div class="form-group">
                      <label class="mr-2">
                          {{ getCompanyKey("is_conditional") }}
                      </label>
                      <b-form-group id="create-11" :class="{
                            'is-invalid':
                              $v.create.is_conditional.$error || errors.is_conditional,
                            'is-valid':
                              !$v.create.is_conditional.$invalid &&
                              !errors.is_conditional,
                          }">
                          <b-form-radio class="d-inline-block" v-model="$v.create.is_conditional.$model"
                                        name="some-radios-create-is_conditional" value="1">{{ $t("general.Yes") }}</b-form-radio>
                          <b-form-radio class="d-inline-block m-1" v-model="$v.create.is_conditional.$model"
                                        name="some-radios-create-is_conditional" value="0">{{ $t("general.No") }}</b-form-radio>
                      </b-form-group>
                      <template v-if="errors.is_conditional">
                          <ErrorMessage v-for="(errorMessage, index) in errors.is_conditional" :key="index">{{ errorMessage
                              }}
                          </ErrorMessage>
                      </template>
                  </div>
              </div>
              <template v-if="create.is_conditional == 1 && isVisible('installment_condation_id')">
                  <div class="col-md-6">
                      <div class="form-group">
                          <label class="my-1 mr-2">
                              {{ getCompanyKey("condition") }}
                              <span v-if="isRequired('installment_condation_id')" class="text-danger">*</span>
                          </label>
                          <multiselect @input="showConditionModal" v-model="create.Condition_id"
                                       :options="conditions.map((type) => type.id)" :custom-label="
                                (opt) => conditions.find((x) => x.id == opt)?
                                  $i18n.locale == 'ar'
                                    ? conditions.find((x) => x.id == opt).name
                                    : conditions.find((x) => x.id == opt).name_e: null
                              " :class="{
                                        'is-invalid':
                                            $v.create.Condition_id.$error || errors.Condition_id,
                                        }">
                          </multiselect>
                          <div v-if="!$v.create.Condition_id.required" class="invalid-feedback">
                              {{ $t("general.fieldIsRequired") }}
                          </div>

                          <template v-if="errors.condition_id">
                              <ErrorMessage v-for="(errorMessage, index) in errors.condition_id" :key="index">{{ errorMessage
                                  }}
                              </ErrorMessage>
                          </template>
                      </div>
                  </div>
              </template>
              <template v-else>
                <div class="col-md-6"></div>
              </template>
              <div class="col-md-6" v-if="isVisible('installment_payment_type_freq')">
                  <div class="form-group">
                      <label class="control-label">
                          {{ getCompanyKey("installmentPaymentType_freq") }}
                          <span v-if="isRequired('installment_payment_type_freq')" class="text-danger">*</span>
                      </label>

                      <b-form-group
                          id="edit-11"
                          :class="{
                              'is-invalid':
                                $v.create.installmentPaymentType_freq.$error ||
                                errors.installmentPaymentType_freq,
                              'is-valid':
                                !$v.create.installmentPaymentType_freq.$invalid &&
                                !errors.installmentPaymentType_freq,
                            }"
                      >
                          <b-form-radio
                              class="d-inline-block"
                              v-model="$v.create.installmentPaymentType_freq.$model"
                              name="installmentPaymentType_freq"
                              value="1"
                          >
                              {{ $t("general.Yes") }}
                          </b-form-radio>
                          <b-form-radio
                              class="d-inline-block m-1"
                              v-model="$v.create.installmentPaymentType_freq.$model"
                              name="installmentPaymentType_freq"
                              value="0"
                          >
                              {{ $t("general.No") }}
                          </b-form-radio>
                      </b-form-group>
                      <template v-if="errors.installmentPaymentType_freq">
                          <ErrorMessage
                              v-for="(
                                errorMessage, index
                              ) in errors.installmentPaymentType_freq"
                              :key="index"
                          >{{ errorMessage }}
                          </ErrorMessage>
                      </template>
                  </div>
              </div>
              <template v-if="create.installmentPaymentType_freq == 1">
                  <div class="col-md-3" v-if="isVisible('step')">
                      <div class="form-group position-relative">
                          <label class="control-label">
                              {{ getCompanyKey("installmentPaymentTyp_step") }}
                              <span v-if="isRequired('step')" class="text-danger">*</span>
                          </label>
                          <select class="custom-select" v-model="create.step">
                              <option value="D">{{ $t('general.daily') }}</option>
                              <option value="M">{{ $t('general.Monthly') }}</option>
                              <option value="Y">{{ $t('general.yearly') }}</option>
                          </select>

                          <template v-if="errors.step">
                              <ErrorMessage v-for="(errorMessage, index) in errors.step" :key="index">
                                  {{ errorMessage }}
                              </ErrorMessage>
                          </template>
                      </div>
                  </div>
                <div class="col-md-3" v-if="isVisible('freq_period')">
                  <div class="form-group">
                    <label class="control-label">
                      {{ getCompanyKey("freq_period") }}
                      <span v-if="isRequired('freq_period')" class="text-danger">*</span>
                    </label>
                    <input
                      type="number"
                      class="form-control"
                      data-create="2"
                      v-model="$v.create.Freq_period.$model"
                      :class="{
                        'is-invalid':
                          $v.create.Freq_period.$error || errors.Freq_period,
                        'is-valid':
                          !$v.create.Freq_period.$invalid && !errors.Freq_period,
                      }"
                    />
                    <template v-if="errors.Freq_period">
                      <ErrorMessage
                        v-for="(errorMessage, index) in errors.Freq_period"
                        :key="index"
                        >{{ errorMessage }}
                      </ErrorMessage>
                    </template>
                  </div>
                </div>
              </template>
              <template v-else>
                <div class="col-md-6"></div>
              </template>
              <div class="col-md-6" v-if="isVisible('is_partially')">
                <div class="form-group">
                  <label class="mr-2">
                    {{ getCompanyKey("installment_payment_is_partially") }}
                  </label>
                  <b-form-group
                    id="edit-11"
                    :class="{
                      'is-invalid':
                        $v.create.is_partially.$error || errors.is_partially,
                      'is-valid':
                        !$v.create.is_partially.$invalid && !errors.is_partially,
                    }"
                  >
                    <b-form-radio
                      class="d-inline-block"
                      v-model="$v.create.is_partially.$model"
                      name="some-radios-is_partially"
                      value="1"
                    >
                      {{ $t("general.Yes") }}
                    </b-form-radio>
                    <b-form-radio
                      class="d-inline-block m-1"
                      v-model="$v.create.is_partially.$model"
                      name="some-radios-is_partially"
                      value="0"
                    >
                      {{ $t("general.No") }}
                    </b-form-radio>
                  </b-form-group>
                  <template v-if="errors.is_partially">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.is_partially"
                      :key="index"
                      >{{ errorMessage }}
                    </ErrorMessage>
                  </template>
                </div>
              </div>
              <div class="col-md-6" v-if="isVisible('is_passed')">
                <div class="form-group">
                  <label class="mr-2">
                    {{ getCompanyKey("is_passed") }}
                  </label>
                  <b-form-group
                    :class="{
                      'is-invalid': $v.create.is_passed.$error || errors.is_passed,
                      'is-valid': !$v.create.is_passed.$invalid && !errors.is_passed,
                    }"
                  >
                    <b-form-radio
                      class="d-inline-block"
                      v-model="$v.create.is_passed.$model"
                      name="some-radioscreate-is_passed"
                      value="1"
                    >
                      {{ $t("general.Yes") }}
                    </b-form-radio>
                    <b-form-radio
                      class="d-inline-block m-1"
                      v-model="$v.create.is_passed.$model"
                      name="some-radioscreate-is_passed"
                      value="0"
                    >
                      {{ $t("general.No") }}
                    </b-form-radio>
                  </b-form-group>
                  <template v-if="errors.is_passed">
                    <ErrorMessage
                      v-for="(errorMessage, index) in errors.is_passed"
                      :key="index"
                      >{{ errorMessage }}
                    </ErrorMessage>
                  </template>
                </div>
              </div>
              <!-- <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="mr-2">
                                            {{ getCompanyKey("is_passed_all") }}
                                        </label>
                                        <b-form-group id="create-11" :class="{
                                            'is-invalid':
                                            $v.create.is_passed_all.$error || errors.is_passed_all,
                                            'is-valid':
                                            !$v.create.is_passed_all.$invalid && !errors.is_passed_all,
                                        }">
                                            <b-form-radio class="d-inline-block"
                                                          v-model="$v.create.is_passed_all.$model"
                                                          name="some-radios-create-is_passed_all" value="1">
                                                {{ $t("general.Yes") }}
                                            </b-form-radio>
                                            <b-form-radio class="d-inline-block m-1"
                                                          v-model="$v.create.is_passed_all.$model"
                                                          name="some-radios-create-is_passed_all" value="0">
                                                {{ $t("general.No") }}
                                            </b-form-radio>
                                        </b-form-group>
                                        <template v-if="errors.is_passed_all">
                                            <ErrorMessage v-for="(errorMessage, index) in errors.is_passed_all"
                                                          :key="index">{{
                                                    errorMessage
                                                }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div> -->
              <!-- <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="mr-2" >
                                            {{ getCompanyKey("is_passed_contract_plan") }}
                                        </label>
                                        <b-form-group id="create-11" :class="{
                                                'is-invalid':
                                                $v.create.is_passed_contract_plan.$error || errors.is_passed_contract_plan,
                                                'is-valid':
                                                !$v.create.is_passed_contract_plan.$invalid &&
                                                !errors.is_passed_contract_plan,
                                            }">
                                            <b-form-radio class="d-inline-block"
                                                          v-model="$v.create.is_passed_contract_plan.$model"
                                                          name="some-radios-create-is_passed_contract_plan"
                                                          value="1">{{
                                                    $t("general.Yes")
                                                }}
                                            </b-form-radio>
                                            <b-form-radio class="d-inline-block m-1"
                                                          v-model="$v.create.is_passed_contract_plan.$model"
                                                          name="some-radios-create-is_passed_contract_plan"
                                                          value="0">{{
                                                    $t("general.No")
                                                }}
                                            </b-form-radio>
                                        </b-form-group>
                                        <template v-if="errors.is_passed_contract_plan">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.is_passed_contract_plan"
                                                :key="index">{{
                                                    errorMessage
                                                }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div> -->
              <!-- <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="mr-2">
                                            {{ getCompanyKey("installment_payment_auto_freq") }}
                                        </label>
                                        <b-form-group id="edit-11" :class="{
                                            'is-invalid': $v.create.auto_freq.$error || errors.auto_freq,
                                            'is-valid': !$v.create.auto_freq.$invalid && !errors.auto_freq,
                                        }">
                                            <b-form-radio class="d-inline-block"
                                                          v-model="$v.create.auto_freq.$model"
                                                          name="some-radioscreate-auto_freq" value="1">{{
                                                    $t("general.Yes")
                                                }}
                                            </b-form-radio>
                                            <b-form-radio class="d-inline-block m-1"
                                                          v-model="$v.create.auto_freq.$model"
                                                          name="some-radioscreate-auto_freq" value="0">{{
                                                    $t("general.No")
                                                }}
                                            </b-form-radio>
                                        </b-form-group>
                                        <template v-if="errors.auto_freq">
                                            <ErrorMessage v-for="(errorMessage, index) in errors.auto_freq"
                                                          :key="index">{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div> -->
          </div>
        </form>
      </b-modal>
    </div>
<!--  /create   -->
</template>

<style>
form {
    position: relative;
}
</style>
