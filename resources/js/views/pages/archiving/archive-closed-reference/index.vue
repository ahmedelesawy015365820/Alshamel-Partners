<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import adminApi from "../../../../api/adminAxios";
import { required, minLength, maxLength, integer } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../../components/widgets/errorMessage";
import loader from "../../../../components/general/loader";
import { dynamicSortString } from "../../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import permissionGuard from "../../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Archive Closed Reference",
    meta: [{ name: "description", content: "Archive Closed Reference" }],
  },
  components: {
    Layout,
    PageHeader,
    ErrorMessage,
    loader,
    Multiselect,
  },
    beforeRouteEnter(to, from, next) {
          next((vm) => {
      return permissionGuard(vm, "Archive Closed Reference", "all Store");
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
      company_id:null,
      per_page: 50,
      search: "", //Search table column
      debounce: {},
      archiveClosedReferencePagination: {},
      archiveClosedReferenceAr: [],
      enabled3: false,
      isLoader: false,
      archDocFields: [], //Use this state to Fetch Archive Doc Fields
      create: {
        arch_docfields_id: "",
        field_value: ""
      }, //Create form
      edit: {
        arch_docfields_id: "",
        field_value: ""
      }, //Edit form
      setting: {
        arch_docfields_id: true,
        field_value: true
      }, //Table columns
      filterSetting: ["arch_docfields_id", "field_value"],
      errors: {}, //Server Side Validation Errors
      isCheckAll: false,
      checkAll: [],
      is_disabled: false,
      current_page: 1,
    };
  },
  validations: {
    create: {
      field_value: { required, minLength: minLength(3), maxLength: maxLength(255) },
      arch_docfields_id: {required,integer},
    },
    edit: {
        field_value: { required, minLength: minLength(3), maxLength: maxLength(255) },
        arch_docfields_id: {required,integer},
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
        this.archiveClosedReferenceAr.forEach((el) => {
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
    /**
     *  get Data Archive Closed Reference
     */
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      for (let i = 0; i > this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/archive-closed-reference?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.archiveClosedReferenceAr = l.data;
          this.archiveClosedReferencePagination = l.pagination;
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
        this.current_page <= this.archiveClosedReferencePagination.last_page &&
        this.current_page != this.archiveClosedReferencePagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i > this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/archive-closed-reference?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.archiveClosedReferenceAr = l.data;
            this.archiveClosedReferencePagination = l.pagination;
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
     *  delete Archive Closed Reference
     */
     deleteArchiveClosedReference(id) {
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
            .delete(`/archive-closed-reference/${id}`)
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
      });
    },
    /**
     *  Bulk delete Archive Closed References
     */
    bulkDeleteArchiveClosedReference(id) {
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
            .post(`/archive-closed-reference/bulk-delete`,{
              ids : id
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
      });
    },
    /**
     *  reset Modal (create)
     */
    resetModalHidden() {
      this.create = {
        arch_docfields_id: "",
        field_value: ""

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
    await this.getArcDocFields();
      this.create = {
        arch_docfields_id: "",
        field_value: ""
    };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  get parent
     */
    async getArcDocFields(){
        await adminApi.get(`/document-field`)
            .then((res) => {
                let l = res.data;
                this.archDocFields = l.data;
            })
            .catch((err) => {
                Swal.fire({
                    icon: 'error',
                    title: `${this.$t('general.Error')}`,
                    text: `${this.$t('general.Thereisanerrorinthesystem')}`,
            });
        });
    },
    /**
     *  create Archive Closed Reference
     */
    resetForm() {
        this.create = {
        arch_docfields_id: "",
        field_value: ""
    };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
    },
    /**
     *  add Archive Closed Reference
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
          .post(`/archive-closed-reference`, {
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
     *  edit Archive Closed Reference
     */
    editSubmit(id) {
      this.$v.edit.$touch();
      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        let { arch_docfields_id, field_value } = this.edit;
        adminApi
          .put(`/archive-closed-reference/${id}`, {
            arch_docfields_id,
            field_value,
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
      await this.getArcDocFields();
      let documentField = this.archiveClosedReferenceAr.find((e) => id == e.id);
      this.edit.arch_docfields_id = documentField.arch_docfields_id;
      this.edit.field_value = documentField.field_value;
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.errors = {};
      this.edit = {
        arch_docfields_id: "",
        field_value: "",

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
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("ArchiveClosedReference.ArchiveClosedReferencesTable") }}</h4>
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
                    <b-form-checkbox v-model="filterSetting" value="arch_docfields_id" class="mb-1">{{
                      $t("general.ArchiveClosedReferenceDocField")
                    }}</b-form-checkbox>
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="field_value"
                      class="mb-1"
                      >{{ $t("general.ArchiveClosedReferenceFieldValue") }}</b-form-checkbox
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
                  <button class="custom-btn-dowonload">
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button class="custom-btn-dowonload">
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
                    @click.prevent="bulkDeleteArchiveClosedReference(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
                    @click.prevent="deleteArchiveClosedReference(checkAll)"
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
                    <b-form-checkbox v-model="setting.arch_docfields_id" class="mb-1"
                      >{{ $t("general.ArchiveClosedReferenceDocField") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.field_value" class="mb-1">
                      {{ $t("general.ArchiveClosedReferenceFieldValue") }}
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
                      {{ archiveClosedReferencePagination.from }}-{{ archiveClosedReferencePagination.to }} /
                      {{ archiveClosedReferencePagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            archiveClosedReferencePagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="getData(archiveClosedReferencePagination.current_page - 1)"
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
                            archiveClosedReferencePagination.last_page ==
                            archiveClosedReferencePagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="getData(archiveClosedReferencePagination.current_page + 1)"
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
              :title="$t('ArchiveClosedReference.AddArchiveClosedReference')"
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
                            {{ $t('ArchiveClosedReference.ArcDocField') }}
                            <span class="text-danger">*</span>
                        </label>

                        <multiselect
                            v-model="create.arch_docfields_id"
                            :options="archDocFields.map(type => type.id)"
                            :custom-label="opt => $i18n.locale ? archDocFields.find(x => x.id == opt).name : archDocFields.find(x => x.id == opt).arch_docfields_id">
                        </multiselect>

                        <div v-if="!$v.create.arch_docfields_id.integer" class="invalid-feedback">{{ $t('general.fieldIsInteger') }}</div>
                        <template v-if="errors.arch_docfields_id">
                            <ErrorMessage v-for="(errorMessage,index) in errors.arch_docfields_id" :key="index">{{ errorMessage }}</ErrorMessage>
                        </template>
                    </div>
                  </div>
                  <div class="col-md-6 direction-ltr" dir="ltr">
                    <div class="form-group">
                      <label for="field-2" class="control-label">
                        {{ $t("ArchiveClosedReference.FieldValue") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input
                        type="text"
                        class="form-control englishInput"
                        v-model="$v.create.field_value.$model"
                        :class="{
                          'is-invalid': $v.create.field_value.$error || errors.field_value,
                          'is-valid': !$v.create.field_value.$invalid && !errors.field_value,
                        }"
                        id="field-2"
                      />
                      <div v-if="!$v.create.field_value.minLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatleast") }}
                        {{ $v.create.field_value.$params.minLength.min }}
                        {{ $t("general.letters") }}
                      </div>
                      <div v-if="!$v.create.field_value.maxLength" class="invalid-feedback">
                        {{ $t("general.Itmustbeatmost") }}
                        {{ $v.create.field_value.$params.maxLength.max }}
                        {{ $t("general.letters") }}
                      </div>
                      <template v-if="errors.field_value">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.field_value"
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
                    <th v-if="setting.arch_docfields_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("ArchiveClosedReference.ArcDocField") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="archiveClosedReferenceAr.sort(sortString('arch_docfields_id'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="archiveClosedReferenceAr.sort(sortString('-arch_docfields_id'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.field_value">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("ArchiveClosedReference.FieldValue") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="archiveClosedReferenceAr.sort(sortString('field_value'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="archiveClosedReferenceAr.sort(sortString('-field_value'))"
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
                <tbody v-if="archiveClosedReferenceAr.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in archiveClosedReferenceAr"
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
                    <td v-if="setting.arch_docfields_id">
                      <h5 class="m-0 font-weight-normal">{{
                      (data.arch_docfields_id) ?
                            ($i18n.locale == 'ar') ?
                                data.arch_docfield.name :
                                data.arch_docfield.name_e :
                        ''
                    }}
                    </h5>
                    </td>
                    <td v-if="setting.field_value">
                      <h5 class="m-0 font-weight-normal">{{ data.field_value }}</h5>
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
                            @click.prevent="deleteArchiveClosedReference(data.id)"
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
                        :title="$t('ArchiveClosedReference.EditArchiveClosedReference')"
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
                                        {{ $t('ArchiveClosedReference.ArcDocField') }}
                                        <span class="text-danger">*</span>
                                    </label>

                                    <multiselect
                                        v-model="edit.arch_docfields_id"
                                        :options="archDocFields.map(type => type.id)"
                                        :custom-label="opt => $i18n.locale ? archDocFields.find(x => x.id == opt).name : archDocFields.find(x => x.id == opt).arch_docfields_id">
                                    </multiselect>

                                    <div v-if="!$v.edit.arch_docfields_id.integer" class="invalid-feedback">{{ $t('general.fieldIsInteger') }}</div>
                                    <template v-if="errors.arch_docfields_id">
                                        <ErrorMessage v-for="(errorMessage,index) in errors.arch_docfields_id" :key="index">{{ errorMessage }}</ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-6 direction-ltr" dir="ltr">
                                <div class="form-group">
                                <label for="field-2" class="control-label">
                                    {{ $t("ArchiveClosedReference.FieldValue") }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control englishInput"
                                    v-model="$v.edit.field_value.$model"
                                    :class="{
                                    'is-invalid': $v.edit.field_value.$error || errors.field_value,
                                    'is-valid': !$v.edit.field_value.$invalid && !errors.field_value,
                                    }"
                                    id="field-2"
                                />
                                <div v-if="!$v.edit.field_value.minLength" class="invalid-feedback">
                                    {{ $t("general.Itmustbeatleast") }}
                                    {{ $v.edit.field_value.$params.minLength.min }}
                                    {{ $t("general.letters") }}
                                </div>
                                <div v-if="!$v.edit.field_value.maxLength" class="invalid-feedback">
                                    {{ $t("general.Itmustbeatmost") }}
                                    {{ $v.edit.field_value.$params.maxLength.max }}
                                    {{ $t("general.letters") }}
                                </div>
                                <template v-if="errors.field_value">
                                    <ErrorMessage
                                    v-for="(errorMessage, index) in errors.field_value"
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
                    <td>
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
