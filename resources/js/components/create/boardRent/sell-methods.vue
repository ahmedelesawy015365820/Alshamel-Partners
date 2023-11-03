<template>
  <!--  create   -->
  <b-modal
    :id="id"
    :title="
      type != 'edit'
        ? getCompanyKey('boardRent_sellMethod_create_form')
        : getCompanyKey('boardRent_sellMethod_edit_form')
    "
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
            {{ type != "edit" ? $t("general.Add") : $t("general.edit") }}
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
        <div class="col-md-12">
          <div class="form-group">
            <label for="field-1" class="control-label">
              {{ getCompanyKey("boardRent_sellMethod_name_ar") }}
              <span class="text-danger">*</span>
            </label>
            <div dir="rtl">
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
                >{{ errorMessage }}</ErrorMessage
              >
            </template>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label for="field-2" class="control-label">
              {{ getCompanyKey("boardRent_sellMethod_name_en") }}
              <span class="text-danger">*</span>
            </label>
            <div dir="ltr">
              <input
                @keyup="englishValue(create.name_e)"
                type="text"
                class="form-control englishInput"
                data-create="2"
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
                >{{ errorMessage }}</ErrorMessage
              >
            </template>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label for="field-2" class="control-label">
              {{ getCompanyKey("boardRent_sellMethod_calculated_percentage") }}
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              class="form-control"
              data-create="2"
              v-model="$v.create.calculated_percentage.$model"
              :class="{
                'is-invalid':
                  $v.create.calculated_percentage.$error ||
                  errors.calculated_percentage,
                'is-valid':
                  !$v.create.calculated_percentage.$invalid &&
                  !errors.calculated_percentage,
              }"
              id="field-2"
            />
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label for="field-2" class="control-label">
              {{ getCompanyKey("boardRent_sellMethod_commission_ratio") }}
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              class="form-control"
              data-create="2"
              v-model="$v.create.commission_ratio.$model"
              :class="{
                'is-invalid':
                  $v.create.commission_ratio.$error || errors.commission_ratio,
                'is-valid':
                  !$v.create.commission_ratio.$invalid &&
                  !errors.commission_ratio,
              }"
              id="field-2"
            />
          </div>
        </div>
      </div>
    </form>
  </b-modal>
  <!--  /create   -->
</template>

<script>
import Switches from "vue-switches";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import { maxLength, minLength, required } from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";

export default {
  name: "sell-methods",
  components: {
    Switches,
    ErrorMessage,
    loader,
  },
  data() {
    return {
      isLoader: false,
      create: {
        name: "",
        name_e: "",
        calculated_percentage: 0,
        commission_ratio: 0,
      },
      company_id: null,
      errors: {},
      is_disabled: false,
      isCustom: false,
    };
  },
  validations: {
    create: {
      name: { required, minLength: minLength(2), maxLength: maxLength(255) },
      name_e: { required, minLength: minLength(2), maxLength: maxLength(255) },
      calculated_percentage: { required },
      commission_ratio: { required },
    },
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
  },
  mixins: [transMixinComp, successError],
  props: {
    id: { default: "create" },
    companyKeys: { default: [] },
    defaultsKeys: { default: [] },
    isPage: { default: true },
    isVisiblePage: { default: null },
    isRequiredPage: { default: null },
    type: { default: "create" },
    idObjEdit: { default: null },
    isPermission: {},
    url: { default: "/boards-rent/sell-methods" },
  },
  methods: {
    getCustomTableFields() {
      this.isCustom = true;
      adminApi
        .get(`/customTable/table-columns/boards_rent_sell_methods`)
        .then((res) => {
          this.fields = res.data;
        })
        .catch((err) => {
          this.errorFun("Error", "Thereisanerrorinthesystem");
        })
        .finally(() => {
          this.isCustom = false;
        });
    },
    isVisible(fieldName) {
      if (!this.isPage) {
        let res = this.fields.filter((field) => {
          return field.column_name == fieldName;
        });
        return res.length > 0 && res[0].is_visible == 1 ? true : false;
      } else {
        return this.isVisiblePage(fieldName);
      }
    },
    isRequired(fieldName) {
      if (!this.isPage) {
        let res = this.fields.filter((field) => {
          return field.column_name == fieldName;
        });
        return res.length > 0 && res[0].is_required == 1 ? true : false;
      } else {
        return this.isRequiredPage(fieldName);
      }
    },

    arabicValue(txt) {
      this.create.name = arabicValue(txt);
      this.edit.name = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
      this.edit.name_e = englishValue(txt);
    },
    defaultData(edit = null) {
      this.create = {
        name: "",
        name_e: "",
        calculated_percentage: 0,
        commission_ratio: 0,
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
    /**
     *  hidden Modal (create)
     */
    resetModal() {
      this.defaultData();
      setTimeout(() => {
        if (this.type != "edit") {
          if (!this.isPage) this.getCustomTableFields();
          this.$nextTick(() => {
            this.$v.$reset();
          });
        } else {
          if (this.idObjEdit.dataObj) {
            let sellMethods = this.idObjEdit.dataObj;
            this.create.name = sellMethods.name;
            this.create.name_e = sellMethods.name_e;
            this.create.calculated_percentage =
              sellMethods.target_calculation_ratio;
            this.create.commission_ratio = sellMethods.commission_ratio;
            this.create.is_all_value = sellMethods.is_all_value;
            this.create.is_default = sellMethods.is_default;
            this.errors = {};
          }
        }
      }, 50);
    },
    /**
     *  create countrie
     */
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
        this.is_disabled = false;
        if (this.type != "edit") {
          adminApi
            .post(this.url, {
              ...this.create,
              target_calculation_ratio: this.create.calculated_percentage,
              commission_ratio: this.create.commission_ratio,
              company_id: this.$store.getters["auth/company_id"],
            })
            .then((res) => {
              this.is_disabled = true;
              if (!this.isPage) this.$emit("created");
              else this.$emit("getDataTable");

              setTimeout(() => {
                this.successFun("Addedsuccessfully");
              }, 500);
            })
            .catch((err) => {
              if (err.response.data) {
                this.errors = err.response.data.errors;
              } else {
                this.errorFun("Error", "Thereisanerrorinthesystem");
              }
            })
            .finally(() => {
              this.isLoader = false;
            });
        } else {
          adminApi
            .put(`${this.url}/${this.idObjEdit.idEdit}`, {
              ...this.create,
              target_calculation_ratio: this.create.calculated_percentage,
              commission_ratio: this.create.commission_ratio,
              company_id: this.$store.getters["auth/company_id"],
            })
            .then((res) => {
              this.$bvModal.hide(this.id);
              this.$emit("getDataTable");
              setTimeout(() => {
                this.successFun("Editsuccessfully");
              }, 500);
            })
            .catch((err) => {
              if (err.response.data) {
                this.errors = err.response.data.errors;
              } else {
                this.errorFun("Error", "Thereisanerrorinthesystem");
              }
            })
            .finally(() => {
              this.isLoader = false;
            });
        }
      }
    },
  },
};
</script>

<style scoped>
</style>
