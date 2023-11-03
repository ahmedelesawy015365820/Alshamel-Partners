<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import outerAxios from "../../../api/outerAxios";
import {required, numeric, integer,maxLength,between,decimal,minValue} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import { dynamicSortNumber } from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import { formatDateTime, formatDateOnly } from "../../../helper/startDate";
import translation from "../../../helper/mixin/translation-mixin";
import DatePicker from "vue2-datepicker";
import InstallmentStatus from "../../../components/create/receivablePayment/installmentStatus";
import InstallmentPlan from "../../../components/create/receivablePayment/installmentPlan.vue";
import Document from "../../../components/create/general/document";
import InstallmentPaymentType from "../../../components/create/receivablePayment/installmentPaymentType.vue";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import permissionGuard from "../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Payment plan installment",
    meta: [{ name: "description", content: "Payment plan installment" }],
  },
  mixins: [translation],
  components: {
    Layout,
    InstallmentStatus,
    PageHeader,
    ErrorMessage,
    loader,
    Multiselect,
    DatePicker,
    Document,
    InstallmentPlan,
    InstallmentPaymentType
  },
  beforeRouteEnter(to, from, next) {
        next((vm) => {
      return permissionGuard(vm, "Payment Plan Installment RP", "all paymentPlanInstallment RP");
    });
    },
  data() {
    return {
      per_page: 50,
      search: "",
      debounce: {},
      payemntPlanInstallmentsPagination: {},
      payment_plan_installments: [],
      installment_payment_plans: [],
      installment_statuses: [],
      documentTypes: [],
      payment_types: [],
      isLoader: false,
      create: {
          installment_payment_plan_id: null,
          payment_plan_installments:
              [
                  {
                      installment_payment_type_id: null,
                      v_date: '',
                      due_date: '',
                      total_amount: 0,
                      paid_amount: 0,
                      installment_status_id: null,
                      doc_type_id: null,
                      ref_id: null,
                      rp_code: '',
                      day_month: 1,
                      is_fixed: 0,
                      note_a: '',
                      note_e: ''
                  }
              ]
      },
      edit: {
          installment_payment_plan_id: null,
          payment_plan_installments: []
      },
      setting: {
          name: true,
          name_e: true,
          is_default: true,
          is_active: true,
          rows: true
      },
      enabled3: true,
      multDate: [{start: new Date(),end: new Date()}],
      multDateEdit: [],
      printLoading: true,
      printObj: {
            id: "printCustom",
        },
      filterSetting: ["name", "name_e"],
      errors: {},
      isCheckAll: false,
      checkAll: [],
      is_disabled: false,
      current_page: 1,
      Tooltip: "",
      mouseEnter: null,
    };
  },
  validations: {
    create: {
      installment_payment_plan_id: { required },
        payment_plan_installments: {
          required,
          $each: {
              installment_payment_type_id: { required },
              installment_status_id: { required },
              doc_type_id: { required },
              v_date: { required },
              due_date: { required },
              total_amount: { required, decimal,minValue: minValue(0.1) },
              paid_amount: { required, decimal,minValue: minValue(0.1) },
              day_month: {required,integer,between: between(1,30)},
              ref_id: { required, decimal,minValue: minValue(0.1) },
              rp_code: { required, decimal,minValue: minValue(0.1) },
              is_fixed: { required, integer },
              note_a: {required,maxLength: maxLength(255)},
              note_e: {required,maxLength: maxLength(255)}
          }
      },
    },
    edit: {
      installment_payment_plan_id: { required },
        payment_plan_installments: {
            required,
            $each: {
                installment_payment_type_id: { required },
                installment_status_id: { required },
                doc_type_id: { required },
                v_date: { required },
                due_date: { required },
                total_amount: { required, decimal,minValue: minValue(0.1) },
                paid_amount: { required, decimal,minValue: minValue(0.1) },
                day_month: {required,integer,between: between(1,30)},
                ref_id: { required, decimal,minValue: minValue(0.1) },
                rp_code: { required, decimal,minValue: minValue(0.1) },
                is_fixed: { required, integer },
                note_a: {required,maxLength: maxLength(255)},
                note_e: {required,maxLength: maxLength(255)}
            }
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
        this.payment_plan_installments.forEach((el) => {
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
    await this.getData();
  },
  methods: {
      isPermission(item) {
          if (this.$store.state.auth.type == 'user'){
              return this.$store.state.auth.permissions.includes(item)
          }
          return true;
      },
    arabicValueNoteCreate(txt,index){
          this.create.payment_plan_installments[index].note_a = arabicValue(txt);
    },
    arabicValueNoteEdit(txt,index){
          this.edit.payment_plan_installments[index].note_a = arabicValue(txt);
    },
    englishValueNoteCreate(txt,index){
          this.create.payment_plan_installments[index].note_e = englishValue(txt);
    },
    englishValueNoteEdit(txt,index){
          this.edit.payment_plan_installments[index].note_e = englishValue(txt);
    },
    addNewField(){
          this.create.payment_plan_installments.push({
                  installment_payment_type_id: null,
                  v_date: formatDateTime(new Date()),
                  due_date: formatDateTime(new Date()),
                  total_amount: 0,
                  paid_amount: 0,
                  installment_status_id: null,
                  doc_type_id: null,
                  ref_id: null,
                  rp_code: '',
                  day_month: 1,
                  is_fixed: 0,
                  note_a: '',
                  note_e: ''
              });
          this.multDate.push({start: new Date(),end: new Date()});
      },
    removeNewField(index){
          if(this.create.payment_plan_installments.length > 1){
              this.create.payment_plan_installments.splice(index,1);
              this.multDate.splice(index,1);
          }
      },
    addNewFieldEdit(){
        this.multDateEdit.push({start: new Date(),end: new Date()});
          this.edit.payment_plan_installments.push({
              installment_payment_type_id: null,
              v_date: formatDateTime(new Date()),
              due_date: formatDateTime(new Date()),
              total_amount: 0,
              paid_amount: 0,
              installment_status_id: null,
              doc_type_id: null,
              ref_id: null,
              rp_code: '',
              day_month: 1,
              is_fixed: 0,
              note_a: '',
              note_e: ''
          });
      },
    removeNewFieldEdit(index){
          if(this.edit.payment_plan_installments.length > 1){
              this.edit.payment_plan_installments.splice(index,1);
              this.multDateEdit.splice(index,1);
          }
      },
    showInstallmentStatusModal(index) {
      if (this.create.payment_plan_installments[index].installment_status_id == 0) {
        this.$bvModal.show("installment-payment-create");
        this.create.payment_plan_installments[index].installment_status_id = null;
      }
    },
    showInstallmentStatusModalEdit(index) {
      if (this.edit.payment_plan_installments[index].installment_status_id == 0) {
        this.$bvModal.show("installment-payment-create");
        this.edit.payment_plan_installments[index].installment_status_id = null;
      }
    },
    showInstallmentPlanModal() {
      if (this.create.installment_payment_plan_id == 0) {
        this.$bvModal.show("installment-payment-plan-create");
        this.create.installment_payment_plan_id = null;
      }
    },
    showInstallmentPlanModalEdit() {
      if (this.edit.installment_payment_plan_id == 0) {
        this.$bvModal.show("installment-payment-plan-create");
        this.edit.installment_payment_plan_id = null;
      }
    },
    v_dateCreate(e,index) {
      if (e) {
        this.create.payment_plan_installments[index].v_date = formatDateTime(e);
      } else {
          this.create.payment_plan_installments[index].v_date = null;
      }
    },
    due_dateCreate(e,index) {
      if (e) {
        this.create.payment_plan_installments[index].due_date = formatDateTime(e);
      } else {
        this.create.payment_plan_installments[index].due_date = null;
      }
    },
    v_dateEdit(e,index) {
          if (e) {
              this.edit.payment_plan_installments[index].v_date = formatDateTime(e);
          } else {
              this.edit.payment_plan_installments[index].v_date = null;
          }
      },
    due_dateEdit(e,index) {
          if (e) {
              this.edit.payment_plan_installments[index].due_date = formatDateTime(e);
          } else {
              this.edit.payment_plan_installments[index].due_date = null;
          }
      },
    /**
     *  get Data payment_plan_installments
     */
    async getData(page = 1) {
      this.isLoader = true;
      let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
            filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }
      await adminApi
        .get(
          `/recievable-payable/rp_installment_p_plan?plan_installments=true&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.payment_plan_installments = l.data;
          this.payemntPlanInstallmentsPagination = l.pagination;
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
        this.current_page <= this.payemntPlanInstallmentsPagination.last_page &&
        this.current_page != this.payemntPlanInstallmentsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let filter = "";
          for (let i = 0; i < this.filterSetting.length; ++i) {
              filter += `columns[${i}]=${this.filterSetting[i]}&`;
          }

        adminApi
          .get(
            `recievable-payable/rp_installment_p_plan?plan_installments=true&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.payment_plan_installments = l.data;
            this.payemntPlanInstallmentsPagination = l.pagination;
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
              .post(`recievable-payable/rp_payment_plan_installment/bulk-delete`, {
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
              .delete(`recievable-payable/rp_payment_plan_installment/${id}`)
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
    resetModalHidden() {
      this.create = {
        installment_payment_plan_id: null,
          payment_plan_installments:
              [
                  {
                      installment_payment_type_id: null,
                      v_date: '',
                      due_date: '',
                      total_amount: 0,
                      paid_amount: 0,
                      Installment_status_id: null,
                      doc_type_id: null,
                      ref_id: null,
                      rp_code: '',
                      day_month: 1,
                      is_fixed: 0,
                      note_a: '',
                      note_e: ''
                  }
              ]
      };
      this.installment_payment_plans = [];
      this.installment_statuses = []
      this.documentTypes = [];
      this. payment_types = [];
     this.multDate = [{start: new Date(),end: new Date()}];
      this.$nextTick(() => {
        this.$v.$reset();
      });
        this.is_disabled = false;
      this.errors = {};
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      await this.getInstallmentPaymentPlans();
      await this.getInstallmentStatuses();
      await this.getInstallPaymentTypes();
      await this.getDocumentTypes();
      this.create = {
          installment_payment_plan_id: null,
          payment_plan_installments:
              [
                  {
                      installment_payment_type_id: null,
                      v_date: formatDateTime(new Date()),
                      due_date: formatDateTime(new Date()),
                      total_amount: 0,
                      paid_amount: 0,
                      installment_status_id: null,
                      doc_type_id: null,
                      ref_id: null,
                      rp_code: '',
                      day_month: 1,
                      is_fixed: 0,
                      note_a: '',
                      note_e: ''
                  }
              ]
      };
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
    },
    /**
     *  create screen
     */
    resetForm() {
      this.create = {
        installment_payment_plan_id: null,
          payment_plan_installments:
              [
                  {
                      installment_payment_type_id: null,
                      v_date: formatDateTime(new Date()),
                      due_date: formatDateTime(new Date()),
                      total_amount: 0,
                      paid_amount: 0,
                      installment_status_id: null,
                      doc_type_id: null,
                      ref_id: null,
                      rp_code: '',
                      day_month: 1,
                      is_fixed: 0,
                      note_a: '',
                      note_e: ''
                  }
              ]
      };
      this.multDate = [{start: new Date(),end: new Date()}];
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
    },
    AddSubmit() {
      if (this.$v.create.$invalid) {
        this.$v.create.$touch();
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.is_disabled = false;
        this.create.payment_plan_installments.map(e => e.installment_payment_plan_id = this.create.installment_payment_plan_id);
          adminApi
          .post(`recievable-payable/rp_payment_plan_installment`, this.create)
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
     *  edit screen
     */
    editSubmit(id) {
      this.$v.edit.$touch();
      if (this.$v.edit.$invalid) {
        return;
      } else {
        this.isLoader = true;
        this.errors = {};
        this.edit.payment_plan_installments.map(e => e.installment_payment_plan_id = this.create.installment_payment_plan_id);
          adminApi
          .put(`recievable-payable/rp_payment_plan_installment/${id}`, this.edit)
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
    async getInstallmentPaymentPlans() {
      await adminApi
        .get(`recievable-payable/rp_installment_p_plan?null_payment_plan_installment=true`)
        .then((res) => {
          let l = res.data.data;
            if(this.isPermission('create paymentPlan RP')){
                l.unshift({
                    id: 0,
                    name: "اضف خطة دفع",
                    name_e: "Add installment payment plan",
                });
            }
          this.installment_payment_plans = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    async getInstallPaymentPlansEdit() {
          this.isLoader = true;

          await adminApi
              .get(`/recievable-payable/rp_installment_p_plan`)
              .then((res) => {
                  let l = res.data.data;
                  this.installment_payment_plans = l;
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
    async getInstallmentStatuses() {
      this.isLoader = true;
      await adminApi
        .get(`recievable-payable/rp_installment_status`)
        .then((res) => {
          this.isLoader = false;
          let l = res.data.data;
          if(this.isPermission('create status RP')){
              l.unshift({
                  id: 0,
                  name: "اضف حالة الدفع",
                  name_e: "Add installment payment status",
              });
          }
          this.installment_statuses = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    async getDocumentTypes() {
      await adminApi
        .get(`/document`)
        .then((res) => {
            let l = res.data.data;
          this.documentTypes = l;
        })
        .catch((err) => {
          Swal.fire({
            icon: "error",
            title: `${this.$t("general.Error")}`,
            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
          });
        });
    },
    async getInstallPaymentTypes() {
          this.isLoader = true;

          await adminApi
              .get(`/recievable-payable/rp_installment_payment_types`)
              .then((res) => {
                  let l = res.data.data;
                  if(this.isPermission('create paymentType RP')){
                      l.unshift({
                          id: 0,
                          name: "اضف نوع دفع",
                          name_e: "Add installment payment type",
                      });
                  }
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
    /**
     *   show Modal (edit)
     */
    async resetModalEdit(id) {
      let paymentPlanInstallment = this.payment_plan_installments.find((e) => id == e.id);
      await this.getInstallPaymentPlansEdit();
      await this.getInstallmentStatuses();
      await this.getInstallPaymentTypes();
      await this.getDocumentTypes();
      this.edit.installment_payment_plan_id = paymentPlanInstallment.payment_plan_installments[0].installment_payment_plan_id;
      paymentPlanInstallment.payment_plan_installments.forEach((e) => {
          this.multDateEdit.push({start: new Date(e.v_date),end: new Date(e.due_date)});
          this.edit.payment_plan_installments.push({
              installment_payment_type_id: e.installment_payment_type_id,
              installment_status_id : e.installment_status_id,
              v_date : e.v_date,
              due_date : e.due_date,
              total_amount : e.total_amount,
              paid_amount : e.paid_amount,
              doc_type_id : e.doc_type_id,
              ref_id : e.ref_id,
              rp_code : e.rp_code,
              day_month: e.day_month,
              is_fixed: e.is_fixed,
              note_a: e.note_a,
              note_e: e.note_e
          });
      });
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.errors = {};
      this.installment_payment_plans = [];
      this.installment_statuses = []
      this.documentTypes = [];
      this. payment_types = [];
      this.edit = {
        installment_payment_plan_id: null,
          payment_plan_installments: []
      };
    },

    /**
     *  start  dynamicSortString
     */
    sortString(value) {
      return dynamicSortString(value);
    },
    sortNumber(value) {
      return dynamicSortNumber(value);
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
          .get(`recievable-payable/rp_payment_plan_installment/logs/${id}`)
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
                  XLSX.writeFile(wb, fn || (('PaymentPlan' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
              }
              this.enabled3 = true;
          }, 100);
      },
    showInstallmentPaymentTypeModal(index) {
          if (this.create.payment_plan_installments[index].installment_payment_type_id == 0) {
              this.$bvModal.show("installment_payment_type_create");
              this.create.payment_plan_installments[index].installment_payment_type_id = null;
          }
      },
    showInstallmentPaymentTypeModalEdit(index) {
          if (this.edit.payment_plan_installments[index].installment_payment_type_id == 0) {
              this.$bvModal.show("installment_payment_type_create");
              this.edit.payment_plan_installments[index].installment_payment_type_id = null;
          }
      },
    showDocumentModal(index) {
          if (this.create.payment_plan_installments[index].doc_type_id == 0) {
              this.$bvModal.show("create-document");
              this.create.payment_plan_installments[index].doc_type_id = null;
          }
      },
    showDocumentModalEdit(index) {
          if (this.edit.payment_plan_installments[index].doc_type_id == 0) {
              this.$bvModal.show("create-document");
              this.edit.payment_plan_installments[index].doc_type_id = null;
          }
      },
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <InstallmentStatus
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getInstallmentStatuses"
    />
    <InstallmentPlan
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getInstallmentPaymentPlans"
    />
    <InstallmentPaymentType
      :companyKeys="companyKeys"
      :defaultsKeys="defaultsKeys"
      @created="getInstallPaymentTypes"
    />

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">
                {{ $t("general.paymentPlanInstallmentsTable") }}
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

            <div class="row justify-content-between align-items-center mb-2 px-1">
              <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                <b-button
                    v-if="isPermission('create paymentPlanInstallment RP')"
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
                  <button class="custom-btn-dowonload" v-print="'#printCustom'">
                    <i class="fe-printer"></i>
                  </button>
                  <button
                    class="custom-btn-dowonload"
                    @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                    v-if="checkAll.length == 1 && isPermission('update paymentPlanInstallment RP')"
                  >
                    <i class="mdi mdi-square-edit-outline"></i>
                  </button>
                  <!-- start mult delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length > 1 && isPermission('delete paymentPlanInstallment RP')"
                    @click.prevent="deleteScreenButton(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1 && isPermission('delete paymentPlanInstallment RP')"
                    @click.prevent="deleteScreenButton(checkAll[0])"
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
                      <a href="javascript:void(0)" class="btn btn-primary btn-sm">{{
                        $t("general.Apply")
                      }}</a>
                    </div>
                  </b-dropdown>
                  <!-- Basic dropdown -->
                  <!-- start Pagination -->
                  <div class="d-inline-flex align-items-center pagination-custom">
                    <div class="d-inline-block" style="font-size: 15px">
                      {{ payemntPlanInstallmentsPagination.from }}-{{
                        payemntPlanInstallmentsPagination.to
                      }}
                      /
                      {{ payemntPlanInstallmentsPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            payemntPlanInstallmentsPagination.current_page == 1
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(payemntPlanInstallmentsPagination.current_page - 1)
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
                            payemntPlanInstallmentsPagination.last_page ==
                            payemntPlanInstallmentsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(payemntPlanInstallmentsPagination.current_page + 1)
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
              :title="getCompanyKey('payment_plan_installment_create_form')"
              title-class="font-18"
              body-class="p-4 "
              dialog-class="modal-full-width"
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
                    @click.prevent="$bvModal.hide(`create`)"
                  >
                    {{ $t("general.Cancel") }}
                  </b-button>
                </div>
                <div class="row">
                  <div class="col-lg-4">
                    <div class="form-group">
                      <label class="my-1 mr-2">{{
                        getCompanyKey("installment_payment_plan")
                      }}</label>
                      <multiselect
                        @input="showInstallmentPlanModal"
                        v-model="create.installment_payment_plan_id"
                        :options="installment_payment_plans.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            $i18n.locale == 'ar'
                              ? installment_payment_plans.find((x) => x.id == opt).name
                              : installment_payment_plans.find((x) => x.id == opt).name_e
                        "
                        :class="{
                          'is-invalid':
                            $v.create.installment_payment_plan_id.$error ||
                            errors.installment_payment_plan_id,
                        }"
                      >
                      </multiselect>
                      <div
                        v-if="!$v.create.installment_payment_plan_id.required"
                        class="invalid-feedback"
                      >
                        {{ $t("general.fieldIsRequired") }}
                      </div>

                      <template v-if="errors.installment_payment_plan_id">
                        <ErrorMessage
                          v-for="(
                            errorMessage, index
                          ) in errors.installment_payment_plan_id"
                          :key="index"
                          >{{ errorMessage }}</ErrorMessage
                        >
                      </template>
                    </div>
                  </div>
                </div>
                <div style="height: 350px; overflow-x: scroll;">
                    <template>
                        <div class="row" v-for="(item,index) in create.payment_plan_installments">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{
                                            getCompanyKey("installment_payment_type_id")
                                        }}</label>
                                    <multiselect
                                        @input="showInstallmentPaymentTypeModal(index)"
                                        v-model="create.payment_plan_installments[index].installment_payment_type_id"
                                        :options="payment_types.map((type) => type.id)"
                                        :custom-label="
                                      (opt) =>
                                        $i18n.locale == 'ar'
                                          ? payment_types.find((x) => x.id == opt).name
                                          : payment_types.find((x) => x.id == opt).name_e
                                    "
                                    >
                                    </multiselect>
                                    <div
                                        v-if="!$v.create.payment_plan_installments.$each[index].installment_payment_type_id.required"
                                        class="invalid-feedback"
                                    >
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
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
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>{{
                                            getCompanyKey("installment_status")
                                        }}</label>
                                    <multiselect
                                        @input="showInstallmentStatusModal(index)"
                                        v-model="create.payment_plan_installments[index].installment_status_id"
                                        :options="installment_statuses.map((type) => type.id)"
                                        :custom-label="
                                          (opt) =>
                                            $i18n.locale == 'ar'
                                              ? installment_statuses.find((x) => x.id == opt).name
                                              : installment_statuses.find((x) => x.id == opt).name_e
                                        "
                                    >
                                    </multiselect>
                                    <div
                                        v-if="!$v.create.payment_plan_installments.$each[index].installment_status_id.required"
                                        class="invalid-feedback"
                                    >
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>

                                    <template v-if="errors.installment_status_id">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.installment_status_id"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>{{ getCompanyKey("doc_type") }}</label>
                                    <multiselect
                                        @input="showDocumentModal(index)"
                                        v-model="create.payment_plan_installments[index].doc_type_id"
                                        :options="documentTypes.map((type) => type.id)"
                                        :custom-label="
                                          (opt) =>
                                            $i18n.locale == 'ar'
                                              ? documentTypes.find((x) => x.id == opt).name
                                              : documentTypes.find((x) => x.id == opt).name_e
                                        "
                                    >
                                    </multiselect>
                                    <div v-if="!$v.create.payment_plan_installments.$each[index].doc_type_id.required" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>

                                    <template v-if="errors.doc_type_id">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.doc_type_id"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("installment_payment_plan_v_date") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <date-picker
                                        type="date"
                                        v-model="multDate[index].start"
                                        :default-value="new Date()"
                                        @change="v_dateCreate($event,index)"
                                        confirm
                                    ></date-picker>
                                    <div v-if="!$v.create.payment_plan_installments.$each[index].v_date.required" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.v_date">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.v_date"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("installment_payment_plan_due_date") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <date-picker
                                        type="date"
                                        v-model="multDate[index].end"
                                        :default-value="new Date()"
                                        @change="due_dateCreate($event,index)"
                                        confirm
                                    ></date-picker>
                                    <div v-if="!$v.create.payment_plan_installments.$each[index].due_date.required" class="invalid-feedback">
                                        {{ $t("general.fieldIsRequired") }}
                                    </div>
                                    <template v-if="errors.due_date">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.due_date"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("installment_payment_plan_total_amount") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        data-create="9"
                                        step="0.1"
                                        v-model="$v.create.payment_plan_installments.$each[index].total_amount.$model"
                                        :class="{
                                          'is-invalid':
                                            $v.create.payment_plan_installments.$each[index].total_amount.$error || errors.total_amount,
                                          'is-valid':
                                            !$v.create.payment_plan_installments.$each[index].total_amount.$invalid && !errors.total_amount,
                                        }"
                                    />
                                    <template v-if="errors.total_amount">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.total_amount"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("installment_payment_plan_paid_amount") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        step="0.1"
                                        v-model="$v.create.payment_plan_installments.$each[index].paid_amount.$model"
                                        :class="{
                                          'is-invalid':
                                            $v.create.payment_plan_installments.$each[index].paid_amount.$error || errors.paid_amount,
                                          'is-valid':
                                            !$v.create.payment_plan_installments.$each[index].paid_amount.$invalid && !errors.paid_amount,
                                        }"
                                    />
                                    <template v-if="errors.paid_amount">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.paid_amount"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("ref_id") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        step="0.1"
                                        v-model="$v.create.payment_plan_installments.$each[index].ref_id.$model"
                                        :class="{
                                          'is-invalid': $v.create.payment_plan_installments.$each[index].ref_id.$error || errors.ref_id,
                                          'is-valid': !$v.create.payment_plan_installments.$each[index].ref_id.$invalid && !errors.ref_id,
                                        }"
                                    />
                                    <template v-if="errors.ref_id">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.ref_id"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("rp_code") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        step="0.1"
                                        v-model="$v.create.payment_plan_installments.$each[index].rp_code.$model"
                                        :class="{
                                          'is-invalid': $v.create.payment_plan_installments.$each[index].rp_code.$error || errors.rp_code,
                                          'is-valid': !$v.create.payment_plan_installments.$each[index].rp_code.$invalid && !errors.rp_code,
                                        }"
                                    />
                                    <template v-if="errors.rp_code">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.rp_code"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("day_mounth") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        min="1"
                                        max="30"
                                        v-model="$v.create.payment_plan_installments.$each[index].day_month.$model"
                                        :class="{
                                          'is-invalid': $v.create.payment_plan_installments.$each[index].day_month.$error || errors.day_month,
                                          'is-valid': !$v.create.payment_plan_installments.$each[index].day_month.$invalid && !errors.day_month,
                                        }"
                                    />
                                    <template v-if="errors.day_month">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.day_month"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("installment_payment_plan_note_a") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        @input="arabicValueNoteCreate(create.payment_plan_installments[index].note_a,index)"
                                        type="text"
                                        class="form-control"
                                        v-model="$v.create.payment_plan_installments.$each[index].note_a.$model"
                                        :class="{
                                          'is-invalid': $v.create.payment_plan_installments.$each[index].note_a.$error || errors.note_a,
                                          'is-valid': !$v.create.payment_plan_installments.$each[index].note_a.$invalid && !errors.note_a,
                                        }"
                                    />
                                    <template v-if="errors.note_a">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.note_a"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ getCompanyKey("installment_payment_plan_note_e") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        @input="englishValueNoteCreate(create.payment_plan_installments[index].note_e,index)"
                                        type="text"
                                        class="form-control"
                                        v-model="$v.create.payment_plan_installments.$each[index].note_e.$model"
                                        :class="{
                                          'is-invalid': $v.create.payment_plan_installments.$each[index].note_e.$error || errors.note_e,
                                          'is-valid': !$v.create.payment_plan_installments.$each[index].note_e.$invalid && !errors.note_e,
                                        }"
                                    />
                                    <template v-if="errors.note_e">
                                        <ErrorMessage
                                            v-for="(errorMessage, index) in errors.note_e"
                                            :key="index"
                                        >{{ errorMessage }}</ErrorMessage
                                        >
                                    </template>
                                </div>
                            </div>
                            <div class="col-md-2" >
                                <div class="form-group">
                                    <label class="mr-2">
                                        {{ getCompanyKey("is_fixed") }}
                                        <span class="text-danger">*</span>
                                    </label>
                                    <b-form-group
                                        :class="{
                                  'is-invalid': $v.create.payment_plan_installments.$each[index].is_fixed.$error || errors.is_fixed,
                                  'is-valid': !$v.create.payment_plan_installments.$each[index].is_fixed.$invalid && !errors.is_fixed,
                                }"
                                    >
                                        <b-form-radio
                                            class="d-inline-block"
                                            v-model="$v.create.payment_plan_installments.$each[index].is_fixed.$model"
                                            :name="`some-radios-${index}`"
                                            value="1"
                                        >{{ $t("general.Yes") }}</b-form-radio
                                        >
                                        <b-form-radio
                                            class="d-inline-block m-1"
                                            v-model="$v.create.payment_plan_installments.$each[index].is_fixed.$model"
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
                                    v-if="(create.payment_plan_installments.length - 1) == index"
                                    type="button"
                                    @click.prevent="addNewField"
                                    class="custom-btn-dowonload"
                                >
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button
                                    v-if="create.payment_plan_installments.length > 1"
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
                                    @click="payment_plan_installments.sort(sortString('name'))"
                                ></i>
                                <i
                                    class="fas fa-arrow-down"
                                    @click="payment_plan_installments.sort(sortString('-name'))"
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
                                    @click="payment_plan_installments.sort(sortString('name_e'))"
                                ></i>
                                <i
                                    class="fas fa-arrow-down"
                                    @click="payment_plan_installments.sort(sortString('-name_e'))"
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
                <tbody v-if="payment_plan_installments.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="isPermission('update paymentPlanInstallment RP')?
                    $bvModal.show(`modal-edit-${data.id}`): false"
                    v-for="(data, index) in payment_plan_installments"
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
                          {{ data.count_payment_plan_installment }}
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
                            v-if="isPermission('update paymentPlanInstallment RP')"
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
                            v-if="isPermission('delete paymentPlanInstallment RP')"
                            class="dropdown-item text-black"
                            href="#"
                            @click.prevent="deleteScreenButton(data.id)"
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
                        :title="getCompanyKey('payment_plan_installment_edit_form')"
                        title-class="font-18"
                        body-class="p-4"
                        dialog-class="modal-full-width"
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
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="my-1 mr-2">{{
                                                getCompanyKey("installment_payment_plan")
                                            }}</label>
                                        <multiselect
                                            :disabled="true"
                                            v-model="edit.installment_payment_plan_id"
                                            :options="installment_payment_plans.map((type) => type.id)"
                                            :custom-label="
                                              (opt) =>
                                                $i18n.locale == 'ar'
                                                  ? installment_payment_plans.find((x) => x.id == opt).name
                                                  : installment_payment_plans.find((x) => x.id == opt).name_e
                                            "
                                            :class="{
                                              'is-invalid':
                                                $v.create.installment_payment_plan_id.$error ||
                                                errors.installment_payment_plan_id,
                                            }"
                                        >
                                        </multiselect>
                                        <div
                                            v-if="!$v.create.installment_payment_plan_id.required"
                                            class="invalid-feedback"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>

                                        <template v-if="errors.installment_payment_plan_id">
                                            <ErrorMessage
                                                v-for="(
                                                        errorMessage, index
                                                      ) in errors.installment_payment_plan_id"
                                                :key="index"
                                            >{{ errorMessage }}</ErrorMessage
                                            >
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div style="height: 350px; overflow-x: scroll;">
                                <template>
                                    <div class="row" v-for="(item,index) in edit.payment_plan_installments">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>{{
                                                        getCompanyKey("installment_payment_type_id")
                                                    }}</label>
                                                <multiselect
                                                    @input="showInstallmentPaymentTypeModalEdit(index)"
                                                    v-model="edit.payment_plan_installments[index].installment_payment_type_id"
                                                    :options="payment_types.map((type) => type.id)"
                                                    :custom-label="
                                                      (opt) =>
                                                        $i18n.locale == 'ar'
                                                          ? payment_types.find((x) => x.id == opt).name
                                                          : payment_types.find((x) => x.id == opt).name_e
                                                    "
                                                >
                                                </multiselect>
                                                <div
                                                    v-if="!$v.create.payment_plan_installments.$each[index].installment_payment_type_id.required"
                                                    class="invalid-feedback"
                                                >
                                                    {{ $t("general.fieldIsRequired") }}
                                                </div>
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
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label>{{
                                                        getCompanyKey("installment_status")
                                                    }}</label>
                                                <multiselect
                                                    @input="showInstallmentStatusModalEdit(index)"
                                                    v-model="edit.payment_plan_installments[index].installment_status_id"
                                                    :options="installment_statuses.map((type) => type.id)"
                                                    :custom-label="
                                                      (opt) =>
                                                        $i18n.locale == 'ar'
                                                          ? installment_statuses.find((x) => x.id == opt).name
                                                          : installment_statuses.find((x) => x.id == opt).name_e
                                                    "
                                                >
                                                </multiselect>
                                                <div
                                                    v-if="!$v.create.payment_plan_installments.$each[index].installment_status_id.required"
                                                    class="invalid-feedback"
                                                >
                                                    {{ $t("general.fieldIsRequired") }}
                                                </div>

                                                <template v-if="errors.installment_status_id">
                                                    <ErrorMessage
                                                        v-for="(errorMessage, index) in errors.installment_status_id"
                                                        :key="index"
                                                    >{{ errorMessage }}</ErrorMessage
                                                    >
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label>{{ getCompanyKey("doc_type") }}</label>
                                                <multiselect
                                                    @input="showDocumentModalEdit(index)"
                                                    v-model="edit.payment_plan_installments[index].doc_type_id"
                                                    :options="documentTypes.map((type) => type.id)"
                                                    :custom-label="
                                                      (opt) =>
                                                        $i18n.locale == 'ar'
                                                          ? documentTypes.find((x) => x.id == opt).name
                                                          : documentTypes.find((x) => x.id == opt).name_e
                                                    "
                                                >
                                                </multiselect>
                                                <div v-if="!$v.create.payment_plan_installments.$each[index].doc_type_id.required" class="invalid-feedback">
                                                    {{ $t("general.fieldIsRequired") }}
                                                </div>

                                                <template v-if="errors.doc_type_id">
                                                    <ErrorMessage
                                                        v-for="(errorMessage, index) in errors.doc_type_id"
                                                        :key="index"
                                                    >{{ errorMessage }}</ErrorMessage
                                                    >
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ getCompanyKey("installment_payment_plan_v_date") }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <date-picker
                                                    type="date"
                                                    v-model="multDateEdit[index].start"
                                                    @change="v_dateEdit($event,index)"
                                                    confirm
                                                ></date-picker>
                                                <div v-if="!$v.edit.payment_plan_installments.$each[index].v_date.required" class="invalid-feedback">
                                                    {{ $t("general.fieldIsRequired") }}
                                                </div>
                                                <template v-if="errors.v_date">
                                                    <ErrorMessage
                                                        v-for="(errorMessage, index) in errors.v_date"
                                                        :key="index"
                                                    >{{ errorMessage }}</ErrorMessage
                                                    >
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ getCompanyKey("installment_payment_plan_due_date") }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <date-picker
                                                    type="date"
                                                    v-model="multDateEdit[index].end"
                                                    @change="due_dateEdit($event,index)"
                                                    confirm
                                                ></date-picker>
                                                <div v-if="!$v.edit.payment_plan_installments.$each[index].due_date.required" class="invalid-feedback">
                                                    {{ $t("general.fieldIsRequired") }}
                                                </div>
                                                <template v-if="errors.due_date">
                                                    <ErrorMessage
                                                        v-for="(errorMessage, index) in errors.due_date"
                                                        :key="index"
                                                    >{{ errorMessage }}</ErrorMessage
                                                    >
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ getCompanyKey("installment_payment_plan_total_amount") }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    step="0.1"
                                                    v-model="$v.edit.payment_plan_installments.$each[index].total_amount.$model"
                                                    :class="{
                                                      'is-invalid':
                                                        $v.edit.payment_plan_installments.$each[index].total_amount.$error || errors.total_amount,
                                                      'is-valid':
                                                        !$v.edit.payment_plan_installments.$each[index].total_amount.$invalid && !errors.total_amount,
                                                    }"
                                                />
                                                <template v-if="errors.total_amount">
                                                    <ErrorMessage
                                                        v-for="(errorMessage, index) in errors.total_amount"
                                                        :key="index"
                                                    >{{ errorMessage }}</ErrorMessage
                                                    >
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ getCompanyKey("installment_payment_plan_paid_amount") }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    step="0.1"
                                                    v-model="$v.edit.payment_plan_installments.$each[index].paid_amount.$model"
                                                    :class="{
                                          'is-invalid':
                                            $v.edit.payment_plan_installments.$each[index].paid_amount.$error || errors.paid_amount,
                                          'is-valid':
                                            !$v.edit.payment_plan_installments.$each[index].paid_amount.$invalid && !errors.paid_amount,
                                        }"
                                                />
                                                <template v-if="errors.paid_amount">
                                                    <ErrorMessage
                                                        v-for="(errorMessage, index) in errors.paid_amount"
                                                        :key="index"
                                                    >{{ errorMessage }}</ErrorMessage
                                                    >
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ getCompanyKey("ref_id") }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    step="0.1"
                                                    v-model="$v.edit.payment_plan_installments.$each[index].ref_id.$model"
                                                    :class="{
                                          'is-invalid': $v.edit.payment_plan_installments.$each[index].ref_id.$error || errors.ref_id,
                                          'is-valid': !$v.edit.payment_plan_installments.$each[index].ref_id.$invalid && !errors.ref_id,
                                        }"
                                                />
                                                <template v-if="errors.ref_id">
                                                    <ErrorMessage
                                                        v-for="(errorMessage, index) in errors.ref_id"
                                                        :key="index"
                                                    >{{ errorMessage }}</ErrorMessage
                                                    >
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ getCompanyKey("rp_code") }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    step="0.1"
                                                    v-model="$v.edit.payment_plan_installments.$each[index].rp_code.$model"
                                                    :class="{
                                          'is-invalid': $v.edit.payment_plan_installments.$each[index].rp_code.$error || errors.rp_code,
                                          'is-valid': !$v.edit.payment_plan_installments.$each[index].rp_code.$invalid && !errors.rp_code,
                                        }"
                                                />
                                                <template v-if="errors.rp_code">
                                                    <ErrorMessage
                                                        v-for="(errorMessage, index) in errors.rp_code"
                                                        :key="index"
                                                    >{{ errorMessage }}</ErrorMessage
                                                    >
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ getCompanyKey("day_mounth") }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    type="number"
                                                    class="form-control"
                                                    min="1"
                                                    max="30"
                                                    v-model="$v.edit.payment_plan_installments.$each[index].day_month.$model"
                                                    :class="{
                                          'is-invalid': $v.edit.payment_plan_installments.$each[index].day_month.$error || errors.day_month,
                                          'is-valid': !$v.edit.payment_plan_installments.$each[index].day_month.$invalid && !errors.day_month,
                                        }"
                                                />
                                                <template v-if="errors.day_month">
                                                    <ErrorMessage
                                                        v-for="(errorMessage, index) in errors.day_month"
                                                        :key="index"
                                                    >{{ errorMessage }}</ErrorMessage
                                                    >
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ getCompanyKey("installment_payment_plan_note_a") }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    @input="arabicValueNoteEdit(edit.payment_plan_installments[index].note_a,index)"
                                                    type="text"
                                                    class="form-control"
                                                    v-model="$v.edit.payment_plan_installments.$each[index].note_a.$model"
                                                    :class="{
                                          'is-invalid': $v.edit.payment_plan_installments.$each[index].note_a.$error || errors.note_a,
                                          'is-valid': !$v.edit.payment_plan_installments.$each[index].note_a.$invalid && !errors.note_a,
                                        }"
                                                />
                                                <template v-if="errors.note_a">
                                                    <ErrorMessage
                                                        v-for="(errorMessage, index) in errors.note_a"
                                                        :key="index"
                                                    >{{ errorMessage }}</ErrorMessage
                                                    >
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ getCompanyKey("installment_payment_plan_note_e") }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    @input="englishValueNoteEdit(edit.payment_plan_installments[index].note_e,index)"
                                                    type="text"
                                                    class="form-control"
                                                    v-model="$v.edit.payment_plan_installments.$each[index].note_e.$model"
                                                    :class="{
                                          'is-invalid': $v.edit.payment_plan_installments.$each[index].note_e.$error || errors.note_e,
                                          'is-valid': !$v.edit.payment_plan_installments.$each[index].note_e.$invalid && !errors.note_e,
                                        }"
                                                />
                                                <template v-if="errors.note_e">
                                                    <ErrorMessage
                                                        v-for="(errorMessage, index) in errors.note_e"
                                                        :key="index"
                                                    >{{ errorMessage }}</ErrorMessage
                                                    >
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-md-2" >
                                            <div class="form-group">
                                                <label class="mr-2">
                                                    {{ getCompanyKey("is_fixed") }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <b-form-group
                                                    :class="{
                                  'is-invalid': $v.edit.payment_plan_installments.$each[index].is_fixed.$error || errors.is_fixed,
                                  'is-valid': !$v.edit.payment_plan_installments.$each[index].is_fixed.$invalid && !errors.is_fixed,
                                }"
                                                >
                                                    <b-form-radio
                                                        class="d-inline-block"
                                                        v-model="$v.edit.payment_plan_installments.$each[index].is_fixed.$model"
                                                        :name="`some-radios-${index}`"
                                                        value="1"
                                                    >{{ $t("general.Yes") }}</b-form-radio
                                                    >
                                                    <b-form-radio
                                                        class="d-inline-block m-1"
                                                        v-model="$v.edit.payment_plan_installments.$each[index].is_fixed.$model"
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
                                                v-if="(edit.payment_plan_installments.length - 1) == index"
                                                type="button"
                                                @click.prevent="addNewFieldEdit"
                                                class="custom-btn-dowonload"
                                            >
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <button
                                                v-if="edit.payment_plan_installments.length > 1"
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
                    <th class="text-center" colspan="14">
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
