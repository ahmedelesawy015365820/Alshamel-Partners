<script>
import permissionGuard from "../../../../helper/permission";


import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import adminApi from "../../../../api/adminAxios";
import Switches from "vue-switches";
import { required, minLength, maxLength, integer } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../../components/widgets/errorMessage";
import loader from "../../../../components/general/loader";
import { dynamicSortString, dynamicSortNumber } from "../../../../helper/tableSort";
import translation from "../../../../helper/mixin/translation-mixin";
import { formatDateOnly } from "../../../../helper/startDate";
import Multiselect from "vue-multiselect";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "customers",
    meta: [{ name: "description", content: "Custom table" }],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
  },
  data() {
    return {
      per_page: 50,
      search: "",
      debounce: {},
      customTablesPagination: {},
      branches: [],
      tables: [],
      isLoader: false,
      Tooltip: "",
      mouseEnter: "",
      mod: '',
      modules: [],
      invisibleColumns: ["id", "company_id",'is_admin', "deleted_at", "created_at", "updated_at","email_verified_at"
          ,"remember_token"],
      create: {
        table_name: "",
        columns: [],
      },
      edit: {
        table_name: "",
        columns: [],
      },
      company_id: null,
      errors: {},
      isCheckAll: false,
      checkAll: [],
      current_page: 1,
      setting: {
        table_name: true,
      },
      is_disabled: false,
      filterSetting: ["table_name"],
    };
  },
  validations: {
    create: {
      table_name: { required },
    },
    edit: {
      table_name: { required },
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
    // search(after, befour) {
    //   clearTimeout(this.debounce);
    //   this.debounce = setTimeout(() => {
    //     this.getData();
    //   }, 400);
    // },
    /**
     * watch check All table
     */
    isCheckAll(after, befour) {
      if (after) {
        this.branches.forEach((el) => {
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
    this.modules = this.$store.state.auth.customModules;
    this.getData('General');
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Custom Table", "all Custom Table");
    });
  },
  methods: {
    getSearch(module){
        clearTimeout(this.debounce);
        this.debounce = setTimeout(() => {
            this.getData(module);
        }, 400);
    },
    async getTables() {
      this.isLoader = true;
      await adminApi
        .get(`/document-field/tables`)
        .then((res) => {
          let l = res.data.data;
          this.tables = l;
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
    async getColumns(table) {
      this.isLoader = true;

      await adminApi
        .get(`/document-field/columns/${table}`)
        .then((res) => {
          let l = res.data.data;
          this.create.columns = l.map((column) => {
            return {
              column_name: column,
              is_required: 1,
              is_visible: 1,
            };
          });
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
    addNewField() {
      this.create.columns.push({
        column_name: "",
        is_required: 1,
        is_visible: 1,
      });
    },
    removeNewField(index) {
      if (this.create.columns.length > 1) {
        this.create.columns.splice(index, 1);
      }
    },
    formatDate(value) {
      return formatDateOnly(value);
    },
    log(id) {
      if (this.mouseEnter != id) {
        this.Tooltip = "";
        this.mouseEnter = id;
        adminApi
          .get(`/customTable/logs/${id}`)
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
    getData(module) {
        if(this.mod != module){
            this.branches = [];
            this.mod = module;
            this.isLoader = true;
            let filter = "";
            for (let i = 0; i < this.filterSetting.length; ++i) {
                filter += `columns[${i}]=${this.filterSetting[i]}&`;
            }

            adminApi
                .get(
                    `/customTable?module=${module}&company_id=${this.company_id}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.branches = l.data;
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
    getDataCurrentPage(module) {
        this.branches = [];
        this.customTablesPagination = {};
        this.current_page = 0;
      if (
        this.current_page <= this.customTablesPagination.last_page &&
        this.current_page != this.customTablesPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/customTable?module=${module}&search=${this.search}&${filter}&company_id=${this.company_id}`
          )
          .then((res) => {
            let l = res.data;
            this.branches = l.data;
            this.customTablesPagination = l.pagination;
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
    deleteBranch(id, module) {
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
              .post(`/customTable/bulk-delete`, { ids: id })
              .then((res) => {
                this.checkAll = [];
                this.getData(module);
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
                  this.getData(module);
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
              .delete(`/customTable/${id}`)
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
        table_name: "",
        columns: [],
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.$bvModal.hide(`create`);
      this.is_disabled = false;
    },
    /**
     *  hidden Modal (create)
     */
    resetModal() {
      this.getTables();
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
        table_name: "",
        columns: [],
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.is_disabled = false;
    },
    AddSubmit() {
      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .post(`/customTable`, { ...this.create, company_id: this.company_id })
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
    editSubmit() {
      this.$v.edit.$touch();

      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .put(`/customTable/update`, { ...this.edit, company_id: this.company_id })
          .then((res) => {
            this.$bvModal.hide(`modal-edit`);
            this.getData(this.mod);
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
      this.$bvModal.show(`modal-edit`);
      let customTable = this.branches.find((e) => id == e.id);
      this.edit.table_name = customTable.table_name;
      this.edit.columns = customTable.columns;
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit() {
      this.errors = {};
      this.edit = {
        table_name: "",
        columns: [],
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
    moveInput(tag, c, index) {
      document.querySelector(`${tag}[data-${c}='${index}']`).focus();
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
              <h4 class="header-title">{{ $t("general.customTable") }}</h4>
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
                      value="table_name"
                      class="mb-1"
                      >{{ getCompanyKey("custom_table_name") }}</b-form-checkbox
                    >
                  </b-dropdown>
                  <!-- Basic dropdown -->
                </div>

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
                    @input="getSearch(mod)"
                    v-model.trim="search"
                    :placeholder="`${$t('general.Search')}...`"
                  />
                </div>
              </div>
            </div>
            <!-- end search -->

            <div class="row justify-content-between align-items-center mb-2 px-1">
              <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                <!-- start create and printer -->
                <b-button
                  v-b-modal.create
                  variant="primary"
                  class="btn-sm mx-1 font-weight-bold"
                  v-if="0"
                >
                  {{ $t("general.Create") }}
                  <i class="fas fa-plus"></i>
                </b-button>
                <div class="d-inline-flex">
                  <button class="custom-btn-dowonload">
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button class="custom-btn-dowonload">
                    <i class="fe-printer"></i>
                  </button>
                  <button
                    class="custom-btn-dowonload"
                    @click="resetModalEdit(checkAll[0])"
                    v-if="checkAll.length == 1"
                  >
                    <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- start mult delete  -->
                  <!--    v-if="checkAll.length > 1"-->
<!--                  <button-->
<!--                    class="custom-btn-dowonload"-->
<!--                    v-if="0"-->
<!--                    @click.prevent="deleteBranch(checkAll)"-->
<!--                  >-->
<!--                    <i class="fas fa-trash-alt"></i>-->
<!--                  </button>-->
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <!-- v-if="checkAll.length == 1" -->
<!--                  <button-->
<!--                    v-if="0"-->
<!--                    class="custom-btn-dowonload"-->
<!--                    @click.prevent="deleteBranch(checkAll[0])"-->
<!--                  >-->
<!--                    <i class="fas fa-trash-alt"></i>-->
<!--                  </button>-->
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
                      :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                      ref="dropdown"
                      class="dropdown-custom-ali"
                    >
                      <b-form-checkbox v-model="setting.table_name" class="mb-1"
                        >{{ getCompanyKey("custom_table_name") }}
                      </b-form-checkbox>
                      <div class="d-flex justify-content-end">
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm"
                          >Apply</a
                        >
                      </div>
                    </b-dropdown>
                    <!-- Basic dropdown -->
                  </div>
                  <!-- end filter and setting -->
                </div>
              </div>
            </div>

            <!--  create   -->
            <b-modal
              size="lg"
              id="create"
              :title="getCompanyKey('custom_table_create_form')"
              title-class="font-18"
              body-class="p-4 "
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
                    :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
                  >
                    {{ $t("general.AddNewRecord") }}
                  </b-button>
                  <!-- Emulate built in modal footer ok and cancel button actions -->
                  <template v-if="!is_disabled">
                    <b-button
                      variant="success"
                      type="submit"
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
                  <b-button
                    variant="danger"
                    type="button"
                    @click.prevent="$bvModal.hide('create')"
                  >
                    {{ $t("general.Cancel") }}
                  </b-button>
                </div>
                <div class="row mb-4">
                  <div class="col-md-6">
                    <div class="form-group position-relative">
                      <label class="control-label">
                        {{ getCompanyKey("custom_table_name") }}
                        <span class="text-danger">*</span>
                      </label>
                      <multiselect
                        @input="getColumns"
                        v-model="create.table_name"
                        :options="tables"
                      >
                      </multiselect>
                      <div
                        v-if="$v.create.table_name.$error || errors.table_name"
                        class="text-danger"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.table_name">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.table_name"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                </div>
                <template v-for="(item, index) in create.columns">
                  <div
                    v-if="!invisibleColumns.includes(item.column_name)"
                    class="row"
                    :key="index"
                  >
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="control-label">
                          {{ getCompanyKey("custom_column") }}
                        </label>
                        <input
                          readonly
                          type="text"
                          class="form-control arabicInput"
                          data-create="1"
                          @keypress.enter="moveInput('input', 'create', 2)"
                          v-model="create.columns[index].column_name"
                          id="field-1"
                        />
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="mr-2">
                          {{ getCompanyKey("custom_is_visible") }}
                          <span class="text-danger">*</span>
                        </label>
                        <b-form-group>
                          <b-form-radio
                            @change="
                              create.columns[index].is_visible == 0
                                ? (create.columns[index].is_required = 0)
                                : null
                            "
                            class="d-inline-block"
                            v-model="create.columns[index].is_visible"
                            value="1"
                            >{{ $t("general.Yes") }}
                          </b-form-radio>
                          <b-form-radio
                            @change="
                              create.columns[index].is_visible == 0
                                ? (create.columns[index].is_required = 0)
                                : null
                            "
                            class="d-inline-block m-1"
                            v-model="create.columns[index].is_visible"
                            value="0"
                            >{{ $t("general.No") }}
                          </b-form-radio>
                        </b-form-group>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label class="mr-2">
                          {{ getCompanyKey("custom_is_required") }}
                          <span class="text-danger">*</span>
                        </label>
                        <b-form-group>
                          <b-form-radio
                            :disabled="create.columns[index].is_visible == 0"
                            class="d-inline-block"
                            v-model="create.columns[index].is_required"
                            value="1"
                            >{{ $t("general.Yes") }}
                          </b-form-radio>
                          <b-form-radio
                            :disabled="create.columns[index].is_visible == 0"
                            class="d-inline-block m-1"
                            v-model="create.columns[index].is_required"
                            value="0"
                            >{{ $t("general.No") }}
                          </b-form-radio>
                        </b-form-group>
                      </div>
                    </div>
                  </div>
                </template>
              </form>
            </b-modal>
            <!--  /create   -->

            <!--  edit   -->
            <b-modal
                  size="lg"
                  :id="`modal-edit`"
                  :title="getCompanyKey('custom_table_edit_form')"
                  title-class="font-18"
                  body-class="p-4"
                  :hide-footer="true"
                  @hidden="resetModalHiddenEdit"
              >
                  <form>
                      <div class="mb-3 d-flex justify-content-end">
                          <!-- Emulate built in modal footer ok and cancel button actions -->
                          <b-button
                              variant="success"
                              type="submit"
                              class="mx-1"
                              v-if="!isLoader"
                              @click.prevent="editSubmit"
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
                              @click.prevent="$bvModal.hide(`modal-edit`)"
                          >
                              {{ $t("general.Cancel") }}
                          </b-button>
                      </div>
                      <div class="row mb-4">
                          <div class="col-md-6">
                              <div class="form-group position-relative">
                                  <label class="control-label">
                                      {{ getCompanyKey("custom_table_name") }}
                                      <span class="text-danger">*</span>
                                  </label>
                                  <multiselect
                                      @input="getColumns"
                                      v-model="edit.table_name"
                                      :options="tables"
                                  >
                                  </multiselect>
                                  <div
                                      v-if="$v.edit.table_name.$error || errors.table_name"
                                      class="text-danger"
                                  >
                                      {{ $t("general.fieldIsRequired") }}
                                  </div>
                                  <template v-if="errors.table_name">
                                      <ErrorMessage
                                          v-for="(errorMessage, index) in errors.table_name"
                                          :key="index"
                                      >{{ errorMessage }}</ErrorMessage
                                      >
                                  </template>
                              </div>
                          </div>
                      </div>
                      <template v-for="(item, index) in edit.columns">
                          <div
                              v-if="!invisibleColumns.includes(item.column_name)"
                              class="row"
                              :key="index"
                          >
                              <div class="col-md-4">
                                  <div class="form-group">
                                      <label class="control-label">
                                          {{ getCompanyKey("custom_column") }}
                                      </label>
                                      <input
                                          readonly
                                          type="text"
                                          class="form-control arabicInput"
                                          data-create="1"
                                          @keypress.enter="moveInput('input', 'create', 2)"
                                          v-model="edit.columns[index].column_name"
                                      />
                                  </div>
                              </div>
                              <div class="col-md-4">
                                  <div class="form-group">
                                      <label class="mr-2">
                                          {{ getCompanyKey("custom_is_visible") }}
                                          <span class="text-danger">*</span>
                                      </label>
                                      <b-form-group>
                                          <b-form-radio
                                              @change="
                                        edit.columns[index].is_visible == 0
                                          ? (edit.columns[index].is_required = 0)
                                          : null
                                      "
                                              class="d-inline-block"
                                              v-model="edit.columns[index].is_visible"
                                              value="1"
                                          >{{ $t("general.Yes") }}
                                          </b-form-radio>
                                          <b-form-radio
                                              @change="
                                        edit.columns[index].is_visible == 0
                                          ? (edit.columns[index].is_required = 0)
                                          : null
                                      "
                                              class="d-inline-block m-1"
                                              v-model="edit.columns[index].is_visible"
                                              value="0"
                                          >{{ $t("general.No") }}
                                          </b-form-radio>
                                      </b-form-group>
                                  </div>
                              </div>
                              <div class="col-md-4">
                                  <div class="form-group">
                                      <label class="mr-2">
                                          {{ getCompanyKey("custom_is_required") }}
                                          <span class="text-danger">*</span>
                                      </label>
                                      <b-form-group>
                                          <b-form-radio
                                              :disabled="edit.columns[index].is_visible == 0"
                                              class="d-inline-block"
                                              v-model="edit.columns[index].is_required"
                                              value="1"
                                          >{{ $t("general.Yes") }}
                                          </b-form-radio>
                                          <b-form-radio
                                              :disabled="edit.columns[index].is_visible == 0"
                                              class="d-inline-block m-1"
                                              v-model="edit.columns[index].is_required"
                                              value="0"
                                          >{{ $t("general.No") }}
                                          </b-form-radio>
                                      </b-form-group>
                                  </div>
                              </div>
                          </div>
                      </template>
                  </form>
              </b-modal>
            <!--  /edit   -->

            <div class="accordion" role="tablist">
                  <template v-for="(module,index) in modules" :index="index">
                      <b-card no-body class="mb-1">
                          <b-card-header header-tag="header" class="p-1" role="tab">
                              <b-button @click.prevent="getData(module)" block v-b-toggle="`accordion-${index}`" variant="info">{{ module }}</b-button>
                          </b-card-header>
                          <b-collapse :id="`accordion-${index}`" :visible="index == 0" accordion="my-accordion" role="tabpanel">
                              <b-card-body>
                                  <!-- start .table-responsive-->
                                  <div class="table-responsive mb-1 custom-table-theme position-relative">
                                      <!--       start loader       -->
                                      <loader size="large" v-if="isLoader" />
                                      <!--       end loader       -->
                                      <table class="table table-borderless table-hover table-centered m-0">
                                          <thead>
                                          <tr>
                                              <th scope="col" style="width: 0">
                                                  <div class="form-check custom-control">
                                                      <input
                                                          class="form-check-input"
                                                          type="checkbox"
                                                          v-model="isCheckAll"
                                                          style="width: 17px; height: 17px"
                                                      />
                                                  </div>
                                              </th>
                                              <th v-if="setting.table_name">
                                                  <div class="d-flex justify-content-center">
                                                      <span>{{ getCompanyKey("custom_table_name") }}</span>
                                                      <div class="arrow-sort">
                                                          <i
                                                              class="fas fa-arrow-up"
                                                              @click="branches.sort(sortString('name'))"
                                                          ></i>
                                                          <i
                                                              class="fas fa-arrow-down"
                                                              @click="branches.sort(sortString('-name'))"
                                                          ></i>
                                                      </div>
                                                  </div>
                                              </th>
                                              <th>
                                                  {{ $t("general.Action") }}
                                              </th>
                                              <th><i class="fas fa-ellipsis-v"></i></th>
                                          </tr>
                                          </thead>
                                          <tbody v-if="branches.length > 0">
                                          <tr
                                              @click.capture="checkRow(data.id)"
                                              @dblclick.prevent="resetModalEdit(data.id)"
                                              v-for="(data, index) in branches"
                                              :key="data.id"
                                              class="body-tr-custom"
                                          >
                                              <td>
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
                                              <td v-if="setting.table_name">
                                                  <h5 class="m-0 font-weight-normal">{{ data.table_name }}</h5>
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
                                                      <div class="dropdown-menu dropdown-menu-custom">
                                                          <a
                                                              class="dropdown-item"
                                                              href="#"
                                                              @click="resetModalEdit(data.id)"
                                                          >
                                                              <div
                                                                  class="d-flex justify-content-between align-items-center text-black"
                                                              >
                                                                  <span>{{ $t("general.edit") }}</span>
                                                                  <i class="mdi mdi-square-edit-outline text-info"></i>
                                                              </div>
                                                          </a>
                                                          <a
                                                              v-if="0"
                                                              class="dropdown-item text-black"
                                                              href="#"
                                                              @click.prevent="deleteBranch(data.id,module)"
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
                                              </td>
                                              <td>
                                                  <button
                                                      @mouseover="log(data.id)"
                                                      @mousemove="log(data.id)"
                                                      type="button"
                                                      class="btn"
                                                      data-toggle="tooltip"
                                                      :data-placement="$i18n.locale == 'en' ? 'left' : 'right'"
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
                              </b-card-body>
                          </b-collapse>
                      </b-card>
                  </template>
              </div>

          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
<style scoped>
.closeField {
  font-size: 48px !important;
  width: 38px !important;
  padding: 0 0 11px 0 !important;
  height: 36px !important;
  text-align: center;
  line-height: 7px !important;
  background-color: hsl(210deg 6% 86%);
  color: red;
  border: none;
}
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
</style>
