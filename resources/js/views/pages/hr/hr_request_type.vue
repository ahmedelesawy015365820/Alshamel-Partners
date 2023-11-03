<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {
  required,
  minLength,
  maxLength,
  decimal,
  minValue,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import {
  dynamicSortString,
  dynamicSortNumber,
} from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import translation from "../../../helper/mixin/translation-mixin";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import permissionGuard from "../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "HR request type",
    meta: [{ name: "description", content: "HR request type" }],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    Multiselect
  },
  data() {
    return {
      per_page: 50,
      search: "",
      debounce: {},
      enabled3: true,
      requestTypesPagination: {},
      requestTypes: [],
      isLoader: false,
      Tooltip: "",
      mouseEnter: "",
      create: {
        name: "",
        name_e: "",
        start_from: 1,
        end_date: 1,
        amount: 1,
        from_hour: 1,
        to_hour: 1,
        managers: []
      },
      edit: {
        name: "",
        name_e: "",
        start_from: 1,
        end_date: 1,
        amount: 1,
        from_hour: 1,
        to_hour: 1,
        managers: []
      },
      company_id: null,
      employees: [],
      errors: {},
      isCheckAll: false,
      checkAll: [],
      current_page: 1,
      setting: {
        name: true,
        name_e: true,
      },
      is_disabled: false,
      filterSetting: ["name", "name_e"],
      printLoading: true,
      printObj: {
        id: "printUnitStatus",
      },
    };
  },
  validations: {
    create: {
      name: { required, minLength: minLength(2), maxLength: maxLength(255) },
      name_e: { required, minLength: minLength(2), maxLength: maxLength(255) },
      start_from: { required },
      end_date: { required },
      amount: { required },
      from_hour: { required },
      to_hour: { required },
      managers: {}
    },
    edit: {
      name: { required, minLength: minLength(2), maxLength: maxLength(255) },
      name_e: { required, minLength: minLength(2), maxLength: maxLength(255) },
      start_from: { required },
      end_date: { required },
      amount: { required },
      from_hour: { required },
      to_hour: { required },
      managers: {}
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
        this.requestTypes.forEach((el) => {
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
  beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Request Type", "all requestTypes hr");
        });
  },
  methods: {
    isPermission(item) {
        if (this.$store.state.auth.type == "admin") {
            return this.$store.state.auth.is_web == 1;
        }
        if (this.$store.state.auth.type == "user") {
            return this.$store.state.auth.permissions.includes(item);
        }
        return true;
    },
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
      this.edit.name = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
      this.edit.name_e = englishValue(txt);
    },
    formatDate(value) {
      return formatDateOnly(value);
    },
    log(id) {
      if (this.mouseEnter != id) {
        this.Tooltip = "";
        this.mouseEnter = id;
        adminApi
          .get(`/hr/request-types/logs/${id}`)
          .then((res) => {
            let l = res.data.data;
            l.forEach((e) => {
              this.Tooltip += `Created By: ${e.causer_type}; Event: ${
                e.event
              }; Description: ${e.description} ;Created At: ${this.formatDate(
                e.created_at
              )} \n`;
            });
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.Thereisanerrorinthesystem")}`,
            });
          });
      } else {
      }
    },
    /**
     *  start get Data countrie && pagination
     */
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      for (let i = 0; i < this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/hr/request-types?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.requestTypes = l.data;
          this.requestTypesPagination = l.pagination;
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
        this.current_page <= this.requestTypesPagination.last_page &&
        this.current_page != this.requestTypesPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/hr/request-types?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}&company_id=${this.company_id}`
          )
          .then((res) => {
            let l = res.data;
            this.requestTypes = l.data;
            this.requestTypesPagination = l.pagination;
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
              .post(`/hr/request-types/bulk-delete`, { ids: id })
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
              .delete(`/hr/request-types/${id}`)
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
      this.create = {
        name: "",
        name_e: "",
        start_from: 1,
        end_date: 1,
        amount: 1,
        from_hour: 1,
        to_hour: 1,
        managers: []
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.is_disabled = false;
      this.$bvModal.hide(`create`);
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      this.create = {
        name: "",
        name_e: "",
        start_from: 1,
        end_date: 1,
        amount: 1,
        from_hour: 1,
        to_hour: 1,
        managers: []
      };

      await this.getEmployees();

      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create countrie
     */
    resetForm() {
      this.create = {
        name: "",
        name_e: "",
        start_from: 1,
        end_date: 1,
        amount: 1,
        from_hour: 1,
        to_hour: 1,
        managers: []
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.is_disabled = false;
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

        adminApi
          .post(`/hr/request-types`, {...this.create,company_id: this.$store.getters["auth/company_id"],})
          .then((res) => {
            this.is_disabled = true;
            this.getData();
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
     *  edit countrie
     */
    editSubmit(id) {
      if (!this.edit.name) {
        this.edit.name = this.edit.name_e;
      }
      if (!this.edit.name_e) {
        this.edit.name_e = this.edit.name;
      }
      this.$v.edit.$touch();

      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .put(`/hr/request-types/${id}`, this.edit)
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
      let requestTypes = this.requestTypes.find((e) => id == e.id);
      this.edit.name = requestTypes.name;
      this.edit.name_e = requestTypes.name_e;
      this.edit.start_from = requestTypes.start_from;
      this.edit.end_date = requestTypes.end_date;
      this.edit.amount = requestTypes.amount;
      this.edit.from_hour = requestTypes.from_hour;
      this.edit.to_hour = requestTypes.to_hour;
      await this.getEmployees();
      requestTypes.managers.forEach(e => {
          this.edit.managers.push(e.id);
      });
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
        start_from: 1,
        end_date: 1,
        amount: 1,
        from_hour: 1,
        to_hour: 1,
          managers: []
      };
    },
    /*
     *  start  dynamicSortString
     */
    sortString(value) {
      return dynamicSortString(value);
    },
    SortNumber(value) {
      return dynamicSortNumber(value);
    },
    /**
     *  start  ckeckRow
     */
    checkRow(id) {
      if (!this.checkAll.includes(id)) {
        this.checkAll.push(id);
      } else {
        let index = this.checkAll.indexOf(id);
        this.checkAll.splice(index, 1);
      }
    },
    /**
     *  end  ckeckRow
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
              ("Unit-Status" + "." || "SheetJSTableExport.") + (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
      async getEmployees() {
              await adminApi
                  .get(`/employees?manage_others=1`)
                  .then((res) => {
                      this.employees = res.data.data;
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
  <Layout>
    <PageHeader />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">
                {{ $t("general.requestTypesTable") }}
              </h4>
              <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
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
                      >{{ getCompanyKey("request_type_name_ar") }}
                    </b-form-checkbox>
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="name_e"
                      class="mb-1"
                    >
                      {{ getCompanyKey("request_type_name_en") }}
                    </b-form-checkbox>
                  </b-dropdown>
                  <!-- Basic dropdown -->
                </div>

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
              </div>
            </div>
            <!-- end search -->

            <div
              class="row justify-content-between align-items-center mb-2 px-1"
            >
              <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                <!-- start create and printer -->
                <b-button
                  v-if="isPermission('create requestTypes hr')"
                  v-b-modal.create
                  variant="primary"
                  class="btn-sm mx-1 font-weight-bold"
                >
                  {{ $t("general.Create") }}
                  <i class="fas fa-plus"></i>
                </b-button>
                <div class="d-inline-flex">
                  <button
                    @click="ExportExcel('xlsx')"
                    class="custom-btn-dowonload"
                  >
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button
                    v-print="'#printUnitStatus'"
                    class="custom-btn-dowonload"
                  >
                    <i class="fe-printer"></i>
                  </button>
                  <button
                    class="custom-btn-dowonload"
                    @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                    v-if="
                      checkAll.length == 1 &&
                      isPermission('update requestTypes hr')
                    "
                  >
                    <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- start mult delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="
                      checkAll.length > 1 &&
                      isPermission('delete requestTypes hr')
                    "
                    @click.prevent="deleteBranch(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="
                      checkAll.length == 1 &&
                      isPermission('delete requestTypes hr')
                    "
                    @click.prevent="deleteBranch(checkAll[0])"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!--  end one delete  -->
                </div>
                <!-- end create and printer -->
              </div>
              <div
                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end"
              >
                <div class="d-fex">
                  <!-- start filter and setting -->
                  <div class="d-inline-block">
                    <b-button class="mx-1 custom-btn-background">
                      {{ $t("general.filter") }}
                      <i class="fas fa-filter"></i>
                    </b-button>
                    <b-button class="mx-1 custom-btn-background">
                      {{ $t("general.group") }}
                      <i class="fe-menu"></i>
                    </b-button>
                    <!-- Basic dropdown -->
                    <b-dropdown
                      variant="primary"
                      :html="`${$t(
                        'general.setting'
                      )} <i class='fe-settings'></i>`"
                      ref="dropdown"
                      class="dropdown-custom-ali"
                    >
                      <b-form-checkbox v-model="setting.name" class="mb-1"
                        >{{ getCompanyKey("request_type_name_ar") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.name_e" class="mb-1">
                        {{ getCompanyKey("request_type_name_en") }}
                      </b-form-checkbox>
                      <div class="d-flex justify-content-end">
                        <a
                          href="javascript:void(0)"
                          class="btn btn-primary btn-sm"
                          >Apply</a
                        >
                      </div>
                    </b-dropdown>
                    <!-- Basic dropdown -->
                  </div>
                  <!-- end filter and setting -->

                  <!-- start Pagination -->
                  <div
                    class="d-inline-flex align-items-center pagination-custom"
                  >
                    <div class="d-inline-block" style="font-size: 13px">
                      {{ requestTypesPagination.from }}-{{
                        requestTypesPagination.to
                      }}
                      /
                      {{ requestTypesPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            requestTypesPagination.current_page == 1
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(requestTypesPagination.current_page - 1)
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
                            requestTypesPagination.last_page ==
                            requestTypesPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(requestTypesPagination.current_page + 1)
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
              :title="getCompanyKey('request_type_create_form')"
              title-class="font-18"
              body-class="p-4"
              size="lg"
              :hide-footer="true"
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
                    :class="[
                      'font-weight-bold px-2',
                      is_disabled ? 'mx-2' : '',
                    ]"
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

                  <b-button
                    variant="danger"
                    type="button"
                    @click.prevent="resetModalHidden"
                  >
                    {{ $t("general.Cancel") }}
                  </b-button>
                </div>
                <b-tabs nav-class="nav-tabs nav-bordered">
                  <b-tab :title="$t('general.DataEntry')" active>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="field-1" class="control-label">
                            {{ getCompanyKey("request_type_name_ar") }}
                            <span class="text-danger">*</span>
                          </label>
                          <div dir="rtl">
                            <input
                              @keyup="arabicValue(create.name)"
                              type="text"
                              class="form-control"
                              data-create="1"
                              v-model="$v.create.name.$model"
                              :class="{
                                'is-invalid':
                                  $v.create.name.$error || errors.name,
                                'is-valid':
                                  !$v.create.name.$invalid && !errors.name,
                              }"
                              id="field-1"
                            />
                          </div>
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
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="field-2" class="control-label">
                            {{ getCompanyKey("request_type_name_en") }}
                            <span class="text-danger">*</span>
                          </label>
                          <div dir="ltr">
                            <input
                              @keyup="englishValue(create.name_e)"
                              type="text"
                              class="form-control englishInput"
                              data-create="2"
                              v-model="$v.create.name_e.$model"
                              :class="{
                                'is-invalid':
                                  $v.create.name_e.$error || errors.name_e,
                                'is-valid':
                                  !$v.create.name_e.$invalid && !errors.name_e,
                              }"
                              id="field-2"
                            />
                          </div>
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
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                    </div>
                  </b-tab>
                  <b-tab :title="$t('general.options')">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="mr-2">
                            {{ getCompanyKey("request_type_start_from") }}
                            <span class="text-danger">*</span>
                          </label>
                          <b-form-group
                            :class="{
                              'is-invalid':
                                $v.create.start_from.$error ||
                                errors.start_from,
                              'is-valid':
                                !$v.create.start_from.$invalid &&
                                !errors.start_from,
                            }"
                          >
                            <b-form-radio
                              class="d-inline-block"
                              v-model="$v.create.start_from.$model"
                              name="start_from_some-radios"
                              :value="1"
                              >{{ $t("general.Active") }}</b-form-radio
                            >
                            <b-form-radio
                              class="d-inline-block m-1"
                              v-model="$v.create.start_from.$model"
                              name="start_from_some-radios"
                              :value="0"
                              >{{ $t("general.Inactive") }}</b-form-radio
                            >
                          </b-form-group>
                          <template v-if="errors.start_from">
                            <ErrorMessage
                              v-for="(errorMessage, index) in errors.start_from"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="mr-2">
                            {{ getCompanyKey("request_type_end_date") }}
                            <span class="text-danger">*</span>
                          </label>
                          <b-form-group
                            :class="{
                              'is-invalid':
                                $v.create.end_date.$error || errors.end_date,
                              'is-valid':
                                !$v.create.end_date.$invalid &&
                                !errors.end_date,
                            }"
                          >
                            <b-form-radio
                              class="d-inline-block"
                              v-model="$v.create.end_date.$model"
                              name="end_date_some-radios"
                              :value="1"
                              >{{ $t("general.Active") }}</b-form-radio
                            >
                            <b-form-radio
                              class="d-inline-block m-1"
                              v-model="$v.create.end_date.$model"
                              name="end_date_some-radios"
                              :value="0"
                              >{{ $t("general.Inactive") }}</b-form-radio
                            >
                          </b-form-group>
                          <template v-if="errors.end_date">
                            <ErrorMessage
                              v-for="(errorMessage, index) in errors.end_date"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="mr-2">
                            {{ getCompanyKey("request_type_amount") }}
                            <span class="text-danger">*</span>
                          </label>
                          <b-form-group
                            :class="{
                              'is-invalid':
                                $v.create.amount.$error || errors.amount,
                              'is-valid':
                                !$v.create.amount.$invalid && !errors.amount,
                            }"
                          >
                            <b-form-radio
                              class="d-inline-block"
                              v-model="$v.create.amount.$model"
                              name="amount_some-radios"
                              :value="1"
                              >{{ $t("general.Active") }}</b-form-radio
                            >
                            <b-form-radio
                              class="d-inline-block m-1"
                              v-model="$v.create.amount.$model"
                              name="amount_some-radios"
                              :value="0"
                              >{{ $t("general.Inactive") }}</b-form-radio
                            >
                          </b-form-group>
                          <template v-if="errors.amount">
                            <ErrorMessage
                              v-for="(errorMessage, index) in errors.amount"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="mr-2">
                            {{ getCompanyKey("request_type_from_hour") }}
                            <span class="text-danger">*</span>
                          </label>
                          <b-form-group
                            :class="{
                              'is-invalid':
                                $v.create.from_hour.$error || errors.from_hour,
                              'is-valid':
                                !$v.create.from_hour.$invalid &&
                                !errors.from_hour,
                            }"
                          >
                            <b-form-radio
                              class="d-inline-block"
                              v-model="$v.create.from_hour.$model"
                              name="from_hour_some-radios"
                              :value="1"
                              >{{ $t("general.Active") }}</b-form-radio
                            >
                            <b-form-radio
                              class="d-inline-block m-1"
                              v-model="$v.create.from_hour.$model"
                              name="from_hour_some-radios"
                              :value="0"
                              >{{ $t("general.Inactive") }}</b-form-radio
                            >
                          </b-form-group>
                          <template v-if="errors.from_hour">
                            <ErrorMessage
                              v-for="(errorMessage, index) in errors.from_hour"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="mr-2">
                            {{ getCompanyKey("request_type_to_hour") }}
                            <span class="text-danger">*</span>
                          </label>
                          <b-form-group
                            :class="{
                              'is-invalid':
                                $v.create.to_hour.$error || errors.to_hour,
                              'is-valid':
                                !$v.create.to_hour.$invalid && !errors.to_hour,
                            }"
                          >
                            <b-form-radio
                              class="d-inline-block"
                              v-model="$v.create.to_hour.$model"
                              name="to_hour_some-radios"
                              :value="1"
                              >{{ $t("general.Active") }}</b-form-radio
                            >
                            <b-form-radio
                              class="d-inline-block m-1"
                              v-model="$v.create.to_hour.$model"
                              name="to_hour_some-radios"
                              :value="0"
                              >{{ $t("general.Inactive") }}</b-form-radio
                            >
                          </b-form-group>
                          <template v-if="errors.to_hour">
                            <ErrorMessage
                              v-for="(errorMessage, index) in errors.amount"
                              :key="index"
                              >{{ errorMessage }}</ErrorMessage
                            >
                          </template>
                        </div>
                      </div>
                      <div class="col-md-4">
                            <div class="form-group">
                                <label class="mr-2">
                                    {{ getCompanyKey("request_type_managers") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <multiselect
                                    :multiple="true"
                                    v-model="create.managers"
                                    :options="employees.map((type) => type.id)"
                                    :custom-label="
                                    (opt) => $i18n.locale == 'ar'?
                                      employees.find((x) => x.id == opt).name:employees.find((x) => x.id == opt).name_e
                                  "
                                >
                                </multiselect>
                                <template v-if="errors.managers">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.managers"
                                        :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                    >
                                </template>
                            </div>
                        </div>
                    </div>
                  </b-tab>
                </b-tabs>
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
                id="printUnitStatus"
              >
                <thead>
                  <tr>
                    <th
                      scope="col"
                      style="width: 0"
                      v-if="enabled3"
                      class="do-not-print"
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
                        <span>{{ getCompanyKey("request_type_name_ar") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="requestTypes.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="requestTypes.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name_e">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("request_type_name_en") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="requestTypes.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="requestTypes.sort(sortString('-name_e'))"
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
                <tbody v-if="requestTypes.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="
                      isPermission('update requestTypes hr')
                        ? $bvModal.show(`modal-edit-${data.id}`)
                        : false
                    "
                    v-for="(data, index) in requestTypes"
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
                            v-if="isPermission('update requestTypes hr')"
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
                            v-if="isPermission('delete requestTypes hr')"
                            class="dropdown-item text-black"
                            href="#"
                            @click.prevent="deleteBranch(data.id)"
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
                        :title="getCompanyKey('request_type_edit_form')"
                        title-class="font-18"
                        size="lg"
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
                          <b-tabs nav-class="nav-tabs nav-bordered">
                            <b-tab :title="$t('general.DataEntry')" active>
                              <div class="row">
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="field-1" class="control-label">
                                      {{
                                        getCompanyKey("request_type_name_ar")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <div dir="rtl">
                                      <input
                                        @keyup="arabicValue(edit.name)"
                                        type="text"
                                        class="form-control"
                                        data-create="1"
                                        v-model="$v.edit.name.$model"
                                        :class="{
                                          'is-invalid':
                                            $v.edit.name.$error || errors.name,
                                          'is-valid':
                                            !$v.edit.name.$invalid &&
                                            !errors.name,
                                        }"
                                        id="field-1"
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
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.name"
                                        :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                      >
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group">
                                    <label for="field-2" class="control-label">
                                      {{
                                        getCompanyKey("request_type_name_en")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <div dir="ltr">
                                      <input
                                        @keyup="englishValue(edit.name_e)"
                                        type="text"
                                        class="form-control englishInput"
                                        data-create="2"
                                        v-model="$v.edit.name_e.$model"
                                        :class="{
                                          'is-invalid':
                                            $v.edit.name_e.$error ||
                                            errors.name_e,
                                          'is-valid':
                                            !$v.edit.name_e.$invalid &&
                                            !errors.name_e,
                                        }"
                                        id="field-2"
                                      />
                                    </div>
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
                                        >{{ errorMessage }}</ErrorMessage
                                      >
                                    </template>
                                  </div>
                                </div>
                              </div>
                            </b-tab>
                            <b-tab :title="$t('general.options')">
                              <div class="row">
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label class="mr-2">
                                      {{
                                        getCompanyKey("request_type_start_from")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <b-form-group
                                      :class="{
                                        'is-invalid':
                                          $v.edit.start_from.$error ||
                                          errors.start_from,
                                        'is-valid':
                                          !$v.edit.start_from.$invalid &&
                                          !errors.start_from,
                                      }"
                                    >
                                      <b-form-radio
                                        class="d-inline-block"
                                        v-model="$v.edit.start_from.$model"
                                        name="start_from_some-radios"
                                        :value="1"
                                        >{{
                                          $t("general.Active")
                                        }}</b-form-radio
                                      >
                                      <b-form-radio
                                        class="d-inline-block m-1"
                                        v-model="$v.edit.start_from.$model"
                                        name="start_from_some-radios"
                                        :value="0"
                                        >{{
                                          $t("general.Inactive")
                                        }}</b-form-radio
                                      >
                                    </b-form-group>
                                    <template v-if="errors.start_from">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.start_from"
                                        :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                      >
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label class="mr-2">
                                      {{
                                        getCompanyKey("request_type_end_date")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <b-form-group
                                      :class="{
                                        'is-invalid':
                                          $v.edit.end_date.$error ||
                                          errors.end_date,
                                        'is-valid':
                                          !$v.edit.end_date.$invalid &&
                                          !errors.end_date,
                                      }"
                                    >
                                      <b-form-radio
                                        class="d-inline-block"
                                        v-model="$v.edit.end_date.$model"
                                        name="end_date_some-radios"
                                        :value="1"
                                        >{{
                                          $t("general.Active")
                                        }}</b-form-radio
                                      >
                                      <b-form-radio
                                        class="d-inline-block m-1"
                                        v-model="$v.edit.end_date.$model"
                                        name="end_date_some-radios"
                                        :value="0"
                                        >{{
                                          $t("general.Inactive")
                                        }}</b-form-radio
                                      >
                                    </b-form-group>
                                    <template v-if="errors.end_date">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.end_date"
                                        :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                      >
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label class="mr-2">
                                      {{ getCompanyKey("request_type_amount") }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <b-form-group
                                      :class="{
                                        'is-invalid':
                                          $v.edit.amount.$error ||
                                          errors.amount,
                                        'is-valid':
                                          !$v.edit.amount.$invalid &&
                                          !errors.amount,
                                      }"
                                    >
                                      <b-form-radio
                                        class="d-inline-block"
                                        v-model="$v.edit.amount.$model"
                                        name="amount_some-radios"
                                        :value="1"
                                        >{{
                                          $t("general.Active")
                                        }}</b-form-radio
                                      >
                                      <b-form-radio
                                        class="d-inline-block m-1"
                                        v-model="$v.edit.amount.$model"
                                        name="amount_some-radios"
                                        :value="0"
                                        >{{
                                          $t("general.Inactive")
                                        }}</b-form-radio
                                      >
                                    </b-form-group>
                                    <template v-if="errors.amount">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.amount"
                                        :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                      >
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label class="mr-2">
                                      {{
                                        getCompanyKey("request_type_from_hour")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <b-form-group
                                      :class="{
                                        'is-invalid':
                                          $v.edit.from_hour.$error ||
                                          errors.from_hour,
                                        'is-valid':
                                          !$v.edit.from_hour.$invalid &&
                                          !errors.from_hour,
                                      }"
                                    >
                                      <b-form-radio
                                        class="d-inline-block"
                                        v-model="$v.edit.from_hour.$model"
                                        name="from_hour_some-radios"
                                        :value="1"
                                        >{{
                                          $t("general.Active")
                                        }}</b-form-radio
                                      >
                                      <b-form-radio
                                        class="d-inline-block m-1"
                                        v-model="$v.edit.from_hour.$model"
                                        name="from_hour_some-radios"
                                        :value="0"
                                        >{{
                                          $t("general.Inactive")
                                        }}</b-form-radio
                                      >
                                    </b-form-group>
                                    <template v-if="errors.from_hour">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.amount"
                                        :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                      >
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                  <div class="form-group">
                                    <label class="mr-2">
                                      {{
                                        getCompanyKey("request_type_to_hour")
                                      }}
                                      <span class="text-danger">*</span>
                                    </label>
                                    <b-form-group
                                      :class="{
                                        'is-invalid':
                                          $v.edit.to_hour.$error ||
                                          errors.to_hour,
                                        'is-valid':
                                          !$v.edit.to_hour.$invalid &&
                                          !errors.to_hour,
                                      }"
                                    >
                                      <b-form-radio
                                        class="d-inline-block"
                                        v-model="$v.edit.to_hour.$model"
                                        name="to_hour_some-radios"
                                        :value="1"
                                        >{{
                                          $t("general.Active")
                                        }}</b-form-radio
                                      >
                                      <b-form-radio
                                        class="d-inline-block m-1"
                                        v-model="$v.edit.to_hour.$model"
                                        name="to_hour_some-radios"
                                        :value="0"
                                        >{{
                                          $t("general.Inactive")
                                        }}</b-form-radio
                                      >
                                    </b-form-group>
                                    <template v-if="errors.to_hour">
                                      <ErrorMessage
                                        v-for="(
                                          errorMessage, index
                                        ) in errors.amount"
                                        :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                      >
                                    </template>
                                  </div>
                                </div>
                                <div class="col-md-4">
                                      <div class="form-group">
                                          <label class="mr-2">
                                              {{ getCompanyKey("request_type_managers") }}
                                              <span class="text-danger">*</span>
                                          </label>
                                          <multiselect
                                              :multiple="true"
                                              v-model="edit.managers"
                                              :options="employees.map((type) => type.id)"
                                              :custom-label="
                                                (opt) => $i18n.locale == 'ar'?
                                                  employees.find((x) => x.id == opt).name:employees.find((x) => x.id == opt).name_e
                                              "
                                          >
                                          </multiselect>
                                          <template v-if="errors.managers">
                                              <ErrorMessage
                                                  v-for="(errorMessage, index) in errors.managers"
                                                  :key="index"
                                              >{{ errorMessage }}</ErrorMessage
                                              >
                                          </template>
                                      </div>
                                  </div>
                              </div>
                            </b-tab>
                          </b-tabs>
                        </form>
                      </b-modal>
                      <!--  /edit   -->
                    </td>
                    <td v-if="enabled3" class="do-not-print">
                      <button
                        @mouseover="log(data.id)"
                        @mousemove="log(data.id)"
                        type="button"
                        class="btn"
                        data-toggle="tooltip"
                        :data-placement="
                          $i18n.locale == 'en' ? 'left' : 'right'
                        "
                        :title="Tooltip"
                      >
                        <i class="fe-info" style="font-size: 22px"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <th class="text-center" colspan="11">
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
