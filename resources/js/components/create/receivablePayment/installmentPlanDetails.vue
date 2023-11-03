<script>
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import { required, integer, numeric } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../../helper/startDate";
import InstallmentPaymentType from "./installmentPaymentType.vue";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";

/**
 * Advanced Table component
 */
export default {
  mixins: [transMixinComp],
  components: {
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
    InstallmentPaymentType,
  },
  data() {
    return {
      parents: [],
      enabled3: false,
      isLoader: false,
      create: {
        is_fixed: 0,
        ln_no: 0,
        installment_payment_type_per: 0,
        installment_payment_type_amount: 0,
        installment_payment_type_freq: 0,
        interest_per: 0,
        interest_value: 0,
        installment_payment_type_id: null,
      },
      errors: {},
      payment_types: [],
      isCheckAll: false,
      checkAll: [],
      is_disabled: false,
    };
  },
  validations: {
    create: {
      is_fixed: { required, numeric },
      ln_no: { required, integer },
      installment_payment_type_per: { required, numeric },
      installment_payment_type_amount: { required, numeric },
      installment_payment_type_freq: { required, numeric },
      interest_per: { required, numeric },
      interest_value: { required, numeric },
      installment_payment_type_id: { required },
    },
  },

  props: ["companyKeys", "defaultsKeys"],

  updated() {
    $(function () {
      $(".englishInput").keypress(function (event) {
        var ew = event.which;
        if (ew == 32) return true;
        if (48 <= ew && ew <= 57) return true;
        if (65 <= ew && ew <= 90) return true;
        if (97 <= ew && ew <= 122) return true;
        return false;
      });
      $(".arabicInput").keypress(function (event) {
        var ew = event.which;
        if (ew == 32) return true;
        if (48 <= ew && ew <= 57) return true;
        if (65 <= ew && ew <= 90) return false;
        if (97 <= ew && ew <= 122) return false;
        return true;
      });
    });
  },
  methods: {
    resetModalHidden() {
      this.create = {
        is_fixed: 0,
        ln_no: 0,
        installment_payment_type_per: 0,
        installment_payment_type_amount: 0,
        installment_payment_type_freq: 0,
        interest_per: 0,
        interest_value: 0,
        installment_payment_type_id: null,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
        this.is_disabled = false;
      this.errors = {};
      this.$bvModal.hide(`installment-payment-details-create`);
    },
    /**
     *  hidden Modal (create)
     */

    async resetModal() {
      await this.getInstallPaymentTypes();
      this.create = {
        is_fixed: 0,
        ln_no: 0,
        installment_payment_type_per: 0,
        installment_payment_type_amount: 0,
        installment_payment_type_freq: 0,
        interest_per: 0,
        interest_value: 0,
        installment_payment_type_id: null,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create module
     */
    async resetForm() {
      await this.getInstallPaymentTypes();
      this.create = {
        is_fixed: 0,
        ln_no: 0,
        installment_payment_type_per: 0,
        installment_payment_type_amount: 0,
        installment_payment_type_freq: 0,
        interest_per: 0,
        interest_value: 0,
        installment_payment_type_id: null,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;
      this.errors = {};
    },

    AddSubmit() {
      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .post(`/recievable-payable/rp_installment_p_plan_details`, this.create)
          .then((res) => {
            this.is_disabled = true;
            this.$emit("created");
            setTimeout(() => {
              Swal.fire({
                icon: "success",
                text: `${this.$t("general.Addedsuccessfully")}`,
                showConfirmButton: false,
                timer: 1500,
              });
            }, 500);
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
    },

    moveInput(tag, c, index) {
      document.querySelector(`${tag}[data-${c}='${index}']`).focus();
    },
    async getInstallPaymentTypes() {
      this.isLoader = true;

      await adminApi
        .get(`/recievable-payable/rp_installment_payment_types`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({
            id: 0,
            name: "اضف نوع دفع",
            name_e: "Add installment payment type",
          });
          this.payment_types = l;
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
    showInstallmentPaymentTypeModal() {
      if (this.create.installment_payment_type_id == 0) {
        this.$bvModal.show("installment_payment_type_create");
        this.create.installment_payment_type_id = null;
      }
    },
    formatDate(value) {
      return formatDateOnly(value);
    },
  },
};
</script>

<template>
  <div>
    <InstallmentPaymentType
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getInstallPaymentTypes"
    />
    <!--  create   -->
    <b-modal
      id="installment-payment-details-create"
      :title="getCompanyKey('InstallmentPaymentPlanDetailCreate')"
      title-class="font-18"
      body-class="p-4 "
      :hide-footer="true"
      size="lg"
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
              type="button"
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
          <!-- Emulate built in modal footer ok and cancel button actions -->

          <b-button variant="danger" type="button" @click.prevent="resetModalHidden">
            {{ $t("general.Cancel") }}
          </b-button>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="my-1 mr-2">{{
                getCompanyKey("installment_payment_type_id")
              }}</label>
              <multiselect
                @input="showInstallmentPaymentTypeModal"
                v-model="create.installment_payment_type_id"
                :options="payment_types.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    $i18n.locale == 'ar'
                      ? payment_types.find((x) => x.id == opt).name
                      : payment_types.find((x) => x.id == opt).name_e
                "
              >
              </multiselect>
              <template v-if="errors.installment_payment_type_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.installment_payment_type_id"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="field-2" class="control-label">
                {{ getCompanyKey("ln_no") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="number"
                class="form-control englishInput"
                data-create="2"
                @keypress.enter="moveInput('select', 'create', 3)"
                v-model="$v.create.ln_no.$model"
                :class="{
                  'is-invalid': $v.create.ln_no.$error || errors.ln_no,
                  'is-valid': !$v.create.ln_no.$invalid && !errors.ln_no,
                }"
                id="field-2"
              />
              <template v-if="errors.ln_no">
                <ErrorMessage v-for="(errorMessage, index) in errors.ln_no" :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="field-2" class="control-label">
                {{ getCompanyKey("installment_payment_type_per") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="number"
                class="form-control englishInput"
                step="0.01"
                data-create="2"
                @keypress.enter="moveInput('select', 'create', 3)"
                v-model="$v.create.installment_payment_type_per.$model"
                :class="{
                  'is-invalid':
                    $v.create.installment_payment_type_per.$error ||
                    errors.installment_payment_type_per,
                  'is-valid':
                    !$v.create.installment_payment_type_per.$invalid &&
                    !errors.installment_payment_type_per,
                }"
              />
              <template v-if="errors.installment_payment_type_per">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.installment_payment_type_per"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="field-2" class="control-label">
                {{ getCompanyKey("installment_payment_type_amount") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="number"
                class="form-control englishInput"
                step="0.01"
                data-create="2"
                @keypress.enter="moveInput('select', 'create', 3)"
                v-model="$v.create.installment_payment_type_amount.$model"
                :class="{
                  'is-invalid':
                    $v.create.installment_payment_type_amount.$error ||
                    errors.installment_payment_type_amount,
                  'is-valid':
                    !$v.create.installment_payment_type_amount.$invalid &&
                    !errors.installment_payment_type_amount,
                }"
              />
              <template v-if="errors.installment_payment_type_amount">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.installment_payment_type_amount"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="field-2" class="control-label">
                {{ getCompanyKey("installment_payment_type_freq") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="number"
                class="form-control englishInput"
                data-create="2"
                @keypress.enter="moveInput('select', 'create', 3)"
                v-model="$v.create.installment_payment_type_freq.$model"
                :class="{
                  'is-invalid':
                    $v.create.installment_payment_type_freq.$error ||
                    errors.installment_payment_type_freq,
                  'is-valid':
                    !$v.create.installment_payment_type_freq.$invalid &&
                    !errors.installment_payment_type_freq,
                }"
              />
              <template v-if="errors.installment_payment_type_freq">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.installment_payment_type_freq"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="field-2" class="control-label">
                {{ getCompanyKey("interest_per") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="number"
                step="0.01"
                class="form-control englishInput"
                data-create="2"
                @keypress.enter="moveInput('select', 'create', 3)"
                v-model="$v.create.interest_per.$model"
                :class="{
                  'is-invalid': $v.create.interest_per.$error || errors.interest_per,
                  'is-valid': !$v.create.interest_per.$invalid && !errors.interest_per,
                }"
              />
              <template v-if="errors.interest_per">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.interest_per"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="field-2" class="control-label">
                {{ getCompanyKey("interest_value") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="number"
                step="0.01"
                class="form-control englishInput"
                data-create="2"
                @keypress.enter="moveInput('select', 'create', 3)"
                v-model="$v.create.interest_value.$model"
                :class="{
                  'is-invalid': $v.create.interest_value.$error || errors.interest_value,
                  'is-valid':
                    !$v.create.interest_value.$invalid && !errors.interest_value,
                }"
              />
              <template v-if="errors.interest_value">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.interest_value"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label class="mr-2">
                {{ getCompanyKey("is_fixed") }}
                <span class="text-danger">*</span>
              </label>
              <b-form-group
                :class="{
                  'is-invalid': $v.create.is_fixed.$error || errors.is_fixed,
                  'is-valid': !$v.create.is_fixed.$invalid && !errors.is_fixed,
                }"
              >
                <b-form-radio
                  class="d-inline-block"
                  v-model="$v.create.is_fixed.$model"
                  name="some-radios"
                  value="1"
                  >{{ $t("general.predefinedDate") }}</b-form-radio
                >
                <b-form-radio
                  class="d-inline-block m-1"
                  v-model="$v.create.is_fixed.$model"
                  name="some-radios"
                  value="0"
                  >{{ $t("general.UndefinedDate") }}</b-form-radio
                >
              </b-form-group>
              <template v-if="errors.is_fixed">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.is_fixed"
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
