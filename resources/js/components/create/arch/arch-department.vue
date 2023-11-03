<script>
import adminApi from "../../../api/adminAxios";
import { required, minLength, maxLength, integer, url } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import {arabicValue, englishValue} from "../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
  components: {
    ErrorMessage,
    loader,
    Multiselect,
  },
  props: ["companyKeys", "defaultsKeys"],

  mixins: [transMixinComp],
  updated() {
    // $(".englishInput").keypress(function (event) {
    //   var ew = event.which;
    //   if (ew == 32) return true;
    //   if (48 <= ew && ew <= 57) return true;
    //   if (65 <= ew && ew <= 90) return true;
    //   if (97 <= ew && ew <= 122) return true;
    //   return false;
    // });
    // $(".arabicInput").keypress(function (event) {
    //   var ew = event.which;
    //   if (ew == 32) return true;
    //   if (48 <= ew && ew <= 57) return false;
    //   if (65 <= ew && ew <= 90) return false;
    //   if (97 <= ew && ew <= 122) return false;
    //   return true;
    // });
  },
  data() {
    return {
      per_page: 50,
      search: "", //Search table column
      debounce: {},
      pagination: {},
      archDepartmentAr: [],
      enabled3: true,
      isLoader: false,
      create: {
        name: "",
        name_e: "",
        parent_id: "",
        is_active: "active",
      }, //Create form
      edit: {
        name: "",
        name_e: "",
        parent_id: "",
        is_active: "active",
      }, //Edit form
      setting: {
        name: true,
        name_e: true,
        parent_id: true,
        is_active: true,
      }, //Table columns
      filterSetting: ["name", "name_e", "is_active", "parent_id"],
      errors: {}, //Server Side Validation Errors
      isCheckAll: false,
      checkAll: [],
      is_disabled: false,
      current_page: 1,
      printLoading: true,
      printObj: {
        id: "printDepartment",
      },
    };
  },
  validations: {
    create: {
      name: { required, minLength: minLength(3), maxLength: maxLength(255) },
      name_e: {
        required,
        minLength: minLength(3),
        maxLength: maxLength(255),
      },
      is_active: { required },
      parent_id: { integer },
    },
    edit: {
      name: { required, minLength: minLength(3), maxLength: maxLength(255) },
      name_e: {
        required,
        minLength: minLength(3),
        maxLength: maxLength(255),
      },
      is_active: { required },
      parent_id: { integer },
    },
  },
  mounted() {
    this.getData();
  },
  methods: {
    getData(page = 1) {
      this.isLoader = true;
      adminApi
        .get(`/arch-department`)
        .then((res) => {
          let l = res.data;
          this.archDepartmentAr = l.data;
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
    /**
     *  reset Modal (create)
     */
    resetModalHidden() {
      this.create = { name: "", name_e: "", is_active: "active", parent_id: "" };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
        this.is_disabled = false;
    },
    /**
     *  hidden Modal (create)
     */
    resetModal() {
      this.create = { name: "", name_e: "", is_active: "active", parent_id: "" };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create Arch Department
     */
    resetForm() {
      this.create = { name: "", name_e: "", is_active: "active", parent_id: "" };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
    },
    /**
     *  add Arch Department
     */
    AddSubmit() {
      if (this.create.name || this.create.name_e) {
        this.create.name = this.create.name ? this.create.name : this.create.name_e;
        this.create.name_e = this.create.name_e ? this.create.name_e : this.create.name;
      }
      this.$v.create.$touch();
      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .post(`/arch-department`, {
            ...this.create,
              company_id: this.$store.getters["auth/company_id"]
          })
          .then((res) => {
            this.getData();
            this.$emit("create-arch-department");
            setTimeout(() => {
              Swal.fire({
                icon: "success",
                text: `${this.$t("general.Addedsuccessfully")}`,
                showConfirmButton: false,
                timer: 1500,
              });
            }, 500);
            this.is_disabled = true;
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
  <!--  create   -->
  <b-modal
    id="create-arch-department"
    :title="getCompanyKey('ArchiveDepartmentCreate')"
    title-class="font-18"
    body-class="p-4 "
    :hide-footer="true"
    @show="resetModal"
    @hidden="resetModalHidden"
  >
    <form>
      <div class="mb-3 d-flex justify-content-end">
        <!-- Start Add New Record Button -->
        <b-button
          variant="success"
          :disabled="!is_disabled"
          @click.prevent="resetForm"
          type="button"
          :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
        >
          {{ $t("general.AddNewRecord") }}
        </b-button>
        <!-- End Add New Record Button -->
        <!-- Emulate built in modal footer ok and cancel button actions -->
        <template v-if="!is_disabled">
          <!-- Start Add Button -->
          <b-button
            variant="success"
            type="submit"
            class="mx-1"
            v-if="!isLoader"
            @click.prevent="AddSubmit"
          >
            {{ $t("general.Add") }}
          </b-button>
          <!-- End Add Button -->
          <b-button variant="success" class="mx-1" disabled v-else>
            <b-spinner small></b-spinner>
            <span class="sr-only">{{ $t("login.Loading") }}...</span>
          </b-button>
        </template>
        <!-- Start Cancel Button Modal -->
        <b-button
          variant="danger"
          type="button"
          @click.prevent="$bvModal.hide(`create-arch-department`)"
        >
          {{ $t("general.Cancel") }}
        </b-button>
        <!-- End Cancel Button Modal -->
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="field-1" class="control-label">
              {{ getCompanyKey("archive_name") }}
              <span class="text-danger">*</span>
            </label>
            <div dir="rtl">
              <input
                type="text"
                class="form-control arabicInput"
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
              <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index"
                >{{ errorMessage }}
              </ErrorMessage>
            </template>
          </div>
        </div>
        <div class="col-md-12 direction-ltr" dir="ltr">
          <div class="form-group">
            <label for="field-2" class="control-label">
              {{ getCompanyKey("archive_name_e") }}
              <span class="text-danger">*</span>
            </label>
            <input
              type="text"
              class="form-control englishInput"
              v-model="$v.create.name_e.$model"
              :class="{
                'is-invalid': $v.create.name_e.$error || errors.name_e,
                'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
              }"
              @keyup="englishValue(create.name_e)"
              id="field-2"
            />
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
              <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index"
                >{{ errorMessage }}
              </ErrorMessage>
            </template>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group position-relative">
            <label class="control-label">
              {{ $t("general.parentId") }}
              <span class="text-danger">*</span>
            </label>

            <multiselect
              v-model="create.parent_id"
              :options="archDepartmentAr.map((type) => type.id)"
              :custom-label="
                (opt) =>
                  $i18n.locale
                    ? archDepartmentAr.find((x) => x.id == opt).name
                    : archDepartmentAr.find((x) => x.id == opt).parent_id
              "
            >
            </multiselect>

            <div v-if="!$v.create.parent_id.integer" class="invalid-feedback">
              {{ $t("general.fieldIsInteger") }}
            </div>
            <template v-if="errors.parent_id">
              <ErrorMessage v-for="(errorMessage, index) in errors.parent_id" :key="index"
                >{{ errorMessage }}
              </ErrorMessage>
            </template>
          </div>
        </div>
        <div class="col-md-12">
          <div class="form-group">
            <label class="mr-2">
              {{ getCompanyKey("archive_status") }}
              <span class="text-danger">*</span>
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
                >{{ $t("general.Active") }}
              </b-form-radio>
              <b-form-radio
                class="d-inline-block m-1"
                v-model="$v.create.is_active.$model"
                name="some-radios"
                value="inactive"
                >{{ $t("general.Inactive") }}
              </b-form-radio>
            </b-form-group>
            <template v-if="errors.is_active">
              <ErrorMessage v-for="(errorMessage, index) in errors.is_active" :key="index"
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
