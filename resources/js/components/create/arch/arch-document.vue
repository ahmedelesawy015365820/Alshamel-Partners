<script>
import adminApi from "../../../api/adminAxios";
import { required, minLength, maxLength, integer, url } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import ArchStatus from "../../../components/create/arch-status.vue";
import ArchDocumentType from "../../../components/create/arch-document-type.vue";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";

/**
 * Advanced Table component
 */
export default {
  components: {
    ErrorMessage,
    loader,
    Multiselect,
    ArchStatus,
    ArchDocumentType,
  },
  mixins: [transMixinComp],
  props: ["companyKeys", "defaultsKeys"],
  updated() {
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
  },
  data() {
    return {
      per_page: 50,
      search: "", //Search table column
      debounce: {},
      pagination: {},
      genArchDocTypeAr: [], //Fetch Parent Table Data
      archDocStatusAr: [], //Fetch Parent Table Data
      dataAr: [], //Same Table Data
      enabled3: true,
      isLoader: false,
      create: {
        gen_arch_doc_type: "",
        doc_description: "",
        doc_status: "",
        url_reference: "",
      }, //Create form
      edit: {
        gen_arch_doc_type: "",
        doc_description: "",
        doc_status: "",
        url_reference: "",
      }, //Edit form
      setting: {
        gen_arch_doc_type: true,
        doc_description: true,
        doc_status: true,
        url_reference: true,
      }, //Table columns
      filterSetting: ["doc_status", "gen_arch_doc_type"],
      errors: {}, //Server Side Validation Errors
      isCheckAll: false,
      checkAll: [],
      is_disabled: false,
      current_page: 1,
    };
  },
  validations: {
    create: {
      gen_arch_doc_type: { required, integer },
      doc_description: { required, minLength: minLength(3), maxLength: maxLength(255) },
      url_reference: {
        required,
        url,
        minLength: minLength(10),
        maxLength: maxLength(200),
      },
      doc_status: { required, integer },
    },
    edit: {
      gen_arch_doc_type: { required, integer },
      doc_description: { required, minLength: minLength(3), maxLength: maxLength(255) },
      url_reference: {
        required,
        url,
        minLength: minLength(10),
        maxLength: maxLength(200),
      },
      doc_status: { required, integer },
    },
  },
  methods: {
    showArchStatusModal() {
      if (this.create.doc_status == 0) {
        this.$bvModal.show("arch-status-create");
        this.create.doc_status = null;
      }
    },
    showArchStatusModalEdit() {
      if (this.edit.doc_status == 0) {
        this.$bvModal.show("arch-status-create");
        this.edit.doc_status = null;
      }
    },
    showArchDocTypeModal() {
      if (this.create.gen_arch_doc_type == 0) {
        this.$bvModal.show("arch-document-type-create");
        this.create.gen_arch_doc_type = null;
      }
    },
    showArchDocTypeModalEdit() {
      if (this.edit.gen_arch_doc_type == 0) {
        this.$bvModal.show("arch-document-type-create");
        this.edit.gen_arch_doc_type = null;
      }
    },
    /**
     *  reset Modal (create)
     */
    resetModalHidden() {
      this.create = {
        gen_arch_doc_type: "",
        doc_description: "",
        doc_status: "",
        url_reference: "",
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
        this.is_disabled = false;
      this.errors = {};
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      await this.getArcDocStatus();
      await this.getGenArchDocType();
      this.create = {
        gen_arch_doc_type: "",
        doc_description: "",
        doc_status: "",
        url_reference: "",
      };
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
      this.create = {
        gen_arch_doc_type: "",
        doc_description: "",
        doc_status: "",
        url_reference: "",
      };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
    },
    /**
     *  add Arch Department
     */
    AddSubmit() {
      this.$v.create.$touch();
      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;
        adminApi
          .post(`/arch-document`, {
            ...this.create,
              company_id: this.$store.getters["auth/company_id"],
          })
          .then((res) => {
            this.getData();
            this.is_disabled = true;
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
    /**
     *  get parent
     */
    async getArcDocStatus() {
      await adminApi
        .get(`/arch-doc-status`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({
            id: 0,
            name: "اضف حالة وثيقة الارشفة",
            name_e: "Add archiving document status",
          });
          this.archDocStatusAr = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    /**
     *  get parent
     */
    async getGenArchDocType() {
      await adminApi
        .get(`/arch-doc-type`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({
            id: 0,
            name: "اضف نوع وثيقة الارشفة",
            name_e: "Add archiving document type",
          });
          this.genArchDocTypeAr = l;
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
    <ArchStatus :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getArcDocStatus" />
    <ArchDocumentType :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getGenArchDocType" />
    <!--  create   -->
    <b-modal
      id="create-arch-document"
      :title="getCompanyKey('arch_document_create_form')"
      title-class="font-18"
      body-class="p-4 "
      :hide-footer="true"
      size="lg"
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
            @click.prevent="$bvModal.hide(`create`)"
          >
            {{ $t("general.Cancel") }}
          </b-button>
          <!-- End Cancel Button Modal -->
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("arch_document_status") }}
                <span class="text-danger">*</span>
              </label>

              <multiselect
                v-model="create.doc_status"
                @input="showArchStatusModal"
                :options="archDocStatusAr.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    $i18n.locale == 'ar'
                      ? archDocStatusAr.find((x) => x.id == opt).name
                      : archDocStatusAr.find((x) => x.id == opt).name_e
                "
              >
              </multiselect>

              <div v-if="!$v.create.doc_status.integer" class="invalid-feedback">
                {{ $t("general.fieldIsInteger") }}
              </div>
              <template v-if="errors.doc_status">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.doc_status"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("arch_document_type") }}
                <span class="text-danger">*</span>
              </label>

              <multiselect
                @input="showArchDocTypeModal"
                v-model="create.gen_arch_doc_type"
                :options="genArchDocTypeAr.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    $i18n.locale == 'ar'
                      ? genArchDocTypeAr.find((x) => x.id == opt).name
                      : genArchDocTypeAr.find((x) => x.id == opt).name_e
                "
              >
              </multiselect>

              <div v-if="!$v.create.gen_arch_doc_type.integer" class="invalid-feedback">
                {{ $t("general.fieldIsInteger") }}
              </div>
              <template v-if="errors.gen_arch_doc_type">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.gen_arch_doc_type"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6 direction-ltr" dir="ltr">
            <div class="form-group">
              <label for="field-2" class="control-label">
                {{ getCompanyKey("arch_document_description") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                v-model="$v.create.doc_description.$model"
                :class="{
                  'is-invalid':
                    $v.create.doc_description.$error || errors.doc_description,
                  'is-valid':
                    !$v.create.doc_description.$invalid && !errors.doc_description,
                }"
                id="field-2"
              />
              <div v-if="!$v.create.doc_description.minLength" class="invalid-feedback">
                {{ $t("general.Itmustbeatleast") }}
                {{ $v.create.doc_description.$params.minLength.min }}
                {{ $t("general.letters") }}
              </div>
              <div v-if="!$v.create.doc_description.maxLength" class="invalid-feedback">
                {{ $t("general.Itmustbeatmost") }}
                {{ $v.create.doc_description.$params.maxLength.max }}
                {{ $t("general.letters") }}
              </div>
              <template v-if="errors.doc_description">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.doc_description"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="field-11" class="control-label">
                {{ getCompanyKey("arch_document_url_reference") }}
                <span class="text-danger">*</span>
              </label>
              <input
                type="url"
                class="form-control"
                v-model.number="$v.create.url_reference.$model"
                :class="{
                  'is-invalid': $v.create.url_reference.$error || errors.url_reference,
                  'is-valid': !$v.create.url_reference.$invalid && !errors.url_reference,
                }"
                id="field-11"
              />
              <div v-if="!$v.create.url_reference.minLength" class="invalid-feedback">
                {{ $t("general.Itmustbeatleast") }}
                {{ $v.create.url_reference.$params.minLength.min }}
                {{ $t("general.letters") }}
              </div>
              <div v-if="!$v.create.url_reference.maxLength" class="invalid-feedback">
                {{ $t("general.Itmustbeatmost") }}
                {{ $v.create.url_reference.$params.maxLength.max }}
                {{ $t("general.letters") }}
              </div>
              <div v-if="!$v.create.url_reference.url" class="invalid-feedback">
                {{ $t("general.Itmustbeyourlink") }}
              </div>
              <template v-if="errors.url_reference">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.url_reference"
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
