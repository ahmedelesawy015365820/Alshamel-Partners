<script>
import adminApi from "../../../api/adminAxios";
import { minLength, maxLength, requiredIf } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import Document from "../../../components/create/general/document";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import Branch from "../../../components/create/general/branch";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";

/**
 * Advanced Table component
 */
export default {
  components: {
    ErrorMessage,
    loader,
    Multiselect,
    Document,
    Branch,
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
    url: { default: "/serials" },
  },
  data() {
    return {
      fields: [],
      documents: [],
      branches: [],
      is_disabled: false,
      isLoader: false,
      types: [],
      isCustom: false,
      create: {
        name: "",
        name_e: "",
        start_no: null,
        perfix: "",
        suffix: "",
        restart_period_id: null,
        branch_id: null,
        document_id: null,
        type: "",
          gender: 2,
      },
      company_id: null,
      errors: {},
      stores: [],
    };
  },
  validations: {
    create: {
      name: {
        required: requiredIf(function (model) {
          return this.isRequired("name");
        }),
        minLength: minLength(3),
        maxLength: maxLength(100),
      },
      name_e: {
        required: requiredIf(function (model) {
          return this.isRequired("name_e");
        }),
        minLength: minLength(3),
        maxLength: maxLength(100),
      },
      start_no: {
        required: requiredIf(function (model) {
          return this.isRequired("start_no");
        }),
        maxLength: maxLength(20),
      },
      suffix: {
        required: requiredIf(function (model) {
          return this.isRequired("suffix");
        }),
        maxLength: maxLength(200),
      },
      perfix: {
        required: requiredIf(function (model) {
          return this.isRequired("perfix");
        }),
        maxLength: maxLength(200),
      },
      restart_period_id: {
        required: requiredIf(function (model) {
          return this.isRequired("restart_period_id");
        }),
        minLength: minLength(1),
        maxLength: maxLength(200),
      },
      document_id: {
        required: requiredIf(function (model) {
          return this.isRequired("document_id");
        }),
      },
      branch_id: {
        required: requiredIf(function (model) {
          return this.isRequired("branch_id");
        }),
      },
        gender: {
            required: requiredIf(function (model) {
                return this.isRequired("gender");
            }),
        },
    },
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
    this.companyId(this.company_id);
  },
  methods: {
    arabicValueName(txt) {
      this.create.name = arabicValue(txt);
    },
    englishValueName(txt) {
      this.create.name_e = englishValue(txt);
    },
      async getCustomTableFields() {
      this.isCustom = true;
      await adminApi
        .get(`/customTable/table-columns/general_serials`)
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
    defaultData(edit = null) {
      this.create = {
        name: "",
        name_e: "",
        start_no: null,
        perfix: "",
        suffix: "",
        restart_period_id: null,
        branch_id: null,
        document_id: null,
          gender: 2,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.is_disabled = false;
    },
    resetForm() {
      this.defaultData();
    },

    /**
     *  start get Data countrie && pagination
     */
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      let _filterSetting = [...this.filterSetting];
      let index = this.filterSetting.indexOf("document");
      if (index > -1) {
        _filterSetting[index] =
          this.$i18n.locale == "ar" ? "document.name" : "document.name_e";
      }
      index = this.filterSetting.indexOf("branch");
      if (index > -1) {
        _filterSetting[index] =
          this.$i18n.locale == "ar" ? "branch.name" : "branch.name_e";
      }
      for (let i = 0; i < _filterSetting.length; ++i) {
        filter += `columns[${i}]=${_filterSetting[i]}&`;
      }
      adminApi
        .get(
          `/serials?page=${page}&per_page=${this.per_page}&company_id=${this.company_id}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.serials = l.data;
          this.serialsPagination = l.pagination;
          this.current_page = l.pagination.current_page;
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
    getDataCurrentPage(page = 1) {
      if (
        this.current_page <= this.serialsPagination.last_page &&
        this.current_page != this.serialsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }
        adminApi
          .get(
            `/serials?page=${this.current_page}&per_page=${this.per_page}&company_id=${this.company_id}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.serials = l.data;
            this.serialsPagination = l.pagination;
            this.current_page = l.pagination.current_page;
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
      }
    },
    /**
     *  end get Data countrie && pagination
     */
    /**
     *  start delete countrie
     */
    deleteBranch(id, index) {
      if (Array.isArray(id)) {
        Swal.fire({
          title: `${this.$t("general.Areyousure")}`,
          text: `${this.$t("general.Youwontbeabletoreverthis")}`,
          type: "warning",
          showCancelButton: true,
          confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
          cancelButtonText: `${this.$t("general.Nocancel")}`,
          confirmButtonClass: "btn btn-success mt-2",
          cancelButtonClass: "btn btn-danger ml-2 mt-2",
          buttonsStyling: false,
        }).then((result) => {
          if (result.value) {
            this.isLoader = true;
            adminApi
              .post(`/serials/bulk-delete`, { ids: id })
              .then((res) => {
                this.checkAll = [];
                this.getData();
                Swal.fire({
                  icon: "success",
                  title: `${this.$t("general.Deleted")}`,
                  text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                  showConfirmButton: false,
                  timer: 1500,
                });
              })
              .catch((err) => {
                if (err.response.status == 400) {
                  Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.CantDeleteRelation")}`,
                  });
                  this.getData();
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
        });
      } else {
        Swal.fire({
          title: `${this.$t("general.Areyousure")}`,
          text: `${this.$t("general.Youwontbeabletoreverthis")}`,
          type: "warning",
          showCancelButton: true,
          confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
          cancelButtonText: `${this.$t("general.Nocancel")}`,
          confirmButtonClass: "btn btn-success mt-2",
          cancelButtonClass: "btn btn-danger ml-2 mt-2",
          buttonsStyling: false,
        }).then((result) => {
          if (result.value) {
            this.isLoader = true;

            adminApi
              .delete(`/serials/${id}`)
              .then((res) => {
                this.checkAll = [];
                this.getData();
                Swal.fire({
                  icon: "success",
                  title: `${this.$t("general.Deleted")}`,
                  text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                  showConfirmButton: false,
                  timer: 1500,
                });
              })

              .catch((err) => {
                if (err.response.status == 400) {
                  Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.CantDeleteRelation")}`,
                  });
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
        });
      }
    },
    /**
     *  end delete countrie
     */
    /**
     *  reset Modal (create)
     */
    resetModalHidden() {
      this.defaultData();
      this.$bvModal.hide(this.id);
    },
    /**
     *  hidden Modal (create)
     */
    resetModal() {
      this.defaultData();
      setTimeout(async () => {
        if (this.type != "edit") {
          if (!this.isPage) await this.getCustomTableFields();
          if (this.isVisible("document_id")) this.getDocument(this.company_id);
          if (this.isVisible("branch_id")) this.getBranch();
          if (this.isVisible("restart_period_id")) this.getPeriod();
        } else {
          if (this.idObjEdit.dataObj) {
            let serial = this.idObjEdit.dataObj;
            this.errors = {};
            if (this.isVisible("document_id"))
              this.getDocument(this.company_id);
            if (this.isVisible("branch_id")) this.getBranch();
            if (this.isVisible("restart_period_id")) this.getPeriod();
            this.create.start_no = serial.start_no;
            this.create.perfix = serial.perfix;
            this.create.suffix = serial.suffix;
            this.create.has_child = serial.has_child;
            this.create.restart_period_id = serial.restart_period_id
              ? serial.restart_period_id
              : null;
            this.create.document_id = serial.document
              ? serial.document.id
              : null;
            this.create.branch_id = serial.branch ? serial.branch.id : null;
            this.errors = {};
            this.create.name = serial.name;
            this.create.name_e = serial.name_e;
            this.create.gender = serial.gender;
          }
        }
      }, 50);
    },
    /**
     *  create countrie
     */

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
        if (!this.create.name) {
          this.create.name = this.create.name_e;
        }
        if (!this.create.name_e) {
          this.create.name_e = this.create.name;
        }

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

    getDocument(id) {
      this.isLoader = true;
      adminApi
        .get(`/document?company_id=${this.company_id}&document=1`)
        .then((res) => {
          let l = res.data.data;
          // l.unshift({ id: 0, name: "اضف مستند", name_e: "Add Document" });
          this.documents = l;
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
    getBranch() {
      this.isLoader = true;

      adminApi
        .get(`/branches/get-drop-down?company_id=${this.company_id}&notParent=1`)
        .then((res) => {
          let l = res.data.data;
          if (this.isPermission("create Branch")) {
            l.unshift({ id: 0, name: "اضف فرع", name_e: "Add Branch" });
          }
          this.branches = l;
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
    getPeriod() {
      this.isLoader = true;

      adminApi
        .get(`/restart-period/get-drop-down`)
        .then((res) => {
          let l = res.data.data;
          this.types = l;
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
    showBranchModal() {
      if (this.create.branch_id == 0) {
        this.$bvModal.show("branch-create");
        this.create.branch_id = null;
      }
    },
    showDocumentModal() {
      if (this.create.document_id == 0) {
        this.$bvModal.show("create-document");
        this.create.document_id = null;
      }
    },
    companyId(id) {
      axios
        .get(
          `${process.env.MIX_APP_URL_OUTSIDE}api/everything_about_the_company/${id}`
        )
        .then((res) => {
          let l = res.data.data;
          if (l.document_company_module.length > 0) {
            let documents = [];
            l.document_company_module.forEach((e) => {
              if (e.document_types.length > 0) {
                e.document_types.forEach((w) => {
                  documents.push({
                    id: w.id,
                    name: w.name,
                    name_e: w.name_e,
                    is_admin: w.is_admin,
                    is_default: 0,
                    company_id: id,
                    document_relateds: w.document_relateds.map((el) => el.id),
                  });
                });
              }
            });
            if (documents.length > 0) {
              documents.forEach((e) => (e.is_admin = 1));
              adminApi
                .post(`/document/from_admin`, { documents })
                .then((res) => {})
                .catch((err) => {
                  Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
                });
            }
          }
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
    <Document
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
      :id="'create-document'"
      @created="getDocument"
    />
    <Branch
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      :isPage="false"
      type="create"
      :isPermission="isPermission"
      :id="'branch-create'"
      @created="getBranch"
    />
    <!--  create   -->
    <b-modal
      :id="id"
      :title="
        type == 'create' ? $t('serial.addserial') : $t('serial.editserial')
      "
      title-class="font-18"
      body-class="p-4 "
      size="lg"
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
          <div class="col-md-6 position-relative" v-if="isVisible('branch_id')">
            <div class="form-group">
              <label>
                {{ getCompanyKey("serial_branch") }}
                <span v-if="isRequired('branch_id')" class="text-danger"
                  >*</span
                >
              </label>
              <multiselect
                @input="showBranchModal"
                v-model="create.branch_id"
                :options="branches.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    branches.find((x) => x.id == opt)
                      ? $i18n.locale == 'ar'
                        ? branches.find((x) => x.id == opt).name
                        : branches.find((x) => x.id == opt).name_e
                      : null
                "
                :class="{
                  'is-invalid': $v.create.branch_id.$error || errors.branch_id,
                }"
              >
              </multiselect>
              <div
                v-if="!$v.create.branch_id.required"
                class="invalid-feedback"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>

              <template v-if="errors.branch_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.branch_id"
                  :key="index"
                  >{{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6" v-if="isVisible('document_id')">
            <div class="form-group position-relative">
              <label class="control-label">
                {{ getCompanyKey("serial_document") }}
                <span v-if="isRequired('document_id')" class="text-danger"
                  >*</span
                >
              </label>
              <multiselect
                v-model="create.document_id"
                @select="showDocumentModal"
                :options="documents.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    documents.find((x) => x.id == opt)
                      ? $i18n.locale == 'ar'
                        ? documents.find((x) => x.id == opt).name
                        : documents.find((x) => x.id == opt).name_e
                      : null
                "
              >
              </multiselect>
              <div
                v-if="$v.create.document_id.$error || errors.document_id"
                class="text-danger"
              >
                {{ $t("general.fieldIsRequired") }}
              </div>
              <template v-if="errors.document_id">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.document_id"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-6" v-if="isVisible('name')">
            <div class="form-group">
              <label for="field-8" class="control-label">
                {{ getCompanyKey("serial_name_ar") }}
                <span v-if="isRequired('name')" class="text-danger">*</span>
              </label>
              <div dir="rtl">
                <input
                  type="text"
                  class="form-control"
                  data-create="1"
                  @keyup="arabicValueName(create.name)"
                  v-model="$v.create.name.$model"
                  :class="{
                    'is-invalid': $v.create.name.$error || errors.name,
                    'is-valid': !$v.create.name.$invalid && !errors.name,
                  }"
                  id="field-8"
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
                >
                  {{ errorMessage }}
                </ErrorMessage>
              </template>
            </div>
          </div>
          <div class="col-md-6" v-if="isVisible('name_e')">
            <div class="form-group">
              <label for="field-9" class="control-label">
                {{ getCompanyKey("serial_name_en") }}
                <span v-if="isRequired('name_e')" class="text-danger">*</span>
              </label>
              <div dir="ltr">
                <input
                  type="text"
                  class="form-control"
                  data-create="2"
                  @keyup="englishValueName(create.name_e)"
                  v-model="$v.create.name_e.$model"
                  :class="{
                    'is-invalid': $v.create.name_e.$error || errors.name_e,
                    'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                  }"
                  id="field-9"
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
          <div class="col-md-6" v-if="isVisible('perfix')">
            <div class="form-group">
              <label for="field-2" class="control-label">
                {{ getCompanyKey("serial_prefix") }}
                <span v-if="isRequired('perfix')" class="text-danger">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                data-create="3"
                v-model="$v.create.perfix.$model"
                :class="{
                  'is-invalid': $v.create.perfix.$error || errors.perfix,
                  'is-valid': !$v.create.perfix.$invalid && !errors.perfix,
                }"
                id="field-2"
              />
              <div v-if="!$v.create.perfix.maxLength" class="invalid-feedback">
                {{ $t("general.Itmustbeatmost") }}
                {{ $v.create.perfix.$params.maxLength.max }}
                {{ $t("general.letters") }}
              </div>
              <template v-if="errors.perfix">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.name_e"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-6" v-if="isVisible('suffix')">
            <div class="form-group">
              <label for="field-1" class="control-label">
                {{ getCompanyKey("serial_suffix") }}

                <span v-if="isRequired('suffix')" class="text-danger">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                data-create="2"
                v-model="$v.create.suffix.$model"
                :class="{
                  'is-invalid': $v.create.suffix.$error || errors.suffix,
                  'is-valid': !$v.create.suffix.$invalid && !errors.suffix,
                }"
                id="field-1"
              />
              <div v-if="!$v.create.suffix.maxLength" class="invalid-feedback">
                {{ $t("general.Itmustbeatmost") }}
                {{ $v.create.suffix.$params.maxLength.max }}
                {{ $t("general.letters") }}
              </div>
              <template v-if="errors.suffix">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.suffix"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-6" v-if="isVisible('restart_period_id')">
            <div class="form-group">
              <label class="control-label">
                {{ getCompanyKey("serial_restart_period") }}
                <span v-if="isRequired('restart_period_id')" class="text-danger"
                  >*</span
                >
              </label>
              <multiselect
                v-model="$v.create.restart_period_id.$model"
                :options="types.map((type) => type.id)"
                :custom-label="
                  (opt) =>
                    types.find((x) => x.id == opt)
                      ? $i18n.locale == 'ar'
                        ? types.find((x) => x.id == opt).name
                        : types.find((x) => x.id == opt).name_e
                      : null
                "
                :class="{
                  'is-invalid':
                    $v.create.restart_period_id.$error ||
                    errors.restart_period_id,
                }"
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
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
          <div class="col-md-6" v-if="isVisible('start_no')">
            <div class="form-group">
              <label for="field-15" class="control-label">
                {{ getCompanyKey("serial_start_no") }}
                <span v-if="isRequired('start_no')" class="text-danger">*</span>
              </label>
              <input
                type="number"
                class="form-control"
                data-create="1"
                v-model="$v.create.start_no.$model"
                :class="{
                  'is-invalid': $v.create.start_no.$error || errors.start_no,
                  'is-valid': !$v.create.start_no.$invalid && !errors.start_no,
                }"
                id="field-15"
              />
              <div
                v-if="!$v.create.start_no.maxLength"
                class="invalid-feedback"
              >
                {{ $t("general.Itmustbeatmost") }}
                {{ $v.create.start_no.$params.maxLength.max }}
                {{ $t("general.letters") }}
              </div>
              <template v-if="errors.start_no">
                <ErrorMessage
                  v-for="(errorMessage, index) in errors.start_no"
                  :key="index"
                  >{{ errorMessage }}</ErrorMessage
                >
              </template>
            </div>
          </div>
            <div class="col-md-6" v-if="isVisible('gender')">
                <div class="form-group">
                    <label class="control-label mr-2">
                        {{ getCompanyKey("serial_gender") }}
                        <span v-if="isRequired('gender')" class="text-danger">*</span>
                    </label>
                    <b-form-group
                        :class="{
                          'is-invalid':
                            $v.create.gender.$error || errors.gender,
                          'is-valid':
                            !$v.create.gender.$invalid && !errors.gender,
                        }"
                    >
                        <b-form-radio
                            class="d-inline-block"
                            v-model="$v.create.gender.$model"
                            name="create_gender"
                            value="1"
                        >{{ $t("general.male") }}
                        </b-form-radio>
                        <b-form-radio
                            class="d-inline-block m-1"
                            v-model="$v.create.gender.$model"
                            name="create_gender"
                            value="0"
                        >{{ $t("general.female") }}
                        </b-form-radio>
                        <b-form-radio
                            class="d-inline-block m-1"
                            v-model="$v.create.gender.$model"
                            name="create_gender"
                            value="2"
                        >{{ $t("general.all") }}
                        </b-form-radio>
                    </b-form-group>
                    <template v-if="errors.gender">
                        <ErrorMessage
                            v-for="(errorMessage, index) in errors.gender"
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

<style>
form {
    position: relative;
}
</style>
