<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import { required, integer, numeric } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../../helper/startDate";
import translation from "../../../helper/mixin/translation-mixin";
import InstallmentPaymentType from "../../../components/create/receivablePayment/installmentPaymentType.vue";
import InstallmentPaymentPlan from "../../../components/create/receivablePayment/installmentPlan";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import permissionGuard from "../../../helper/permission";


/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Installment Payment Plan Detail",
    meta: [
      {
        name: "Installment Payment Plan Detail",
        content: "Installment Payment Plan Detail",
      },
    ],
  },
  mixins: [translation],
  components: {
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
    InstallmentPaymentType,
    InstallmentPaymentPlan
  },
  // beforeRouteEnter(to, from, next) {
  //       next((vm) => {
  //     return permissionGuard(vm, "Installment Payment Plan Detail", "all Store");
  //   });
  // },
  data() {
    return {
      enabled3: true,
      printLoading: true,
      func: true,
      printObj: {
            id: "printCustom",
        },
      per_page: 50,
      search: "",
      debounce: {},
      planDetailsPagination: {},
      planDetails: [],
      parents: [],
      isLoader: false,
      create: {
          installment_payment_plan_id: null,
          installment_payment_plan_details:
           [
              {
                  installment_payment_type_id: null,
                  is_fixed: 0,
                  ln_no: 0,
                  installment_payment_type_per: 0,
                  installment_payment_type_amount: 0,
                  installment_payment_type_freq: 0,
                  interest_per: 0,
                  interest_value: 0,
              }
          ]
      },
      edit: {
          installment_payment_plan_id: null,
          installment_payment_plan_details: []
      },
      errors: {},
      payment_types: [],
      payment_plans: [],
      isCheckAll: false,
      checkAll: [],
      current_page: 1,
      setting: {
          name: true,
          name_e: true,
          is_default: true,
          is_active: true,
          rows: true
      },
      is_disabled: false,
      filterSetting: [
          "name", "name_e"
      ],
      Tooltip: "",
      mouseEnter: null,
    };
  },
  validations: {
    create: {
          installment_payment_plan_id: { required },
          installment_payment_plan_details: {
              required,
              $each: {
                  installment_payment_type_id: { required },
                  is_fixed: { required, numeric },
                  ln_no: { required, integer },
                  installment_payment_type_per: { required, numeric },
                  installment_payment_type_freq: { required, numeric },
                  interest_per: { required, numeric },
              }
          }
      },
    edit: {
        installment_payment_plan_id: { required },
        installment_payment_plan_details: {
            required,
            $each: {
                installment_payment_type_id: { required },
                is_fixed: { required, numeric },
                ln_no: { required, integer },
                installment_payment_type_per: { required, numeric },
                installment_payment_type_freq: { required, numeric },
                interest_per: { required, numeric },
            }
        }
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
        this.planDetails.forEach((el) => {
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
    this.getData();
  },
  methods: {
      addNewField(){
          this.create.installment_payment_plan_details.push({
              installment_payment_type_id: null,
              is_fixed: 0,
              ln_no: 0,
              installment_payment_type_per: 0,
              installment_payment_type_freq: 0,
              interest_per: 0,
          });
      },
      removeNewField(index){
          if(this.create.installment_payment_plan_details.length > 1){
              this.create.installment_payment_plan_details.splice(index,1);
          }
      },
      addNewFieldEdit(){
          this.edit.installment_payment_plan_details.push({
              installment_payment_type_id: null,
              is_fixed: 0,
              ln_no: 0,
              installment_payment_type_per: 0,
              installment_payment_type_freq: 0,
              interest_per: 0,
          });
      },
      removeNewFieldEdit(index){
          if(this.edit.installment_payment_plan_details.length > 1){
              this.edit.installment_payment_plan_details.splice(index,1);
          }
      },
    /**
     *  start get Data module && pagination
     */
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      for (let i = 0; i < this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }

      adminApi
        .get(
          `/recievable-payable/rp_installment_p_plan?plan_details=true&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.planDetails = l.data;
          this.planDetailsPagination = l.pagination;
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
        this.current_page <= this.planDetailsPagination.last_page &&
        this.current_page != this.planDetailsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/recievable-payable/rp_installment_p_plan?plan_details=true&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.planDetails = l.data;
            this.planDetailsPagination = l.pagination;
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
     *  end get Data module && pagination
     */
    /**
     *  start delete module
     */
    deleteModule(id, index) {
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
              .post(`/recievable-payable/rp_installment_p_plan_details/bulk-delete`, {
                ids: id,
              })
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
              .delete(`/recievable-payable/rp_installment_p_plan_details/${id}`)
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
     *  end delete module
     */
    /**
     *  reset Modal (create)
     */
    resetModalHidden() {
      this.create = {
          installment_payment_plan_id: null,
          installment_payment_plan_details:
              [
                  {
                      installment_payment_type_id: null,
                      is_fixed: 0,
                      ln_no: 0,
                      installment_payment_type_per: 0,
                      installment_payment_type_freq: 0,
                      interest_per: 0,
                  }
              ]
      };
      this.payment_plans = [];
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
      await this.getInstallPaymentTypes();
      await this.getInstallPaymentPlans();
      this.create = {
          installment_payment_plan_id: null,
          installment_payment_plan_details:
              [
                  {
                      installment_payment_type_id: null,
                      is_fixed: 0,
                      ln_no: 0,
                      installment_payment_type_per: 0,
                      installment_payment_type_freq: 0,
                      interest_per: 0,
                  }
              ]
      };
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create module
     */
     async resetForm() {
        await this.getInstallPaymentPlans();
        this.create = {
          installment_payment_plan_id: null,
          installment_payment_plan_details:
              [
                  {
                      installment_payment_type_id: null,
                      is_fixed: 0,
                      ln_no: 0,
                      installment_payment_type_per: 0,
                      installment_payment_type_freq: 0,
                      interest_per: 0,
                  }
              ]
      };
        this.$nextTick(() => {
          this.$v.$reset();
        });
        this.is_disabled = false;
        this.errors = {};
    },

    AddSubmit() {
      this.$v.create.$touch();

      if (this.$v.create.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};

        this.create.installment_payment_plan_details.map(e => e.installment_payment_plan_id = this.create.installment_payment_plan_id);

        adminApi
          .post(`/recievable-payable/rp_installment_p_plan_details`, {
              installment_payment_plan_details:this.create.installment_payment_plan_details
          })
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
     *  edit module
     */
    editSubmit(id) {
      this.$v.edit.$touch();

      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};

          this.edit.installment_payment_plan_details.map(e => e.installment_payment_plan_id = this.edit.installment_payment_plan_id);

          adminApi
          .put(`/recievable-payable/rp_installment_p_plan_details/${id}`, {
              installment_payment_plan_details: this.edit.installment_payment_plan_details
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
      await this.getInstallPaymentTypes();
      await this.getInstallPaymentPlansEdit();
      this.func = false;
      let module = this.planDetails.find((e) => id == e.id);
      this.edit.installment_payment_plan_id = module.id;
      module.installment_payment_plan_details.forEach((e,index) => {
          this.edit.installment_payment_plan_details.push({
              is_fixed : e.is_fixed,
              ln_no : e.ln_no,
              installment_payment_type_per : e.installment_payment_type_per,
              installment_payment_type_freq : e.installment_payment_type_freq,
              interest_per : e.interest_per,
              installment_payment_type_id : e.installment_payment_type_id
          });
      });
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.errors = {};
      this.func = true;
      this.edit = {
          installment_payment_plan_id: null,
          installment_payment_plan_details: []
      };
      this.payment_plans = [];
    },
    /**
     *  start  dynamicSortString
     */
    sortString(value) {
      return dynamicSortString(value);
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
    async getInstallPaymentTypes() {
      this.isLoader = true;

      await adminApi
        .get(`/recievable-payable/rp_installment_payment_types`)
        .then((res) => {
          let l = res.data.data;
          l.unshift({
            id: 0,
            name: "اضف نوع دفع",
            name_e: "Add installment payment type",
          });
          this.payment_types = l;
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
    async getInstallPaymentPlans() {
          this.isLoader = true;

          await adminApi
              .get(`/recievable-payable/rp_installment_p_plan?null_relation_plan_details=true`)
              .then((res) => {
                  let l = res.data.data;
                  l.unshift({
                      id: 0,
                      name: "اضف خطه دفع",
                      name_e: "Add installment payment plan",
                  });
                  this.payment_plans = l;
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
    async getInstallPaymentPlansEdit() {
          this.isLoader = true;

          await adminApi
              .get(`/recievable-payable/rp_installment_p_plan`)
              .then((res) => {
                  let l = res.data.data;
                  l.unshift({
                      id: 0,
                      name: "اضف خطه دفع",
                      name_e: "Add installment payment plan",
                  });
                  this.payment_plans = l;
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
    showInstallmentPaymentTypeModal(index) {
      if (this.create.installment_payment_plan_details[index].installment_payment_type_id == 0) {
        this.$bvModal.show("installment_payment_type_create");
        this.create.installment_payment_plan_details[index].installment_payment_type_id = null;
      }
    },
    showInstallmentPaymentTypeModalEdit(index) {
      if (this.edit.installment_payment_plan_details[index].installment_payment_type_id == 0) {
        this.$bvModal.show("installment_payment_type_create");
        this.edit.installment_payment_plan_details[index].installment_payment_type_id = null;
      }
    },
    showInstallmentPaymentPlanModal() {
          if (this.create.installment_payment_plan_id == 0) {
              this.$bvModal.show("installment-payment-plan-create");
              this.create.installment_payment_plan_id = null;
          }
      },
    showInstallmentPaymentPlanModalEdit() {
          if (this.edit.installment_payment_plan_id == 0) {
              this.$bvModal.show("installment-payment-plan-create");
              this.edit.installment_payment_plan_id = null;
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
          .get(`/recievable-payable/rp_installment_p_plan_details/logs/${id}`)
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
    ExportExcel(type, fn, dl) {
          this.enabled3 = false;
          setTimeout(() => {
              let elt = this.$refs.exportable_table;
              let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
              if (dl) {
                  XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
              } else {
                  XLSX.writeFile(wb, fn || (('InstallmentPaymentPlanDetail' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
              }
              this.enabled3 = true;
          }, 100);
      },
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <InstallmentPaymentType
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getInstallPaymentTypes"
    />
    <InstallmentPaymentPlan
        :companyKeys="companyKeys"
        :defaultsKeys="defaultsKeys"
        @created="func ? getInstallPaymentPlans:getInstallPaymentPlansEdit"
    />
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">
                {{ $t("general.InstallmentPaymentPlanDetailTable") }}
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
                      <b-form-checkbox v-model="filterSetting" value="name" class="mb-1">
                          {{ getCompanyKey("installment_payment_name_ar") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="filterSetting" value="name_e" class="mb-1">
                          {{ getCompanyKey("installment_payment_name_en") }}
                      </b-form-checkbox>
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
                >
                  {{ $t("general.Create") }}
                  <i class="fas fa-plus"></i>
                </b-button>
                <div class="d-inline-flex">
                  <button class="custom-btn-dowonload" @click="ExportExcel('xlsx')">
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button class="custom-btn-dowonload"  v-print="'#printCustom'">
                    <i class="fe-printer"></i>
                  </button>
                  <button
                    class="custom-btn-dowonload"
                    @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                    v-if="checkAll.length == 1"
                  >
                    <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- start mult delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length > 1"
                    @click.prevent="deleteModule(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
                    @click.prevent="deleteModule(checkAll[0])"
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
                      :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                      ref="dropdown"
                      class="dropdown-custom-ali"
                    >
                        <b-form-checkbox v-model="setting.name" class="mb-1"
                        >{{ getCompanyKey("installment_payment_name_ar") }}
                        </b-form-checkbox>
                        <b-form-checkbox v-model="setting.name_e" class="mb-1">
                            {{ getCompanyKey("installment_payment_name_en") }}
                        </b-form-checkbox>
                        <b-form-checkbox v-model="setting.is_default" class="mb-1">
                            {{ getCompanyKey("is_default") }}
                        </b-form-checkbox>
                        <b-form-checkbox v-model="setting.is_active" class="mb-1">
                            {{ getCompanyKey("is_active") }}
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

                  <!-- start Pagination -->
                  <div class="d-inline-flex align-items-center pagination-custom">
                    <div class="d-inline-block" style="font-size: 13px">
                      {{ planDetailsPagination.from }}-{{ planDetailsPagination.to }} /
                      {{ planDetailsPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            planDetailsPagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="getData(planDetailsPagination.current_page - 1)"
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
                            planDetailsPagination.last_page ==
                            planDetailsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="getData(planDetailsPagination.current_page + 1)"
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
              :title="getCompanyKey('InstallmentPaymentPlanDetailCreate')"
              title-class="font-18"
              body-class="p-4 "
              :hide-footer="true"
              dialog-class="modal-full-width"
              @show="resetModal"
              @hidden="resetModalHidden"
            >
              <form>
                <div class="d-flex justify-content-end">
                  <b-button
                    variant="success"
                    :disabled="!is_disabled"
                    @click.prevent="resetForm"
                    type="button"
                    :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
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
                <div class="row" >
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="my-1 mr-2">{{
                        getCompanyKey("installment_payment_plan_id")
                      }}</label>
                      <multiselect
                        @input="showInstallmentPaymentPlanModal"
                        v-model="create.installment_payment_plan_id"
                        :options="payment_plans.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            $i18n.locale == 'ar'
                              ? payment_plans.find((x) => x.id == opt).name
                              : payment_plans.find((x) => x.id == opt).name_e
                        "
                      >
                      </multiselect>
                      <template v-if="errors.installment_payment_plan_id">
                        <ErrorMessage
                          v-for="(
                            errorMessage, index
                          ) in errors.installment_payment_plan_id"
                          :key="index"
                          >{{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                </div>
                <div style="height: 350px; overflow-x: scroll;">
                    <template v-for="(item,index) in create.installment_payment_plan_details">
                        <div class="row" :key="index">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="mr-2">{{
                                        getCompanyKey("installment_payment_type_id")
                                        }}</label>
                                    <multiselect
                                        @input="showInstallmentPaymentTypeModal(index)"
                                        v-model="create.installment_payment_plan_details[index].installment_payment_type_id"
                                        :options="payment_types.map((type) => type.id)"
                                        :custom-label="
                                      (opt) =>
                                        $i18n.locale == 'ar'
                                          ? payment_types.find((x) => x.id == opt).name
                                          : payment_types.find((x) => x.id == opt).name_e
                                    "
                                    >
                                    </multiselect>
                                    <template v-if="errors.installment_payment_type_id">
                                        <ErrorMessage
                                            v-for="(
                                        errorMessage, index
                                      ) in errors.installment_payment_type_id"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">
                                        {{ getCompanyKey("ln_no") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        class="form-control englishInput"
                                        v-model="$v.create.installment_payment_plan_details.$each[index].ln_no.$model"
                                        :class="{
                                      'is-invalid': $v.create.installment_payment_plan_details.$each[index].ln_no.$error || errors.ln_no,
                                      'is-valid': !$v.create.installment_payment_plan_details.$each[index].ln_no.$invalid && !errors.ln_no,
                                    }"
                                        id="field-2"
                                    />
                                    <template v-if="errors.ln_no">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.ln_no"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">
                                        {{ getCompanyKey("installment_payment_type_per") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        class="form-control englishInput"
                                        step="0.01"
                                        v-model="$v.create.installment_payment_plan_details.$each[index].installment_payment_type_per.$model"
                                        :class="{
                                      'is-invalid':
                                        $v.create.installment_payment_plan_details.$each[index].installment_payment_type_per.$error ||
                                        errors.installment_payment_type_per,
                                      'is-valid':
                                        !$v.create.installment_payment_plan_details.$each[index].installment_payment_type_per.$invalid &&
                                        !errors.installment_payment_type_per,
                                    }"
                                    />
                                    <template v-if="errors.installment_payment_type_per">
                                        <ErrorMessage
                                            v-for="(
                            errorMessage, index
                          ) in errors.installment_payment_type_per"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">
                                        {{ getCompanyKey("installment_payment_type_freq") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        class="form-control englishInput"
                                        v-model="$v.create.installment_payment_plan_details.$each[index].installment_payment_type_freq.$model"
                                        :class="{
                          'is-invalid':
                            $v.create.installment_payment_plan_details.$each[index].installment_payment_type_freq.$error ||
                            errors.installment_payment_type_freq,
                          'is-valid':
                            !$v.create.installment_payment_plan_details.$each[index].installment_payment_type_freq.$invalid &&
                            !errors.installment_payment_type_freq,
                        }"
                                    />
                                    <template v-if="errors.installment_payment_type_freq">
                                        <ErrorMessage
                                            v-for="(
                            errorMessage, index
                          ) in errors.installment_payment_type_freq"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="field-2" class="control-label">
                                        {{ getCompanyKey("interest_per") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        class="form-control englishInput"
                                        v-model="$v.create.installment_payment_plan_details.$each[index].interest_per.$model"
                                        :class="{
                                      'is-invalid':
                                        $v.create.installment_payment_plan_details.$each[index].interest_per.$error || errors.interest_per,
                                      'is-valid':
                                        !$v.create.installment_payment_plan_details.$each[index].interest_per.$invalid && !errors.interest_per,
                                    }"
                                    />
                                    <template v-if="errors.interest_per">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.interest_per"
                                            :key="index"
                                        >{{ errorMessage }}
                                        </ErrorMessage>
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-1" style="padding: 0">
                                <div class="form-group">
                                    <label class="mr-2">
                                        {{ getCompanyKey("is_fixed") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <b-form-group
                                        :class="{
                                  'is-invalid': $v.create.installment_payment_plan_details.$each[index].is_fixed.$error || errors.is_fixed,
                                  'is-valid': !$v.create.installment_payment_plan_details.$each[index].is_fixed.$invalid && !errors.is_fixed,
                                }"
                                    >
                                        <b-form-radio
                                            style="font-size: 12px;"
                                            class="d-inline-block"
                                            v-model="$v.create.installment_payment_plan_details.$each[index].is_fixed.$model"
                                            :name="`some-radios-${index}`"
                                            value="1"
                                        >{{ $t("general.Yes") }}</b-form-radio
                                        >
                                        <b-form-radio
                                            style="font-size: 12px;"
                                            class="d-inline-block m-1"
                                            v-model="$v.create.installment_payment_plan_details.$each[index].is_fixed.$model"
                                            :name="`some-radios-${index}`"
                                            value="0"
                                        >{{ $t("general.No") }}</b-form-radio
                                        >
                                    </b-form-group>
                                    <template v-if="errors.is_fixed">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.is_fixed"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-1 p-0 pt-3">
                                <button
                                    v-if="(create.installment_payment_plan_details.length - 1) == index"
                                    type="button"
                                    @click.prevent="addNewField"
                                    class="custom-btn-dowonload"
                                >
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button
                                    v-if="create.installment_payment_plan_details.length > 1"
                                    type="button"
                                    @click.prevent="removeNewField(index)"
                                    class="custom-btn-dowonload"
                                >
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </template>
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
                              <span>{{ getCompanyKey("installment_payment_name_ar") }}</span>
                              <div class="arrow-sort">
                                  <i
                                      class="fas fa-arrow-up"
                                      @click="installmentStatus.sort(sortString('name'))"
                                  ></i>
                                  <i
                                      class="fas fa-arrow-down"
                                      @click="installmentStatus.sort(sortString('-name'))"
                                  ></i>
                              </div>
                          </div>
                      </th>
                      <th v-if="setting.name_e">
                          <div class="d-flex justify-content-center">
                              <span>{{ getCompanyKey("installment_payment_name_en") }}</span>
                              <div class="arrow-sort">
                                  <i
                                      class="fas fa-arrow-up"
                                      @click="installmentStatus.sort(sortString('name_e'))"
                                  ></i>
                                  <i
                                      class="fas fa-arrow-down"
                                      @click="installmentStatus.sort(sortString('-name_e'))"
                                  ></i>
                              </div>
                          </div>
                      </th>
                      <th v-if="setting.is_default">
                          <div class="d-flex justify-content-center">
                              <span>{{ getCompanyKey("is_default") }}</span>
                          </div>
                      </th>
                      <th v-if="setting.is_active">
                          <div class="d-flex justify-content-center">
                              <span>{{ getCompanyKey("is_active") }}</span>
                          </div>
                      </th>
                      <th v-if="setting.rows" class="do-not-print">
                          {{ $t("general.rows") }}
                      </th>
                      <th v-if="enabled3" class="do-not-print">
                        {{ $t("general.Action") }}
                      </th>
                      <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                  </tr>
                </thead>
                <tbody v-if="planDetails.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in planDetails"
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
                      <td v-if="setting.is_default">
                      <span
                          :class="[
                          data.is_default == 1 ? 'text-success' : 'text-danger',
                          'badge',
                        ]"
                      >
                        {{
                              data.is_default == 1
                                  ? `${$t("general.Active")}`
                                  : `${$t("general.Inactive")}`
                          }}
                      </span>
                      </td>
                      <td v-if="setting.is_active">
                      <span
                          :class="[
                          data.is_active == 1 ? 'text-success' : 'text-danger',
                          'badge',
                        ]"
                      >
                        {{
                              data.is_active == 1
                                  ? `${$t("general.Active")}`
                                  : `${$t("general.Inactive")}`
                          }}
                      </span>
                      </td>
                      <th v-if="setting.rows" class="do-not-print">
                          {{ data.count_installment_payment_Plan_Detail }}
                      </th>
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
                            @click.prevent="deleteModule(data.id)"
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
                        :title="getCompanyKey('InstallmentPaymentPlanDetailEdit')"
                        title-class="font-18"
                        body-class="p-4"
                        :ref="`edit-${data.id}`"
                        :hide-footer="true"
                        dialog-class="modal-full-width"
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
                          <div class="row mb-3">
                            <div class="col-md-4">
                              <div class="form-group">
                                <label class="my-1 mr-2">
                                  {{ getCompanyKey("installment_payment_plan_id") }}
                                </label>
                                <multiselect
                                  @input="showInstallmentPaymentPlanModalEdit"
                                  v-model="edit.installment_payment_plan_id"
                                  :options="payment_plans.map((type) => type.id)"
                                  :custom-label="
                                    (opt) =>
                                      $i18n.locale == 'ar'
                                        ? payment_plans.find((x) => x.id == opt).name
                                        : payment_plans.find((x) => x.id == opt).name_e
                                  "
                                >
                                </multiselect>
                                <template v-if="errors.installment_payment_plan_id">
                                  <ErrorMessage
                                    v-for="(
                                      errorMessage, index
                                    ) in errors.installment_payment_plan_id"
                                    :key="index"
                                    >{{ errorMessage }}
                                  </ErrorMessage>
                                </template>
                              </div>
                            </div>
                          </div>
                          <div style="height: 350px; overflow-x: scroll;">
                              <template v-for="(item,index) in edit.installment_payment_plan_details">
                                <div class="row" :key="index">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="mr-2">
                                                {{ getCompanyKey("installment_payment_type_id") }}
                                            </label>
                                            <multiselect
                                                @input="showInstallmentPaymentTypeModalEdit(index)"
                                                v-model="edit.installment_payment_plan_details[index].installment_payment_type_id"
                                                :options="payment_types.map((type) => type.id)"
                                                :custom-label="
                                            (opt) =>
                                              $i18n.locale == 'ar'
                                                ? payment_types.find((x) => x.id == opt).name
                                                : payment_types.find((x) => x.id == opt).name_e
                                          "
                                            >
                                            </multiselect>
                                            <template v-if="errors.installment_payment_type_id">
                                                <ErrorMessage
                                                    v-for="(
                                  errorMessage, index
                                ) in errors.installment_payment_type_id"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="edit-2" class="control-label">
                                                {{ getCompanyKey("ln_no") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control englishInput"
                                                v-model="$v.edit.installment_payment_plan_details.$each[index].ln_no.$model"
                                                :class="{
                                          'is-invalid': $v.edit.installment_payment_plan_details.$each[index].ln_no.$error || errors.ln_no,
                                          'is-valid': !$v.edit.installment_payment_plan_details.$each[index].ln_no.$invalid && !errors.ln_no,
                                        }"
                                                id="edit-2"
                                            />
                                            <template v-if="errors.ln_no">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.ln_no"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">
                                                {{ getCompanyKey("installment_payment_type_per") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control englishInput"
                                                step="0.01"
                                                v-model="$v.edit.installment_payment_plan_details.$each[index].installment_payment_type_per.$model"
                                                :class="{
                  'is-invalid':
                    $v.edit.installment_payment_plan_details.$each[index].installment_payment_type_per.$error ||
                    errors.installment_payment_type_per,
                  'is-valid':
                    !$v.edit.installment_payment_plan_details.$each[index].installment_payment_type_per.$invalid &&
                    !errors.installment_payment_type_per,
                }"
                                            />
                                            <template v-if="errors.installment_payment_type_per">
                                                <ErrorMessage
                                                    v-for="(
                                                errorMessage, index
                                              ) in errors.installment_payment_type_per"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">
                                                {{ getCompanyKey("installment_payment_type_freq") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control englishInput"
                                                v-model="$v.edit.installment_payment_plan_details.$each[index].installment_payment_type_freq.$model"
                                                :class="{
                                          'is-invalid':
                                            $v.edit.installment_payment_plan_details.$each[index].installment_payment_type_freq.$error ||
                                            errors.installment_payment_type_freq,
                                          'is-valid':
                                            !$v.edit.installment_payment_plan_details.$each[index].installment_payment_type_freq.$invalid &&
                                            !errors.installment_payment_type_freq,
                                        }"
                                            />
                                            <template v-if="errors.installment_payment_type_freq">
                                                <ErrorMessage
                                                    v-for="(
                                            errorMessage, index
                                          ) in errors.installment_payment_type_freq"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">
                                                {{ getCompanyKey("interest_per") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="number"
                                                step="0.01"
                                                class="form-control englishInput"
                                                v-model="$v.edit.installment_payment_plan_details.$each[index].interest_per.$model"
                                                :class="{
                              'is-invalid':
                                $v.edit.installment_payment_plan_details.$each[index].interest_per.$error || errors.interest_per,
                              'is-valid':
                                !$v.edit.installment_payment_plan_details.$each[index].interest_per.$invalid && !errors.interest_per,
                            }"
                                            />
                                            <template v-if="errors.interest_per">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.interest_per"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-1" style="padding: 0">
                                        <div class="form-group">
                                            <label class="mr-2">
                                                {{ getCompanyKey("is_fixed") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <b-form-group
                                                :class="{
                                              'is-invalid': $v.edit.installment_payment_plan_details.$each[index].is_fixed.$error || errors.is_fixed,
                                              'is-valid': !$v.edit.installment_payment_plan_details.$each[index].is_fixed.$invalid && !errors.is_fixed,
                                            }"
                                            >
                                                <b-form-radio
                                                    class="d-inline-block"
                                                    style="font-size: 12px;"
                                                    v-model="$v.edit.installment_payment_plan_details.$each[index].is_fixed.$model"
                                                    :name="`some-radios-${index}`"
                                                    value="1"
                                                >{{ $t("general.Yes") }}</b-form-radio
                                                >
                                                <b-form-radio
                                                    style="font-size: 12px;"
                                                    class="d-inline-block m-1"
                                                    v-model="$v.edit.installment_payment_plan_details.$each[index].is_fixed.$model"
                                                    :name="`some-radios-${index}`"
                                                    value="0"
                                                >{{ $t("general.No") }}</b-form-radio
                                                >
                                            </b-form-group>
                                            <template v-if="errors.is_fixed">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.is_fixed"
                                                    :key="index"
                                                >{{ errorMessage }}</ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-1 p-0 pt-3">
                                        <button
                                            v-if="(edit.installment_payment_plan_details.length - 1) == index"
                                            type="button"
                                            @click.prevent="addNewFieldEdit"
                                            class="custom-btn-dowonload"
                                        >
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button
                                            v-if="edit.installment_payment_plan_details.length > 1"
                                            type="button"
                                            @click.prevent="removeNewFieldEdit(index)"
                                            class="custom-btn-dowonload"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </template>
                          </div>
                        </form>
                      </b-modal>
                      <!--  /edit   -->
                    </td>
                    <td v-if="enabled3" class="do-not-print">
                      <button
                        @mousemove="log(data.id)"
                        @mouseover="log(data.id)"
                        type="button"
                        class="btn"
                        :id="`tooltip-${data.id}`"
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
            <!-- end .table-responsive -->
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
