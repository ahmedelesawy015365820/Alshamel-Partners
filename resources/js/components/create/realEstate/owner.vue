<template>
  <div>
    <bankAccount
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getBankAcount"
      :id="'bank-account-create'"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
    />
    <Country
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getCategory"
      :id="'country-create'"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
    />
    <City
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getCity(create.country_id)"
      :id="'city-create'"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
    />
    <b-modal
      :id="id"
      :title="
        type != 'edit'
          ? getCompanyKey('owner_create_form')
          : getCompanyKey('owner_edit_form')
      "
      title-class="font-18"
      dialog-class="modal-full-width"
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
          <div class="col-md-4" v-if="isVisible('name')">
            <div class="form-group">
              <label for="field-1" class="control-label">
                {{ getCompanyKey("owner_name_ar") }}
                <span v-if="isRequired('name')" class="text-danger">*</span>
              </label>
              <div dir="rtl">
                <input
                  @keyup="arabicValue(create.name)"
                  type="text"
                  class="form-control"
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
          <div class="col-md-4" v-if="isVisible('name_e')">
            <div class="form-group">
              <label for="field-2" class="control-label">
                {{ getCompanyKey("owner_name_en") }}
                <span v-if="isRequired('name_e')" class="text-danger">*</span>
              </label>
              <div dir="ltr">
                <input
                  @keyup="englishValue(create.name_e)"
                  type="text"
                  class="form-control"
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
        </div>
        <hr
          style="
            margin: 10px 0 !important;
            border-top: 1px solid rgb(141 163 159 / 42%);
          "
        />
        <div class="row">
          <div class="col-md-3" v-if="isVisible('nationality_id')">
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("owner_nationality") }}
                <span v-if="isRequired('nationality_id')" class="text-danger">*</span>
              </label>
              <multiselect
                v-model="$v.create.nationality_id.$model"
                :options="nationalities.map((type) => type.id)"
                :custom-label="
                  (opt) => nationalities.find((x) => x.id == opt)?nationalities.find((x) => x.id == opt).name:''
                "
              >
              </multiselect>
              <div
                v-if="$v.create.nationality_id.$error || errors.nationality_id"
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.nationality_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.nationality_id"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-3" v-if="isVisible('country_id')">
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("country") }}
                <span class="text-danger">*</span>
              </label>
              <multiselect
                @input="showCountryModal"
                v-model="$v.create.country_id.$model"
                :options="countries.map((type) => type.id)"
                :custom-label="(opt) => countries.find((x) => x.id == opt)?countries.find((x) => x.id == opt).name:''"
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
          <div class="col-md-3">
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("city") }}
                <span class="text-danger">*</span>
              </label>
              <multiselect
                @input="getCity()"
                v-model="$v.create.city_id.$model"
                :options="cities.map((type) => type.id)"
                :custom-label="(opt) => cities.find((x) => x.id == opt)?cities.find((x) => x.id == opt).name:''"
              >
              </multiselect>
              <div
                v-if="$v.create.city_id.$error || errors.city_id"
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.city_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.city_id"
                  :key="index"
                >
                  {{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("owner_national_id") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="number"
                class="form-control"
                step="0.1"
                v-model="$v.create.national_id.$model"
                :class="{
                  'is-invalid':
                    $v.create.national_id.$error || errors.national_id,
                  'is-valid':
                    !$v.create.national_id.$invalid && !errors.national_id,
                }"
              />
              <template v-if="errors.national_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.national_id"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
        </div>
        <hr
          style="
            margin: 10px 0 !important;
            border-top: 1px solid rgb(141 163 159 / 42%);
          "
        />
        <div class="row">
          <div class="col-md-3">
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("bank_account") }}
                <span class="text-danger">*</span>
              </label>
              <multiselect
                @input="showBankAccountModal"
                v-model="$v.create.bank_account_id.$model"
                :options="bank_accounts.map((type) => type.id)"
                :custom-label="
                  (opt) => bank_accounts.find((x) => x.id == opt)?bank_accounts.find((x) => x.id == opt).account_number:''
                "
              >
              </multiselect>
              <div
                v-if="
                  $v.create.bank_account_id.$error || errors.bank_account_id
                "
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.bank_account_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.bank_account_id"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("owner_code") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="number"
                class="form-control"
                v-model="$v.create.rb_code.$model"
                :class="{
                  'is-invalid': $v.create.rb_code.$error || errors.rb_code,
                  'is-valid': !$v.create.rb_code.$invalid && !errors.rb_code,
                }"
              />
              <template v-if="errors.rb_code">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.rb_code"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("owner_phone") }}
                <span class="text-danger">*</span>
              </label>
              <VuePhoneNumberInput
                v-model="$v.create.phone.$model"
                :default-country-code="codeCountry"
                valid-color="#28a745"
                error-color="#dc3545"
                :preferred-countries="['FR', 'EG', 'DE']"
                @update="updatePhone"
              />
              <template v-if="errors.phone">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.phone"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("owner_whatsapp") }}
                <span class="text-danger">*</span>
              </label>
              <VuePhoneNumberInput
                v-model="$v.create.whatsapp.$model"
                :default-country-code="codeCountry"
                valid-color="#28a745"
                error-color="#dc3545"
                :preferred-countries="['FR', 'EG', 'DE']"
                @update="updateWhatsapp"
              />
              <template v-if="errors.whatsapp">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.whatsapp"
                  :key="index"
                >
                  {{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("owner_email") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                v-model="$v.create.email.$model"
                :class="{
                  'is-invalid': $v.create.email.$error || errors.email,
                  'is-valid': !$v.create.email.$invalid && !errors.email,
                }"
              />
              <template v-if="errors.email">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.email"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
        </div>
        <hr
          style="
            margin: 10px 0 !important;
            border-top: 1px solid rgb(141 163 159 / 42%);
          "
        />
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("owner_contact_person") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                v-model="$v.create.contact_person.$model"
                :class="{
                  'is-invalid':
                    $v.create.contact_person.$error || errors.contact_person,
                  'is-valid':
                    !$v.create.contact_person.$invalid &&
                    !errors.contact_person,
                }"
              />
              <template v-if="errors.contact_person">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.contact_person"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("owner_contact_phones") }}
                <span class="text-danger">*</span>
              </label>
              <VuePhoneNumberInput
                v-model="$v.create.contact_phones.$model"
                :default-country-code="codeCountry"
                valid-color="#28a745"
                error-color="#dc3545"
                :preferred-countries="['FR', 'EG', 'DE']"
                @update="updateContract"
              />
              <template v-if="errors.contact_phones">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.contact_phones"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
        </div>
      </form>
    </b-modal>
  </div>
