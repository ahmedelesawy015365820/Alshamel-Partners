<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import { required, minLength, maxLength, integer, url } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import translation from "../../../helper/mixin/translation-mixin";
import ArchStatus from "../../../components/create/arch/arch-status.vue";
import ArchDocumentType from "../../../components/create/arch/arch-document-type.vue";
import permissionGuard from "../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Archive Document",
    meta: [{ name: "description", content: "Archive Document" }],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    ErrorMessage,
    loader,
    Multiselect,
    ArchStatus,
    ArchDocumentType,
  },
  beforeRouteEnter(to, from, next) {
        next((vm) => {
      return permissionGuard(vm, "Archiving Document", "all Store");
    });


    },
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
        company_id: null,
      current_page: 1,
        printLoading: true,
        printObj: {
            id: "printMe",
        }
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
        this.archDepartmentAr.forEach((el) => {
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
     *  get Data Arch Department
     */
    getData(page = 1) {
      this.isLoader = true;
      let _filterSetting = [...this.filterSetting];
      let index = this.filterSetting.indexOf("doc_status");
      if (index > -1) {
        _filterSetting[index] =
          this.$i18n.locale == "ar" ? "docStatus.name" : "docStatus.name_e";
      }
      index = this.filterSetting.indexOf("gen_arch_doc_type");
      if (index > -1) {
        _filterSetting[index] =
          this.$i18n.locale == "ar" ? "genArchDocType.name" : "genArchDocType.name_e";
      }

      let filter = "";
      for (let i = 0; i < _filterSetting.length; ++i) {
        filter += `columns[${i}]=${_filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/arch-document?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.dataAr = l.data;
          this.pagination = l.pagination;
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
    getDataCurrentPage() {
      if (
        this.current_page <= this.pagination.last_page &&
        this.current_page != this.pagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
              let _filterSetting = [...this.filterSetting];
      let index = this.filterSetting.indexOf("doc_status");
      if (index > -1) {
        _filterSetting[index] =
          this.$i18n.locale == "ar" ? "docStatus.name" : "docStatus.name_e";
      }
      index = this.filterSetting.indexOf("gen_arch_doc_type");
      if (index > -1) {
        _filterSetting[index] =
          this.$i18n.locale == "ar" ? "genArchDocType.name" : "genArchDocType.name_e";
      }
        let filter = "";
        for (let i = 0; i < _filterSetting.length; ++i) {
          filter += `columns[${i}]=${_filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/arch-document?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.dataAr = l.data;
            this.pagination = l.pagination;
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
     *  delete Arch Department
     */
    singleDelete(id) {
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
            .delete(`/arch-document/${id}`)
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
     *  Bulk delete Arch Departments
     */
    bulkDelete(id) {
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
            .post(`/arch-document/bulk-delete`, {
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
            company_id: this.company_id
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
     *  edit Arch Department
     */
    editSubmit(id) {
      this.$v.edit.$touch();
      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        let { gen_arch_doc_type, doc_description, doc_status, url_reference } = this.edit;
        adminApi
          .put(`/arch-document/${id}`, {
            gen_arch_doc_type,
            doc_description,
            doc_status,
            url_reference,
            company_id: this.company_id
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
      await this.getArcDocStatus();
      await this.getGenArchDocType();
      let serverData = this.dataAr.find((e) => id == e.id);
      this.edit.gen_arch_doc_type = serverData.gen_arch_doc_type.id
        ? serverData.gen_arch_doc_type.id
        : "";
      this.edit.doc_description = serverData.doc_description;
      this.edit.doc_status = serverData.doc_status.id ? serverData.doc_status.id : "";
      this.edit.url_reference = serverData.url_reference;
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.errors = {};
      this.edit = {
        gen_arch_doc_type: "",
        doc_description: "",
        doc_status: "",
        url_reference: "",
      };
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
        .get(`/gen-arch-doc-type`)
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
      /**
       *   Export Excel
       */
      ExportExcel(type, fn, dl) {
          this.enabled3 = false;
          setTimeout(() => {
              let elt = this.$refs.exportable_table;
              let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
              if (dl) {
                  XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
              } else {
                  XLSX.writeFile(wb, fn || (('Archive Document' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
              }
              this.enabled3 = true;
          }, 100);
      }
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <ArchStatus :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getArcDocStatus" />
    <ArchDocumentType :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getGenArchDocType" />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("general.arch_document_table") }}</h4>
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
                      value="gen_arch_doc_type"
                      class="mb-1"
                      >{{ getCompanyKey("arch_document_type") }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="doc_status"
                      class="mb-1"
                      >{{ getCompanyKey("arch_document_status") }}</b-form-checkbox
                    >
                  </b-dropdown>
                  <!-- Basic dropdown -->
                </div>
                <!-- End search button -->
                <!-- Start Search TB -->
                <div class="d-inline-block position-relative" style="width: 77%">
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

            <div class="row justify-content-between align-items-center mb-2 px-1">
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
                  <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button v-print="'#printMe'" class="custom-btn-dowonload">
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
                    @click.prevent="bulkDelete(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
                    @click.prevent="singleDelete(checkAll)"
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
                    :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                    ref="dropdown"
                    class="dropdown-custom-ali"
                  >
                    <b-form-checkbox v-model="setting.doc_status" class="mb-1">
                      {{ getCompanyKey("arch_document_status") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.url_reference" class="mb-1">
                      {{ getCompanyKey("arch_document_url_reference") }}
                    </b-form-checkbox>
                    <div class="d-flex justify-content-end">
                      <a href="javascript:void(0)" class="btn btn-primary btn-sm">{{
                        $t("general.Apply")
                      }}</a>
                    </div>
                  </b-dropdown>
                  <!-- End setting dropdown -->
                  <!-- start Pagination -->
                  <div class="d-inline-flex align-items-center pagination-custom">
                    <div class="d-inline-block" style="font-size: 15px">
                      {{ pagination.from }}-{{ pagination.to }} /
                      {{ pagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events': pagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="getData(pagination.current_page - 1)"
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
                            pagination.last_page == pagination.current_page ? 'none' : '',
                        }"
                        @click.prevent="getData(pagination.current_page + 1)"
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
                          >{{ errorMessage }}</ErrorMessage
                        >
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

                      <div
                        v-if="!$v.create.gen_arch_doc_type.integer"
                        class="invalid-feedback"
                      >
                        {{ $t("general.fieldIsInteger") }}
                      </div>
                      <template v-if="errors.gen_arch_doc_type">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.gen_arch_doc_type"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
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
                            !$v.create.doc_description.$invalid &&
                            !errors.doc_description,
                        }"
                        id="field-2"
                      />
                      <div
                        v-if="!$v.create.doc_description.minLength"
                        class="invalid-feedback"
                      >
                        {{ $t("general.Itmustbeatleast") }}
                        {{ $v.create.doc_description.$params.minLength.min }}
                        {{ $t("general.letters") }}
                      </div>
                      <div
                        v-if="!$v.create.doc_description.maxLength"
                        class="invalid-feedback"
                      >
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.create.doc_description.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                      </div>
                      <template v-if="errors.doc_description">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.doc_description"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
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
                          'is-invalid':
                            $v.create.url_reference.$error || errors.url_reference,
                          'is-valid':
                            !$v.create.url_reference.$invalid && !errors.url_reference,
                        }"
                        id="field-11"
                      />
                      <div
                        v-if="!$v.create.url_reference.minLength"
                        class="invalid-feedback"
                      >
                        {{ $t("general.Itmustbeatleast") }}
                        {{ $v.create.url_reference.$params.minLength.min }}
                        {{ $t("general.letters") }}
                      </div>
                      <div
                        v-if="!$v.create.url_reference.maxLength"
                        class="invalid-feedback"
                      >
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
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                </div>
              </form>
            </b-modal>
            <!--  /create   -->

            <!-- start .table-responsive-->
            <div class="table-responsive mb-3 custom-table-theme position-relative">
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

              <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                     id="printMe">
                <thead>
                  <tr>
                    <th v-if="enabled3" class="do-not-print" scope="col" style="width: 0">
                      <div class="form-check custom-control">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          v-model="isCheckAll"
                          style="width: 17px; height: 17px"
                        />
                      </div>
                    </th>
                    <th v-if="setting.gen_arch_doc_type">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("arch_document_type") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="dataAr.sort(sortString('gen_arch_doc_type'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="dataAr.sort(sortString('-gen_arch_doc_type'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.doc_status">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("arch_document_status") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="dataAr.sort(sortString('doc_status'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="dataAr.sort(sortString('-doc_status'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.doc_description">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("arch_document_description") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="dataAr.sort(sortString('doc_description'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="dataAr.sort(sortString('-doc_description'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="enabled3" class="do-not-print">
                      {{ $t("general.Action") }}
                    </th>
                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                  </tr>
                </thead>
                <tbody v-if="dataAr.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in dataAr"
                    :key="data.id"
                    class="body-tr-custom"
                  >
                    <td v-if="enabled3" class="do-not-print">
                      <div class="form-check custom-control" style="min-height: 1.9em">
                        <input
                          style="width: 17px; height: 17px"
                          class="form-check-input"
                          type="checkbox"
                          :value="data.id"
                          v-model="checkAll"
                        />
                      </div>
                    </td>
                    <td v-if="setting.gen_arch_doc_type">
                      <h5 class="m-0 font-weight-normal">
                        {{
                          data.gen_arch_doc_type
                            ? $i18n.locale == "ar"
                              ? data.gen_arch_doc_type.name
                              : data.gen_arch_doc_type.name_e
                            : ""
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.doc_status">
                      <h5 class="m-0 font-weight-normal">
                        {{
                          data.doc_status
                            ? $i18n.locale == "ar"
                              ? data.doc_status.name
                              : data.doc_status.name_e
                            : ""
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.doc_description">
                      <h5 class="m-0 font-weight-normal">{{ data.doc_description }}</h5>
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
                              <i class="mdi mdi-square-edit-outline text-info"></i>
                            </div>
                          </a>
                          <a
                            class="dropdown-item text-black"
                            href="#"
                            @click.prevent="singleDelete(data.id)"
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
                        :title="getCompanyKey('arch_document_edit_form')"
                        title-class="font-18"
                        body-class="p-4"
                        size="lg"
                        :ref="`edit-${data.id}`"
                        :hide-footer="true"
                        @show="resetModalEdit(data.id)"
                        @hidden="resetModalHiddenEdit(data.id)"
                      >
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

                            <b-button variant="success" class="mx-1" disabled v-else>
                              <b-spinner small></b-spinner>
                              <span class="sr-only">{{ $t("login.Loading") }}...</span>
                            </b-button>

                            <b-button
                              variant="danger"
                              type="button"
                              @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)"
                            >
                              {{ $t("general.Cancel") }}
                            </b-button>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group position-relative">
                                <label class="control-label">
                                  {{ getCompanyKey("arch_document_status") }}
                                  <span class="text-danger">*</span>
                                </label>

                                <multiselect
                                  @input="showArchStatusModalEdit"
                                  v-model="edit.doc_status"
                                  :options="archDocStatusAr.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      $i18n.locale == 'ar'
                                        ? archDocStatusAr.find((x) => x.id == opt).name
                                        : archDocStatusAr.find((x) => x.id == opt).name_e
                                  "
                                >
                                </multiselect>

                                <div
                                  v-if="!$v.edit.doc_status.integer"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.fieldIsInteger") }}
                                </div>
                                <template v-if="errors.doc_status">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.doc_status"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
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
                                  @input="showArchDocTypeModalEdit"
                                  v-model="edit.gen_arch_doc_type"
                                  :options="genArchDocTypeAr.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      $i18n.locale == 'ar'
                                        ? genArchDocTypeAr.find((x) => x.id == opt).name
                                        : genArchDocTypeAr.find((x) => x.id == opt).name_e
                                  "
                                >
                                </multiselect>

                                <div
                                  v-if="!$v.edit.gen_arch_doc_type.integer"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.fieldIsInteger") }}
                                </div>
                                <template v-if="errors.gen_arch_doc_type">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.gen_arch_doc_type"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
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
                                  class="form-control englishInput"
                                  v-model="$v.edit.doc_description.$model"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.doc_description.$error ||
                                      errors.doc_description,
                                    'is-valid':
                                      !$v.edit.doc_description.$invalid &&
                                      !errors.doc_description,
                                  }"
                                />
                                <div
                                  v-if="!$v.edit.doc_description.minLength"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.Itmustbeatleast") }}
                                  {{ $v.edit.doc_description.$params.minLength.min }}
                                  {{ $t("general.letters") }}
                                </div>
                                <div
                                  v-if="!$v.edit.doc_description.maxLength"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.edit.doc_description.$params.maxLength.max }}
                                  {{ $t("general.letters") }}
                                </div>
                                <template v-if="errors.doc_description">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.doc_description"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
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
                                  v-model.number="$v.edit.url_reference.$model"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.url_reference.$error ||
                                      errors.url_reference,
                                    'is-valid':
                                      !$v.edit.url_reference.$invalid &&
                                      !errors.url_reference,
                                  }"
                                  id="field-11"
                                />
                                <div
                                  v-if="!$v.edit.url_reference.minLength"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.Itmustbeatleast") }}
                                  {{ $v.edit.url_reference.$params.minLength.min }}
                                  {{ $t("general.letters") }}
                                </div>
                                <div
                                  v-if="!$v.edit.url_reference.maxLength"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.Itmustbeatmost") }}
                                  {{ $v.edit.url_reference.$params.maxLength.max }}
                                  {{ $t("general.letters") }}
                                </div>
                                <div
                                  v-if="!$v.edit.url_reference.url"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.Itmustbeyourlink") }}
                                </div>
                                <template v-if="errors.url_reference">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.url_reference"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                          </div>
                        </form>
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
@media print {
    .do-not-print {
        display: none;
    }

    .arrow-sort {
        display: none;
    }
}
</style>
