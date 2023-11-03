<script>
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {
  required,
  minLength,
  maxLength,
  integer,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../general/loader";
import {
  dynamicSortString,
  dynamicSortNumber,
} from "../../../helper/tableSort";
import { formatDateOnly } from "../../../helper/startDate";
import Multiselect from "vue-multiselect";
import MainContactGroup from "../../../components/create/receivablePayment/mainContactGroup";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Subcontact group",
    meta: [{ name: "description", content: "Subcontact group" }],
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
    url: { default: "/recievable-payable/rp_sub_contact_group" },
    governorate_id: { default: null },
    country_id: { default: null },
    city_id: { default: null },
  },
  components: {
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
    MainContactGroup,
  },
  data() {
    return {
      isLoader: false,
      isCustom:false,
      mainContractGroups: [],
      glCoases: [],
      create: {
        name: "",
        name_e: "",
        gl_acc_no: "",
        rp_main_contact_group_id: "",
      },
      company_id: null,
      errors: {},
      is_disabled: false,
    };
  },
  validations: {
    create: {
      name: { required, minLength: minLength(2), maxLength: maxLength(100) },
      name_e: { required, minLength: minLength(2), maxLength: maxLength(100) },
      gl_acc_no: { required },
      rp_main_contact_group_id: { required },
    },
  },
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
        if (48 <= ew && ew <= 57) return false;
        if (65 <= ew && ew <= 90) return false;
        if (97 <= ew && ew <= 122) return false;
        return true;
      });
    });
  },
  methods: {
    showMainContactModal() {
      if (this.create.rp_main_contact_group_id == 0) {
        this.$bvModal.show("main_contact_group_create");
        this.create.rp_main_contact_group_id = null;
      }
    },
    formatDate(value) {
      return formatDateOnly(value);
    },
    defaultData(edit = null) {
      this.create = {
        name: "",
        name_e: "",
        gl_acc_no: "",
        rp_main_contact_group_id: "",
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
          this.getMainContractGroups();
          this.getGlCoas();
        } else {
          if (this.idObjEdit.dataObj) {
            let unitStatus = this.idObjEdit.dataObj;
            this.errors = {};
            this.getMainContractGroups();
            this.getGlCoas();
            this.create.name = unitStatus.name;
            this.create.name_e = unitStatus.name_e;
            this.create.gl_acc_no = unitStatus.gl_acc_no;
            this.create.rp_main_contact_group_id =
              unitStatus.rp_main_contact_group_id;
          }
        }
      }, 50);
    },
    async getCustomTableFields() {
      this.isCustom = true;
      await adminApi
        .get(`/customTable/table-columns/rp_sub_contact_groups`)
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
    async getMainContractGroups() {
      this.isLoader = true;

      await adminApi
        .get(`recievable-payable/rp_main_contact_group`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({
            id: 0,
            name: "اضف جروب العقد الاساسي",
            name_e: "Add main contract category",
          });
          this.mainContractGroups = l;
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
    async getGlCoas() {
      this.isLoader = true;
      await adminApi
        .get(`gl/coa`)
        .then((res) => {
          this.glCoases = res.data.data;
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
    moveInput(tag, c, index) {
      document.querySelector(`${tag}[data-${c}='${index}']`).focus();
    },
  },
};
</script>

<template>
  <div>
    <MainContactGroup
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getMainContractGroups"
      :id="'main_contact_group_create'"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
    />
    <!--  create   -->
    <b-modal
      :id="id"
      :title="
        type != 'edit'
          ? getCompanyKey('subcontact_group_create_form')
          : getCompanyKey('subcontact_group_edit_form')
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
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("main_contact_group") }}
                <span class="text-danger">*</span>
              </label>
              <multiselect
                @input="showMainContactModal"
                v-model="create.rp_main_contact_group_id"
                :options="mainContractGroups.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    $i18n.locale == 'ar'
                      ? mainContractGroups.find((x) => x.id == opt).name
                      : mainContractGroups.find((x) => x.id == opt).name_e
                "
              >
              </multiselect>
              <div
                v-if="
                  $v.create.rp_main_contact_group_id.$error ||
                  errors.rp_main_contact_group_id
                "
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.rp_main_contact_group_id">
                <ErrorMessage
                  v-for="(
                    errorMessage, index
                  ) in errors.rp_main_contact_group_id"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("gl_coa_acc") }}
                <span class="text-danger">*</span>
              </label>
              <multiselect
                v-model="create.gl_acc_no"
                :options="glCoases.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    $i18n.locale == 'ar'
                      ? glCoases.find((x) => x.id == opt).name
                      : glCoases.find((x) => x.id == opt).name_e
                "
              >
              </multiselect>
              <div
                v-if="$v.create.gl_acc_no.$error || errors.gl_acc_no"
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.gl_acc_no">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.gl_acc_no"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label for="field-1" class="control-label">
                {{ getCompanyKey("subcontact_group_name_ar") }}
                <span class="text-danger">*</span>
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
                {{ getCompanyKey("subcontact_group_name_en") }}
                <span class="text-danger">*</span>
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
      </form>
    </b-modal>
    <!--  /create   -->
  </div>
</template>