</template>

<script>
import adminApi from "../../../api/adminAxios";
import {
  required,
  minLength,
  maxLength,
  integer,
  numeric,
  email,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../general/loader";
import Country from "../general/country";
import City from "../general/city";
import bankAccount from "../general/bankAccount";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";

// require styles
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";
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
    url: { default: "/real-estate/owners" },
  },
  components: {
    ErrorMessage,
    loader,
    Country,
    City,
    Multiselect,
    bankAccount,
  },
  data() {
    return {
      isVaildPhone: false,
      owners: [],
      cities: [],
      countries: [],
      bank_accounts: [],
      nationalities: [],
      isLoader: false,
      isCustom: false,
      codeCountry: "",
      create: {
        name: "",
        name_e: "",
        phone_code: "",
        phone: "",
        email: "",
        rb_code: null,
        nationality_id: null,
        contact_person: "",
        contact_phones: "",
        national_id: null,
        bank_account_id: null,
        country_id: null,
        city_id: null,
        whatsapp: "",
      },
      errors: {},
      image: "",
      is_disabled: false,
      fields: []
    };
  },
  validations: {
    create: {
      name: { required, minLength: minLength(2), maxLength: maxLength(100) },
      name_e: { required, minLength: minLength(2), maxLength: maxLength(100) },
      phone: { required, maxLength: maxLength(100) },
      email: { maxLength: maxLength(100), email },
      rb_code: { required, integer, maxLength: maxLength(9) },
      nationality_id: { required, integer, maxLength: maxLength(40) },
      contact_person: { maxLength: maxLength(100) },
      contact_phones: { integer, maxLength: maxLength(100) },
      national_id: { required, integer },
      country_id: { required },
      city_id: { required },
      bank_account_id: { required },
      whatsapp: { integer },
    },
  },
  computed: {},
  mounted() {
    this.$store.dispatch("locationIp/getIp");
  },
  methods: {
    async getCustomTableFields() {
      this.isCustom = true;
      await adminApi
        .get(`/customTable/table-columns/rlst_owners`)
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
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
    },
    defaultData(edit = null) {
      this.codeCountry = this.$store.getters["locationIp/countryCode"];
      this.create = {
        name: "",
        name_e: "",
        phone: "",
        email: "",
        phone_code: "",
        rb_code: null,
        nationality_id: null,
        contact_person: "",
        contact_phones: "",
        national_id: null,
        bank_account_id: null,
        country_id: null,
        city_id: null,
        whatsapp: "",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.is_disabled = false;
      this.isVaildPhone = false;
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
          this.getCategory();
          this.getBankAcount();
        } else {
          if (this.idObjEdit.dataObj) {
            let build = this.idObjEdit.dataObj;
            this.errors = {};
            if (this.isVisible("country_id")) this.getCategory();
            if (this.isVisible("city_id")) this.getCity(build.country.id);
            if (this.isVisible("bank_account_id")) this.getBankAcount();
            this.create.name = build.name;
            this.create.name_e = build.name_e;
            this.create.phone_code = build.phone_code;
            this.create.phone = build.phone;
            this.create.city_id = build.city.id;
            this.create.country_id = build.country.id;
            this.create.bank_account_id = build.ban1k_account.id;
            this.create.nationality_id = build.nationality.id;
            this.create.email = build.email;
            this.create.rb_code = build.rb_code;
            this.create.contact_person = build.contact_person;
            this.create.whatsapp = build.whatsapp;
            this.create.contact_phones = build.contact_phones;
            this.create.national_id = build.national_id;
            this.errors = {};
            this.isVaildPhone = false;
          }
        }
      }, 50);
    },
    async resetForm() {
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

      if (this.$v.create.$invalid && !this.isVaildPhone) {
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
    async getCategory() {
      this.create.city_id = null;
      await adminApi
        .get(`/countries?is_active=active`)
        .then((res) => {
          let l = res.data.data;
          this.nationalities = l;
          l.unshift({ id: 0, name: "اضافة دولة", name_e: "Add Country" });
          this.countries = l;
          this.nationalities = l.slice(1);
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    async getBankAcount() {
      await adminApi
        .get(`/bank-accounts`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, account_number: "Add Bank Account" });
          this.bank_accounts = l;
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
    async getCity(id = null) {
      if (this.create.city_id == 0) {
        this.$bvModal.show("city-create");
        this.create.city_id = null;
      }
      if (id) {
        this.create.city_id = null;
        await adminApi
          .get(`/cities?country_id=${id}`)
          .then((res) => {
            let l = res.data.data;
            l.unshift({ id: 0, name: "اضافة مدينة", name_e: "Add City" });
            this.cities = l;
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.Thereisanerrorinthesystem")}`,
            });
          });
      }
    },
    showCountryModal() {
      if (this.create.country_id == 0) {
        this.$bvModal.show("country-create");
        this.create.country_id = null;
      } else {
        this.getCity(this.create.country_id);
      }
    },
    showBankAccountModal() {
      if (this.create.bank_account_id == 0) {
        this.$bvModal.show("bank-account-create");
        this.create.bank_account_id = null;
      }
    },
    updatePhone(e) {
      this.create.phone = e.phoneNumber;
      this.create.phone_code = e.countryCallingCode;
      this.isVaildPhone = e.isValid;
    },
    updateWhatsapp(e) {
      this.create.whatsapp = e.phoneNumber;
    },
    updateContract(e) {
      this.create.contact_phones = e.phoneNumber;
    },
  },
};
</script>


<style scoped>
form {
    position: relative;
}
</style>
