<script>
import Layout from "../../views/layouts/main";
import adminApi from "../../api/adminAxios";
import { required, minLength, maxLength, integer } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../components/widgets/errorMessage";
import loader from "../general/loader";
import { dynamicSortString } from "../../helper/tableSort";
import Multiselect from "vue-multiselect";
import DocDepartment from "../../components/create/arch/arch-department";
import transMixinComp from "../../helper/mixin/translation-comp-mixin";

/**
 * Advanced Table component
 */
export default {
  props: ["created","arch_doc_type_id", "document_data", "companyKeys", "defaultsKeys"],
  mixins: [transMixinComp],

  page: {
    title: "archiving",
    meta: [{ name: "archiving", content: "Document Department" }],
  },
  components: {
    Layout,
    ErrorMessage,
    loader,
    Multiselect,
    DocDepartment,
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
      documents: [],
      archDocFieldData: [],
      enabled3: true,
      isLoader: false,
      create: {
        arch_doc_type_id: this.arch_doc_type_id,
        arch_department_id: "",
      }, //Create form
      errors: {}, //Server Side Validation Errors
      isCheckAll: false,
      checkAll: [],
      is_disabled: false,
      current_page: 1,
    };
  },
  validations: {
    create: {
      arch_department_id: { required },
    },
  },
  watch: {
    arch_doc_type_id(after, befour) {
      this.getData();
    },

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
  mounted() {
    this.getDepartment();
  },
  methods: {
    /**
     *  get Data document field
     */
    async getData(page = 1) {
      this.isLoader = true;
      if (this.arch_doc_type_id)
      {
          setTimeout(() => {
              adminApi
                  .get(
                      `/arch-doc-type-department/getDepartmentByDocument/${this.arch_doc_type_id}`
                  )
                  .then((res) => {
                      let l = res.data;
                      this.storedData = l.data;
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
          }, 1000);
      }
    },
    /**
     *  delete document field
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
            .delete(`/arch-doc-type-department/${id}`)
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
     *  get arch doc field data
     */
    async getDepartment() {
      await adminApi
        .get(`/arch-department?is_key=1`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({
            id: 0,
            name: "Add Arch Department",
            name_e: "Add Arch Department",
          });
          this.archDocFieldData = l;
          this.getData();
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
      this.create = {
        arch_department_id: "",
        arch_doc_type_id: this.arch_doc_type_id,
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
      // call api
      await this.getDepartment();
      this.create = {
        arch_department_id: "",
        arch_doc_type_id: this.arch_doc_type_id,
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
    resetForm() {
      this.create = {
        arch_department_id: "",
        arch_doc_type_id: this.arch_doc_type_id,
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
      this.$v.create.$touch();
      if (this.$v.create.$invalid) {
        return;
      } else {
        this.create.arch_doc_type_id = this.arch_doc_type_id;
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;
        adminApi
          .post(`/arch-doc-type-department`, { ...this.create , company_id: this.$store.getters["auth/company_id"],})
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
            this.resetModalHidden();
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
    showDocFieldModal() {
      if (this.create.arch_department_id == 0) {
        this.$bvModal.show("create-arch-department");
        this.create.arch_department_id = null;
      } else {
        this.AddSubmit();
      }
    },
  },
};
</script>

<template>
  <div class="row">
    <DocDepartment
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @create-arch-department="getDepartment"
    />
    <!--    <div class="col-md-12 text-center">-->
    <!--      <h3>-->
    <!--        {{ $t("general.DocumentName") }} :-->
    <!--        {{ $i18n.locale == "ar" ? document_data.name : document_data.name_e }}-->
    <!--      </h3>-->
    <!--    </div>-->
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>
                  {{ getCompanyKey("department") }}
                  <span class="text-danger">*</span>
                </label>

                <multiselect
                  @input="showDocFieldModal"
                  v-model="create.arch_department_id"
                  :options="archDocFieldData.map((type) => type.id)"
                  :custom-label="
                    (opt) =>
                      $i18n.locale
                        ? archDocFieldData.find((x) => x.id == opt).name
                        : archDocFieldData.find((x) => x.id == opt).name_e
                  "
                >
                </multiselect>

                <template v-if="errors.arch_department_id">
                  <ErrorMessage
                    v-for="(errorMessage, index) in errors.arch_department_id"
                    :key="index"
                    >{{ errorMessage }}
                  </ErrorMessage>
                </template>
              </div>
            </div>
          </div>
          <!--  /create   -->

          <!-- start .table-responsive-->
          <div class="table-responsive mb-3 custom-table-theme position-relative">
            <!--       start loader       -->
            <loader size="large" v-if="isLoader" />
            <!--       end loader       -->

            <table
              class="table table-borderless table-hover table-centered m-0"
              ref="exportable_table"
              id="printField"
            >
              <thead>
                <tr>
                  <th>
                    <div class="d-flex justify-content-center">
                      <span>{{ getCompanyKey("document") }}</span>
                      <div class="arrow-sort">
                        <i
                          class="fas fa-arrow-up"
                          @click="storedData.sort(sortString('arch_doc_type_id'))"
                        ></i>
                        <i
                          class="fas fa-arrow-down"
                          @click="storedData.sort(sortString('-arch_doc_type_id'))"
                        ></i>
                      </div>
                    </div>
                  </th>
                  <th>
                    <div class="d-flex justify-content-center">
                      <span>{{ getCompanyKey("department") }}</span>
                      <div class="arrow-sort">
                        <i
                          class="fas fa-arrow-up"
                          @click="storedData.sort(sortString('arch_department_id'))"
                        ></i>
                        <i
                          class="fas fa-arrow-down"
                          @click="storedData.sort(sortString('-arch_department_id'))"
                        ></i>
                      </div>
                    </div>
                  </th>
                  <th>
                    {{ $t("general.Action") }}
                  </th>
                </tr>
              </thead>
              <tbody v-if="storedData.length > 0">
                <tr
                  @click.capture="checkRow(data.id)"
                  @dblclick.prevent="$bvModal.show(`doc-dep-modal-edit-${data.id}`)"
                  v-for="(data, index) in storedData"
                  :key="data.id"
                  class="body-tr-custom"
                >
                  <td>
                    <h5 class="m-0 font-weight-normal">
                      {{
                        data.doc_type
                          ? $i18n.locale == "ar"
                            ? data.doc_type.name
                            : data.doc_type.name_e
                          : ""
                      }}
                    </h5>
                  </td>
                  <td>
                    <h5 class="m-0 font-weight-normal">
                      {{
                        data.department
                          ? $i18n.locale == "ar"
                            ? data.department.name
                            : data.department.name_e
                          : ""
                      }}
                    </h5>
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
                        v-if="!data.parent_id"
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
                  </td>
                </tr>
              </tbody>
              <tbody v-else>
                <tr>
                  <th class="text-center" colspan="3">
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
