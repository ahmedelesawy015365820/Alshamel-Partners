<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import { required, requiredIf, minLength, maxLength } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import Multiselect from "vue-multiselect";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import translation from "../../../helper/mixin/translation-mixin";
import propertyTree from "../../../components/create/general/property-tree";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import permissionGuard from "../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Document Field",
    meta: [{ name: "description", content: "Document Field" }],
  },
  mixins: [translation],
  components: {
    Multiselect,
    Layout,
    PageHeader,
    ErrorMessage,
    loader,
    propertyTree,
  },
  beforeRouteEnter(to, from, next) {

    next((vm) => {
      return permissionGuard(vm, "Archiving Document Field", "all Store");
    });


  },
  // updated() {
  //   $(".englishInput").keypress(function (event) {
  //     var ew = event.which;
  //     if (ew == 32) return true;
  //     if (48 <= ew && ew <= 57) return true;
  //     if (65 <= ew && ew <= 90) return true;
  //     if (97 <= ew && ew <= 122) return true;
  //     return false;
  //   });
  //   $(".arabicInput").keypress(function (event) {
  //     var ew = event.which;
  //     if (ew == 32) return true;
  //     if (48 <= ew && ew <= 57) return false;
  //     if (65 <= ew && ew <= 90) return false;
  //     if (97 <= ew && ew <= 122) return false;
  //     return true;
  //   });
  // },
  data() {
    return {
        enabled3: true,
        printLoading: true,
        printObj: {
            id: "printCustom",
        },
      per_page: 50,
      showInput: false,
      search: "", //Search table column
      debounce: {},
      tables: [],
      columns: [],
      documentFieldPagination: {},
      documentFields: [],
      isLoader: false,
      dataTypes: [],
      properties: [],
      create: {
        name: "",
        name_e: "",
        type: "",
        is_reference: 0,
        lookup_table: "",
        lookup_table_column: "",
        tree_property_id: null,
        field_characters: null,
      }, //Create form
      edit: {
        name: "",
        name_e: "",
        type: "",
        is_reference: 0,
        lookup_table: "",
        lookup_table_column: "",
        tree_property_id: null,
        field_characters: null,
      }, //Edit form
      setting: {
        name: true,
        name_e: true,
        type: true,
        is_reference: true,
        lookup_table: true,
        lookup_table_column: true,
        field_characters: true,
      }, //Table columns
      filterSetting: ["name", "name_e"],
      company_id:null,
      errors: {}, //Server Side Validation Errors
      isCheckAll: false,
      checkAll: [],
      is_disabled: false,
      current_page: 1,
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
      type: { required },
      is_reference: { required },
      lookup_table: {},
      lookup_table_column: {},
      tree_property_id: {
        required: requiredIf(function (model) {
          return this.create.type == 10;
        }),
      },
      field_characters: {
        required: requiredIf(function (model) {
          return this.create.type != 7;
        }),
      },
    },
    edit: {
      name: { required, minLength: minLength(3), maxLength: maxLength(255) },
      name_e: {
        required,
        minLength: minLength(3),
        maxLength: maxLength(255),
      },
      type: { required },
      is_reference: { required },
      lookup_table: {},
      lookup_table_column: {},
      tree_property_id: {
        required: requiredIf(function (model) {
          return this.edit.type == 10;
        }),
      },
      field_characters: {
        required: requiredIf(function (model) {
          return this.edit.type != 7;
        }),
      },
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
        this.documentFields.forEach((el) => {
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
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
      this.edit.name = arabicValue(txt);
    },

    englishValue(txt) {
      this.create.name_e = englishValue(txt);
      this.edit.name_e = englishValue(txt);
    },

    async getProperties() {
      await adminApi
        .get(`/tree-properties/root-nodes`)
        .then((res) => {
          let l = res.data;
          l.unshift({ id: 0, name: "اضف خاصية", name_e: "Add property" });
          this.properties = l;
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
     *  get Data document field
     */
    getData(page = 1) {
      this.isLoader = true;

      let filter = "";
      for (let i = 0; i > this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/document-field?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.documentFields = l.data;
          this.documentFieldPagination = l.pagination;
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
    getDataTypes() {
      this.isLoader = true;
      adminApi
        .get(`/data-types`)
        .then((res) => {
          this.dataTypes = res.data.data;
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
        this.current_page <= this.documentFieldPagination.last_page &&
        this.current_page != this.documentFieldPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i > this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }
        adminApi
          .get(
            `/document-field?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.documentFields = l.data;
            this.documentFieldPagination = l.pagination;
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
    deleteDocumentField(id, index) {
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
              .post(`/document-field/bulk-delete`, { ids: id })
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
              .delete(`/document-field/${id}`)
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

    resetModalHidden() {
      this.create = {
        name: "",
        name_e: "",
        type: "",
        is_reference: 0,
        lookup_table: "",
        lookup_table_column: {},
        field_characters: null,
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
      await this.getProperties();
      await this.getTables();
      this.getDataTypes();
      this.create = {
        name: "",
        name_e: "",
        type: "",
        is_reference: 0,
        lookup_table: "",
        lookup_table_column: "",
        tree_property_id: null,
        field_characters: null,
      };
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
      await this.getTables();
      this.create = {
        name: "",
        name_e: "",
        type: "",
        is_reference: 0,
        lookup_table: "",
        lookup_table_column: "",
        tree_property_id: null,
        field_characters: null,
      };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
    },
    /**
     *  add document field
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
        this.is_disabled = false;
        adminApi
          .post(`/document-field`, {
            ...this.create,
            data_type_id: this.create.type,
            type: undefined,
            is_reference: this.create.is_reference == "1" ? 1 : 0,
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
     *  edit document field
     */
    editSubmit(id) {
      if (this.edit.name || this.edit.name_e) {
        this.edit.name = this.edit.name ? this.edit.name : this.edit.name_e;
        this.edit.name_e = this.edit.name_e ? this.edit.name_e : this.create.name;
      }
      this.$v.edit.$touch();
      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        let {
          name,
          name_e,
          lookup_table,
          is_reference,
          type,
          lookup_table_column,
          tree_property_id,
          field_characters,
        } = this.edit;
        adminApi
          .put(`/document-field/${id}`, {
            name,
            name_e,
            is_reference: is_reference == "1" ? 1 : 0,
            data_type_id: type,
            lookup_table_column,
            lookup_table,
            tree_property_id,
            field_characters,
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
      let documentField = this.documentFields.find((e) => id == e.id);
      await this.getTables();
      await this.getProperties();
      if (documentField.lookup_table) {
        await this.getColumns(documentField.lookup_table);
      }
      this.getDataTypes();
      this.edit.name = documentField.name;
      this.edit.name_e = documentField.name_e;
      this.edit.type = documentField.data_type.id;
      this.edit.is_reference = documentField.is_reference;
      this.edit.lookup_table = documentField.lookup_table;
      this.edit.tree_property_id = documentField.tree_property_id;
      this.edit.field_characters = documentField.field_characters;
      if (this.edit.type == "lookup(Table)") {
        this.showInput = true;
      }
      this.edit.lookup_table_column = documentField.lookup_table_column;
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
        type: "",
        is_reference: 0,
        lookup_table: "",
        lookup_table_column: "",
        tree_property_id: null,
        field_characters: null,
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
          this.columns = l;
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
    lookUpChange() {
      if (this.create.type == 11 || this.edit.type == 11) {
        this.showInput = true;
      } else {
        this.showInput = false;
      }
    },
      showTreeCreate(){
          if (this.create.tree_property_id == 0) {
              this.$bvModal.show("property-create");
              this.create.tree_property_id = null;
          }
      },
      showTreeEdit(){
          if (this.edit.tree_property_id == 0) {
              this.$bvModal.show("property-create");
              this.edit.tree_property_id = null;
          }
      },
      ExportExcel(type, fn, dl) {
          this.enabled3 = false;
          setTimeout(() => {
              let elt = this.$refs.exportable_table;
              let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
              if (dl) {
                  XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
              } else {
                  XLSX.writeFile(wb, fn || (('document-field' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
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
    <propertyTree
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getProperties"
    />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("DocumentField.DocumentFieldsTable") }}</h4>
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
                      {{ getCompanyKey("documentFieldName") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="filterSetting" value="name_e" class="mb-1">
                      {{ getCompanyKey("documentFieldNameE") }}
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
                  v-b-modal.create
                  variant="primary"
                  class="btn-sm mx-1 font-weight-bold"
                >
                  {{ $t("general.Create") }}
                  <i class="fas fa-plus"></i>
                </b-button>
                <!-- end create modal  -->
                <div class="d-inline-flex">
                  <button class="custom-btn-dowonload" @click="ExportExcel('xlsx')">
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button class="custom-btn-dowonload"  v-print="'#printCustom'">
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
                    @click.prevent="deleteDocumentField(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
                    @click.prevent="deleteDocumentField(checkAll[0])"
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
                    <b-form-checkbox v-model="setting.name" class="mb-1"
                      >{{ getCompanyKey("documentFieldName") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.name_e" class="mb-1">
                      {{ getCompanyKey("documentFieldNameE") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.type" class="mb-1">
                      {{ getCompanyKey("document-field-type") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.is_reference" class="mb-1">
                      {{ getCompanyKey("document-field-reference") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.lookup_table" class="mb-1">
                      {{ getCompanyKey("document-field-lookup") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.field_characters" class="mb-1">
                      {{ getCompanyKey("arch_doc_field_character") }}
                    </b-form-checkbox>
                    <b-form-checkbox v-model="setting.lookup_table_column" class="mb-1">
                      {{ getCompanyKey("document-field-lookup_column") }}
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
                      {{ documentFieldPagination.from }}-{{ documentFieldPagination.to }}
                      /
                      {{ documentFieldPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            documentFieldPagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="getData(documentFieldPagination.current_page - 1)"
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
                            documentFieldPagination.last_page ==
                            documentFieldPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="getData(documentFieldPagination.current_page + 1)"
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
              :title="getCompanyKey('documentFieldCreate')"
              title-class="font-18"
              body-class="p-4"
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
                  <div class="col-md-6 direction" dir="rtl">
                    <div class="form-group">
                      <label for="field-1" class="control-label">
                        {{ getCompanyKey("documentFieldName") }}
                        <span class="text-danger">*</span>
                      </label>
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
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6 direction-ltr" dir="ltr">
                    <div class="form-group">
                      <label for="field-2" class="control-label">
                        {{ getCompanyKey("documentFieldNameE") }}
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
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>
                        {{ getCompanyKey("document-field-type") }}
                        <span class="text-danger">*</span>
                      </label>
                      <select
                        class="custom-select"
                        @change="lookUpChange"
                        v-model="$v.create.type.$model"
                        :class="{
                          'is-invalid': $v.create.type.$error || errors.type,
                          'is-valid': !$v.create.type.$invalid && !errors.type,
                        }"
                      >
                        <option v-for="type in dataTypes" :key="type.id" :value="type.id">
                          {{ $i18n.locale == "ar" ? type.name : type.name_e }}...
                        </option>
                      </select>
                      <template v-if="errors.type">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.type"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-if="create.type && create.type != 7">
                    <div class="form-group">
                      <label class="control-label">
                        {{ getCompanyKey("arch_doc_field_character") }}
                        <span class="text-danger">*</span>
                      </label>
                      <input
                        type="text"
                        class="form-control"
                        v-model="$v.create.field_characters.$model"
                        :class="{
                          'is-invalid':
                            $v.create.field_characters.$error || errors.field_characters,
                          'is-valid':
                            !$v.create.field_characters.$invalid &&
                            !errors.field_characters,
                        }"
                      />
                      <template v-if="errors.field_characters">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.field_characters"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div v-if="create.type == 10" class="col-md-6">
                    <div class="form-group">
                      <label>
                        {{ getCompanyKey("property") }}
                        <span class="text-danger">*</span>
                      </label>
                      <select
                        @change="showTreeCreate"
                        class="custom-select"
                        v-model="$v.create.tree_property_id.$model"
                        :class="{
                          'is-invalid':
                            $v.create.tree_property_id.$error || errors.tree_property_id,
                          'is-valid':
                            !$v.create.tree_property_id.$invalid &&
                            !errors.tree_property_id,
                        }"
                      >
                        <option
                          v-for="property in properties"
                          :key="property.id"
                          :value="property.id"
                        >
                          {{ $i18n.locale == "ar" ? property.name : property.name_e }}
                        </option>
                      </select>

                      <template v-if="errors.type">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.type"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-show="showInput">
                    <div class="form-group position-relative">
                      <label class="control-label">
                        {{ getCompanyKey("document-field-lookup") }}
                        <span class="text-danger">*</span>
                      </label>
                      <multiselect
                        @input="getColumns(create.lookup_table)"
                        v-model="$v.create.lookup_table.$model"
                        :options="tables"
                        :custom-label="(opt) => opt"
                      >
                      </multiselect>
                      <div
                        v-if="$v.create.lookup_table.$error || errors.lookup_table"
                        class="text-danger"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.lookup_table">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.lookup_table"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6" v-show="create.lookup_table">
                    <div class="form-group position-relative">
                      <label class="control-label">
                        {{ getCompanyKey("document-field-lookup_column") }}
                        <span class="text-danger">*</span>
                      </label>
                      <multiselect
                        v-model="$v.create.lookup_table_column.$model"
                        :options="columns"
                        :custom-label="(opt) => opt"
                      >
                      </multiselect>
                      <div
                        v-if="
                          $v.create.lookup_table_column.$error ||
                          errors.lookup_table_column
                        "
                        class="text-danger"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>
                      <template v-if="errors.lookup_table_column">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.lookup_table_column"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="mr-2">
                        {{ getCompanyKey("document-field-reference") }}
                        <span class="text-danger">*</span>
                      </label>
                      <b-form-group
                        :class="{
                          'is-invalid':
                            $v.create.is_reference.$error || errors.is_reference,
                          'is-valid':
                            !$v.create.is_reference.$invalid && !errors.is_reference,
                        }"
                      >
                        <b-form-radio
                          class="d-inline-block"
                          v-model="$v.create.is_reference.$model"
                          name="some-radios"
                          value="1"
                          >{{ $t("general.isReferenceYes") }}</b-form-radio
                        >
                        <b-form-radio
                          class="d-inline-block m-1"
                          v-model="$v.create.is_reference.$model"
                          name="some-radios"
                          value="0"
                          >{{ $t("general.isReferenceNo") }}</b-form-radio
                        >
                      </b-form-group>
                      <template v-if="errors.is_reference">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.is_reference"
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
            <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table"
                 id="printCustom">
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

              <table class="table table-borderless table-hover table-centered m-0">
                <thead>
                  <tr>
                    <th scope="col" style="width: 0" v-if="enabled3" class="do-not-print">
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
                        <span>{{ getCompanyKey("documentFieldName") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="documentFields.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="documentFields.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name_e">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("documentFieldNameE") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="documentFields.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="documentFields.sort(sortString('-name_e'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.is_reference">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("document-field-reference") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="documentFields.sort(sortString('is_reference'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="documentFields.sort(sortString('-is_reference'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.type">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("document-field-type") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="documentFields.sort(sortString('type'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="documentFields.sort(sortString('-type'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.field_characters">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("arch_doc_field_character") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="documentFields.sort(sortString('field_characters'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="documentFields.sort(sortString('-field_characters'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <td v-if="setting.lookup_table">
                      {{ getCompanyKey("document-field-lookup") }}
                    </td>
                    <td v-if="setting.lookup_table_column">
                      {{ getCompanyKey("document-field-lookup_column") }}
                    </td>
                    <th v-if="enabled3" class="do-not-print">
                      {{ $t("general.Action") }}
                    </th>
                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                  </tr>
                </thead>
                <tbody v-if="documentFields.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in documentFields"
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
                    <td v-if="setting.name">
                      <h5 class="m-0 font-weight-normal">{{ data.name }}</h5>
                    </td>
                    <td v-if="setting.name_e">
                      <h5 class="m-0 font-weight-normal">{{ data.name_e }}</h5>
                    </td>
                    <td v-if="setting.is_reference">
                      <span
                        :class="[
                          data.is_reference == 1 ? 'text-success' : 'text-danger',
                          'badge',
                        ]"
                      >
                        {{
                          data.is_reference == 1
                            ? `${$t("general.Yes")}`
                            : `${$t("general.No")}`
                        }}
                      </span>
                    </td>
                    <td v-if="setting.type">
                      <h5 class="m-0 font-weight-normal">
                        {{
                          $i18n.locale == "ar"
                            ? data.data_type.name
                            : data.data_type.name_e
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.field_characters">
                      {{ data.field_characters }}
                    </td>
                    <td v-if="setting.lookup_table">
                      {{ data.lookup_table ? data.lookup_table : "-" }}
                    </td>
                    <td v-if="setting.lookup_table_column">
                      {{ data.lookup_table_column ? data.lookup_table_column : "-" }}
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
                            @click.prevent="deleteDocumentField(data.id)"
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
                        :title="getCompanyKey('documentFieldEdit')"
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
                            <div class="col-md-6 direction" dir="rtl">
                              <div class="form-group">
                                <label for="field-u-1" class="control-label">
                                  {{ getCompanyKey("documentFieldName") }}
                                  <span class="text-danger">*</span>
                                </label>
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
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div class="col-md-6 direction-ltr" dir="ltr">
                              <div class="form-group">
                                <label for="field-u-2" class="control-label">
                                  {{ getCompanyKey("documentFieldNameE") }}
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
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>
                                  {{ getCompanyKey("document-field-type") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <select
                                  class="custom-select"
                                  @change="lookUpChange"
                                  v-model="$v.edit.type.$model"
                                  :class="{
                                    'is-invalid': $v.edit.type.$error || errors.type,
                                    'is-valid': !$v.edit.type.$invalid && !errors.type,
                                  }"
                                >
                                  <option
                                    v-for="type in dataTypes"
                                    :key="type.id"
                                    :value="type.id"
                                  >
                                    {{
                                      $i18n.locale == "ar" ? type.name : type.name_e
                                    }}...
                                  </option>
                                </select>
                                <template v-if="errors.type">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.type"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div class="col-md-6" v-if="edit.type && edit.type != 7">
                              <div class="form-group">
                                <label class="control-label">
                                  {{ getCompanyKey("arch_doc_field_character") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <input
                                  type="text"
                                  class="form-control"
                                  v-model="$v.edit.field_characters.$model"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.field_characters.$error ||
                                      errors.field_characters,
                                    'is-valid':
                                      !$v.edit.field_characters.$invalid &&
                                      !errors.field_characters,
                                  }"
                                />
                                <template v-if="errors.field_characters">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.field_characters"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div v-if="edit.type == 10" class="col-md-6">
                              <div class="form-group">
                                <label>
                                  {{ getCompanyKey("property") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <select
                                  class="custom-select"
                                  @change="showTreeEdit"
                                  v-model="$v.edit.tree_property_id.$model"
                                  :class="{
                                    'is-invalid':
                                      $v.edit.tree_property_id.$error ||
                                      errors.tree_property_id,
                                    'is-valid':
                                      !$v.edit.tree_property_id.$invalid &&
                                      !errors.tree_property_id,
                                  }"
                                >
                                  <option
                                    v-for="property in properties"
                                    :key="property.id"
                                    :value="property.id"
                                  >
                                    {{
                                      $i18n.locale == "ar"
                                        ? property.name
                                        : property.name_e
                                    }}
                                  </option>
                                </select>

                                <template v-if="errors.type">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.type"
                                    :key="index"
                                    >{{ errorMessage }}</ErrorMessage
                                  >
                                </template>
                              </div>
                            </div>
                            <div class="col-md-6" v-show="showInput">
                              <div class="form-group position-relative">
                                <label class="control-label">
                                  {{ getCompanyKey("document-field-lookup_column") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <multiselect
                                  @input="getColumns(edit.lookup_table)"
                                  v-model="$v.edit.lookup_table.$model"
                                  :options="tables"
                                  :custom-label="(opt) => opt"
                                >
                                </multiselect>
                                <div
                                  v-if="
                                    $v.edit.lookup_table.$error || errors.lookup_table
                                  "
                                  class="text-danger"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.lookup_table">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.lookup_table"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-6" v-show="edit.lookup_table">
                              <div class="form-group position-relative">
                                <label class="control-label">
                                  {{ getCompanyKey("document-field-lookup") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <multiselect
                                  v-model="$v.edit.lookup_table_column.$model"
                                  :options="columns"
                                  :custom-label="(opt) => opt"
                                >
                                </multiselect>
                                <div
                                  v-if="
                                    $v.edit.lookup_table_column.$error ||
                                    errors.lookup_table_column
                                  "
                                  class="text-danger"
                                >
                                  {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.lookup_table_column">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.lookup_table_column"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="mr-2">
                                  {{ getCompanyKey("document-field-reference") }}
                                  <span class="text-danger">*</span>
                                </label>
                                <b-form-group
                                  :class="{
                                    'is-invalid':
                                      $v.edit.is_reference.$error || errors.is_reference,
                                    'is-valid':
                                      !$v.edit.is_reference.$invalid &&
                                      !errors.is_reference,
                                  }"
                                >
                                  <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.edit.is_reference.$model"
                                    name="some-radios"
                                    value="1"
                                    >{{ $t("general.isReferenceYes") }}</b-form-radio
                                  >
                                  <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.edit.is_reference.$model"
                                    name="some-radios"
                                    value="0"
                                    >{{ $t("general.isReferenceNo") }}</b-form-radio
                                  >
                                </b-form-group>
                                <template v-if="errors.is_reference">
                                  <ErrorMessage
                                    v-for="(errorMessage, index) in errors.is_reference"
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
                    <th class="text-center" colspan="10">
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
