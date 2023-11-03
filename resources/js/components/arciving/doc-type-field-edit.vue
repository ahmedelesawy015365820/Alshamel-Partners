<script>
import Layout from "../../views/layouts/main";
import adminApi from "../../api/adminAxios";
import { required, minLength, maxLength, integer } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../components/widgets/errorMessage";
import loader from "../general/loader";
import { dynamicSortString } from "../../helper/tableSort";
import Multiselect from "vue-multiselect";
import ArchDoc from "../../components/create/arch/gen-arch-doc-type";
import DocField from "../../components/create/arch/doc-field";
import transMixinComp from "../../helper/mixin/translation-comp-mixin";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Arch Doc Type Field",
    meta: [{ name: "description", content: "Arch Doc Type Field" }],
  },
  mixins: [transMixinComp],

  props: [
    "arch_doc_type_id",
    "doc_type_field",
    "document_data",
    "companyKeys",
    "defaultsKeys",
  ],
  components: {
    Layout,
    ErrorMessage,
    loader,
    Multiselect,
    ArchDoc,
    DocField,
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      if (
        vm.$store.state.auth.work_flow_trees.includes("arch doc type fields") ||
        vm.$store.state.auth.work_flow_trees.includes("archiving")
      ) {
        return true;
      } else {
        return vm.$router.push({ name: "home" });
      }
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
      dataTablePaginations: {},
      storedData: [],
      genArchDocData: [],
      archDocFieldData: [],
      enabled3: true,
      isLoader: false,
      edit: [], //edit form
      allOrder: [],
      allDrop: [],
      filterSetting: ["gen_doc_type_id", "doc_field_id"],
      errors: {}, //Server Side Validation Errors
      isCheckAll: false,
      checkAll: [],
      is_disabled: false,
      current_page: 1,
    };
  },
  validations: {
    edit: {
      required,
      $each: {
        doc_field_id: { required },
        field_order: { required, integer },
        is_required: { required },
      },
    },
  },
  watch: {
    arch_doc_type_id(after, before) {
      this.getArchDocType();
    },
    /**
     * watch check All table
     */
    isCheckAll(after, befour) {
      if (after) {
        this.storedData.forEach((el) => {
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
    await this.getArchDocType();
  },
  methods: {
    /**
     *  get Data document field
     */
    addNewField() {
      this.edit.push({
        doc_type_id: this.arch_doc_type_id,
        doc_field_id: null,
        field_order: null,
        is_required: 1,
        parent_id: false,
      });
      this.allOrder.push({ order: true });
      this.allDrop.push({ order: true });
    },
    removeNewField(index) {
      if (this.edit.length > 1) {
        this.edit.splice(index, 1);
        this.allOrder.splice(index, 1);
        this.allDrop.splice(index, 1);
      }
    },
    /**
     *  get gen doc type data
     */
    async getGenDocType() {
      await adminApi
        .get(`/gen-arch-doc-type`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({
            id: 0,
            name: "Add Gen Arch Doc Type",
            name_e: "Add Gen Arch Doc Type",
          });
          this.genArchDocData = l;
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
     *  get arch doc field data
     */
    async getArchDocType() {
      this.isLoader = true;
      await adminApi
        .get(`/document-field`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({ id: 0, name: "Add Document Field", name_e: "Add Document Field" });
          this.archDocFieldData = l;
          this.edit = [];
          setTimeout(() => {
            if (this.doc_type_field.length > 0 && this.edit.length == 0) {
              this.resetModalEdit(this.arch_doc_type_id);
              this.isLoader = false;
            } else if (this.doc_type_field.length == 0) {
              this.allOrder.push({ order: true });
              this.allDrop.push({ order: true });
              this.edit.push({
                doc_type_id: null,
                doc_field_id: null,
                field_order: null,
                is_required: 1,
              });
              this.isLoader = false;
            }
          }, 5000);
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
     *  hidden Modal (edit)
     */
    async resetModal() {
      // call api
      await this.getGenDocType();
      await this.getArchDocType();
      this.edit = {
        gen_doc_type_id: "",
        doc_field_id: "",
        field_order: "",
        is_required: 1,
        parent_id: false,
      };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  add document field
     */
    AddSubmit() {
      let isTrue = this.allOrder.some((el) => el.order == false);
      let isTrueDrop = this.allDrop.some((el) => el.order == false);
      this.$v.edit.$touch();
      if (this.$v.edit.$invalid || isTrue || isTrueDrop) {
        return;
      } else {
        let newEdit = [];
        this.edit.map((el) => {
          return (el.doc_type_id = this.arch_doc_type_id);
        });
        this.edit.forEach((el) => {
          newEdit.push({
            doc_type_id: el.doc_type_id,
            doc_field_id: el.doc_field_id,
            field_order: el.field_order,
            is_required: el.is_required,
          });
        });
        this.isLoader = true;
        this.errors = {};
        adminApi
          .post(`/arch-doc-type-field/bulk-update`, newEdit)
          .then((res) => {
            this.$nextTick(() => {
              this.$v.$reset();
            });
            setTimeout(() => {
              Swal.fire({
                icon: "success",
                text: `${this.$t("general.Editsuccessfully")}`,
                showConfirmButton: false,
                timer: 1500,
              });
            }, 500);
            this.$emit("update-doc-type-field");
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
      let editGenDocType = this.doc_type_field.sort((a, b) =>
        parseInt(a.field_order) > parseInt(b.field_order) ? 1 : -1
      );
      await editGenDocType.forEach((el) => {
        this.allOrder.push({ order: true });
        this.allDrop.push({ order: true });
        this.edit.push({
          doc_type_id: this.arch_doc_type_id,
          doc_field_id: el.doc_field,
          field_order: el.field_order,
          is_required: el.is_required,
          parent_id: el.parent_id,
        });
      });
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.errors = {};
      this.edit = {
        gen_doc_type_id: "",
        doc_field_id: "",
        field_order: "",
        is_required: 1,
        parent_id: false,
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
    showDocFieldModalEdit(index) {
      this.orderDropChange(index);
      if (this.edit[index].doc_field_id == 0) {
        this.$bvModal.show("create-doc-field");
        this.edit[index].doc_field_id = null;
      } else {
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
              ("Arch Doc Type Field" + "." || "SheetJSTableExport.") + (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
    orderChange(index) {
      let fill = this.edit.filter(
        (el, ind) => index != ind && el.field_order == this.edit[index].field_order
      );
      if (fill.length > 0) {
        this.allOrder.forEach((el) => {
          el.order = false;
        });
      } else {
        this.allOrder.forEach((el) => {
          el.order = true;
        });
      }
    },
    orderDropChange(index) {
      let fill = this.edit.filter(
        (el, ind) => index != ind && el.doc_field_id == this.edit[index].doc_field_id
      );
      if (fill.length > 0) {
        this.allDrop.forEach((el) => {
          el.order = false;
        });
      } else {
        this.allDrop.forEach((el) => {
          el.order = true;
        });
      }
    },
  },
};
</script>

<template>
  <div class="row position-relative" style="height: 500px; overflow-x: scroll">
    <DocField
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @create="getArchDocType"
    />
    <loader size="large" v-if="isLoader" />
    <!--        <div class="col-md-12 text-center">-->
    <!--            <h3>{{$t('general.DocumentName')}} : {{ $i18n.locale == "ar" ? document_data.name : document_data.name_e }}</h3>-->
    <!--        </div>-->
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <form>
            <div class="mb-3 d-flex justify-content-between">
              <!-- Start Add New Record Button -->
              <b-button
                @click.prevent="addNewField"
                style="background-color: rgb(218 220 222); color: #000"
                type="button"
                :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
              >
                + {{ $t("general.AddNewRecord") }}
              </b-button>

              <b-button
                variant="success"
                type="button"
                v-if="!isLoader"
                @click.prevent="AddSubmit"
              >
                {{ $t("general.Edit") }}
              </b-button>
              <b-button variant="success" class="mx-1" disabled v-else>
                <b-spinner small></b-spinner>
                <span class="sr-only">{{ $t("login.Loading") }}...</span>
              </b-button>
              <!-- End Cancel Button Modal -->
            </div>
            <template v-for="(item, index) in edit">
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>
                      {{ getCompanyKey("arch_doc_field") }}
                      <span class="text-danger">*</span>
                    </label>

                    <multiselect
                      :disabled="item.parent_id"
                      @input="showDocFieldModalEdit(index)"
                      v-model="$v.edit.$each[index].doc_field_id.$model"
                      :options="archDocFieldData.map((type) => type.id)"
                      :custom-label="
                        (opt) =>
                          $i18n.locale
                            ? archDocFieldData.find((x) => x.id == opt).name
                            : archDocFieldData.find((x) => x.id == opt).name_e
                      "
                      :class="{
                        'is-invalid':
                          $v.edit.$each[index].doc_field_id.$error ||
                          errors.doc_field_id ||
                          !allDrop[index].order,
                        'is-valid':
                          !$v.edit.$each[index].doc_field_id.$invalid &&
                          !errors.doc_field_id &&
                          allDrop[index].order,
                      }"
                    >
                    </multiselect>
                    <div v-if="!allDrop[index].order" class="invalid-feedback">
                      {{ $t("general.thisFieldIsUniq") }}
                    </div>

                    <template v-if="errors.doc_field_id">
                      <ErrorMessage
                        v-for="(errorMessage, index) in errors.doc_field_id"
                        :key="index"
                        >{{ errorMessage }}
                      </ErrorMessage>
                    </template>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="field-1" class="control-label">
                      {{ getCompanyKey("arch_doc_field_order") }}
                      <span class="text-danger">*</span>
                    </label>
                    <input
                      :disabled="item.parent_id"
                      type="number"
                      min="1"
                      @input="orderChange(index)"
                      class="form-control englishInput"
                      v-model="$v.edit.$each[index].field_order.$model"
                      :class="{
                        'is-invalid':
                          $v.edit.$each[index].field_order.$error ||
                          errors.field_order ||
                          !allOrder[index].order,
                        'is-valid':
                          !$v.edit.$each[index].field_order.$invalid &&
                          !errors.field_order &&
                          allOrder[index].order,
                      }"
                      id="field-1"
                    />
                    <div
                      v-if="!$v.edit.$each[index].field_order.integer"
                      class="invalid-feedback"
                    >
                      {{ $t("general.numericValidation") }}
                    </div>
                    <template v-if="errors.field_order">
                      <ErrorMessage
                        v-for="(errorMessage, index) in errors.field_order"
                        :key="index"
                        >{{ errorMessage }}
                      </ErrorMessage>
                    </template>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="mr-2">
                      {{ getCompanyKey("is_required") }}
                      <span class="text-danger">*</span>
                    </label>
                    <b-form-group
                      :disabled="item.parent_id"
                      :class="{
                        'is-invalid':
                          $v.edit.$each[index].is_required.$error || errors.is_required,
                        'is-valid':
                          !$v.edit.$each[index].is_required.$invalid &&
                          !errors.is_required,
                      }"
                    >
                      <b-form-radio
                        class="d-inline-block"
                        v-model="$v.edit.$each[index].is_required.$model"
                        value="1"
                        >{{ $t("general.isReferenceYes") }}
                      </b-form-radio>
                      <b-form-radio
                        class="d-inline-block m-1"
                        v-model="$v.edit.$each[index].is_required.$model"
                        value="0"
                        >{{ $t("general.isReferenceNo") }}
                      </b-form-radio>
                    </b-form-group>
                    <template v-if="errors.is_required">
                      <ErrorMessage
                        v-for="(errorMessage, index) in errors.is_required"
                        :key="index"
                        >{{ errorMessage }}
                      </ErrorMessage>
                    </template>
                  </div>
                </div>
                <div class="col-md-1 p-0 pt-3" v-if="edit.length > 1 && !item.parent_id">
                  <button
                    type="button"
                    @click.prevent="removeNewField(index)"
                    class="custom-btn-dowonload"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </div>
              </div>
            </template>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="scss">
@media print {
  .do-not-print {
    display: none;
  }

  .arrow-sort {
    display: none;
  }
}
.card {
  .multiselect__content-wrapper {
    max-height: 170px !important;
  }
}
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
</style>
