<template>
  <!--  create   -->
  <b-modal
    :id="id"
    :title="type != 'edit'?getCompanyKey('salesmanplan_create_form'):getCompanyKey('salesmanplan_edit_form')"
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
        <div class="col-md-12" v-if="isVisible('name')">
          <div class="form-group">
            <label class="control-label">
              {{ getCompanyKey("salesmanplan_name_ar") }}
              <span v-if="isRequired('name')" class="text-danger">*</span>
            </label>
            <div dir="rtl">
              <input
                type="text"
                class="form-control arabicInput"
                data-create="1"
                v-model="$v.create.name.$model"
                :class="{
                  'is-invalid': $v.create.name.$error || errors.name,
                  'is-valid': !$v.create.name.$invalid && !errors.name,
                }"
                @keyup="arabicValue(create.name)"
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
                >{{ errorMessage }}
              </ErrorMessage>
            </template>
          </div>
        </div>
        <div class="col-md-12" v-if="isVisible('name_e')">
          <div class="form-group">
            <label class="control-label">
              {{ getCompanyKey("salesmanplan_name_en") }}
              <span v-if="isRequired('name_e')" class="text-danger">*</span>
            </label>
            <div dir="ltr">
              <input
                type="text"
                class="form-control englishInput"
                data-create="2"
                v-model="$v.create.name_e.$model"
                :class="{
                  'is-invalid': $v.create.name_e.$error || errors.name_e,
                  'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                }"
                @keyup="englishValue(create.name_e)"
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
        <div class="col-md-12" v-if="isVisible('salesmen_plans_source_id')">
          <div class="form-group position-relative">
            <label class="control-label">
              {{ getCompanyKey("source") }}
              <span
                v-if="isRequired('salesmen_plans_source_id')"
                class="text-danger"
                >*</span
              >
            </label>
            <multiselect
              v-model="create.salesmen_plans_source_id"
              :options="sources.map((type) => type.id)"
              :custom-label="(opt) => sources.find((x) => x.id == opt)?
                 $i18n.locale == 'ar'? sources.find((x) => x.id == opt).name:sources.find((x) => x.id == opt).name_e
                :null
              "
            >
            </multiselect>
            <div
              v-if="
                $v.create.salesmen_plans_source_id.$error ||
                errors.salesmen_plans_source_id
              "
              class="text-danger"
            >
              {{ $t("general.fieldIsRequired") }}
            </div>
            <template v-if="errors.salesmen_plans_source_id">
              <ErrorMessage
                v-for="(errorMessage, index) in errors.branch_id"
                :key="index"
                >{{ errorMessage }}
              </ErrorMessage>
            </template>
          </div>
        </div>
        <div class="col-md-12" v-if="isVisible('restart_period_id')">
          <div class="form-group position-relative">
            <label class="control-label">
              {{ getCompanyKey("period") }}
              <span v-if="isRequired('restart_period_id')" class="text-danger"
                >*</span
              >
            </label>
            <multiselect
              v-model="create.restart_period_id"
              :options="periods.map((type) => type.id)"
              :custom-label="(opt) => periods.find((x) => x.id == opt)?
                 $i18n.locale == 'ar'? periods.find((x) => x.id == opt).name:periods.find((x) => x.id == opt).name_e
                :null
              "
            >
            </multiselect>
            <div
              v-if="
                $v.create.restart_period_id.$error || errors.restart_period_id
              "
              class="text-danger"
            >
              {{ $t("general.fieldIsRequired") }}
            </div>
            <template v-if="errors.restart_period_id">
              <ErrorMessage
                v-for="(errorMessage, index) in errors.restart_period_id"
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
</template>

<script>
import adminApi from "../../../api/adminAxios";
import {
  required,
  minLength,
  maxLength,
  integer,
  requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";
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
  },
  props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/salesmen-plans'}
    },
  data() {
    return {
      fields: [],
      isLoader: false,
      sources: [],
      periods: [],
      isCustom: false,
      create: {
        name: "",
        name_e: "",
        salesmen_plans_source_id: null,
        restart_period_id: null,
      },
      company_id: null,
      errors: {},
      branches: [],
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
      salesmen_plans_source_id: {
        required: requiredIf(function (model) {
          return this.isRequired("salesmen_plans_source_id");
        }),
      },
      restart_period_id: {
        required: requiredIf(function (model) {
          return this.isRequired("restart_period_id");
        }),
      },
    },
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
  },
  methods: {
      async getCustomTableFields() {
        this.isCustom = true;
       await adminApi
        .get(`/customTable/table-columns/general_salesmen_plans`)
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
            salesmen_plans_source_id: null,
            restart_period_id: null,
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
                 if(!this.isPage)  await this.getCustomTableFields();
                 if (this.isVisible("salesmen_plans_source_id"))  this.getSources();
                 if (this.isVisible("restart_period_id"))  this.getPeriods();
             }else {
                 if(this.idObjEdit.dataObj){
                     let branch = this.idObjEdit.dataObj;
                     this.errors = {};
                     this.create.name = branch.name;
                     this.create.name_e = branch.name_e;
                     this.create.salesmen_plans_source_id = branch.salesmen_plans_source.id;
                     this.create.restart_period_id = branch.restart_period.id;
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
          }
      },
    getSources() {
      this.isLoader = true;

       adminApi
        .get(`/salesmen-plans-source/get-drop-down`)
        .then((res) => {
          let l = res.data.data;
          this.sources = l;
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
    getPeriods() {
      this.isLoader = true;

       adminApi
        .get(`/restart-period/get-drop-down`)
        .then((res) => {
          let l = res.data.message;
          this.periods = l;
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
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
    },
  },
};
</script>

<style scoped>
form {
    position: relative;
}
</style>
