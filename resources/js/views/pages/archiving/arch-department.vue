<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import { required, minLength, maxLength, integer } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import translation from "../../../helper/mixin/translation-mixin";
import DocField from "../../../components/create/arch/doc-field";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import permissionGuard from "../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Archive Department",
    meta: [{ name: "description", content: "Archive Department" }],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    ErrorMessage,
    loader,
    Multiselect,
    DocField,
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Archive Department", "all Store");
    });

  },
  data() {
    return {
      company_id:null,
      per_page: 50,
      search: "", //Search table column
      debounce: {},
      pagination: {},
      archDepartmentAr: [],
      enabled3: true,
      isLoader: false,
      archDocFieldData: [],
      create: {
        name: "",
        name_e: "",
        parent_id: "",
        is_active: "active",
        is_key: 1,
        key_value: "",
      }, //Create form
      edit: {
        name: "",
        name_e: "",
        parent_id: "",
        is_active: "active",
        is_key: 1,
        key_value: "",
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
      is_key: { required },
      key_value: {},
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
      is_key: { required },
      key_value: {},
      parent_id: { integer },
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
  async mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
    await this.getData();
    await this.getArchDocType();
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

    /**
     *  get Data Arch Department
     */
    getData(page = 1) {
      this.isLoader = true;

      let filter = "";
      for (let i = 0; i < this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/arch-department?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.archDepartmentAr = l.data;
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
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/arch-department?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.archDepartmentAr = l.data;
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
     *  get arch doc field data
     */
    async getArchDocType() {
      await adminApi
        .get(`/document-field`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "Add Document Field", name_e: "Add Document Field" });
          this.archDocFieldData = l;
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
     *  delete Arch Department
     */
    deleteArchDepartment(id) {
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
            .delete(`/arch-department/${id}`)
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
    bulkDeleteArchDepartment(id) {
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
            .post(`/arch-department/bulk-delete`, {
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
        name: "",
        name_e: "",
        is_active: "active",
        is_key: 1,
        key_value: "",
        parent_id: "",
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
      await this.getArchDocType();
      this.create = {
        name: "",
        name_e: "",
        is_active: "active",
        is_key: 1,
        key_value: "",
        parent_id: "",
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
        name: "",
        name_e: "",
        is_active: "active",
        is_key: 1,
        key_value: "",
        parent_id: "",
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
      if (this.create.name || this.create.name_e) {
        this.create.name = this.create.name ? this.create.name : this.create.name_e;
        this.create.name_e = this.create.name_e ? this.create.name_e : this.create.name;
      }
    //   if (parseInt(this.create.is_key) != 1) {
    //     this.create.key_value = "";
    //   }
    //   if (parseInt(this.create.is_key) == 1 && !this.create.key_value) {
    //     return;
    //   }
      this.$v.create.$touch();
      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;
        adminApi
          .post(`/arch-department`, {
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
      if (this.edit.name || this.edit.name_e) {
        this.edit.name = this.edit.name ? this.edit.name : this.edit.name_e;
        this.edit.name_e = this.edit.name_e ? this.edit.name_e : this.create.name;
      }
    //   if (parseInt(this.edit.is_key) != 1) {
    //     this.edit.key_value = "";
    //   }
    //   if (parseInt(this.edit.is_key) == 1 && !this.edit.key_value) {
    //     return;
    //   }
      this.$v.edit.$touch();
      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        let { name, name_e, is_active, is_key, key_value, parent_id } = this.edit;
        adminApi
          .put(`/arch-department/${id}`, {
            name,
            name_e,
            is_active,
            is_key,
            key_value,
            parent_id,
            company_id: this.company_id
          })
          .then((res) => {
            this.$bvModal.hide(`modal-edit-department-${id}`);
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
    resetModalEdit(id) {
      let archDepartment = this.archDepartmentAr.find((e) => id == e.id);
      this.edit.name = archDepartment.name;
      this.edit.name_e = archDepartment.name_e;
      this.edit.is_active = archDepartment.is_active;
      this.edit.is_key = archDepartment.is_key ? 1 : 0;
      this.edit.key_value = archDepartment.is_key ? archDepartment.key_value.id : "";
      this.edit.parent_id = archDepartment.parent_id;
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.errors = {};
      this.edit = {
        name: "",
        name_e: "",
        is_active: "active",
        is_key: 1,
        key_value: "",
        parent_id: "",
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
              ("Document Department" + "." || "SheetJSTableExport.") + (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
    showDocFieldModal() {
      if (this.create.key_value == 0) {
        this.$bvModal.show("create-doc-field");
        this.create.key_value = "";
      }
    },

    showDocFieldModalEdit() {
      if (this.edit.key_value == 0) {
        this.$bvModal.show("create-doc-field");
        this.edit.key_value = "";
      }
    },
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <div class="row">
      <DocField
        :companyKeys="companyKeys"
        :defaultsKeys="defaultsKeys"
        @create="getArchDocType"
      />
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("ArchDepartment.ArchDepartmentTable") }}</h4>
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
                    <b-form-checkbox v-model="filterSetting" value="name" class="mb-1">
                      {{ getCompanyKey("archive_name") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="filterSetting" value="name_e" class="mb-1"
                      >{{ getCompanyKey("archive_name_e") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.is_active" class="mb-1">
                      {{ getCompanyKey("archive_status") }}
                    </b-form-checkbox>
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
                  v-b-modal.create-department
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
                  <button v-print="'#printDepartment'" class="custom-btn-dowonload">
                    <i class="fe-printer"></i>
                  </button>
                  <!-- Start one edit -->
                  <button
                    class="custom-btn-dowonload"
                    @click="$bvModal.show(`modal-edit-department-${checkAll[0]}`)"
                    v-if="checkAll.length == 1"
                  >
                    <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- End one edit -->
                  <!-- start mult delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length > 1"
                    @click.prevent="bulkDeleteArchDepartment(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
                    @click.prevent="deleteArchDepartment(checkAll)"
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
                    <b-form-checkbox v-model="setting.parent_id" class="mb-1">
                      {{ $t("general.parentId") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.name" class="mb-1"
                      >{{ getCompanyKey("archive_name") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.name_e" class="mb-1">
                      {{ getCompanyKey("archive_name_e") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.is_active" class="mb-1">
                      {{ getCompanyKey("archive_status") }}
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
              id="create-department"
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
                    @click.prevent="$bvModal.hide(`create-department`)"
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
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.parent_id"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <!-- <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="mr-2">
                                                {{ $t('general.is_key') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <b-form-group
                                                :class="{
                                                    'is-invalid':
                                                      $v.create.is_key.$error || errors.is_key,
                                                    'is-valid':
                                                      !$v.create.is_key.$invalid && !errors.is_key,
                                                  }"
                                            >
                                                <b-form-radio
                                                    class="d-inline-block"
                                                    v-model="$v.create.is_key.$model"
                                                    name="is_key"
                                                    value="1"
                                                >{{ $t("general.Yes") }}
                                                </b-form-radio
                                                >
                                                <b-form-radio
                                                    class="d-inline-block m-1"
                                                    v-model="$v.create.is_key.$model"
                                                    name="is_key"
                                                    value="0"
                                                >{{ $t("general.No") }}
                                                </b-form-radio
                                                >
                                            </b-form-group>
                                            <template v-if="errors.is_key">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.is_key"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-12" v-if="create.is_key == 1">
                                        <div class="form-group">
                                            <label>
                                                {{ getCompanyKey("arch_doc_field") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                @input="showDocFieldModal()"
                                                v-model="$v.create.key_value.$model"
                                                :options="archDocFieldData.map((type) => type.id)"
                                                :custom-label="
                                                  (opt) =>
                                                    $i18n.locale
                                                      ? archDocFieldData.find((x) => x.id == opt).name
                                                      : archDocFieldData.find((x) => x.id == opt).name_e
                                                "
                                            >
                                            </multiselect>
                                            <template v-if="errors.key_value">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.key_value"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div> -->
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

            <!-- start .table-responsive-->
            <div class="table-responsive mb-3 custom-table-theme position-relative">
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

              <table
                class="table table-borderless table-hover table-centered m-0"
                ref="exportable_table"
                id="printDepartment"
              >
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
                    <th v-if="setting.parent_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.parentId") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="archDepartmentAr.sort(sortString('parent_id'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="archDepartmentAr.sort(sortString('-parent_id'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("archive_name") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="archDepartmentAr.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="archDepartmentAr.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name_e">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("archive_name_e") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="archDepartmentAr.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="archDepartmentAr.sort(sortString('-name_e'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.is_active">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("archive_status") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="archDepartmentAr.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="archDepartmentAr.sort(sortString('-name_e'))"
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
                <tbody v-if="archDepartmentAr.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-department-${data.id}`)"
                    v-for="(data, index) in archDepartmentAr"
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
                    <td v-if="setting.parent_id">
                      <h5 class="m-0 font-weight-normal">
                        {{
                          data.parent_id
                            ? $i18n.locale == "ar"
                              ? data.arch_department.name
                              : data.arch_department.name_e
                            : ""
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.name">
                      <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                    </td>
                    <td v-if="setting.name_e">
                      <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                    </td>
                    <td v-if="setting.is_active">
                      <span
                        :class="[
                          data.is_active == 'active' ? 'text-success' : 'text-danger',
                          'badge',
                        ]"
                      >
                        {{
                          data.is_active == "active"
                            ? `${$t("general.Active")}`
                            : `${$t("general.Inactive")}`
                        }}
                      </span>
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
                            @click="$bvModal.show(`modal-edit-department-${data.id}`)"
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
                            @click.prevent="deleteArchDepartment(data.id)"
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
                        :id="`modal-edit-department-${data.id}`"
                        :title="getCompanyKey('ArchiveDepartmentEdit')"
                        title-class="font-18"
                        body-class="p-4"
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
                              @click.prevent="
                                $bvModal.hide(`modal-edit-department-${data.id}`)
                              "
                            >
                              {{ $t("general.Cancel") }}
                            </b-button>
                          </div>
                          <div class="row">
                            <div class="col-md-12">
                              <div class="form-group">
                                <label for="field-u-1" class="control-label">
                                  {{ getCompanyKey("archive_name") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <div dir="rtl">
                                  <input
                                    @keyup="arabicValue(edit.name)"
                                    type="text"
                                    class="form-control"
                                    v-model="$v.edit.name.$model"
                                    :class="{
                                      'is-invalid': $v.edit.name.$error || errors.name,
                                      'is-valid': !$v.edit.name.$invalid && !errors.name,
                                    }"
                                    :placeholder="$t('general.Name')"
                                    id="field-u-1"
                                  />
                                </div>
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
                                    v-for="(errorMessage, index) in errors.name"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-12 direction-ltr" dir="ltr">
                              <div class="form-group">
                                <label for="field-u-2" class="control-label">
                                  {{ getCompanyKey("archive_name_e") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <input
                                  @keyup="englishValue(edit.name_e)"
                                  type="text"
                                  class="form-control"
                                  v-model="$v.edit.name_e.$model"
                                  :class="{
                                    'is-invalid': $v.edit.name_e.$error || errors.name_e,
                                    'is-valid':
                                      !$v.edit.name_e.$invalid && !errors.name_e,
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
                                    v-for="(errorMessage, index) in errors.name_e"
                                    :key="index"
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
                                  v-model="edit.parent_id"
                                  :options="archDepartmentAr.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      $i18n.locale
                                        ? archDepartmentAr.find((x) => x.id == opt).name
                                        : archDepartmentAr.find((x) => x.id == opt)
                                            .parent_id
                                  "
                                >
                                </multiselect>

                                <div
                                  v-if="!$v.edit.parent_id.integer"
                                  class="invalid-feedback"
                                >
                                  {{ $t("general.fieldIsInteger") }}
                                </div>
                                <template v-if="errors.parent_id">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.parent_id"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <!-- <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label class="mr-2">
                                                                {{ $t('general.is_key') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <b-form-group
                                                                :class="{
                                                                    'is-invalid':
                                                                      $v.edit.is_key.$error || errors.is_key,
                                                                    'is-valid':
                                                                      !$v.edit.is_key.$invalid && !errors.is_key,
                                                                  }"
                                                            >
                                                                <b-form-radio
                                                                    class="d-inline-block"
                                                                    v-model="$v.edit.is_key.$model"
                                                                    name="is_key"
                                                                    value="1"
                                                                >{{ $t("general.Yes") }}
                                                                </b-form-radio
                                                                >
                                                                <b-form-radio
                                                                    class="d-inline-block m-1"
                                                                    v-model="$v.edit.is_key.$model"
                                                                    name="is_key"
                                                                    value="0"
                                                                >{{ $t("general.No") }}
                                                                </b-form-radio
                                                                >
                                                            </b-form-group>
                                                            <template v-if="errors.is_key">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.is_key"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12" v-if="edit.is_key == 1">
                                                        <div class="form-group">
                                                            <label>
                                                                {{ getCompanyKey("arch_doc_field") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                @input="showDocFieldModalEdit()"
                                                                v-model="$v.edit.key_value.$model"
                                                                :options="archDocFieldData.map((type) => type.id)"
                                                                :custom-label="
                                                                  (opt) =>
                                                                    $i18n.locale
                                                                      ? archDocFieldData.find((x) => x.id == opt).name
                                                                      : archDocFieldData.find((x) => x.id == opt).name_e
                                                                "
                                                            >
                                                            </multiselect>
                                                            <template v-if="errors.key_value">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.key_value"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div> -->
                            <div class="col-md-12">
                              <div class="form-group">
                                <label class="mr-2">
                                  {{ getCompanyKey("archive_status") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <b-form-group
                                  :class="{
                                    'is-invalid':
                                      $v.edit.is_active.$error || errors.is_active,
                                    'is-valid':
                                      !$v.edit.is_active.$invalid && !errors.is_active,
                                  }"
                                >
                                  <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.edit.is_active.$model"
                                    name="some-radios"
                                    value="active"
                                    >{{ $t("general.Active") }}
                                  </b-form-radio>
                                  <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.edit.is_active.$model"
                                    name="some-radios"
                                    value="inactive"
                                    >{{ $t("general.Inactive") }}
                                  </b-form-radio>
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
