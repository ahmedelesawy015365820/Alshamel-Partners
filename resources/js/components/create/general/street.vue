<template>
  <div>
    <Avenue
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
      :id="'avenue-create'"
      @created="getAvenue"
    />
    <!--  create   -->
    <b-modal
      :id="id"
      :title="
        type != 'edit'
          ? getCompanyKey('street_create_form')
          : getCompanyKey('street_edit_form')
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
          <div class="col-md-12" v-if="isVisible('avenue_id') && !avenue_id">
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("street_avenue") }}
                <span v-if="isRequired('avenue_id')" class="text-danger"
                  >*</span
                >
              </label>
              <multiselect
                @input="showAvenueModal"
                v-model="create.avenue_id"
                :options="avenues.map((type) => type.id)"
                :custom-label="(opt) => avenues.find((x) => x.id == opt)?$i18n.locale == 'ar'?avenues.find((x) => x.id == opt).name:avenues.find((x) => x.id == opt).name_e:null"
              >
              </multiselect>
              <div
                v-if="$v.create.avenue_id.$error || errors.avenue_id"
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.avenue_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.avenue_id"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-12" v-if="isVisible('name')">
            <div class="form-group">
              <label for="field-1" class="control-label">
                {{ getCompanyKey("street_name_ar") }}
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
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-12" v-if="isVisible('name_e')">
            <div class="form-group">
              <label for="field-2" class="control-label">
                {{ getCompanyKey("street_name_en") }}
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
          <div class="col-md-12" v-if="isVisible('is_active')">
            <div class="form-group">
              <label class="mr-2">
                {{ getCompanyKey("street_status") }}
                <span v-if="isRequired('is_active')" class="text-danger"
                  >*</span
                >
              </label>
              <b-form-group
                :class="{
                  'is-invalid': $v.create.is_active.$error || errors.is_active,
                  'is-valid':
                    !$v.create.is_active.$invalid && !errors.is_active,
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
        </div>
      </form>
    </b-modal>
    <!--  /create   -->
  </div>
</template>

<script>
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import { maxLength, minLength, requiredIf } from "vuelidate/lib/validators";
import Layout from "../../../views/layouts/main";
import PageHeader from "../../general/Page-header";
import Switches from "vue-switches";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import Avenue from "./avenue";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";

export default {
  name: "street",
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
    avenue_id: { default: null },
    url: { default: "/streets" },
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
  },
  data() {
    return {
      fields: [],
      isLoader: false,
      create: {
        name: "",
        name_e: "",
        avenue_id: "",
        is_active: "active",
      },
      company_id: null,
      errors: {},
      avenues: [],
      isCustom: false,
      is_disabled: false,
    };
  },
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
    Avenue,
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
      avenue_id: {
        required: requiredIf(function (model) {
          return this.isRequired("avenue_id");
        }),
      },
      is_active: {
        required: requiredIf(function (model) {
          return this.isRequired("is_active");
        }),
      },
    },
  },
  methods: {
    async getAvenue() {
      this.isLoader = true;
      await adminApi
        .get(`/avenues/get-drop-down`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "اضافة منطقه", name_e: "Add Avenue" });
          this.avenues = l;
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
    showAvenueModal() {
      if (this.create.avenue_id == 0) {
        this.$bvModal.show("avenue-create");
        this.create.avenue_id = null;
      }
    },
    resetModalHidden() {
      this.defaultData();
      this.$bvModal.hide(this.id);
    },
    AddSubmit() {
      if (!this.create.name) {
        this.create.name = this.create.name_e;
      }
      if (!this.create.name_e) {
        this.create.name_e = this.create.name;
      }

     if( this.isVisible('avenue_id') && this.avenue_id ) this.create.avenue_id = this.avenue_id;

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
    resetModal() {
      this.defaultData();
      setTimeout(async () => {
        if (this.type != "edit") {
          if (!this.isPage) await this.getCustomTableFields();
          this.$nextTick(() => {
            this.$v.$reset();
          });
          if (this.isVisible("avenue_id")) this.getAvenue();
        } else {
          if (this.idObjEdit.dataObj) {
              if (this.isVisible("avenue_id")) this.getAvenue();
            let avenues = this.idObjEdit.dataObj;
            this.errors = {};
            this.create.name = avenues.name;
            this.create.name_e = avenues.name_e;
            this.create.is_active = avenues.is_active;
            this.create.avenue_id = avenues.avenue ? avenues.avenue.id : null;
            this.errors = {};
          }
        }
      }, 50);
    },
    resetForm() {
      this.defaultData();
    },
    defaultData(edit = null) {
      this.create = {
        name: "",
        name_e: "",
        avenue_id: null,
        is_active: "active",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.is_disabled = false;
    },
    async getCustomTableFields() {
      this.isCustom = true;
      await adminApi
        .get(`/customTable/table-columns/general_streets`)
        .then((res) => {
          this.fields = res.data;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
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
  },
};
</script>

<style scoped>
form {
  position: relative;
}
</style>
