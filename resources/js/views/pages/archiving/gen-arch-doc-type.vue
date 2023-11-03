<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import { required, minLength, maxLength } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import DocumentStatus from "../../../components/arciving/arch-doc-status";
import DocumentTypeField from "../../../components/arciving/doc-type-field";
import DocumentTypeFieldEdit from "../../../components/arciving/doc-type-field-edit";
import DocumentDepartment from "../../../components/arciving/document-department";
import translation from "../../../helper/mixin/translation-mixin";
import permissionGuard from "../../../helper/permission";

import ArchStatus from "../../../components/create/arch/arch-status";
import { arabicValue, englishValue } from "../../../helper/langTransform";
/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Gen Arch Doc Type",
    meta: [{ name: "description", content: "Gen Arch Doc Type" }],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    ErrorMessage,
    loader,
    Multiselect,
    DocumentStatus,
    DocumentDepartment,
    DocumentTypeField,
    DocumentTypeFieldEdit,
    ArchStatus,
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Gen Archiving Document Type", "Gen Archiving Document Type");
    });
  },
  data() {
    return {
      documentStatuses: [],
      documentTypeStatuses: [],
      per_page: 50,
      search: "", //Search table column
      debounce: {},
      genArchDocTypePagination: {},
      genArchDocType: [],
      parent: [],
      enabled3: true,
      isLoader: false,
      arch_doc_type_id: null,
      doc_type_field: [],
      status_id: null,
      create: {
        name: "",
        name_e: "",
        parent_id: "",
        is_valid: 1,
      }, //Create form
      edit: {
        name: "",
        name_e: "",
        parent_id: "",
        is_valid: 1,
      }, //Edit form
      setting: {
        name: true,
        name_e: true,
        parent_id: true,
        is_valid: true,
      }, //Table columns
      filterSetting: ["name", "name_e"],
      errors: {}, //Server Side Validation Errors
      isCheckAll: false,
      checkAll: [],
      is_disabled: false,
      current_page: 1,
      printLoading: true,
      company_id: null,
      printObj: {
        id: "printType",
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
      is_valid: { required },
    },
    edit: {
      name: { required, minLength: minLength(3), maxLength: maxLength(255) },
      name_e: {
        required,
        minLength: minLength(3),
        maxLength: maxLength(255),
      },
      is_valid: { required },
    },
  },
  watch: {
    /**
     * watch per_page
     */
    per_page(after, befour) {
      this.getData();
    },
    /**
     * watch search
     */
    search(after, befour) {
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.getData();
      }, 400);
    },
    /**
     * watch check All table
     */
    isCheckAll(after, befour) {
      if (after) {
        this.genArchDocType.forEach((el) => {
          if (!this.checkAll.includes(el.id)) {
            this.checkAll.push(el.id);
          }
        });
      } else {
        this.checkAll = [];
      }
    },
  },
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
    this.getData();
    this.getDocumentStatuses();
  },
  methods: {
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
      this.edit.name = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
      this.edit.name_e = englishValue(txt);
    },
    addDocumentStatus(id) {
      if (id == 0) {
        this.$bvModal.show("arch-status-create");
        setTimeout(() => {
          this.status_id = null;
        }, 100);
        return;
      }
      this.isLoader = true;
      adminApi
        .post(`arch-doc-type/add-status-to-document`, {
          doc_type_id: this.arch_doc_type_id,
          status_id: id,
        })
        .then((res) => {
          this.isLoader = false;
          this.getDocumentTypeStatuses();
          this.status_id = null;

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
          this.status_id = null;

          if (err.response.status == 422) {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.ExistBefore")}`,
            });
            this.isLoader = false;
            return;
          }
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    deleteDocumentStatus(id) {
      this.isLoader = true;
      adminApi
        .delete(
          `arch-doc-type/remove-status-from-document/${this.arch_doc_type_id}/${id}`
        )
        .then((res) => {
          this.isLoader = false;
          this.getDocumentTypeStatuses();
          setTimeout(() => {
            Swal.fire({
              icon: "success",
              text: `${this.$t("general.DeletedSuccessfully")}`,
              showConfirmButton: false,
              timer: 1500,
            });
          }, 500);
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    async getDocumentTypeStatuses() {
      await adminApi
        .get(`/arch-doc-type/doc-statuses/${this.arch_doc_type_id}`)
        .then((res) => {
          this.documentTypeStatuses = res.data;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },

    async getDocumentStatuses() {
      await adminApi
        .get(`/arch-doc-status`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({
            id: 0,
            name: "اضف حالة ملف",
            name_e: "Add document status",
          });
          this.documentStatuses = l;
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
     *  get Data document field
     */
    getData(page = 1) {
      this.isLoader = true;

      let filter = "";
      for (let i = 0; i < this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/arch-doc-type?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.genArchDocType = l.data;
          this.genArchDocTypePagination = l.pagination;
          this.current_page = l.pagination.current_page;
          if (this.arch_doc_type_id) {
            let arch_doc_type_id = this.arch_doc_type_id;
            let editGenDocType = this.genArchDocType.find(
              (e) => arch_doc_type_id == e.id
            );
            this.doc_type_field = editGenDocType.doc_type_field;
          }
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
    getDataCurrentPage() {
      if (
        this.current_page <= this.genArchDocTypePagination.last_page &&
        this.current_page != this.genArchDocTypePagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/arch-doc-type?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.genArchDocType = l.data;
            this.genArchDocTypePagination = l.pagination;
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
     *  delete document field
     */
    deleteGenArchDocType(id) {
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
            .delete(`/arch-doc-type/${id}`)
            .then((res) => {
              this.getData();
              this.checkAll = [];
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
    },
    /**
     *  Bulk delete document fields
     */
    bulkDeleteGenArchDocType(id) {
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
            .post(`/arch-doc-type/bulk-delete`, {
              ids: id,
            })
            .then((res) => {
              this.getData();
              this.checkAll = [];
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
    },

    /**
     *  get parent data
     */
    async getParent() {
      await adminApi
        .get(`/arch-doc-type`)
        .then((res) => {
          let l = res.data;
          this.parent = l.data;
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
     *  reset Modal (create)
     */
    resetModalHidden() {
      this.create = { name: "", name_e: "", is_valid: 1, parent_id: "" };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.arch_doc_type_id = null;
      this.doc_type_field = [];
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      // call parent api
      await this.getParent();
      this.getDocumentStatuses();
      this.create = { name: "", name_e: "", is_valid: 1, parent_id: "" };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create document field
     */
    async resetForm() {
      // call parent api
      await this.getParent();
      this.create = { name: "", name_e: "", is_valid: 1, parent_id: "" };
      this.is_disabled = false;
      this.arch_doc_type_id = null;
      this.doc_type_field = [];
      this.$nextTick(() => {
        this.$v.$reset();
      });
    },
    /**
     *  add document field
     */
    AddSubmit() {
      if (this.create.name || this.create.name_e) {
        this.create.name = this.create.name
          ? this.create.name
          : this.create.name_e;
        this.create.name_e = this.create.name_e
          ? this.create.name_e
          : this.create.name;
      }
      this.$v.create.$touch();
      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;
        adminApi
          .post(`/arch-doc-type`, {
            ...this.create,
            is_valid: this.create.is_valid == "1" ? 1 : 0,
            company_id: this.company_id,
          })
          .then((res) => {
            this.arch_doc_type_id = res.data.data.id;
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
     *  edit document field
     */
    editSubmit(id) {
      if (this.edit.name || this.edit.name_e) {
        this.edit.name = this.edit.name ? this.edit.name : this.edit.name_e;
        this.edit.name_e = this.edit.name_e
          ? this.edit.name_e
          : this.create.name;
      }
      this.$v.edit.$touch();
      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        let { name, name_e, is_valid, parent_id } = this.edit;
        adminApi
          .put(`/arch-doc-type/${id}`, {
            name,
            name_e,
            is_valid: is_valid == "1" ? 1 : 0,
            parent_id,
            company_id: this.company_id,
          })
          .then((res) => {
            this.$bvModal.hide(`modal-edit-${id}`);
            this.getData();
            setTimeout(() => {
              Swal.fire({
                icon: "success",
                text: `${this.$t("general.Editsuccessfully")}`,
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
     *   show Modal (edit)
     */
    async resetModalEdit(id) {
      await this.getParent();
      let editGenDocType = this.genArchDocType.find((e) => id == e.id);
      this.edit.name = editGenDocType.name;
      this.edit.name_e = editGenDocType.name_e;
      this.edit.parent_id = editGenDocType.parent_id
        ? editGenDocType.parent_id.id
        : "";
      this.edit.is_valid = editGenDocType.is_valid;
      this.errors = {};
      this.arch_doc_type_id = editGenDocType.id;
      this.doc_type_field = editGenDocType.doc_type_field;
      this.getDocumentTypeStatuses();
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.errors = {};
      this.edit = {
        name: "",
        name_e: "",
        is_valid: 1,
        parent_id: "",
      };
      this.arch_doc_type_id = null;
    },

    /**
     *  start  dynamicSortString
     */
    sortString(value) {
      return dynamicSortString(value);
    },
    checkRow(id) {
      if (!this.checkAll.includes(id)) {
        this.checkAll.push(id);
      } else {
        let index = this.checkAll.indexOf(id);
        this.checkAll.splice(index, 1);
      }
    },
    /**
     *   Export Excel
     */
    ExportExcel(type, fn, dl) {
      this.enabled3 = false;
      setTimeout(() => {
        let elt = this.$refs.exportable_table;
        let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
        if (dl) {
          XLSX.write(wb, { bookType: type, bookSST: true, type: "base64" });
        } else {
          XLSX.writeFile(
            wb,
            fn ||
              ("Document Type" + "." || "SheetJSTableExport.") +
                (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
  },
};
</script>

<template>
  <Layout>
    <ArchStatus
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getDocumentStatuses"
    />
    <PageHeader />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("general.DocumentTable") }}</h4>
              <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                <!-- Start search button -->
                <div class="d-inline-block" style="width: 22.2%">
                  <!-- Basic dropdown -->
                  <b-dropdown
                    variant="primary"
                    :text="$t('general.searchSetting')"
                    ref="dropdown"
                    class="btn-block setting-search"
                  >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="name"
                      class="mb-1"
                    >
                      {{ $t("general.Name") }}
                    </b-form-checkbox>
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="name_e"
                      class="mb-1"
                    >
                      {{ $t("general.Name_en") }}
                    </b-form-checkbox>
                  </b-dropdown>
                  <!-- Basic dropdown -->
                </div>
                <!-- End search button -->
                <!-- Start Search TB -->
                <div
                  class="d-inline-block position-relative"
                  style="width: 77%"
                >
                  <span
                    :class="[
                      'search-custom position-absolute',
                      $i18n.locale == 'ar' ? 'search-custom-ar' : '',
                    ]"
                  >
                    <i class="fe-search"></i>
                  </span>
                  <input
                    class="form-control"
                    style="display: block; width: 93%; padding-top: 3px"
                    type="text"
                    v-model.trim="search"
                    :placeholder="`${$t('general.Search')}...`"
                  />
                </div>
                <!-- End Search TB -->
              </div>
            </div>

            <div
              class="row justify-content-between align-items-center mb-2 px-1"
            >
              <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                <!-- start create modal  -->
                <b-button
                  v-b-modal.create
                  variant="primary"
                  class="btn-sm mx-1 font-weight-bold"
                >
                  {{ $t("general.Create") }}
                  <i class="fas fa-plus"></i>
                </b-button>
                <!-- end create modal  -->
                <div class="d-inline-flex">
                  <button
                    @click="ExportExcel('xlsx')"
                    class="custom-btn-dowonload"
                  >
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button v-print="'#printType'" class="custom-btn-dowonload">
                    <i class="fe-printer"></i>
                  </button>
                  <!-- Start one edit -->
                  <button
                    class="custom-btn-dowonload"
                    @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                    v-if="checkAll.length == 1"
                  >
                    <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- End one edit -->
                  <!-- start mult delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length > 1"
                    @click.prevent="bulkDeleteGenArchDocType(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
                    @click.prevent="deleteGenArchDocType(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!--  end one delete  -->
                </div>
              </div>
              <div
                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end"
              >
                <div>
                  <!-- Start filter -->
                  <b-button class="mx-1 custom-btn-background">
                    {{ $t("general.filter") }}
                    <i class="fas fa-filter"></i>
                  </b-button>
                  <!-- End filter -->
                  <!-- Start group -->
                  <b-button class="mx-1 custom-btn-background">
                    {{ $t("general.group") }}
                    <i class="fe-menu"></i>
                  </b-button>
                  <!-- End group -->
                  <!-- Start setting dropdown -->
                  <b-dropdown
                    variant="primary"
                    :html="`${$t(
                      'general.setting'
                    )} <i class='fe-settings'></i>`"
                    ref="dropdown"
                    class="dropdown-custom-ali"
                  >
                    <b-form-checkbox v-model="setting.name" class="mb-1"
                      >{{ $t("general.Name") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.name_e" class="mb-1">
                      {{ $t("general.Name_en") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.parent_id" class="mb-1">
                      {{ $t("general.parentId") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.is_valid" class="mb-1">
                      {{ $t("general.isValid") }}
                    </b-form-checkbox>
                    <div class="d-flex justify-content-end">
                      <a
                        href="javascript:void(0)"
                        class="btn btn-primary btn-sm"
                        >{{ $t("general.Apply") }}</a
                      >
                    </div>
                  </b-dropdown>
                  <!-- End setting dropdown -->
                  <!-- start Pagination -->
                  <div
                    class="d-inline-flex align-items-center pagination-custom"
                  >
                    <div class="d-inline-block" style="font-size: 15px">
                      {{ genArchDocTypePagination.from }}-{{
                        genArchDocTypePagination.to
                      }}
                      /
                      {{ genArchDocTypePagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            genArchDocTypePagination.current_page == 1
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(genArchDocTypePagination.current_page - 1)
                        "
                      >
                        <span>&lt;</span>
                      </a>
                      <input
                        type="text"
                        @keyup.enter="getDataCurrentPage()"
                        v-model="current_page"
                        class="pagination-current-page"
                      />
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            genArchDocTypePagination.last_page ==
                            genArchDocTypePagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(genArchDocTypePagination.current_page + 1)
                        "
                      >
                        <span>&gt;</span>
                      </a>
                    </div>
                  </div>
                  <!-- end Pagination -->
                </div>
              </div>
            </div>

            <!--  create   -->
            <b-modal
              id="create"
              :title="$t('GenArchDocType.AddGenArchDocType')"
              title-class="font-18"
              body-class="paddingUnset"
              dialog-class="modal-full-width"
              :hide-footer="true"
              size="lg"
              @show="resetModal"
              @hidden="resetModalHidden"
            >
              <form>
                <div class="card">
                  <div class="card-body">
                    <div class="mb-3 d-flex justify-content-end">
                      <!-- Start Add New Record Button -->
                      <b-button
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
                        <b-button
                          variant="success"
                          class="mx-1"
                          disabled
                          v-else
                        >
                          <b-spinner small></b-spinner>
                          <span class="sr-only"
                            >{{ $t("login.Loading") }}...</span
                          >
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
                    <b-tabs nav-class="nav-tabs nav-bordered">
                      <b-tab :title="$t('general.DocumentType')" active>
                        <div class="row">
                          <div class="col-md-6 direction" dir="rtl">
                            <div class="form-group">
                              <label for="field-1" class="control-label">
                                {{ $t("general.Name") }}
                                <span class="text-danger">*</span>
                              </label>
                              <input
                                type="text"
                                class="form-control"
                                v-model="$v.create.name.$model"
                                :class="{
                                  'is-invalid':
                                    $v.create.name.$error || errors.name,
                                  'is-valid':
                                    !$v.create.name.$invalid && !errors.name,
                                }"
                                @keyup="arabicValue(create.name)"
                                id="field-1"
                              />
                              <div
                                v-if="!$v.create.name.minLength"
                                class="invalid-feedback"
                              >
                                {{ $t("general.Itmustbeatleast") }}
                                {{ $v.create.name.$params.minLength.min }}
                                {{ $t("general.letters") }}
                              </div>
                              <div
                                v-if="!$v.create.name.maxLength"
                                class="invalid-feedback"
                              >
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
                          <div class="col-md-6 direction-ltr" dir="ltr">
                            <div class="form-group">
                              <label for="field-2" class="control-label">
                                {{ $t("general.Name_en") }}
                                <span class="text-danger">*</span>
                              </label>
                              <input
                                @keyup="englishValue(create.name_e)"
                                type="text"
                                class="form-control"
                                v-model="$v.create.name_e.$model"
                                :class="{
                                  'is-invalid':
                                    $v.create.name_e.$error || errors.name_e,
                                  'is-valid':
                                    !$v.create.name_e.$invalid &&
                                    !errors.name_e,
                                }"
                                id="field-2"
                              />
                              <div
                                v-if="!$v.create.name_e.minLength"
                                class="invalid-feedback"
                              >
                                {{ $t("general.Itmustbeatleast") }}
                                {{ $v.create.name_e.$params.minLength.min }}
                                {{ $t("general.letters") }}
                              </div>
                              <div
                                v-if="!$v.create.name_e.maxLength"
                                class="invalid-feedback"
                              >
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
                          <div class="col-md-6">
                            <div class="form-group">
                              <label>
                                {{ $t("general.parentId") }}
                                <span class="text-danger">*</span>
                              </label>

                              <multiselect
                                v-model="create.parent_id"
                                :options="parent.map((type) => type.id)"
                                :custom-label="
                                  (opt) =>
                                    $i18n.locale
                                      ? parent.find((x) => x.id == opt).name
                                      : parent.find((x) => x.id == opt).name_e
                                "
                              >
                              </multiselect>

                              <template v-if="errors.parent_id">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.parent_id"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label class="mr-2">
                                {{ $t("general.isValid") }}
                                <span class="text-danger">*</span>
                              </label>
                              <b-form-group
                                :class="{
                                  'is-invalid':
                                    $v.create.is_valid.$error ||
                                    errors.is_valid,
                                  'is-valid':
                                    !$v.create.is_valid.$invalid &&
                                    !errors.is_valid,
                                }"
                              >
                                <b-form-radio
                                  class="d-inline-block"
                                  v-model="$v.create.is_valid.$model"
                                  name="some-radios"
                                  value="1"
                                >
                                  {{ $t("general.isReferenceYes") }}
                                </b-form-radio>
                                <b-form-radio
                                  class="d-inline-block m-1"
                                  v-model="$v.create.is_valid.$model"
                                  name="some-radios"
                                  value="0"
                                  >{{ $t("general.isReferenceNo") }}
                                </b-form-radio>
                              </b-form-group>
                              <template v-if="errors.is_valid">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.is_valid"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                        </div>
                      </b-tab>
                      <b-tab
                        :disabled="!arch_doc_type_id"
                        :title="$t('general.DocumentDepartment')"
                      >
                        <DocumentDepartment
                          :arch_doc_type_id="arch_doc_type_id"
                          :document_data="create"
                          :companyKeys="companyKeys"
                          :defaultsKeys="defaultsKeys"
                        />
                      </b-tab>
                      <b-tab
                        :disabled="!arch_doc_type_id"
                        :title="$t('menuitems.ArchDocTypeField.text')"
                      >
                        <!-- <DocumentTypeField
                          :arch_doc_type_id="arch_doc_type_id"
                          :document_data="create"
                          :companyKeys="companyKeys"
                          :defaultsKeys="defaultsKeys"
                        /> -->
                        <DocumentTypeFieldEdit
                          :arch_doc_type_id="arch_doc_type_id"
                          :doc_type_field="doc_type_field"
                          @update-doc-type-field="getData"
                          :document_data="edit"
                          :companyKeys="companyKeys"
                          :defaultsKeys="defaultsKeys"
                        />
                      </b-tab>

                      <b-tab
                        :disabled="!arch_doc_type_id"
                        :title="$t('general.DocumentStatus')"
                      >
                        <!--                                                <div class="col-md-12 text-center">-->
                        <!--                                                    <h3>{{$t('general.DocumentName')}} : {{ $i18n.locale == "ar" ? edit.name : edit.name_e }}</h3>-->
                        <!--                                                </div>-->
                        <div class="col-md-6 mb-4 p-0 position-relative">
                          <div class="form-group">
                            <label class="my-1 mr-2">{{
                              $t("general.DocumentStatus")
                            }}</label>
                            <multiselect
                              v-model="status_id"
                              @select="addDocumentStatus"
                              :options="documentStatuses.map((type) => type.id)"
                              :custom-label="
                                (opt) =>
                                  $i18n.locale
                                    ? documentStatuses.find((x) => x.id == opt)
                                        .name
                                    : documentStatuses.find((x) => x.id == opt)
                                        .name_e
                              "
                            >
                            </multiselect>
                          </div>
                        </div>
                        <!-- start .table-responsive-->
                        <div
                          class="table-responsive mb-3 custom-table-theme position-relative"
                        >
                          <!--       start loader       -->
                          <loader size="large" v-if="isLoader" />
                          <!--       end loader       -->

                          <table
                            class="table table-borderless table-hover table-centered m-0"
                          >
                            <thead>
                              <tr>
                                <th>
                                  <div class="d-flex justify-content-center">
                                    <span>{{ $t("general.Name") }}</span>
                                  </div>
                                </th>
                                <th>
                                  <div class="d-flex justify-content-center">
                                    <span>{{ $t("general.Name_en") }}</span>
                                  </div>
                                </th>
                                <th>{{ $t("general.Action") }}</th>
                              </tr>
                            </thead>
                            <tbody v-if="documentTypeStatuses.length > 0">
                              <tr
                                v-for="(data, index) in documentTypeStatuses"
                                :key="data.id"
                                class="body-tr-custom"
                              >
                                <td>
                                  {{ data.name }}
                                </td>
                                <td>
                                  {{ data.name_e }}
                                </td>

                                <td>
                                  <div class="btn-group">
                                    <button
                                      type="button"
                                      class="btn btn-sm dropdown-toggle dropdown-coustom"
                                      data-toggle="dropdown"
                                      aria-expanded="false"
                                    >
                                      {{ $t("general.commands") }}
                                      <i class="fas fa-angle-down"></i>
                                    </button>
                                    <div
                                      class="dropdown-menu dropdown-menu-custom"
                                    >
                                      <a
                                        class="dropdown-item text-black"
                                        href="#"
                                        @click.prevent="
                                          deleteDocumentStatus(data.id)
                                        "
                                      >
                                        <div
                                          class="d-flex justify-content-between align-items-center text-black"
                                        >
                                          <span>{{
                                            $t("general.delete")
                                          }}</span>
                                          <i
                                            class="fas fa-times text-danger"
                                          ></i>
                                        </div>
                                      </a>
                                    </div>
                                  </div>
                                </td>
                              </tr>
                            </tbody>
                            <tbody v-else>
                              <tr>
                                <th class="text-center" colspan="15">
                                  {{ $t("general.notDataFound") }}
                                </th>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <!-- end .table-responsive-->
                      </b-tab>
                    </b-tabs>
                  </div>
                </div>
              </form>
            </b-modal>
            <!--  /create   -->

            <!-- start .table-responsive-->
            <div
              class="table-responsive mb-3 custom-table-theme position-relative"
            >
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

              <table
                class="table table-borderless table-hover table-centered m-0"
                ref="exportable_table"
                id="printType"
              >
                <thead>
                  <tr>
                    <th
                      v-if="enabled3"
                      class="do-not-print"
                      scope="col"
                      style="width: 0"
                    >
                      <div class="form-check custom-control">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          v-model="isCheckAll"
                          style="width: 17px; height: 17px"
                        />
                      </div>
                    </th>
                    <th v-if="setting.name">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.Name") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="genArchDocType.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="genArchDocType.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name_e">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.Name_en") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="genArchDocType.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="genArchDocType.sort(sortString('-name_e'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.is_valid">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.isValid") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="genArchDocType.sort(sortString('is_valid'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              genArchDocType.sort(sortString('-is_valid'))
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.parent_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.parentId") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="
                              genArchDocType.sort(sortString('parent_id'))
                            "
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              genArchDocType.sort(sortString('-parent_id'))
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="enabled3" class="do-not-print">
                      {{ $t("general.Action") }}
                    </th>
                    <th v-if="enabled3" class="do-not-print">
                      <i class="fas fa-ellipsis-v"></i>
                    </th>
                  </tr>
                </thead>
                <tbody v-if="genArchDocType.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in genArchDocType"
                    :key="data.id"
                    class="body-tr-custom"
                  >
                    <td v-if="enabled3" class="do-not-print">
                      <div
                        class="form-check custom-control"
                        style="min-height: 1.9em"
                      >
                        <input
                          style="width: 17px; height: 17px"
                          class="form-check-input"
                          type="checkbox"
                          :value="data.id"
                          v-model="checkAll"
                        />
                      </div>
                    </td>
                    <td v-if="setting.name">
                      <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                    </td>
                    <td v-if="setting.name_e">
                      <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                    </td>
                    <td v-if="setting.is_valid">
                      <span
                        :class="[
                          data.is_valid == 1 ? 'text-success' : 'text-danger',
                          'badge',
                        ]"
                      >
                        {{
                          data.is_valid == 1
                            ? `${$t("general.Yes")}`
                            : `${$t("general.No")}`
                        }}
                      </span>
                    </td>
                    <td v-if="setting.parent_id">
                      <h5 class="m-0 font-weight-normal">
                        {{
                          data.parent_id
                            ? $i18n.locale == "ar"
                              ? data.parent_id.name
                              : data.parent_id.name_e
                            : ""
                        }}
                      </h5>
                    </td>
                    <td v-if="enabled3" class="do-not-print">
                      <div class="btn-group">
                        <button
                          type="button"
                          class="btn btn-sm dropdown-toggle dropdown-coustom"
                          data-toggle="dropdown"
                          aria-expanded="false"
                        >
                          {{ $t("general.commands") }}
                          <i class="fas fa-angle-down"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-custom">
                          <a
                            class="dropdown-item"
                            href="#"
                            @click="$bvModal.show(`modal-edit-${data.id}`)"
                          >
                            <div
                              class="d-flex justify-content-between align-items-center text-black"
                            >
                              <span>{{ $t("general.edit") }}</span>
                              <i
                                class="mdi mdi-square-edit-outline text-info"
                              ></i>
                            </div>
                          </a>
                          <a
                            class="dropdown-item text-black"
                            href="#"
                            @click.prevent="deleteGenArchDocType(data.id)"
                          >
                            <div
                              class="d-flex justify-content-between align-items-center text-black"
                            >
                              <span>{{ $t("general.delete") }}</span>
                              <i class="fas fa-times text-danger"></i>
                            </div>
                          </a>
                        </div>
                      </div>

                      <!--  edit   -->
                      <b-modal
                        :id="`modal-edit-${data.id}`"
                        :title="
                          $t('GenArchDocType.EditGenArchDocType') +
                          ' : ' +
                          ($i18n.locale == 'ar' ? edit.name : edit.name_e)
                        "
                        title-class="font-18"
                        body-class="paddingUnset"
                        dialog-class="modal-full-width"
                        size="lg"
                        :ref="`edit-${data.id}`"
                        :hide-footer="true"
                        @show="resetModalEdit(data.id)"
                        @hidden="resetModalHiddenEdit(data.id)"
                      >
                        <b-tabs nav-class="nav-tabs nav-bordered">
                          <b-tab :title="$t('general.DocumentType')" active>
                            <form>
                              <div class="mb-3 d-flex justify-content-end">
                                <!-- Emulate built in modal footer ok and cancel button actions -->
                                <b-button
                                  variant="success"
                                  type="submit"
                                  class="mx-1"
                                  v-if="!isLoader"
                                  @click.prevent="editSubmit(data.id)"
                                >
                                  {{ $t("general.Edit") }}
                                </b-button>

                                <b-button
                                  variant="success"
                                  class="mx-1"
                                  disabled
                                  v-else
                                >
                                  <b-spinner small></b-spinner>
                                  <span class="sr-only"
                                    >{{ $t("login.Loading") }}...</span
                                  >
                                </b-button>

                                <b-button
                                  variant="danger"
                                  type="button"
                                  @click.prevent="
                                    $bvModal.hide(`modal-edit-${data.id}`)
                                  "
                                >
                                  {{ $t("general.Cancel") }}
                                </b-button>
                              </div>
                              <div class="row">
                                <div class="col-md-6 direction" dir="rtl">
                                  <div class="form-group">
                                    <label
                                      for="field-u-1"
                                      class="control-label"
                                    >
                                      {{ $t("general.Name") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input
                                      @keyup="arabicValue(edit.name)"
                                      type="text"
                                      class="form-control"
                                      v-model="$v.edit.name.$model"
                                      :class="{
                                        'is-invalid':
                                          $v.edit.name.$error || errors.name,
                                        'is-valid':
                                          !$v.edit.name.$invalid &&
                                          !errors.name,
                                      }"
                                      :placeholder="$t('general.Name')"
                                      id="field-u-1"
                                    />
                                    <div
                                      v-if="!$v.edit.name.minLength"
                                      class="invalid-feedback"
                                    >
                                      {{ $t("general.Itmustbeatleast") }}
                                      {{ $v.edit.name.$params.minLength.min }}
                                      {{ $t("general.letters") }}
                                    </div>
                                    <div
                                      v-if="!$v.edit.name.maxLength"
                                      class="invalid-feedback"
                                    >
                                      {{ $t("general.Itmustbeatmost") }}
                                      {{ $v.edit.name.$params.maxLength.max }}
                                      {{ $t("general.letters") }}
                                    </div>
                                    <template v-if="errors.name">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.name"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-6 direction-ltr" dir="ltr">
                                  <div class="form-group">
                                    <label
                                      for="field-u-2"
                                      class="control-label"
                                    >
                                      {{ $t("general.Name_en") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <input
                                      @keyup="englishValue(edit.name_e)"
                                      type="text"
                                      class="form-control"
                                      v-model="$v.edit.name_e.$model"
                                      :class="{
                                        'is-invalid':
                                          $v.edit.name_e.$error ||
                                          errors.name_e,
                                        'is-valid':
                                          !$v.edit.name_e.$invalid &&
                                          !errors.name_e,
                                      }"
                                      :placeholder="$t('general.Name_en')"
                                      id="field-u-2"
                                    />
                                    <div
                                      v-if="!$v.edit.name_e.minLength"
                                      class="invalid-feedback"
                                    >
                                      {{ $t("general.Itmustbeatleast") }}
                                      {{ $v.edit.name_e.$params.minLength.min }}
                                      {{ $t("general.letters") }}
                                    </div>
                                    <div
                                      v-if="!$v.edit.name_e.maxLength"
                                      class="invalid-feedback"
                                    >
                                      {{ $t("general.Itmustbeatmost") }}
                                      {{ $v.edit.name_e.$params.maxLength.max }}
                                      {{ $t("general.letters") }}
                                    </div>
                                    <template v-if="errors.name_e">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.name_e"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label>
                                      {{ $t("general.parentId") }}
                                    </label>

                                    <multiselect
                                      v-model="edit.parent_id"
                                      :options="parent.map((type) => type.id)"
                                      :custom-label="
                                        (opt) =>
                                          $i18n.locale
                                            ? parent.find((x) => x.id == opt)
                                                .name
                                            : parent.find((x) => x.id == opt)
                                                .name_e
                                      "
                                    >
                                    </multiselect>

                                    <template v-if="errors.parent_id">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.parent_id"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label class="mr-2">
                                      {{ $t("general.isValid") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <b-form-group
                                      :class="{
                                        'is-invalid':
                                          $v.edit.is_valid.$error ||
                                          errors.is_valid,
                                        'is-valid':
                                          !$v.edit.is_valid.$invalid &&
                                          !errors.is_valid,
                                      }"
                                    >
                                      <b-form-radio
                                        class="d-inline-block"
                                        v-model="$v.edit.is_valid.$model"
                                        name="some-radios"
                                        value="1"
                                      >
                                        {{ $t("general.isReferenceYes") }}
                                      </b-form-radio>
                                      <b-form-radio
                                        class="d-inline-block m-1"
                                        v-model="$v.edit.is_valid.$model"
                                        name="some-radios"
                                        value="0"
                                      >
                                        {{ $t("general.isReferenceNo") }}
                                      </b-form-radio>
                                    </b-form-group>
                                    <template v-if="errors.is_valid">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.is_valid"
                                        :key="index"
                                        >{{ errorMessage }}
                                      </ErrorMessage>
                                    </template>
                                  </div>
                                </div>
                              </div>
                            </form>
                          </b-tab>
                          <b-tab
                            :disabled="!arch_doc_type_id"
                            :title="$t('general.DocumentDepartment')"
                          >
                            <DocumentDepartment
                              :arch_doc_type_id="arch_doc_type_id"
                              :document_data="edit"
                              :companyKeys="companyKeys"
                              :defaultsKeys="defaultsKeys"
                            />
                          </b-tab>
                          <b-tab
                            :disabled="!arch_doc_type_id"
                            :title="$t('menuitems.ArchDocTypeField.text')"
                          >
                            <DocumentTypeFieldEdit
                              :arch_doc_type_id="arch_doc_type_id"
                              :doc_type_field="doc_type_field"
                              @update-doc-type-field="getData"
                              :document_data="edit"
                              :companyKeys="companyKeys"
                              :defaultsKeys="defaultsKeys"
                            />
                          </b-tab>

                          <b-tab
                            :disabled="!arch_doc_type_id"
                            :title="$t('general.DocumentStatus')"
                          >
                            <!--                                                    <div class="col-md-12 text-center">-->
                            <!--                                                        <h3>{{$t('general.DocumentName')}} : {{ $i18n.locale == "ar" ? edit.name : edit.name_e }}</h3>-->
                            <!--                                                    </div>-->
                            <div class="col-md-6 mb-4 p-0 position-relative">
                              <div class="form-group">
                                <label class="my-1 mr-2">{{
                                  $t("general.DocumentStatus")
                                }}</label>
                                <multiselect
                                  v-model="status_id"
                                  @select="addDocumentStatus"
                                  :options="
                                    documentStatuses.map((type) => type.id)
                                  "
                                  :custom-label="
                                    (opt) =>
                                      $i18n.locale
                                        ? documentStatuses.find(
                                            (x) => x.id == opt
                                          ).name
                                        : documentStatuses.find(
                                            (x) => x.id == opt
                                          ).name_e
                                  "
                                >
                                </multiselect>
                              </div>
                            </div>
                            <!-- start .table-responsive-->
                            <div
                              class="table-responsive mb-3 custom-table-theme position-relative"
                            >
                              <!--       start loader       -->
                              <loader size="large" v-if="isLoader" />
                              <!--       end loader       -->

                              <table
                                class="table table-borderless table-hover table-centered m-0"
                              >
                                <thead>
                                  <tr>
                                    <th>
                                      <div
                                        class="d-flex justify-content-center"
                                      >
                                        <span>{{ $t("general.Name") }}</span>
                                      </div>
                                    </th>
                                    <th>
                                      <div
                                        class="d-flex justify-content-center"
                                      >
                                        <span>{{ $t("general.Name_en") }}</span>
                                      </div>
                                    </th>
                                    <th>{{ $t("general.Action") }}</th>
                                  </tr>
                                </thead>
                                <tbody v-if="documentTypeStatuses.length > 0">
                                  <tr
                                    v-for="(
                                      data, index
                                    ) in documentTypeStatuses"
                                    :key="data.id"
                                    class="body-tr-custom"
                                  >
                                    <td>
                                      {{ data.name }}
                                    </td>
                                    <td>
                                      {{ data.name_e }}
                                    </td>
                                    <td>
                                      <div class="btn-group">
                                        <button
                                          type="button"
                                          class="btn btn-sm dropdown-toggle dropdown-coustom"
                                          data-toggle="dropdown"
                                          aria-expanded="false"
                                        >
                                          {{ $t("general.commands") }}
                                          <i class="fas fa-angle-down"></i>
                                        </button>
                                        <div
                                          class="dropdown-menu dropdown-menu-custom"
                                        >
                                          <a
                                            class="dropdown-item text-black"
                                            href="#"
                                            @click.prevent="
                                              deleteDocumentStatus(data.id)
                                            "
                                          >
                                            <div
                                              class="d-flex justify-content-between align-items-center text-black"
                                            >
                                              <span>{{
                                                $t("general.delete")
                                              }}</span>
                                              <i
                                                class="fas fa-times text-danger"
                                              ></i>
                                            </div>
                                          </a>
                                        </div>
                                      </div>
                                    </td>
                                  </tr>
                                </tbody>
                                <tbody v-else>
                                  <tr>
                                    <th class="text-center" colspan="15">
                                      {{ $t("general.notDataFound") }}
                                    </th>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                            <!-- end .table-responsive-->
                          </b-tab>
                        </b-tabs>
                      </b-modal>
                      <!--  /edit   -->
                    </td>
                    <td v-if="enabled3" class="do-not-print">
                      <i class="fe-info" style="font-size: 22px"></i>
                    </td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <th class="text-center" colspan="6">
                      {{ $t("general.notDataFound") }}
                    </th>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- end .table-responsive-->
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<style scoped>
.dropdown-menu-custom-company.dropdown .dropdown-menu {
  padding: 5px 10px !important;
  overflow-y: scroll;
  height: 400px;
}

.modal-dialog .card {
  margin: 0 !important;
}

.modal-body.paddingUnset {
  padding: 0 !important;
}

.modal-dialog .card-body {
  padding: 1.5rem 1.5rem 0 1.5rem !important;
}

.nav-bordered {
  border: unset !important;
}

.nav {
  background-color: #dff0fe;
}

.tab-content {
  padding: 70px 60px 40px;
  min-height: 300px;
  background-color: #f5f5f5;
  position: relative;
}

.nav-tabs .nav-link {
  border: 1px solid #b7b7b7 !important;
  background-color: #d7e5f2;
  border-bottom: 0 !important;
  margin-bottom: 1px;
}

.nav-tabs .nav-link.active,
.nav-tabs .nav-item.show .nav-link {
  color: #000;
  background-color: hsl(0deg 0% 96%);
  border-bottom: 0 !important;
}

@media print {
  .do-not-print {
    display: none;
  }

  .arrow-sort {
    display: none;
  }
}
</style>
