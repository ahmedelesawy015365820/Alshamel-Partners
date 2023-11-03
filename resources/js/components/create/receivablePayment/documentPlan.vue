<script>
import adminApi from "../../../api/adminAxios";
import {
  required,
  minLength,
  maxLength,
  integer,
} from "vuelidate/lib/validators";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import {
  dynamicSortString,
  dynamicSortNumber,
} from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import InstallmentPlan from "../../../components/create/receivablePayment/installmentPlan.vue";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

/**
 * Advanced Table component
 */
export default {
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
    url: { default: "/recievable-payable/rp_document_plan" },
  },
  components: {
    ErrorMessage,
    loader,
    Multiselect,
    InstallmentPlan,
  },
  data() {
    return {
      isLoader: false,
      isCustom: false,
      installment_payment_plans: [],
      create: {
        plan_id: null,
      },
      errors: {},
      is_disabled: false,
    };
  },
  validations: {
    create: {
      plan_id: { required },
    },
  },
  methods: {
    async getCustomTableFields() {
      this.isCustom = true;
      await adminApi
        .get(`/customTable/table-columns/general_avenues`)
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
    showInstallmentPlanModal() {
      if (this.create.plan_id == 0) {
        this.$bvModal.show("installment-payment-plan-create");
        this.create.plan_id = null;
      }
    },

    defaultData(edit = null) {
      this.create = {
        plan_id: "",
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
      setTimeout(async () => {
        if (this.type != "edit") {
          if (!this.isPage) await this.getCustomTableFields();
          this.$nextTick(() => {
            this.$v.$reset();
          });
          this.getInstallmentPaymentPlans();
        } else {
          if (this.idObjEdit.dataObj) {
            let documentPlan = this.idObjEdit.dataObj;
            this.getInstallmentPaymentPlans();
            this.create.plan_id = documentPlan.plan_id;
            this.errors = {};
          }
        }
      }, 50);
    },

    async resetForm() {
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
        if (this.type != "edit") {
          adminApi
            .post(this.url, {
              ...this.create,
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

    moveInput(tag, c, index) {
      document.querySelector(`${tag}[data-${c}='${index}']`).focus();
    },
    async getInstallmentPaymentPlans() {
      this.isLoader = true;
      await adminApi
        .get(`recievable-payable/rp_installment_p_plan`)
        .then((res) => {
          this.isLoader = false;
          let l = res.data.data;
          if (this.isPermission("create paymentPlan RP")) {
            l.unshift({
              id: 0,
              name: "اضف خطة دفع",
              name_e: "Add installment payment plan",
            });
          }
          this.installment_payment_plans = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
  },
};
</script>

<template>
  <div>
    <InstallmentPlan
      :id="'installment-payment-plan-create'"
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getInstallmentPaymentPlans"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
    />
    <!--  create   -->
    <b-modal
    :id="id"
      :title="
        type != 'edit'
          ? getCompanyKey('installment_document_plan_create_form')
          : getCompanyKey('installment_document_plan_edit_form')
      "
      title-class="font-18"
      body-class="p-4 "
      :hide-footer="true"
      @show="resetModal"
      @hidden="resetModalHidden"
    >
      <form>
        <div class="mb-3 d-flex justify-content-end">
          <b-button
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
              {{ $t("general.Add") }}
            </b-button>
            <b-button variant="success" class="mx-1" disabled v-else>
              <b-spinner small></b-spinner>
              <span class="sr-only">{{ $t("login.Loading") }}...</span>
            </b-button>
          </template>
          <b-button
            @click.prevent="$bvModal.hide(`create`)"
            variant="danger"
            type="button"
          >
            {{ $t("general.Cancel") }}
          </b-button>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("installment_plan") }}
                <span class="text-danger">*</span>
              </label>
              <multiselect
                @input="showInstallmentPlanModal"
                v-model="create.plan_id"
                :options="installment_payment_plans.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    $i18n.locale == 'ar'
                      ? installment_payment_plans.find((x) => x.id == opt).name
                      : installment_payment_plans.find((x) => x.id == opt)
                          .name_e
                "
              >
              </multiselect>
              <div
                v-if="$v.create.plan_id.$error || errors.plan_id"
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.plan_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.plan_id"
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
