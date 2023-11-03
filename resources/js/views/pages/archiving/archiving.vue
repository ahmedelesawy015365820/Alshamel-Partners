<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import { required, requiredIf } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import { formatDateTime } from "../../../helper/startDate";
import translation from "../../../helper/mixin/translation-mixin";
import DatePicker from "vue2-datepicker";
import rootData from "./root.json";
import TreeBrowser from "../../../components/arch-screen/tree";
import Files from "../../../components/arch-screen/files.vue";
import Details from "../../../components/arch-screen/details.vue";
import General from "../../../components/create/general";
import VueHtml2pdf from "vue-html2pdf";
import permissionGuard from "../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Archiving",
    meta: [{ name: "description", content: "Archiving" }],
  },
  mixins: [translation],
  components: {
    Layout,
    DatePicker,
    PageHeader,
    ErrorMessage,
    loader,
    Multiselect,
    TreeBrowser,
    Files,
    Details,
    VueHtml2pdf,
    General,
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Archiving", "all Store");
    });
  },
  data() {
    return {
      printLoading: true,
      printObj: {
        id: "printDepartment",
      },
      images: [],
      media: {},
      saveImageName: [],
      child_doc_types: [],
      showPhoto: "/images/img-1.png",
      root: [],
      per_page: 6,
      type: "",
      search: "",
      expanded: [],
      favourite: false,
      debounce: {},
      archivesPagination: {},
      archives: [],
      documentTypes: [],
      documentStatuses: [],
      enabled3: false,
      isLoader: false,
      currentNode: null,
      currentDocument: null,
      archive_id: null,
      fileFields: [],
      from: 0,
      to: 0,
      dropListPopup: false,
      fromDate: new Date(),
      toDate: new Date(),
      currentField: null,
      fileImages: [],
      secondLevelNodes: [],
      fields: [],
      searchFinished: false,
      properties: [],
      treePropId: null,
      arch_doc_type_id: null,
      create: {
        timestamp: null,
        job_file_number: null,
        media: [],
        custom_timestamp: null,
      },
      edit: {
        job_file_number: null,
        document_type_id: null,
        status_id: null,
        description: null,
        timestamp: "",
        custom_timestamp: null,
        old_media: [],
      },
      archiveFiles: [],
      nodeFields: [],
      setting: {
        job_file_number: true,
        document_type_id: true,
        status_id: true,
        timestamp: true,
      },
      filterSetting: [],
      searchDocumentTypeId: null,
      searchFieldId: null,
      errors: {},
      lockups: [],
      isCheckAll: false,
      checkAll: [],
      is_disabled: false,
      current_page: 1,
      company_id: null,
      Tooltip: "",
      mouseEnter: null,
      isActiveFile: false,
      isSearch: false,
      lockupTableObject: null,
    };
  },
  validations: {
    create: { media: {} },
    edit: { media: {} },
    nodeFields: {
      $each: {
        value: { required },
      },
    },
  },
  watch: {
    /**
     * watch per_page
     */
    per_page(after, befour) {
      // this.getData();
    },
    /**
     * watch search
     */
    search(after, befour) {
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.getFieldData();
        this.isSearch = !this.isSearch;
      }, 400);
    },
    /**
     * watch check All table
     */
    isCheckAll(after, befour) {
      if (after) {
        this.archiveFiles.forEach((el) => {
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
    // await this.getData();
    this.getTree();
    this.getSecondLevelNodes();
  },
  methods: {
    setChildNodes(result) {
      adminApi
        .get(
          `/arch-archive-files/getKeys?doc_type_id=${result.node.doc_type_id}
      &arch_department_id=${result.node.arch_department_id}&key_name_e=${result.node.name_e}`
        )
        .then((res) => {
          result.node.children = res.data;
          result.expanded.push(result.node);
          this.expanded = result.expanded;
        });
    },
    getFieldData() {
      this.getPaginatedArchFiles(1);
      // this.currentNode = null;
    },
    getPaginatedArchFiles(page) {
      this.current_page = page ? page : this.current_page;
      if (!this.currentNode) {
        this.getData(page);
        return;
      } else {
        if (!this.search) {
          if (this.currentNode.parent_doc_type_children) {
            //If node selected is key value
            this.getArchiveFiles();
          } else if (this.currentNode.arch_documents) {
            //If node selected department
            this.getArchiveFilesByDepartmentDocument(this.currentNode.id, null);
          } else if (this.currentNode.key) {
            //If node selected parent document
            this.getArchiveFilesByDepartmentDocument(
              this.currentNode.arch_department_id,
              this.currentNode.id
            );
          }
        } else {
          this.getData(page);
        }
      }
    },
    async getArchiveFiles(docId) {
      this.arch_doc_type_id = docId;
      if (!this.currentNode.archive_file) {
        return;
      }
      await adminApi
        .get(
          `/arch-archive-files/valueMedia?value=${
            typeof this.currentNode.name_e === "object"
              ? this.currentNode.name_e.name_e
              : this.currentNode.name_e
          }&department_id=${this.currentNode.archive_file.arch_department_id}
          &parent_arch_doc_type_id=${this.currentNode.parent_doc_id}
          &arch_doc_type_id=${
            this.arch_doc_type_id ? this.arch_doc_type_id : ""
          }
          &page=${this.current_page}&per_page=${this.per_page}`
        )
        .then((res) => {
          let l = res.data;
          this.archiveFiles = l.data;
          this.archivesPagination = l.pagination;
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
    async getArchiveFilesByDepartmentDocument(departmentId, parentDocumentId) {
      await adminApi
        .get(
          `arch-archive-files/files_Department_Doc_Type?arch_department_id=${departmentId}&arch_doc_type_id=${
            parentDocumentId ? parentDocumentId : ""
          }&page=${this.current_page}&per_page=${this.per_page}&doc_type_id=${
            parentDocumentId ? parentDocumentId : ""
          }&search=${this.search}`
        )
        .then((res) => {
          let l = res.data;
          this.archiveFiles = l.data;
          this.archivesPagination = l.pagination;
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
    getDocumentFields(node) {
      adminApi
        .get(`/arch-doc-type-field/id-doctype-field/${node.id}`)
        .then((res) => {
          node.doc_type_field = res.data.data;
          this.lockups = [];
          node.doc_type_field.sort((a, b) =>
            parseInt(a.field_order) > parseInt(b.field_order) ? 1 : -1
          );
          this.nodeFields = [...node.doc_type_field].map((field) => {
            if (field.doc_field_id.data_type.name_e == "Lookup (table)") {
              this.getLookup(
                field.doc_field_id.lookup_table,
                field.doc_field_id.lookup_table_column,
                field.doc_field_id.name_e
              );
            }
            // if (field.doc_field_id.data_type.name_e == "ENUM (droplist)") {
            //    this.getProperties();
            // }
            return {
              ...field,
              value: "",
            };
          });
          this.getProperties();
        });
    },
    getCurrentTreeProps(treePropertyId) {
      let res = this.properties.filter((prop) => {
        return prop.tree_property_id == treePropertyId;
      });
      return res.length ? res[0].list : [];
    },
    getProperties() {
      let props = [];
      let filterRes = this.nodeFields.filter((field) => {
        return (
          field.doc_field_id.tree_property_id &&
          field.doc_field_id.data_type.name_e == "ENUM (droplist)"
        );
      });
      filterRes.forEach((element) => {
        adminApi
          .get(
            `tree-properties/child-nodes/${element.doc_field_id.tree_property_id}`
          )
          .then((res) => {
            let l = res.data;
            l.unshift({ id: 0, name: "اضف خاصية", name_e: "Add property" });
            let prop = {
              tree_property_id: element.doc_field_id.tree_property_id,
              list: l,
            };
            props.push(prop);
          });
      });
      this.properties = props;
    },
    updateCurrentPropertyTreeList() {
      adminApi
        .get(`tree-properties/child-nodes/${this.treePropId}`)
        .then((res) => {
          let l = res.data;
          l.unshift({ id: 0, name: "اضف خاصية", name_e: "Add property" });
          let currProp = null;
          let treePropId = this.treePropId;
          this.properties.forEach((prop) => {
            if (prop.tree_property_id == treePropId) {
              currProp = prop;
              return;
            }
          });
          currProp.list = l;
          this.dropListPopup = false;
          this.treePropId = null;
        });
    },
    print() {
      window.print();
    },
    showPropertyModal(property, treePropId) {
      if (property.id == 0) {
        this.dropListPopup = true;
        this.treePropId = treePropId;
        this.$bvModal.show("property-create");
        this.nodeFields.forEach((field, index) => {
          if (field.doc_field_id.tree_property_id) {
            field.value = null;
            return;
          }
        });
      }
    },
    getCurrentField(id) {
      this.searchFinished = false;
      this.from = 0;
      this.to = 0;
      this.toDate = new Date();
      this.fromDate = new Date();
      this.search = "";

      this.currentField = this.fields.filter((field) => {
        return field.name_e == id;
      })[0];
    },
    getFields(id) {
      this.from = 0;
      this.to = 0;
      this.toDate = new Date();
      this.fromDate = new Date();
      this.search = "";
      if (!id) {
        this.fields = [];
        return;
      }
      this.isLoader = true;
      adminApi
        .get(`arch-archive-files/docType-child-archiv-files?doc_type_id=${id}`)
        .then((res) => {
          this.fields = res.data;
          if (this.fields.length) {
            this.searchFieldId = this.fields[0].name_e;
            this.currentField = this.fields.filter((field) => {
              return field.name_e == this.searchFieldId;
            })[0];
          }
          this.isLoader = false;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
          this.isLoader = false;
        })
        .finally(() => {});
    },
    showFileModal(file) {
      this.fileFields = file.data_type_value;
      this.fileImages = file.media ? file.media : [];
      this.$bvModal.show("show-file");
    },
    showModal(node) {
      this.currentDocument = null;
      this.$bvModal.show("create");
    },
    async getLookup(table, column, field_name) {
      await adminApi
        .get(`/document-field/column-data/${table}/${column}`)
        .then((res) => {
          let l = res.data;
          l.data.unshift({
            id: this.$i18n.locale == "ar" ? "اضف" : "Add",
            name: "اضف",
            name_e: "Add",
          });
          let result = null;
          if (this.lockupTableObject) {
            result = this.lockups.find(
              (e) => this.lockupTableObject.lookup_table == e.table
            );
            result.field_data = l.data;
          } else {
            this.lockups.push({
              field_name: field_name,
              column: column,
              table: table,
              field_data: l.data,
            });
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
    showComponentModal(chose, table, index) {
      if (chose == "Add" || chose == "اضف" || chose == 0) {
        this.lockupTableObject = {
          lookup_table: this.nodeFields[index].doc_field_id.lookup_table,
          lookup_table_column:
            this.nodeFields[index].doc_field_id.lookup_table_column,
          name_e: this.nodeFields[index].doc_field_id.name_e,
        };
        if (table == "general_employees") {
          this.$bvModal.show("employee-create");
          this.nodeFields[index].value = "";
        }
        if (table == "general_customers") {
          this.$bvModal.show("customer-general-create");
          this.nodeFields[index].value = "";
        }
        if (table == "general_tree_properties") {
          this.$bvModal.show("property-create");
          this.nodeFields[index].value = "";
        }
        if (table == "general_countries") {
          this.$bvModal.show("country-create-general");
          this.nodeFields[index].value = "";
        }
        if (table == "general_cities") {
          this.$bvModal.show("city-create-general");
          this.nodeFields[index].value = "";
        }
        if (table == "general_banks") {
          this.$bvModal.show("bank_create_form_general");
          this.nodeFields[index].value = "";
        }
        if (table == "general_bank_accounts") {
          this.$bvModal.show("bank-account-create-general");
          this.nodeFields[index].value = "";
        }
        if (table == "general_branches") {
          this.$bvModal.show("branch-create-general");
          this.nodeFields[index].value = "";
        }
        if (table == "general_avenues") {
          this.$bvModal.show("avenues-create-general");
          this.nodeFields[index].value = "";
        }
      }
    },
    nodeWasClicked(result) {
      this.currentNode = result;
      this.searchFinished = false;
      this.child_doc_types =
        this.currentNode &&
        this.currentNode.parent_doc_type_children &&
        this.currentNode.parent_doc_type_children.length
          ? [
              { id: 0, name: "الكل", name_e: "All" },
              ...this.currentNode.parent_doc_type_children,
            ]
          : [];
      this.arch_doc_type_id = null;
      this.getPaginatedArchFiles(1);
      this.searchFields = false;
      this.isActiveFile = !this.isActiveFile;
      this.$store.commit("archiving/archiveFileEmity");
      this.$store.commit("archiving/objectActiveEmity");
    },
    timestamp(e) {
      if (e) {
        this.create.timestamp = formatDateTime(e);
        this.edit.timestamp = formatDateTime(e);
      } else {
        this.create.timestamp = null;
        this.edit.timestamp = null;
      }
    },
    async getTree() {
      this.isLoader = true;
      await adminApi
        .get(`/arch-department/parent_department`)
        .then((res) => {
          let root = res.data.data;
          root.forEach((node) => {
            if (node.children) {
              node.children.forEach((child) => {
                if (child.doc_type_field) {
                  child.doc_type_field.sort((a, b) =>
                    parseInt(a.field_order) > parseInt(b.field_order) ? 1 : -1
                  );
                }
              });
            }
          });
          this.root = root;
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
    async getPdf(id) {
      if (!id) return;
      await adminApi
        .get(`/arch-archive-files/pdf/${id}`)
        .then((res) => {})
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    /**
     *  get Data archives
     */
    async getData(page = 1) {
      this.searchFinished = false;
      this.isLoader = true;
      if (this.from && !this.to) {
        this.to = this.from;
      }
      if (this.fromDate && !this.toDate) {
        this.toDate = this.fromDate;
      }
      if (this.to && !this.from) {
        this.from = this.to;
      }
      if (this.toDate && !this.fromDate) {
        this.fromDate = this.toDate;
      }
      await adminApi
        .get(
          `/arch-archive-files?page=${page}&per_page=${this.per_page}&search=${this.search}&favourite=${this.favourite}`,
          {
            params: this.currentField
              ? {
                  field: {
                    from:
                      this.currentField.data_type == "INTEGER"
                        ? this.from
                        : this.fromDate,
                    to:
                      this.currentField.data_type == "INTEGER"
                        ? this.to
                        : this.toDate,
                    text: this.search,
                    range: ["INTEGER", "DATE"].includes(
                      this.currentField.data_type
                    ),
                    data_type: this.currentField.data_type,
                  },
                }
              : {},
          }
        )
        .then((res) => {
          let l = res.data;
          this.archiveFiles = l.data;
          this.archivesPagination = l.pagination;
          this.current_page = l.pagination.current_page;
          this.searchFinished = true;
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
      if (this.from && !this.to) {
        this.to = this.from;
      }
      if (this.fromDate && !this.toDate) {
        this.toDate = this.fromDate;
      }
      if (this.to && !this.from) {
        this.from = this.to;
      }
      if (this.toDate && !this.fromDate) {
        this.fromDate = this.toDate;
      }

      if (
        this.current_page <= this.archivesPagination.last_page &&
        this.current_page != this.archivesPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        adminApi
          .get(
            `/arch-archive-files?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&arch_doc_type_id=${this.filterSetting}&favourite=${this.favourite}`,
            {
              params: this.currentField
                ? {
                    field: {
                      from:
                        this.currentField.doc_field_id.data_type.name_e ==
                        "INTEGER"
                          ? this.from
                          : this.fromDate,
                      to:
                        this.currentField.doc_field_id.data_type.name_e ==
                        "INTEGER"
                          ? this.to
                          : this.toDate,
                      text: this.search,
                      range: ["INTEGER", "DATE"].includes(
                        this.currentField.doc_field_id.data_type.name_e
                      ),
                      data_type:
                        this.currentField.doc_field_id.data_type.name_e,
                    },
                  }
                : {},
            }
          )
          .then((res) => {
            let l = res.data;
            this.archiveFiles = l.data;
            this.archivesPagination = l.pagination;
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
    getSecondLevelNodes() {
      this.isLoader = true;
      adminApi
        .get(`/arch-doc-type/nodes-level-two`)
        .then((res) => {
          this.secondLevelNodes = res.data.data;
          this.searchDocumentTypeId = this.secondLevelNodes.length
            ? this.secondLevelNodes[0].id
            : null;
          this.getFields(this.searchDocumentTypeId);
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
     *  delete screen button
     */
    deleteScreenButton(id, index) {
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
              .post(`/arch-archive-files/bulk-delete`, { ids: id })
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
              .delete(`/arch-archive-files/${id}`)
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
     *  reset Modal (create)
     */
    async resetModalHidden() {
      // if (this.images.length > 0) {
      // await this.getPdf(this.archive_id);
      // }
      await this.getArchiveFiles();
      this.create = {
        job_file_number: null,
        document_type_id: null,
        status_id: null,
        description: null,
        timestamp: "",
        custom_timestamp: null,
        media: null,
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.images = [];
      this.errors = {};
      this.archive_id = null;
      this.lockupTableObject = null;
      this.type = "";
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      this.getProperties();
      this.create = {
        data_type_value: null,
        data_type_id: null,
        media: [],
      };
      this.showPhoto = "/images/img-1.png";
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.media = {};
      this.images = [];

      this.errors = {};
    },
    /**
     *  create screen
     */
    resetForm() {
      this.lockupTableObject = null;
      this.create = {
        data_type_value: null,
        data_type_id: null,
        media: [],
      };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.media = {};
      this.images = [];
    },
    AddSubmit() {
      this.errors = {};
      this.is_disabled = false;
      let currentDocument = this.currentDocument;
      let dataTypeValue = [];
      this.nodeFields.forEach((field) => {
        dataTypeValue.push({
          value:
            typeof field.value === "object" && field.value.name_e
              ? field.value.name_e
              : field.value,
          name_e: field.doc_field_id.name_e,
          name: field.doc_field_id.name,
          is_reference: field.doc_field_id.is_reference,
          data_type: field.doc_field_id.data_type.name_e,
          company_id: this.company_id,
        });
      });
      this.$v.nodeFields.$touch();
      let check = false;
      this.nodeFields.forEach((field, index) => {
        if (
          field.is_required == 1 &&
          this.$v.nodeFields.$each[index].value.$error
        ) {
          return (check = true);
        }
      });
      if (check) {
        return;
      }
      this.isLoader = true;
      adminApi
        .post(`/arch-archive-files`, {
          arch_doc_type_id: currentDocument.id,
          arch_department_id: this.currentNode.arch_department_id,
          data_type_value: dataTypeValue,
        })
        .then((res) => {
          //Update tree
          if (
            this.currentNode &&
            this.currentNode.key &&
            this.currentNode.key.length
          ) {
            let archFileKeyValue = res.data.data.data_type_value.filter(
              (field) => {
                return field.name_e == this.currentNode.key[0].name_e;
              }
            );
            archFileKeyValue =
              archFileKeyValue.length > 0 ? archFileKeyValue[0] : null;
            if (archFileKeyValue) {
              if (this.currentNode.key[0].children) {
                let check = this.currentNode.key[0].children.filter(
                  (element) => {
                    return archFileKeyValue.value == element.name_e;
                  }
                );
                if (check.length == 0) {
                  this.currentNode.key[0].children.push({
                    name: archFileKeyValue.value,
                    name_e: archFileKeyValue.value,
                    archive_file: res.data.data,
                    parent_doc_type_children: this.currentNode.sub_docs,
                    parent_doc_id: res.data.data.parent_doc_id,
                  });
                  let index = this.expanded.findIndex(this.currentNode.key[0]);
                  this.expanded.splice(2, 1);
                  this.expanded.push(this.currentNode.key[0]);
                }
              }
            }
          }
          //Update tree
          this.archive_id = res.data.data.id;
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
    },
    /**
     *  edit screen
     */
    editSubmit(id) {
      this.$v.edit.$touch();
      this.images.forEach((e) => {
        this.edit.old_media.push(e.id);
      });
      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        adminApi
          .post(`/arch-archive-files/${id}`, {
            ...this.edit,
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
     *  get workflows
     */
    async getDocumentTypes() {
      this.isLoader = true;
      await adminApi
        .get(`/gen-arch-doc-type`)
        .then((res) => {
          this.isLoader = false;
          this.documentTypes = res.data.data;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    async getStatuses() {
      this.isLoader = true;
      await adminApi
        .get(`/arch-doc-status`)
        .then((res) => {
          this.isLoader = false;
          this.documentStatuses = res.data.data;
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
     *   show Modal (edit)
     */
    async resetModalEdit(id) {
      let archive = this.archiveFiles.find((e) => id == e.id);
      await this.getDocumentTypes();
      await this.getStatuses();
      this.edit.job_file_number = archive.job_file_number;
      this.edit.document_type_id = archive.document_type_id;
      this.edit.status_id = archive.status_id;
      this.edit.description = archive.description;
      this.edit.custom_timestamp = new Date(archive.custom_timestamp);
      this.edit.timestamp = formatDateTime(archive.timestamp);
      this.images = archive.media ?? [];
      if (this.images && this.images.length > 0) {
        this.showPhoto = this.images[this.images.length - 1].webp;
      } else {
        this.showPhoto = "/images/img-1.png";
      }
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.errors = {};
      this.edit = {
        job_file_number: null,
        document_type_id: null,
        status_id: null,
        description: null,
        timestamp: "",
        custom_timestamp: null,
        old_media: [],
      };
      this.images = [];
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
    formatDate(value) {
      return formatDateOnly(value);
    },
    log(id) {
      if (this.mouseEnter != id) {
        this.Tooltip = "";
        this.mouseEnter = id;
        adminApi
          .get(`/arch-archive-files/logs/${id}`)
          .then((res) => {
            let l = res.data.data;
            l.forEach((e) => {
              this.Tooltip += `Created By: ${e.causer_type}; Event: ${
                e.event
              }; Description: ${e.description} ;Created At: ${this.formatDate(
                e.created_at
              )} \n`;
            });
            $(`#tooltip-${id}`).tooltip();
          })
          .catch((err) => {
            Swal.fire({
              icon: "error",
              title: `${this.$t("general.Error")}`,
              text: `${this.$t("general.Thereisanerrorinthesystem")}`,
            });
          });
      }
    },
    /**
     *  start Image ceate
     */
    changePhoto() {
      document.getElementById("uploadImageCreate").click();
    },
    changePhotoEdit() {
      document.getElementById("uploadImageEdit").click();
    },
    onImageChanged(e) {
      const file = e.target.files[0];
      this.type = file.type;
      this.addImage(file);
    },
    addImage(file) {
      this.media = file; //upload
      if (file) {
        this.idDelete = null;
        let is_media = this.images.find(
          (e) => e.name == file.name.slice(0, file.name.indexOf("."))
        );
        this.idDelete = is_media ? is_media.id : null;
        if (!this.idDelete) {
          this.isLoader = true;
          let formDate = new FormData();
          formDate.append("media[0]", this.media);
          adminApi
            .post(`/media`, formDate)
            .then((res) => {
              let old_media = [];
              this.images.forEach((e) => old_media.push(e.id));
              let new_media = [];
              res.data.data.forEach((e) => new_media.push(e.id));
              adminApi
                .put(`/arch-archive-files/${this.archive_id}`, {
                  old_media,
                  media: new_media,
                })
                .then((res) => {
                  this.images = res.data.data.media ?? [];
                  if (this.images && this.images.length > 0) {
                    this.showPhoto = this.images[this.images.length - 1].webp;
                  } else {
                    this.showPhoto = "/images/img-1.png";
                  }
                  // this.getData();
                  if (this.type == "application/pdf") {
                    this.$bvModal.hide("create");
                  }
                })
                .catch((err) => {
                  Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
                });
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
        } else {
          Swal.fire({
            title: `${this.$t("general.Thisfilehasalreadybeenuploaded")}`,
            type: "warning",
            showCancelButton: true,
            confirmButtonText: `${this.$t("general.Replace")}`,
            cancelButtonText: `${this.$t("general.Nocancel")}`,
            confirmButtonClass: "btn btn-success mt-2",
            cancelButtonClass: "btn btn-danger ml-2 mt-2",
            buttonsStyling: false,
          }).then((result) => {
            if (result.value) {
              this.isLoader = true;
              let formDate = new FormData();
              formDate.append("media[0]", this.media);
              adminApi
                .post(`/media`, formDate)
                .then((res) => {
                  let old_media = [];
                  this.images.forEach((e) => old_media.push(e.id));
                  old_media.splice(old_media.indexOf(this.idDelete), 1);
                  let new_media = [];
                  res.data.data.forEach((e) => new_media.push(e.id));

                  adminApi
                    .put(`/arch-archive-files/${this.country_id}`, {
                      old_media,
                      media: new_media,
                    })
                    .then((res) => {
                      this.images = res.data.data.media ?? [];
                      if (this.images && this.images.length > 0) {
                        this.showPhoto =
                          this.images[this.images.length - 1].webp;
                      } else {
                        this.showPhoto = "/images/img-1.png";
                      }
                      this.getData();
                    })
                    .catch((err) => {
                      Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                      });
                    });
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
          });
        }
      }
    },
    deleteImageCreate(id, index) {
      let old_media = [];
      this.images.forEach((e) => {
        if (e.id != id) {
          old_media.push(e.id);
        }
      });
      adminApi
        .put(`/arch-archive-files/${this.archive_id}`, { old_media })
        .then((res) => {
          this.images = res.data.data.media ?? [];
          if (this.images && this.images.length > 0) {
            this.showPhoto = this.images[this.images.length - 1].webp;
          } else {
            this.showPhoto = "/images/img-1.png";
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
    /**
     *  end Image ceate
     */
    /**
     *  start pdf
     */
    generateReport() {
      this.$refs.html2Pdf.generatePdf();
    },
    async deleteFileComponent(id) {
      let file = this.archiveFiles.find((e) => (e.id = id));
      this.$store.commit("archiving/objectActiveEmity");
      this.$store.commit("archiving/archiveFileUpdate", file);
      await this.getTree();
      await this.getData();
    },
    async showFavorite() {
      this.favourite = !this.favourite;
      await this.getData();
    },
  },
};
</script>
<template>
  <Layout>
    <PageHeader />
    <General
      :currentNode="currentNode"
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="
        dropListPopup
          ? updateCurrentPropertyTreeList()
          : getLookup(
              lockupTableObject.lookup_table,
              lockupTableObject.lookup_table_column,
              lockupTableObject.name_e
            )
      "
    />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("general.archivingTable") }}</h4>
              <div
                class="d-flex col-xs-10 col-md-9 col-lg-7"
                style="font-weight: 500"
              >
                <div class="d-flex">
                  <!-- Basic dropdown -->
                  <b-dropdown
                    variant="primary"
                    :text="$t('general.workspace')"
                    ref="dropdown"
                    class="btn-block setting-search m-2"
                  >
                    <b-form-checkbox
                      v-for="node in secondLevelNodes"
                      :key="node.id"
                      v-model="searchDocumentTypeId"
                      :value="node.id"
                      class="mb-1"
                      @change="getFields"
                    >
                      {{ $i18n.locale == "ar" ? node.name : node.name_e }}
                    </b-form-checkbox>
                  </b-dropdown>
                  <!-- Basic dropdown -->
                  <!-- Basic dropdown -->
                  <b-dropdown
                    variant="primary"
                    :text="$t('general.fields')"
                    ref="dropdown"
                    class="btn-block setting-search m-2"
                  >
                    <b-form-checkbox
                      v-for="field in fields"
                      :key="field.name_e"
                      v-model="searchFieldId"
                      :value="field.name_e"
                      class="mb-1"
                      @change="getCurrentField"
                    >
                      {{ $i18n.locale == "ar" ? field.name : field.name_e }}
                    </b-form-checkbox>
                  </b-dropdown>
                  <!-- Basic dropdown -->
                </div>
                <template
                  v-if="currentField && currentField.data_type == 'INTEGER'"
                >
                  <div
                    class="d-inline-block position-relative m-2"
                    style="width: 100%"
                  >
                    <div class="row">
                      <div class="col-6">
                        <input
                          class="form-control"
                          style="padding-top: 3px; display: inline-block"
                          type="text"
                          v-model.trim="from"
                          :placeholder="`${$t('general.from')}`"
                          @keyup="getFieldData()"
                        />
                      </div>
                      <div class="col-6">
                        <input
                          class="form-control"
                          style="padding-top: 3px; display: inline-block"
                          type="text"
                          v-model.trim="to"
                          @keyup="getFieldData()"
                          :placeholder="`${$t('general.to')}`"
                        />
                      </div>
                    </div>
                  </div>
                </template>
                <template
                  v-else-if="currentField && currentField.data_type == 'DATE'"
                >
                  <div
                    class="d-inline-block position-relative m-2"
                    style="width: 100%"
                  >
                    <div class="row">
                      <div class="col-6">
                        <date-picker
                          @change="getFieldData()"
                          v-model="fromDate"
                          type="date"
                          confirm
                        ></date-picker>
                      </div>
                      <div class="col-6">
                        <date-picker
                          @change="getFieldData()"
                          v-model="toDate"
                          type="date"
                          confirm
                        ></date-picker>
                      </div>
                    </div>
                  </div>
                </template>
                <template v-else-if="currentField">
                  <div
                    class="d-inline-block position-relative m-2"
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
                      @input="searchFinished = false"
                      style="display: block; width: 93%; padding-top: 3px"
                      type="text"
                      v-model.trim="search"
                      :placeholder="`${$t('general.Search')}...`"
                    />
                  </div>
                </template>
              </div>
            </div>

            <div
              class="row justify-content-between align-items-center mt-3 mb-2 px-1"
            >
              <div class="col-md-9 d-flex align-items-center mb-1 mb-xl-0">
                <b-button
                  variant="primary"
                  type="button"
                  class="btn-sm mx-1 font-weight-bold"
                  @click.prevent="showFavorite"
                >
                  {{ $t("general.favorite") }}
                  <i v-if="favourite" class="fa fa-star"></i>
                  <i v-else class="far fa-star"></i>
                </b-button>
                <div
                  style="width: 60%; position: relative; top: 3px"
                  v-if="currentNode && currentNode.parent_doc_type_children"
                >
                  <div>
                    <div class="form-group">
                      <multiselect
                        v-model="arch_doc_type_id"
                        @select="getArchiveFiles"
                        :options="child_doc_types.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            $i18n.locale == 'ar'
                              ? child_doc_types.find((x) => x.id == opt).name
                              : child_doc_types.find((x) => x.id == opt).name_e
                        "
                      >
                      </multiselect>
                    </div>
                  </div>
                </div>
                <!--                <b-button-->
                <!--                  v-b-modal.create-->
                <!--                  variant="primary"-->
                <!--                  class="btn-sm mx-1 font-weight-bold"-->
                <!--                >-->
                <!--                  {{ $t("general.UploadMany") }}-->
                <!--                  <i class="fas fa-plus"></i>-->
                <!--                </b-button>-->
                <!--                <b-button-->
                <!--                  v-b-modal.create-->
                <!--                  variant="primary"-->
                <!--                  class="btn-sm mx-1 font-weight-bold"-->
                <!--                >-->
                <!--                  {{ $t("general.Workflow") }}-->
                <!--                  <i class="fas fa-plus"></i>-->
                <!--                </b-button>-->
                <!--                <b-button-->
                <!--                  v-b-modal.create-->
                <!--                  variant="primary"-->
                <!--                  class="btn-sm mx-1 font-weight-bold"-->
                <!--                >-->
                <!--                  {{ $t("general.View") }}-->
                <!--                  <i class="fas fa-plus"></i>-->
                <!--                </b-button>-->
                <!--                <b-button-->
                <!--                  v-b-modal.create-->
                <!--                  variant="primary"-->
                <!--                  class="btn-sm mx-1 font-weight-bold"-->
                <!--                >-->
                <!--                  {{ $t("general.ChangeStatus") }}-->
                <!--                  <i class="fas fa-plus"></i>-->
                <!--                </b-button>-->
                <!--                <div class="btn-group">-->
                <!--                  <button-->
                <!--                    type="button"-->
                <!--                    class="btn btn-sm dropdown-toggle dropdown-coustom"-->
                <!--                    data-toggle="dropdown"-->
                <!--                    aria-expanded="false"-->
                <!--                  >-->
                <!--                    {{ $t("general.commands") }}-->
                <!--                    <i class="fas fa-angle-down"></i>-->
                <!--                  </button>-->
                <!--                  <div class="dropdown-menu dropdown-menu-custom">-->
                <!--                    <a class="dropdown-item" href="#">-->
                <!--                      <div-->
                <!--                        class="d-flex justify-content-between align-items-center text-black"-->
                <!--                      >-->
                <!--                        <span>{{ $t("general.print") }}</span>-->
                <!--                      </div>-->
                <!--                    </a>-->
                <!--                    <a class="dropdown-item text-black" href="#">-->
                <!--                      <div-->
                <!--                        class="d-flex justify-content-between align-items-center text-black"-->
                <!--                      >-->
                <!--                        <span>{{ $t("general.send_email") }}</span>-->
                <!--                      </div>-->
                <!--                    </a>-->
                <!--                    <a class="dropdown-item text-black" href="#">-->
                <!--                      <div-->
                <!--                        class="d-flex justify-content-between align-items-center text-black"-->
                <!--                      >-->
                <!--                        <span>{{ $t("general.send_user") }}</span>-->
                <!--                      </div>-->
                <!--                    </a>-->
                <!--                  </div>-->
                <!--                </div>-->
                <!--                <div class="d-inline-flex">-->
                <!--                  <button-->
                <!--                    class="custom-btn-dowonload"-->
                <!--                    @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"-->
                <!--                    v-if="checkAll.length == 1"-->
                <!--                  >-->
                <!--                    <i class="mdi mdi-square-edit-outline"></i>-->
                <!--                  </button>-->
                <!--                  &lt;!&ndash; start mult delete  &ndash;&gt;-->
                <!--                  <button-->
                <!--                    class="custom-btn-dowonload"-->
                <!--                    v-if="checkAll.length > 1"-->
                <!--                    @click.prevent="deleteScreenButton(checkAll)"-->
                <!--                  >-->
                <!--                    <i class="fas fa-trash-alt"></i>-->
                <!--                  </button>-->
                <!--                  &lt;!&ndash; end mult delete  &ndash;&gt;-->
                <!--                  &lt;!&ndash;  start one delete  &ndash;&gt;-->
                <!--                  <button-->
                <!--                    class="custom-btn-dowonload"-->
                <!--                    v-if="checkAll.length == 1"-->
                <!--                    @click.prevent="deleteScreenButton(checkAll[0])"-->
                <!--                  >-->
                <!--                    <i class="fas fa-trash-alt"></i>-->
                <!--                  </button>-->
                <!--                  &lt;!&ndash;  end one delete  &ndash;&gt;-->
                <!--                </div>-->
              </div>
              <div
                class="col-xs-10 col-md-9 col-lg-3 d-flex align-items-center justify-content-end"
              >
                <div>
                  <!-- <b-button class="mx-1 custom-btn-background">
                                                                              {{ $t("general.filter") }}
                                                                              <i class="fas fa-filter"></i>
                                                                            </b-button> -->
                  <!-- start Pagination -->
                  <div
                    class="d-inline-flex align-items-center pagination-custom"
                  >
                    <div class="d-inline-block" style="font-size: 15px">
                      {{ archivesPagination.from }}-{{ archivesPagination.to }}
                      /
                      {{ archivesPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            archivesPagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="
                          getPaginatedArchFiles(
                            archivesPagination.current_page - 1
                          )
                        "
                      >
                        <span>&lt;</span>
                      </a>
                      <input
                        type="text"
                        @keyup.enter="getPaginatedArchFiles()"
                        v-model="current_page"
                        class="pagination-current-page"
                      />
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            archivesPagination.last_page ==
                            archivesPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getPaginatedArchFiles(
                            archivesPagination.current_page + 1
                          )
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
              dialog-class="modal-full-width"
              id="create"
              :title="$t('general.FileUploads')"
              title-class="font-18"
              size="lg"
              body-class="p-4 "
              :hide-footer="true"
              @show="resetModal"
              @hidden="resetModalHidden"
            >
              <form>
                <div class="card">
                  <div class="card-body">
                    <div class="mb-3 d-flex justify-content-end">
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

                      <b-button
                        variant="danger"
                        type="button"
                        @click.prevent="$bvModal.hide(`create`)"
                      >
                        {{ $t("general.Cancel") }}
                      </b-button>
                    </div>
                  </div>
                  <b-tabs nav-class="nav-tabs nav-bordered">
                    <b-tab :title="$t('general.DataEntry')" active>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="my-1 mr-2">{{
                              $t("general.document")
                            }}</label>
                            <multiselect
                              @select="getDocumentFields"
                              v-model="currentDocument"
                              :options="currentNode ? currentNode.sub_docs : []"
                              :custom-label="
                                (opt) =>
                                  $i18n.locale == 'ar' ? opt.name : opt.name_e
                              "
                            >
                            </multiselect>
                          </div>
                        </div>
                        <template v-if="currentDocument">
                          <div
                            v-for="(field, index) in nodeFields"
                            :key="field.id"
                            class="col-lg-6"
                          >
                            <div
                              v-if="
                                field.doc_field_id.data_type.name_e == 'DATE'
                              "
                              class="form-group"
                            >
                              <label class="control-label">
                                {{
                                  $i18n.locale == "ar"
                                    ? field.doc_field_id.name
                                    : field.doc_field_id.name_e
                                }}
                                <span
                                  v-if="field.is_required == 1"
                                  class="text-danger"
                                  >*</span
                                >
                              </label>
                              <date-picker
                                v-model="field.value"
                                type="date"
                                confirm
                              ></date-picker>
                            </div>
                            <div
                              v-else-if="
                                field.doc_field_id.data_type.name_e ==
                                'Lookup (table)'
                              "
                              class="form-group"
                            >
                              <label class="control-label">
                                {{
                                  $i18n.locale == "ar"
                                    ? field.doc_field_id.name
                                    : field.doc_field_id.name_e
                                }}</label
                              >
                              <span
                                v-if="field.is_required == 1"
                                class="text-danger"
                                >*</span
                              >
                              <multiselect
                                v-model="
                                  $v.nodeFields.$each[index].value.$model
                                "
                                @input="
                                  showComponentModal(
                                    $v.nodeFields.$each[index].value.$model,
                                    lockups.find(
                                      (e) =>
                                        field.doc_field_id.name_e ==
                                        e.field_name
                                    ).table,
                                    index
                                  )
                                "
                                :options="
                                  lockups.length > 0 &&
                                  lockups.find(
                                    (e) =>
                                      field.doc_field_id.name_e == e.field_name
                                  )
                                    ? lockups
                                        .find(
                                          (e) =>
                                            field.doc_field_id.name_e ==
                                            e.field_name
                                        )
                                        .field_data.map(
                                          (type) =>
                                            type[
                                              lockups.find(
                                                (e) =>
                                                  field.doc_field_id.name_e ==
                                                  e.field_name
                                              ).column
                                            ]
                                        )
                                    : []
                                "
                                :class="{
                                  'is-invalid':
                                    field.is_required == 1 &&
                                    $v.nodeFields.$each[index].value.$error,
                                  'is-valid':
                                    field.is_required != 1 ||
                                    !$v.nodeFields.$each[index].value.$invalid,
                                }"
                              >
                              </multiselect>
                            </div>
                            <template
                              v-else-if="
                                field.doc_field_id.data_type.name_e ==
                                'ENUM (droplist)'
                              "
                            >
                              <div class="form-group">
                                <label class="control-label">
                                  {{
                                    $i18n.locale == "ar"
                                      ? field.doc_field_id.name
                                      : field.doc_field_id.name_e
                                  }}</label
                                >
                                <span
                                  v-if="field.is_required == 1"
                                  class="text-danger"
                                  >*</span
                                >
                                <multiselect
                                  @input="
                                    showPropertyModal(
                                      $event,
                                      field.doc_field_id.tree_property_id
                                    )
                                  "
                                  v-model="
                                    $v.nodeFields.$each[index].value.$model
                                  "
                                  :options="
                                    getCurrentTreeProps(
                                      field.doc_field_id.tree_property_id
                                    )
                                  "
                                  :custom-label="
                                    (opt) =>
                                      $i18n.locale == 'ar'
                                        ? opt.name
                                        : opt.name_e
                                  "
                                  :class="{
                                    'is-invalid':
                                      field.is_required == 1 &&
                                      $v.nodeFields.$each[index].value.$error,
                                    'is-valid':
                                      field.is_required != 1 ||
                                      !$v.nodeFields.$each[index].value
                                        .$invalid,
                                  }"
                                >
                                </multiselect>
                              </div>
                            </template>
                            <div
                              v-else-if="
                                field.doc_field_id.data_type.name_e == 'TIME'
                              "
                              class="form-group"
                            >
                              <label class="control-label">
                                {{
                                  $i18n.locale == "ar"
                                    ? field.doc_field_id.name
                                    : field.doc_field_id.name_e
                                }}
                                <span
                                  v-if="field.is_required == 1"
                                  class="text-danger"
                                  >*</span
                                >
                              </label>
                              <b-form-timepicker
                                v-model="field.value"
                              ></b-form-timepicker>
                            </div>
                            <div
                              v-else-if="
                                field.doc_field_id.data_type.name_e ==
                                'TIMESTAMP'
                              "
                              class="form-group"
                            >
                              <label class="control-label">
                                {{
                                  $i18n.locale == "ar"
                                    ? field.doc_field_id.name
                                    : field.doc_field_id.name_e
                                }}
                                <span
                                  v-if="field.is_required == 1"
                                  class="text-danger"
                                  >*</span
                                >
                              </label>
                              <date-picker
                                v-model="field.value"
                                confirm
                                type="datetime"
                              ></date-picker>
                            </div>
                            <div
                              v-else-if="
                                field.doc_field_id.data_type.name_e == 'INTEGER'
                              "
                              class="form-group"
                            >
                              <label class="control-label">
                                {{
                                  $i18n.locale == "ar"
                                    ? field.doc_field_id.name
                                    : field.doc_field_id.name_e
                                }}
                                <span
                                  v-if="field.is_required == 1"
                                  class="text-danger"
                                  >*</span
                                >
                              </label>
                              <input
                                type="number"
                                class="form-control"
                                data-create="9"
                                step="1"
                                v-model="
                                  $v.nodeFields.$each[index].value.$model
                                "
                                :class="{
                                  'is-invalid':
                                    field.is_required == 1 &&
                                    $v.nodeFields.$each[index].value.$error,
                                  'is-valid':
                                    field.is_required != 1 ||
                                    !$v.nodeFields.$each[index].value.$invalid,
                                }"
                              />
                            </div>
                            <div
                              v-else-if="
                                field.doc_field_id.data_type.name_e == 'BOOLEAN'
                              "
                              class="form-group"
                            >
                              <label class="control-label">
                                {{
                                  $i18n.locale == "ar"
                                    ? field.doc_field_id.name
                                    : field.doc_field_id.name_e
                                }}
                                <span
                                  v-if="field.is_required == 1"
                                  class="text-danger"
                                  >*</span
                                >
                              </label>
                              <select
                                class="form-control"
                                v-model="
                                  $v.nodeFields.$each[index].value.$model
                                "
                                :class="{
                                  'is-invalid':
                                    field.is_required == 1 &&
                                    $v.nodeFields.$each[index].value.$error,
                                  'is-valid':
                                    field.is_required != 1 ||
                                    !$v.nodeFields.$each[index].value.$invalid,
                                }"
                              >
                                <option value="true">
                                  {{ $t("general.true") }}
                                </option>
                                <option value="false">
                                  {{ $t("general.false") }}
                                </option>
                              </select>
                            </div>
                            <div
                              v-else-if="
                                field.doc_field_id.data_type.name_e ==
                                  'FLOAT' ||
                                field.doc_field_id.data_type.name_e == 'DOUBLE'
                              "
                              class="form-group"
                            >
                              <label class="control-label">
                                {{
                                  $i18n.locale == "ar"
                                    ? field.doc_field_id.name
                                    : field.doc_field_id.name_e
                                }}
                                <span
                                  v-if="field.is_required == 1"
                                  class="text-danger"
                                  >*</span
                                >
                              </label>
                              <input
                                type="number"
                                class="form-control"
                                data-create="9"
                                step="0.001"
                                v-model="
                                  $v.nodeFields.$each[index].value.$model
                                "
                                :class="{
                                  'is-invalid':
                                    field.is_required == 1 &&
                                    $v.nodeFields.$each[index].value.$error,
                                  'is-valid':
                                    field.is_required != 1 ||
                                    !$v.nodeFields.$each[index].value.$invalid,
                                }"
                              />
                            </div>
                            <div v-else class="form-group">
                              <label class="control-label">
                                {{
                                  $i18n.locale == "ar"
                                    ? field.doc_field_id.name
                                    : field.doc_field_id.name_e
                                }}
                                <span
                                  v-if="field.is_required == 1"
                                  class="text-danger"
                                  >*</span
                                >
                              </label>
                              <input
                                type="text"
                                class="form-control"
                                data-create="9"
                                v-model="
                                  $v.nodeFields.$each[index].value.$model
                                "
                                :class="{
                                  'is-invalid':
                                    field.is_required == 1 &&
                                    $v.nodeFields.$each[index].value.$error,
                                  'is-valid':
                                    field.is_required != 1 ||
                                    !$v.nodeFields.$each[index].value.$invalid,
                                }"
                              />
                            </div>
                          </div>
                        </template>
                      </div>
                    </b-tab>
                    <b-tab
                      :disabled="!archive_id"
                      :title="$t('general.Uploads')"
                    >
                      <div class="row">
                        <input
                          accept="image/png, image/gif, image/jpeg, image/jpg"
                          type="file"
                          id="uploadImageCreate"
                          @change.prevent="onImageChanged"
                          class="input-file-upload position-absolute"
                          :class="[
                            'd-none',
                            {
                              'is-invalid':
                                $v.create.media.$error || errors.media,
                              'is-valid':
                                !$v.create.media.$invalid && !errors.media,
                            },
                          ]"
                        />
                        <div class="col-md-8 my-1">
                          <!-- file upload -->
                          <div
                            class="row align-content-between"
                            style="width: 100%; height: 100%"
                          >
                            <div class="col-12">
                              <div class="d-flex flex-wrap">
                                <div
                                  :class="[
                                    'dropzone-previews col-4 position-relative mb-2',
                                  ]"
                                  v-for="(photo, index) in images"
                                  :key="photo.id"
                                >
                                  <div
                                    :class="[
                                      'card mb-0 shadow-none border',
                                      images.length - 1 == index
                                        ? 'bg-primary'
                                        : '',
                                    ]"
                                  >
                                    <div class="p-2">
                                      <div class="row align-items-center">
                                        <div
                                          class="col-auto"
                                          @click="showPhoto = photo.webp"
                                        >
                                          <img
                                            data-dz-thumbnail
                                            :src="photo.webp"
                                            class="avatar-sm rounded bg-light"
                                            @error="src = '/images/img-1.png'"
                                          />
                                        </div>
                                        <div class="col pl-0">
                                          <a
                                            href="javascript:void(0);"
                                            :class="[
                                              'font-weight-bold',
                                              images.length - 1 == index
                                                ? 'text-white'
                                                : 'text-muted',
                                            ]"
                                            data-dz-name
                                          >
                                            {{ photo.name }}
                                          </a>
                                        </div>
                                        <!-- Button -->
                                        <a
                                          href="javascript:void(0);"
                                          :class="[
                                            'btn-danger dropzone-close',
                                            $i18n.locale == 'ar'
                                              ? 'dropzone-close-rtl'
                                              : '',
                                          ]"
                                          data-dz-remove
                                          @click.prevent="
                                            deleteImageCreate(photo.id, index)
                                          "
                                        >
                                          <i class="fe-x"></i>
                                        </a>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="footer-image col-12">
                              <b-button
                                @click="changePhoto"
                                variant="success"
                                type="button"
                                class="mx-1 font-weight-bold px-3"
                                v-if="!isLoader"
                              >
                                {{ $t("general.Add") }}
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
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="show-dropzone">
                            <img
                              :src="showPhoto"
                              class="img-thumbnail"
                              @error="src = '/images/img-1.png'"
                            />
                          </div>
                        </div>
                      </div>
                    </b-tab>
                  </b-tabs>
                </div>
              </form>
            </b-modal>
            <!--show file-->

            <b-modal
              dialog-class="modal-full-width"
              id="show-file"
              :title="$t('general.SHOW_FILE')"
              title-class="font-18"
              size="lg"
              body-class="p-4 "
              :hide-footer="true"
              @hidden="showPhoto = '/images/img-1.png'"
            >
              <form>
                <div class="d-flex justify-content-end">
                  <button
                    type="button"
                    v-print="'#printDepartment'"
                    class="custom-btn-dowonload"
                  >
                    <i class="fe-printer"></i>
                  </button>
                  <a
                    v-if="fileImages.length > 0"
                    class="custom-btn-dowonload"
                    :href="fileImages[fileImages.length - 1].url"
                    download
                  >
                    <i class="fa fa-file-pdf"></i>
                  </a>
                </div>
                <div
                  class="card"
                  id="printDepartment"
                  style="background: none !important"
                >
                  <div class="row">
                    <div
                      v-for="(field, index) in fileFields"
                      :key="index"
                      class="col-lg-6"
                    >
                      <div class="form-group">
                        <label class="control-label">
                          {{ $i18n.locale == "ar" ? field.name : field.name_e }}
                        </label>
                        <input
                          readonly
                          :value="
                            typeof field.value === 'object'
                              ? $i18n.locale == 'ar'
                                ? field.value.name
                                : field.value.name_e
                              : field.value
                          "
                          type="text"
                          class="form-control"
                          data-create="9"
                          step="0.1"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="row mt-4">
                    <div class="col-md-8 my-1">
                      <!-- file upload -->
                      <div
                        class="row align-content-between"
                        style="width: 100%; height: 100%"
                      >
                        <div class="col-12">
                          <div class="d-flex flex-wrap">
                            <div
                              :class="[
                                'dropzone-previews col-12 position-relative mb-2',
                              ]"
                              v-for="(photo, index) in fileImages"
                              :key="photo.id"
                            >
                              <div
                                v-if="photo.mime_type != 'application/pdf'"
                                :class="['card mb-0 shadow-none border']"
                              >
                                <div class="p-2">
                                  <div class="row align-items-center">
                                    <div
                                      class="col-auto"
                                      @click="showPhoto = photo.webp"
                                    >
                                      <img
                                        data-dz-thumbnail
                                        :src="photo.webp"
                                        class="avatar-sm rounded bg-light"
                                        @error="src = '/images/img-1.png'"
                                      />
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="single-image col-md-4">
                      <div class="show-dropzone">
                        <img
                          :src="showPhoto"
                          class="img-thumbnail"
                          @error="src = '/images/img-1.png'"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </b-modal>

            <div class="row">
              <div class="col-lg-3">
                <div class="p-1">
                  <template v-if="this.currentNode">
                    {{ $t("general.currentNode") }} :
                    {{
                      $i18n.locale == "ar"
                        ? typeof this.currentNode.name === "object"
                          ? $i18n.locale == "ar"
                            ? this.currentNode.name.name
                            : this.currentNode.name.name_e
                          : this.currentNode.name
                        : typeof this.currentNode.name_e === "object"
                        ? $i18n.locale == "ar"
                          ? this.currentNode.name_e.name
                          : this.currentNode.name_e.name_e
                        : this.currentNode.name_e
                    }}
                  </template>
                </div>
                <div class="references border">
                  <TreeBrowser
                    @nodeExpanded="setChildNodes"
                    @onClick="nodeWasClicked"
                    @onDoubleClicked="showModal"
                    :nodes="root"
                  />
                </div>
              </div>
              <div class="col-lg-5">
                <template v-if="archiveFiles.length">
                  <Files
                    @onDoubleClicked="showFileModal"
                    :archiveFiles="archiveFiles"
                    :isActiveFile="isActiveFile"
                    :isSearch="isSearch"
                  />
                </template>
                <div v-else-if="searchFinished" class="text-center">
                  {{
                    $t("general.NO_FILES_FOUND", {
                      value: from || to ? `${from} - ${to}` : search,
                    })
                  }}
                </div>
              </div>
              <div
                class="col-lg-4"
                v-if="$store.state.archiving.archiveFile.length > 0"
              >
                <Details
                  :currentNode="currentNode"
                  @getDataTree="getData"
                  @pdfPopup="generateReport"
                  @deleteFile="deleteFileComponent"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--      <div>-->
    <!--          <VueHtml2pdf-->
    <!--              :show-layout="false"-->
    <!--              :float-layout="false"-->
    <!--              :enable-download="true"-->
    <!--              :preview-modal="true"-->
    <!--              :paginate-elements-by-height="2400"-->
    <!--              filename="myPDF"-->
    <!--              :pdf-quality="2"-->
    <!--              :manual-pagination="false"-->
    <!--              pdf-format="a4"-->
    <!--              pdf-orientation="landscape"-->
    <!--              pdf-content-width="800px"-->
    <!--              ref="html2Pdf"-->
    <!--          >-->
    <!--              <section slot="pdf-content" id="printDepartment">-->
    <!--                  <div class="row m-0">-->
    <!--                      <div-->
    <!--                          v-for="(field, index) in fileFields"-->
    <!--                          :key="index"-->
    <!--                          style="width: 45% !important; margin: auto"-->
    <!--                      >-->
    <!--                          <div class="form-group" >-->
    <!--                              <label class="control-label">-->
    <!--                                  {{ $i18n.locale == "ar" ? field.name : field.name_e }}-->
    <!--                              </label>-->
    <!--                              <input-->
    <!--                                  readonly-->
    <!--                                  :value="field.value"-->
    <!--                                  type="text"-->
    <!--                                  class="form-control"-->
    <!--                                  data-create="9"-->
    <!--                                  step="0.1"-->
    <!--                                  style=""-->
    <!--                              />-->
    <!--                          </div>-->
    <!--                      </div>-->
    <!--                  </div>-->
    <!--                  <div class="col-md-12 my-1" v-for="(photo, index) in fileImages" :key="photo.id">-->
    <!--                      <div class="p-2">-->
    <!--                          <div class="row align-items-center">-->
    <!--                              <div class="col-auto" @click="showPhoto = photo.webp">-->
    <!--                                  <img-->
    <!--                                      data-dz-thumbnail-->
    <!--                                      :src="photo.webp"-->
    <!--                                      class="avatar-xxl rounded bg-light"-->
    <!--                                      style="width: 1000px !important; height: 600px !important;"-->
    <!--                                      @error="src = '/images/img-1.png'"-->
    <!--                                  />-->
    <!--                              </div>-->
    <!--                          </div>-->
    <!--                      </div>-->
    <!--                  </div>-->
    <!--              </section>-->
    <!--          </VueHtml2pdf>-->
    <!--      </div>-->
  </Layout>
</template>
<style lang="scss">
.file-print {
  background: none;
}

@media print {
  .col-lg-6 {
    width: 50%;
  }

  .avatar-sm {
    width: 100%;
    height: auto;
  }

  .single-image {
    display: none;
  }
}

.references {
  width: 260px;
  padding: 5px 0;
  background: #f8fcff;
  box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 0px 1px;
  height: 420px;
  overflow: auto;

  ul {
    list-style: none;
  }

  ul li {
    padding: 10px 0;
  }
}

input[type="file"] {
  display: none;
}

.modal-dialog .card {
  margin: 0 !important;
}

.country.modal-body {
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

.img-thumbnail {
  max-height: 400px !important;
}

.upload-file {
  label:hover {
    cursor: pointer;
  }

  label {
    padding: 8px;
    border-radius: 5px;
    background: #1abc9c;
    margin-top: 36px;
    color: #fff !important;
  }
}
</style>



