<script>
import adminApi from "../../../../api/adminAxios";
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import {minValue, required} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../../components/widgets/errorMessage";
import loader from "../../../../components/general/loader";
import {dynamicSortString} from "../../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import {formatDateOnly} from "../../../../helper/startDate";
import translation from "../../../../helper/mixin/translation-mixin";
import Saleman from "../../../../components/create/general/saleman.vue";
import customerGeneral from "../../../../components/create/general/customerGeneral";
import Branch from "../../../../components/create/general/branch";
import DatePicker from "vue2-datepicker";
import permissionGuard from "../../../../helper/permission";


export default {
    page: {
        title: "receipt voucher",
        meta: [{name: "receipt voucher", content: 'receipt voucher'}],
    },
    mixins: [translation],
    components: {
        Branch,
        Saleman,
        ErrorMessage,
        loader,
        Multiselect,
        customerGeneral,
        Layout,
        PageHeader,
        DatePicker
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Receipt Voucher RP", "all receiptVoucher RP");
    });

    },
    data() {
        return {
            per_page: 50,
            search: "",
            debounce: {},
            invoicesPagination: {},
            invoices: [],
            customers: [],
            salesmen: [],
            paymentMethods: [],
            customerBreak:[],
            breakTransactions:[],
            totalOrderAmount: 0,
            totalTransferAmount: 0,
            openTransfer:false,
            amountPaid:0,
            remainingAmount:0,
            transaction:[],
            enabled3: true,
            isLoader: false,
            invoice_id: null,
            create: {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesman_id: null,
                payment_method_id: null,
                customer_id: null,
                document_id:5,
                module_type:"voucher",
                amount_status:"Paid"
            },
            edit: {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesman_id: null,
                payment_method_id: null,
                customer_id: null,
                serial_number: null,
                document_id:5,
                module_type:"voucher",
                amount_status:"Paid"
            },
            setting: {
                date: true,
                customer_id: true,
                salesman_id: true,
                serial_number: true,
            },
            filterSetting: [
                this.$i18n.locale == 'ar' ? 'salesman.name' : 'salesman.name_e',
                this.$i18n.locale == 'ar' ? 'customer.name' : 'customer.name_e',
            ],
            errors: {},
            branches: [],
            isCheckAll: false,
            checkAll: [],
            is_disabled: false,
            current_page: 1,
            company_id: 48,
            Tooltip: "",
            mouseEnter: null,
            amount:'',
            printLoading: true,
            printObj: {
                id: "printReservation",
            }
        };
    },
    validations: {
        create: {
            date: {required},
            customer_id: {required},
            branch_id: {required},
            salesman_id: {required},
            payment_method_id: {required}
        },
        edit: {
            date: {required},
            customer_id: {required},
            branch_id: {required},
            salesman_id: {required},
            payment_method_id: {required},
            serial_number: {required},
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
                this.invoices.forEach((el) => {
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
        isPermission(item) {
            if (this.$store.state.auth.type == 'user'){
                return this.$store.state.auth.permissions.includes(item)
            }
            return true;
        },
        showSaleManModal() {
            if (this.create.salesman_id == 0) {
                this.$bvModal.show("saleman-create");
                this.create.salesman_id = null;
            }
        },
        showSaleManModalEdit() {
            if (this.edit.salesman_id == 0) {
                this.$bvModal.show("saleman-create");
                this.edit.salesman_id = null;
            }
        },
        async showCustomerModal() {
            if (this.create.customer_id == 0) {
                this.$bvModal.show("customer-general-create");
                this.create.customer_id = null;
            }else {
               await this.getBreakCustomer(this.create.customer_id);
            }
        },
        async showCustomerModalEdit() {
            if (this.edit.customer_id == 0) {
                this.$bvModal.show("customer-general-create");
                this.edit.customer_id = null;
            }else {
                await this.getBreakCustomer(this.edit.customer_id);
            }
        },
        showBranchModal() {
            if (this.create.branch_id == 0) {
                this.$bvModal.show("create_branch");
                this.create.branch_id = null;
            }
        },
        showBranchModalEdit() {
            if (this.edit.branch_id == 0) {
                this.$bvModal.show("create_branch");
                this.edit.branch_id = null;
            }
        },

        /**
         *  get Data invoices
         */
        getData(page = 1) {
            this.isLoader = true;
            let _filterSetting = [...this.filterSetting];
            let index = this.filterSetting.indexOf("customer_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
            }
            index = this.filterSetting.indexOf("salesman_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "salesman.name" : "salesman.name_e";
            }
            let filter = "";
            for (let i = 0; i < _filterSetting.length; ++i) {
                filter += `columns[${i}]=${_filterSetting[i]}&`;
            }
            adminApi
                .get(
                    `real-estate/invoices?voucher=true&module_type=voucher&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.invoices = l.data;
                    this.invoicesPagination = l.pagination;
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
                this.current_page <= this.invoicesPagination.last_page &&
                this.current_page != this.invoicesPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let _filterSetting = [...this.filterSetting];
                let index = this.filterSetting.indexOf("customer_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
                }
                index = this.filterSetting.indexOf("salesman_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "salesman.name" : "salesman.name_e";
                }
                let filter = "";
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `real-estate/invoices?voucher=true&module_type=voucher&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.invoices = l.data;
                        this.invoicesPagination = l.pagination;
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
                            .post(`real-estate/invoices/bulk-delete`, {ids: id})
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
                            .delete(`real-estate/invoices/${id}`)
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
            this.customerBreak=[];
            this.breakTransactions=[];
            this.totalOrderAmount= 0;
            this.totalTransferAmount= 0;
            this.openTransfer=false;
            this.amountPaid=0;
            this.remainingAmount=0;
            this.amount=0;
            this.create = {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesman_id: null,
                payment_method_id: null,
                customer_id: null,
                document_id: 5,
                module_type:"voucher",
                amount_status:"Paid"
            };
            this.invoice_id = null;

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
            await this.getCustomers();
            await this.getBranches();
            await this.getSalesmen();
            await this.getPaymentMethod();
            await this.resetModalHidden();
            this.is_disabled = false;
        },
        /**
         *  create screen
         */
        resetForm() {
            this.resetModalHidden();
            this.is_disabled = false;
        },
        AddSubmit() {
            if (this.$v.create.$invalid) {
                this.$v.create.$touch();
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                let ids = [];
                let transactions = [];
                this.breakTransactions.forEach((el)=>{
                    ids.push(el.id);
                    transactions.push({
                        break_id:el.id,
                        date:el.instalment_date,
                        amount:parseInt(el.amount),
                        module_type:el.break_type,
                        amount_status:el.amount_status
                    })
                });
                this.create.break_downs = ids ;
                this.create.transactions = transactions;

                adminApi
                    .post(`real-estate/invoices`, {...this.create})
                    .then((res) => {
                        this.getData();
                        this.invoice_id = res.data.data.id;
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
            // this.images.forEach((e) => {
            //     this.edit.old_media.push(e.id);
            // });

            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                let ids = [];
                let transactions = [];
                this.breakTransactions.forEach((el)=>{
                    ids.push(el.id);
                    transactions.push({
                        break_id:el.id,
                        date:el.instalment_date,
                        amount:parseInt(el.amount),
                        module_type:el.break_type,
                        amount_status:el.amount_status,
                    })
                });
                this.edit.break_downs = ids ;
                this.edit.transactions = transactions;
                adminApi
                    .put(`real-estate/invoices/${id}`, {...this.edit})
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
        async getCustomers() {
            this.isLoader = true;
            await adminApi
                .get(`/general-customer`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if(this.isPermission('create Customer')) {
                        l.unshift({id: 0, name: "اضافة زبون", name_e: "Add customer"});
                    }
                    this.customers = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getBranches() {
            this.isLoader = true;
            await adminApi
                .get(`/branches?document_id=5`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if(this.isPermission('create Branch')){
                        l.unshift({id: 0, name: "اضف فرع", name_e: "Add branch"});
                    }
                    this.branches = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getSalesmen() {
            this.isLoader = true;
            await adminApi
                .get(`/salesmen`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if(this.isPermission('create Sales Man')){
                        l.unshift({id: 0, name: "اضافة رجل مبيعات", name_e: "Add sale man"});
                    }
                    this.salesmen = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getPaymentMethod() {
            this.isLoader = true;
            await adminApi
                .get(`/payment-methods`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.paymentMethods = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getBreakCustomer(id) {
            this.isLoader = true;
            this.customerBreak = [];
            await adminApi
                .get(`/recievable-payable/rp_break_down?customer_id=${id}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.forEach((e) => {
                        this.customerBreak.push({
                            id:e.id,
                            break_type:e.break_type,
                            document:e.document,
                            amount:parseInt(e.total),
                            instalment_date:e.instalment_date,
                            salesman: this.handelSalesman(e),
                            serial_number: this.handelSerial(e),
                            amount_status:e.amount_status
                        });
                    });
                    this.accountTotalAmount();
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        handelSalesman(e)
        {
            if (e.break_type == 'invoice')
            {
                if (e.invoice)
                {
                    if (e.invoice.salesman)
                    {
                       return this.$i18n.locale == "ar" ? e.invoice.salesman.name : e.invoice.salesman.name_e;
                    }else {
                        return '---';
                    }
                }else {
                    return '---';
                }
            }
            if (e.break_type == 'contract')
            {
                if (e.contract)
                {
                    if (e.contract.salesman)
                    {
                        return this.$i18n.locale == "ar" ? e.contract.salesman.name : e.contract.salesman.name_e;
                    }else {
                        return '---';
                    }
                }else {
                    return '---';
                }
            }
            if (e.break_type == 'documentHeader')
            {
                if (e.documentHeader)
                {
                    if (e.documentHeader.employee)
                    {
                        return this.$i18n.locale == "ar" ? e.documentHeader.employee.name : e.documentHeader.employee.name_e;
                    }else {
                        return '---';
                    }
                }else {
                    return '---';
                }
            }

            return '---';
        },
        handelSerial(e)
        {
            if (e.break_type == 'invoice')
            {
                if (e.invoice)
                {
                    return e.invoice.prefix;
                }else {
                    return '---';
                }
            }
            if (e.break_type == 'contract')
            {
                if (e.contract)
                {
                    return e.contract.prefix;
                }else {
                    return '---';
                }
            }
            if (e.break_type == 'documentHeader')
            {
                if (e.documentHeader)
                {
                    return e.documentHeader.prefix;
                }else {
                    return '---';
                }
            }

            return '---';
        },
        async getTransactions(id) {
            this.isLoader = true;
            await adminApi
                .get(`/transactions?invoice_id=${id}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.forEach((e) => {
                        this.breakTransactions.push({
                            id:e.break_id,
                            break_type:e.break_down.break_type,
                            amount:parseInt(e.amount),
                            instalment_date:e.break_down.instalment_date,
                            serial_number: e.break_down.break_type == 'invoice' ? e.break_down.invoice? e.break_down.invoice.salesman?e.break_down.invoice.salesman.name:'---':'---':e.break_down.break_type == 'contract' ?e.break_down.contract? e.break_down.contract.salesman?e.break_down.contract.salesman.name:'---':'---':e.break_down.break_type == 'reservation' ?e.break_down.reservation? e.break_down.reservation.salesman?e.break_down.reservation.salesman.name:'---':'---':'---',
                            salesman: e.break_down.break_type == 'invoice'? e.break_down.invoice? e.break_down.invoice.prefix:'---' : e.break_down.break_type == 'contract'?e.break_down.contract?e.break_down.contract.prefix:'---': e.break_down.break_type == 'reservation'?e.break_down.reservation?e.break_down.reservation.prefix:'---':'---',
                            amount_status:e.break_down.amount_status
                        });
                    });
                    this.accountTotalAmount();
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
            this.customerBreak=[];
            this.breakTransactions=[];
            this.totalOrderAmount= 0;
            this.totalTransferAmount= 0;
            this.openTransfer=false;
            this.amountPaid=0;
            this.remainingAmount=0;
            this.amount=0;
            let invoice = this.invoices.find((e) => id == e.id);
            this.invoice_id = invoice.id;
            await this.getCustomers();
            await this.getSalesmen();
            await this.getPaymentMethod();
            await this.getBranches();
            this.edit.date = invoice.date;
            this.edit.customer_id = invoice.customer.id;
            this.edit.salesman_id = invoice.salesman.id;
            this.edit.payment_method_id = invoice.paymentMethod.id;
            this.edit.branch_id = invoice.branch.id;
            this.edit.document_id = invoice.document_id;
            this.edit.serial_number  = invoice.serial_number;
            this.edit.module_type = invoice.module_type;
            await this.getBreakCustomer(this.edit.customer_id);
            await this.getTransactions(id);
            this.amount= this.totalTransferAmount ;
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.customerBreak=[];
            this.breakTransactions=[];
            this.totalOrderAmount= 0;
            this.totalTransferAmount= 0;
            this.openTransfer=true;
            this.amountPaid=0;
            this.remainingAmount=0;
            this.amount=0;
            this.errors = {};
            this.edit = {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesman_id: null,
                payment_method_id: null,
                document_id: 5,
                customer_id: null,
                serial_number: null,
                module_type:"voucher",
                amount_status:"Paid"
            };
            this.invoice_id = null;
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
                    .get(`real-estate/invoices/logs/${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.forEach((e) => {
                            this.Tooltip += `Created By: ${e.causer_type}; Event: ${e.event
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
                    XLSX.writeFile(wb, fn || (('Reservation' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },

        //Transfer break To Transactions
        orderTransferToTransaction(index){
            if (this.remainingAmount > 0){
                let orderTransfer = "";
                let endAmount = 0;
                endAmount = parseInt(this.remainingAmount) - parseInt(this.customerBreak[index].amount);
                if (endAmount >= 0){
                    orderTransfer = this.customerBreak.splice(index,1);
                    orderTransfer[0].amount_status = "Paid";
                    this.breakTransactions.push(orderTransfer[0]);
                }else {
                    this.breakTransactions.push({
                        id:this.customerBreak[index].id,
                        break_type:this.customerBreak[index].break_type,
                        document:this.customerBreak[index].document,
                        instalment_date:this.customerBreak[index].instalment_date,
                        serial_number:this.customerBreak[index].serial_number,
                        salesman:this.customerBreak[index].salesman,
                        amount:parseInt(this.remainingAmount),
                        amount_status:"Paid_Partially",
                    });
                    this.customerBreak[index].amount = parseInt(this.customerBreak[index].amount) - parseInt(this.remainingAmount);
                }

                this.accountTotalAmount();

                this.remainingAmount = parseInt(this.amountPaid) - parseInt(this.totalTransferAmount);
            }
        },
        //return Transaction To customer Break
        ReturnTransactionToOrder(index){
            let orderTransfer = "";
            let orderReturn = "";
            this.remainingAmount += parseInt(this.breakTransactions[index].amount);
            orderReturn = this.customerBreak.find(e => e.id == this.breakTransactions[index].id);
            if (orderReturn){
                orderReturn.amount += this.breakTransactions[index].amount;
                this.breakTransactions.splice(index,1);
            }else {
                orderTransfer = this.breakTransactions.splice(index,1);
                this.customerBreak.push(orderTransfer[0]);
            }
            this.accountTotalAmount();
        },
        // calculate total amount in customer Break and Transactions
        accountTotalAmount(){
            //account total amount order
            this.totalOrderAmount = 0;
            this.customerBreak.forEach((el,index)=>{
                this.totalOrderAmount += parseInt(el.amount);
            });

            //account total amount transfer order
            this.totalTransferAmount = 0;
            this.breakTransactions.forEach((el,index)=>{
                this.totalTransferAmount += parseInt(el.amount);
            });
        },
        // change Status Transfer open or close
        changeStatusTransfer(event){
            let amount = 0 ;
            amount = event.target.value;
            if (amount > 0){
                this.openTransfer = true;
                this.amountPaid = parseInt(amount);
                this.remainingAmount = parseInt(this.amountPaid) - parseInt(this.totalTransferAmount);
            }else{
                this.openTransfer = false;
            }
        }
    }
}
</script>

<template>
    <Layout>
        <PageHeader/>
        <Saleman :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getSalesmen"/>
        <customerGeneral :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCustomers"/>
        <Branch :id="'create_branch'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getBranches"/>
        <div class="invoice-container row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ $t("general.ReceiptVoucherTable") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting"
                                                         :value="$i18n.locale == 'ar' ? 'salesman.name' : 'salesman.name_e'"
                                                         class="mb-1">{{ getCompanyKey("sale_man") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting"
                                                         :value="$i18n.locale == 'ar' ? 'customer.name' : 'customer.name_e'"
                                                         class="mb-1">{{ getCompanyKey("customer") }}
                                        </b-form-checkbox>
                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                </div>

                                <div class="d-inline-block position-relative" style="width: 77%">
                                    <span
                                        :class="['search-custom position-absolute', $i18n.locale == 'ar' ? 'search-custom-ar' : '',]">
                                        <i class="fe-search"></i>
                                    </span>
                                    <input class="form-control" style="display: block; width: 93%; padding-top: 3px"
                                           type="text" v-model.trim="search"
                                           :placeholder="`${$t('general.Search')}...`"/>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <b-button v-if="isPermission('create receiptVoucher RP')" v-b-modal.create variant="primary" class="btn-sm mx-1 font-weight-bold">
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printReservation'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <button class="custom-btn-dowonload"
                                            @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                            v-if="checkAll.length == 1 && isPermission('update receiptVoucher RP')">
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
<!--                                    <button class="custom-btn-dowonload" v-if="checkAll.length > 1"-->
<!--                                            @click.prevent="deleteScreenButton(checkAll)">-->
<!--                                        <i class="fas fa-trash-alt"></i>-->
<!--                                    </button>-->
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
<!--                                    <button class="custom-btn-dowonload" v-if="checkAll.length == 1"-->
<!--                                            @click.prevent="deleteScreenButton(checkAll[0])">-->
<!--                                        <i class="fas fa-trash-alt"></i>-->
<!--                                    </button>-->
                                    <!--  end one delete  -->
                                </div>
                            </div>
                            <div class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end">
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
                                    <b-dropdown variant="primary"
                                                :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                                                ref="dropdown"
                                                class="dropdown-custom-ali">
                                        <b-form-checkbox v-model="setting.date" class="mb-1">{{
                                                getCompanyKey("invoice_date")
                                            }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="setting.customer_id" class="mb-1">{{
                                                getCompanyKey("customer")
                                            }}
                                        </b-form-checkbox>

                                        <b-form-checkbox v-model="setting.salesman_id" class="mb-1">
                                            {{ getCompanyKey("sale_man") }}
                                        </b-form-checkbox>

                                        <b-form-checkbox v-model="setting.serial_number" class="mb-1">
                                            {{ $t("general.serial_number") }}
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
                                            {{ invoicesPagination.from }}-{{ invoicesPagination.to }}
                                            /
                                            {{ invoicesPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a href="javascript:void(0)" :style="{
                                                'pointer-events':
                                                    invoicesPagination.current_page == 1 ? 'none' : '',
                                            }" @click.prevent="getData(invoicesPagination.current_page - 1)">
                                                <span>&lt;</span>
                                            </a>
                                            <input type="text" @keyup.enter="getDataCurrentPage()"
                                                   v-model="current_page"
                                                   class="pagination-current-page"/>
                                            <a href="javascript:void(0)" :style="{
                                                'pointer-events':
                                                    invoicesPagination.last_page ==
                                                        invoicesPagination.current_page
                                                        ? 'none'
                                                        : '',
                                            }" @click.prevent="getData(invoicesPagination.current_page + 1)">
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!--  create   -->
                        <b-modal dialog-class="modal-full-width" id="create"
                                 :title="$t('general.createNewReceiptVoucher')"
                                 title-class="font-18" body-class="p-4 " :hide-footer="true" @show="resetModal"
                                 @hidden="resetModalHidden">
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <b-button variant="success" :disabled="!is_disabled" @click.prevent="resetForm"
                                              type="button"
                                              :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                                        {{ $t("general.AddNewRecord") }}
                                    </b-button>
                                    <template v-if="!is_disabled">
                                        <b-button variant="success" type="button" class="mx-1" v-if="!isLoader"
                                                  @click.prevent="AddSubmit">
                                            {{ $t("general.Add") }}
                                        </b-button>

                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                        </b-button>
                                    </template>

                                    <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`create`)">
                                        {{ $t("general.Cancel") }}
                                    </b-button>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 position-relative">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("branch") }}</label>
                                            <multiselect @input="showBranchModal" v-model="create.branch_id"
                                                         :options="branches.map((type) => type.id)" :custom-label="
                                                    (opt) =>
                                                        $i18n.locale == 'ar'
                                                            ? branches.find((x) => x.id == opt).name
                                                            : branches.find((x) => x.id == opt).name_e
                                                " :class="{
                                                        'is-invalid':
                                                            $v.create.branch_id.$error || errors.branch_id,
                                                    }">
                                            </multiselect>
                                            <div v-if="!$v.create.branch_id.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>

                                            <template v-if="errors.branch_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.branch_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3 position-relative">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("invoice_date") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="$v.create.date.$model"
                                                format="YYYY-MM-DD"
                                                valueType="format"
                                                :confirm="false"
                                                :class="{
                                                    'is-invalid': $v.create.date.$error || errors.date,
                                                    'is-valid': !$v.create.date.$invalid && !errors.date,
                                                }">
                                            </date-picker>
                                            <template v-if="errors.date">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.date" :key="index">
                                                    {{
                                                        errorMessage
                                                    }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3 position-relative">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("customer") }}</label>
                                            <multiselect @input="showCustomerModal" v-model="create.customer_id"
                                                         :options="customers.map((type) => type.id)" :custom-label="
                                                    (opt) =>
                                                        $i18n.locale == 'ar'
                                                            ? customers.find((x) => x.id == opt).name
                                                            : customers.find((x) => x.id == opt).name_e
                                                " :class="{
                                                            'is-invalid':
                                                                $v.create.customer_id.$error || errors.customer_id,
                                                        }">
                                            </multiselect>
                                            <div v-if="!$v.create.customer_id.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>

                                            <template v-if="errors.customer_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.customer_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3 position-relative">
                                        <div class="form-group">
                                            <label>{{ getCompanyKey("sale_man") }}</label>
                                            <multiselect @input="showSaleManModal" v-model="create.salesman_id"
                                                         :options="salesmen.map((type) => type.id)" :custom-label="
                                                    (opt) =>
                                                        $i18n.locale == 'ar'
                                                            ? salesmen.find((x) => x.id == opt).name
                                                            : salesmen.find((x) => x.id == opt).name_e
                                                " :class="{
                                                    'is-invalid':
                                                        $v.create.salesman_id.$error || errors.salesman_id,
                                                }">
                                            </multiselect>
                                            <div v-if="!$v.create.salesman_id.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.salesman_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.button_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3 position-relative">
                                        <div class="form-group">
                                            <label>{{ $t("general.paymentMethod") }}</label>
                                            <multiselect v-model="create.payment_method_id"
                                                         :options="paymentMethods.map((type) => type.id)" :custom-label="
                                                    (opt) =>
                                                        $i18n.locale == 'ar'
                                                            ? paymentMethods.find((x) => x.id == opt).name
                                                            : paymentMethods.find((x) => x.id == opt).name_e"
                                                         :class="{ 'is-invalid':$v.create.payment_method_id.$error || errors.payment_method_id,}">
                                            </multiselect>
                                            <div v-if="!$v.create.payment_method_id.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.payment_method_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.button_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3 position-relative">
                                        <div class="form-group">
                                            <label>{{ $t("general.amount") }}</label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                step="any"
                                                v-model="amount"
                                                @input="changeStatusTransfer"
                                                :disabled="breakTransactions.length>0?true:false"
                                            />
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="page-content">
                                            <div class="px-0">
                                                <div class="row mt-4">
                                                    <div class="col-12 col-lg-12">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="text-center text-150">
                                                                    <i style="font-size:20px" class="fa fa-book fa-2x text-success-m2 mr-1"></i>
                                                                    <span class="text-default-d3">{{$t("general.invoice_details")}}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!-- .row -->
                                                        <div class="mt-4">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="card-header p-0">
                                                                        <h3 class="card-title float-left">
                                                                            {{$t('general.CustomerDebts')}}
                                                                        </h3>
                                                                    </div>
                                                                    <div class="table-responsive mb-3 custom-table-theme position-relative">
                                                                        <!--       start loader       -->
                                                                        <loader size="large" v-if="isLoader" />
                                                                        <!--       end loader       -->

                                                                        <table class="table table-borderless table-hover table-centered m-0" >
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>{{ $t("general.salesmen") }}</th>
                                                                                    <th>{{ $t("general.invoiceSerial") }}</th>
                                                                                    <th>{{ $t("general.type") }}</th>
                                                                                    <th>{{ $t("general.Date") }}</th>
                                                                                    <th>{{ $t("general.amount") }}</th>
                                                                                    <th>{{ $t("general.Action") }}</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody v-if="customerBreak.length > 0">
                                                                            <tr v-for="(data, index) in customerBreak"
                                                                                :key="data.id"
                                                                                class="body-tr-custom"
                                                                            >
                                                                                <td>
                                                                                    <h5 class="m-0 font-weight-normal">{{ data.serial_number }}</h5>
                                                                                </td>
                                                                                <td>
                                                                                    <h5 class="m-0 font-weight-normal">{{ data.salesman }}</h5>
                                                                                </td>
                                                                                <td>
                                                                                    <h5 v-if="data.document" class="m-0 font-weight-normal">{{$i18n.locale == "ar" ? data.document.name : data.document.name_e}}</h5>
                                                                                    <h5 v-else class="m-0 font-weight-normal">---</h5>
                                                                                </td>
                                                                                <td>
                                                                                    <h5 class="m-0 font-weight-normal">
                                                                                        {{ data.instalment_date }}
                                                                                    </h5>
                                                                                </td>
                                                                                <td>
                                                                                    <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>
                                                                                </td>
                                                                                <td v-if="openTransfer">
                                                                                    <button @click.prevent="orderTransferToTransaction(index)" class="btn btn-primary btn-sm">>></button>
                                                                                </td>
                                                                                <td v-else><button :disabled="true" class="btn btn-secondary btn-sm">>></button></td>
                                                                            </tr>
                                                                            <tr v-if="customerBreak.length > 0" class="total-amount">
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td>{{$t('general.totalAmount')}}</td>
                                                                                <td v-html="totalOrderAmount" class="amount-red"></td>
                                                                                <td></td>
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
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="card-header p-0">
                                                                        <h3 class="card-title float-left">
                                                                            {{$t('general.transactions')}}
                                                                        </h3>
                                                                    </div>
                                                                    <div class="table-responsive mb-3 custom-table-theme position-relative">
                                                                        <!--       start loader       -->
                                                                        <loader size="large" v-if="isLoader" />
                                                                        <!--       end loader       -->

                                                                        <table class="table table-borderless table-hover table-centered m-0" >
                                                                            <thead>
                                                                                <tr>
                                                                                    <th>{{ $t("general.salesmen") }}</th>
                                                                                    <th>{{ $t("general.invoiceSerial") }}</th>
                                                                                    <th>{{ $t("general.type") }}</th>
                                                                                    <th>{{ $t("general.Date") }}</th>
                                                                                    <th>{{ $t("general.amount") }}</th>
                                                                                    <th>{{ $t("general.Action") }}</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody v-if="breakTransactions.length > 0">
                                                                              <tr v-for="(data, index) in breakTransactions"
                                                                                :key="data.id"
                                                                                class="body-tr-custom"
                                                                            >
                                                                                  <td>
                                                                                      <h5 class="m-0 font-weight-normal">{{ data.serial_number }}</h5>
                                                                                  </td>
                                                                                  <td>
                                                                                      <h5 class="m-0 font-weight-normal">{{ data.salesman }}</h5>
                                                                                  </td>
                                                                                  <td>
                                                                                      <h5 v-if="data.document" class="m-0 font-weight-normal">{{$i18n.locale == "ar" ? data.document.name : data.document.name_e}}</h5>
                                                                                      <h5 v-else class="m-0 font-weight-normal">---</h5>
                                                                                  </td>
                                                                                  <td>
                                                                                      <h5 class="m-0 font-weight-normal">
                                                                                          {{ data.instalment_date }}
                                                                                      </h5>
                                                                                  </td>
                                                                                  <td>
                                                                                      <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>
                                                                                  </td>
                                                                                  <td v-if="openTransfer">
                                                                                      <button @click.prevent="ReturnTransactionToOrder(index)" class="btn btn-primary btn-sm"><<</button>
                                                                                  </td>
                                                                                  <td v-else><button :disabled="true" class="btn btn-secondary btn-sm"><<</button></td>
                                                                            </tr>
                                                                              <tr v-if="breakTransactions.length > 0" class="total-amount">
                                                                                  <td></td>
                                                                                  <td></td>
                                                                                  <td></td>
                                                                                  <td><h5 class="m-0 font-weight-normal">{{$t('general.totalAmount')}}</h5></td>
                                                                                  <td v-html="totalTransferAmount" class="amount-red"></td>
                                                                                  <td></td>
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
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label>{{$t('general.TheDifference')}}</label>
                                                                    <input type="text"
                                                                           class="form-control mb-1 input-Sender"
                                                                           v-model="remainingAmount"
                                                                           disabled
                                                                    >
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader"/>
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                                   id="printReservation">
                                <thead>
                                <tr>
                                    <th scope="col" style="width: 0" v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control">
                                            <input class="form-check-input" type="checkbox" v-model="isCheckAll"
                                                   style="width: 17px; height: 17px"/>
                                        </div>
                                    </th>
                                    <th v-if="setting.date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("invoice_date") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="
                                                        invoices.sort(
                                                            sortString(
                                                                $i18n.locale == 'ar' ? 'field_title' : 'field_title_e'
                                                            )
                                                        )
                                                    "></i>
                                                <i class="fas fa-arrow-down" @click="
                                                        invoices.sort(
                                                            sortString($i18n.locale == 'ar' ? '-name' : '-name_e')
                                                        )
                                                    "></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.serial_number">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.serial_number") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="
                                                        invoices.sort(
                                                            sortString(
                                                                $i18n.locale == 'ar' ? 'serial_number' : 'serial_number'
                                                            )
                                                        )
                                                    "></i>
                                                <i class="fas fa-arrow-down" @click="
                                                        invoices.sort(
                                                            sortString($i18n.locale == 'ar' ? '-serial_number' : '-serial_number')
                                                        )
                                                    "></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.customer_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("customer") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="
                                                        invoices.sort(
                                                            sortString($i18n.locale == 'ar' ? 'name' : 'name_e')
                                                        )
                                                    "></i>
                                                <i class="fas fa-arrow-down" @click="
                                                        invoices.sort(
                                                            sortString($i18n.locale == 'ar' ? '-name' : '-name_e')
                                                        )
                                                    "></i>
                                            </div>
                                        </div>
                                    </th>

                                    <th v-if="setting.salesman_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("sale_man") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="
                                                        invoices.sort(
                                                            sortString($i18n.locale == 'ar' ? 'name' : 'name_e')
                                                        )
                                                    "></i>
                                                <i class="fas fa-arrow-down" @click="
                                                        invoices.sort(
                                                            sortString($i18n.locale == 'ar' ? '-name' : '-name_e')
                                                        )
                                                    "></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t("general.Action") }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="invoices.length > 0">
                                <tr @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="isPermission('update receiptVoucher RP')?
                                    $bvModal.show(`modal-edit-${data.id}`):false"
                                    v-for="(data, index) in invoices" :key="data.id" class="body-tr-custom">
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control" style="min-height: 1.9em">
                                            <input style="width: 17px; height: 17px" class="form-check-input"
                                                   type="checkbox" :value="data.id" v-model="checkAll"/>
                                        </div>
                                    </td>
                                    <td v-if="setting.date">
                                        <h5 class="m-0 font-weight-normal">
                                            {{ data.date }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.serial_number">
                                        <h5 class="m-0 font-weight-normal">
                                            {{ data.prefix }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.customer_id">
                                        <h5 class="m-0 font-weight-normal">
                                            {{
                                                $i18n.locale == "ar" ? data.customer.name : data.customer.name_e
                                            }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.salesman_id">
                                        <h5 class="m-0 font-weight-normal">
                                            {{
                                                $i18n.locale == "ar" ? data.salesman.name : data.salesman.name_e
                                            }}
                                        </h5>
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                {{ $t("general.commands") }}
                                                <i class="fas fa-angle-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-custom">
                                                <a class="dropdown-item" href="#"
                                                   v-if="isPermission('update receiptVoucher RP')"
                                                   @click="$bvModal.show(`modal-edit-${data.id}`)">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black">
                                                        <span>{{ $t("general.edit") }}</span>
                                                        <i class="mdi mdi-square-edit-outline text-info"></i>
                                                    </div>
                                                </a>
<!--                                                <a class="dropdown-item text-black" href="#"-->
<!--                                                   @click.prevent="deleteScreenButton(data.id)">-->
<!--                                                    <div-->
<!--                                                        class="d-flex justify-content-between align-items-center text-black">-->
<!--                                                        <span>{{ $t("general.delete") }}</span>-->
<!--                                                        <i class="fas fa-times text-danger"></i>-->
<!--                                                    </div>-->
<!--                                                </a>-->
                                            </div>
                                        </div>

                                        <!--  edit   -->
                                        <b-modal dialog-class="modal-full-width" :id="`modal-edit-${data.id}`"
                                                 :title="$t('general.editReceiptVoucher')" title-class="font-18"
                                                 body-class="p-4" :ref="`edit-${data.id}`" :hide-footer="true"
                                                 @show="resetModalEdit(data.id)"
                                                 @hidden="resetModalHiddenEdit(data.id)">
                                            <form>
                                                <div class="mb-3 d-flex justify-content-end">
                                                    <!-- Emulate built in modal footer ok and cancel button actions -->
                                                    <b-button variant="success" type="button" class="mx-1"
                                                              v-if="!isLoader" @click.prevent="editSubmit(data.id)">
                                                        {{ $t("general.Edit") }}
                                                    </b-button>

                                                    <b-button variant="success" class="mx-1" disabled v-else>
                                                        <b-spinner small></b-spinner>
                                                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                    </b-button>

                                                    <b-button variant="danger" type="button"
                                                              @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)">
                                                        {{ $t("general.Cancel") }}
                                                    </b-button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3 position-relative">
                                                        <div class="form-group">
                                                            <label>{{ getCompanyKey("branch") }}</label>
                                                            <multiselect @input="showBranchModalEdit"
                                                                         v-model="edit.branch_id"
                                                                         :options="branches.map((type) => type.id)"
                                                                         :custom-label="
                                                                        (opt) =>$i18n.locale == 'ar'
                                                                                ? branches.find((x) => x.id == opt).name
                                                                                : branches.find((x) => x.id == opt).name_e"
                                                                         :class="{
                                                                            'is-invalid':$v.edit.branch_id.$error || errors.branch_id,
                                                                        }">
                                                            </multiselect>
                                                            <div v-if="!$v.edit.branch_id.required"
                                                                 class="invalid-feedback">
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>

                                                            <template v-if="errors.branch_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.branch_id"
                                                                    :key="index">{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 position-relative">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("invoice_date") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input type="date" class="form-control" data-create="9"
                                                                   v-model="$v.edit.date.$model" :class="{
                                                                        'is-invalid': $v.edit.date.$error || errors.date,
                                                                        'is-valid': !$v.edit.date.$invalid && !errors.date,
                                                                    }"/>
                                                            <template v-if="errors.date">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.date"
                                                                    :key="index">{{ errorMessage}}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3" >
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ $t('general.serial_number') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                v-model="$v.edit.serial_number.$model"
                                                                :class="{
                                                                    'is-invalid': $v.edit.serial_number.$error || errors.serial_number,
                                                                    'is-valid': !$v.edit.serial_number.$invalid && !errors.serial_number,
                                                                  }"
                                                            />
                                                            <template v-if="errors.serial_number">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.serial_number"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 position-relative">
                                                        <div class="form-group">
                                                            <label>{{ getCompanyKey("customer") }}</label>
                                                            <multiselect @input="showCustomerModalEdit"
                                                                         v-model="$v.edit.customer_id.$model"
                                                                         :options="customers.map((type) => type.id)"
                                                                         :custom-label="
                                                                        (opt) =>
                                                                            $i18n.locale == 'ar'
                                                                                ? customers.find((x) => x.id == opt).name
                                                                                : customers.find((x) => x.id == opt).name_e
                                                                    " :class="{
                                                                            'is-invalid':
                                                                                $v.edit.customer_id.$error || errors.customer_id,
                                                                        }">
                                                            </multiselect>
                                                            <div v-if="!$v.edit.customer_id.required"
                                                                 class="invalid-feedback">
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>

                                                            <template v-if="errors.customer_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.customer_id"
                                                                    :key="index">{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 position-relative">
                                                        <div class="form-group">
                                                            <label>{{ getCompanyKey("sale_man") }}</label>
                                                            <multiselect @input="showSaleManModalEdit"
                                                                         v-model="edit.salesman_id"
                                                                         :options="salesmen.map((type) => type.id)"
                                                                         :custom-label="
                                                                        (opt) =>
                                                                            $i18n.locale == 'ar'
                                                                                ? salesmen.find((x) => x.id == opt).name
                                                                                : salesmen.find((x) => x.id == opt).name_e
                                                                    " :class="{
                                                                            'is-invalid':
                                                                                $v.edit.salesman_id.$error || errors.salesman_id,
                                                                        }">
                                                            </multiselect>
                                                            <div v-if="!$v.edit.salesman_id.required"
                                                                 class="invalid-feedback">
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.salesman_id">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.button_id"
                                                                    :key="index">{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 position-relative">
                                                        <div class="form-group">
                                                            <label>{{ $t("general.paymentMethod") }}</label>
                                                            <multiselect v-model="edit.payment_method_id"
                                                                         :options="paymentMethods.map((type) => type.id)" :custom-label="
                                                                        (opt) => $i18n.locale == 'ar'
                                                                            ? paymentMethods.find((x) => x.id == opt).name
                                                                            : paymentMethods.find((x) => x.id == opt).name_e"
                                                                         :class="{ 'is-invalid':$v.edit.payment_method_id.$error || errors.payment_method_id,}">
                                                            </multiselect>
                                                            <div v-if="!$v.edit.payment_method_id.required" class="invalid-feedback">
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.payment_method_id">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.button_id"
                                                                              :key="index">{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 position-relative">
                                                        <div class="form-group">
                                                            <label>{{ $t("general.amount") }}</label>
                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                step="any"
                                                                v-model="amount"
                                                                @input="changeStatusTransfer"
                                                            />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="page-content">
                                                            <div class="px-0">
                                                                <div class="row mt-4">
                                                                    <div class="col-12 col-lg-12">
                                                                        <div class="row">
                                                                            <div class="col-12">
                                                                                <div class="text-center text-150">
                                                                                    <i style="font-size:20px" class="fa fa-book fa-2x text-success-m2 mr-1"></i>
                                                                                    <span class="text-default-d3">{{$t("general.invoice_details")}}</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <!-- .row -->
                                                                        <div class="mt-4">
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <div class="card-header p-0">
                                                                                        <h3 class="card-title float-left">
                                                                                            {{$t('general.CustomerDebts')}}
                                                                                        </h3>
                                                                                    </div>
                                                                                    <div class="table-responsive mb-3 custom-table-theme position-relative">
                                                                                        <!--       start loader       -->
                                                                                        <loader size="large" v-if="isLoader" />
                                                                                        <!--       end loader       -->

                                                                                        <table class="table table-borderless table-hover table-centered m-0" >
                                                                                            <thead>
                                                                                            <tr>
                                                                                                <th>{{ $t("general.salesmen") }}</th>
                                                                                                <th>{{ $t("general.invoiceSerial") }}</th>
                                                                                                <th>{{ $t("general.type") }}</th>
                                                                                                <th>{{ $t("general.Date") }}</th>
                                                                                                <th>{{ $t("general.amount") }}</th>
                                                                                                <th>{{ $t("general.Action") }}</th>
                                                                                            </tr>
                                                                                            </thead>
                                                                                            <tbody v-if="customerBreak.length > 0">
                                                                                            <tr v-for="(data, index) in customerBreak"
                                                                                                :key="data.id"
                                                                                                class="body-tr-custom"
                                                                                            >
                                                                                                <td>
                                                                                                    <h5 class="m-0 font-weight-normal">{{ data.serial_number }}</h5>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <h5 class="m-0 font-weight-normal">{{ data.salesman }}</h5>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <h5 v-if="data.document" class="m-0 font-weight-normal">{{$i18n.locale == "ar" ? data.document.name : data.document.name_e}}</h5>
                                                                                                    <h5 v-else class="m-0 font-weight-normal">---</h5>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <h5 class="m-0 font-weight-normal">
                                                                                                        {{ data.instalment_date }}
                                                                                                    </h5>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>
                                                                                                </td>
                                                                                                <td v-if="openTransfer">
                                                                                                    <button @click.prevent="orderTransferToTransaction(index)" class="btn btn-primary btn-sm">>></button>
                                                                                                </td>
                                                                                                <td v-else><button :disabled="true" class="btn btn-secondary btn-sm">>></button></td>
                                                                                            </tr>
                                                                                            <tr v-if="customerBreak.length > 0" class="total-amount">
                                                                                                <td></td>
                                                                                                <td></td>
                                                                                                <td></td>
                                                                                                <td>{{$t('general.totalAmount')}}</td>
                                                                                                <td v-html="totalOrderAmount" class="amount-red"></td>
                                                                                                <td></td>
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
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="card-header p-0">
                                                                                        <h3 class="card-title float-left">
                                                                                            {{$t('general.transactions')}}
                                                                                        </h3>
                                                                                    </div>
                                                                                    <div class="table-responsive mb-3 custom-table-theme position-relative">
                                                                                        <!--       start loader       -->
                                                                                        <loader size="large" v-if="isLoader" />
                                                                                        <!--       end loader       -->

                                                                                        <table class="table table-borderless table-hover table-centered m-0" >
                                                                                            <thead>
                                                                                            <tr>
                                                                                                <th>{{ $t("general.salesmen") }}</th>
                                                                                                <th>{{ $t("general.invoiceSerial") }}</th>
                                                                                                <th>{{ $t("general.type") }}</th>
                                                                                                <th>{{ $t("general.Date") }}</th>
                                                                                                <th>{{ $t("general.amount") }}</th>
                                                                                                <th>{{ $t("general.Action") }}</th>
                                                                                            </tr>
                                                                                            </thead>
                                                                                            <tbody v-if="breakTransactions.length > 0">
                                                                                            <tr v-for="(data, index) in breakTransactions"
                                                                                                :key="data.id"
                                                                                                class="body-tr-custom"
                                                                                            >
                                                                                                <td>
                                                                                                    <h5 class="m-0 font-weight-normal">{{ data.serial_number }}</h5>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <h5 class="m-0 font-weight-normal">{{ data.salesman }}</h5>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <h5 v-if="data.document" class="m-0 font-weight-normal">{{$i18n.locale == "ar" ? data.document.name : data.document.name_e}}</h5>
                                                                                                    <h5 v-else class="m-0 font-weight-normal">---</h5>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <h5 class="m-0 font-weight-normal">
                                                                                                        {{ data.instalment_date }}
                                                                                                    </h5>
                                                                                                </td>
                                                                                                <td>
                                                                                                    <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>
                                                                                                </td>
                                                                                                <td v-if="openTransfer">
                                                                                                    <button @click.prevent="ReturnTransactionToOrder(index)" class="btn btn-primary btn-sm"><<</button>
                                                                                                </td>
                                                                                                <td v-else><button :disabled="true" class="btn btn-secondary btn-sm"><<</button></td>
                                                                                            </tr>
                                                                                            <tr v-if="breakTransactions.length > 0" class="total-amount">
                                                                                                <td></td>
                                                                                                <td></td>
                                                                                                <td></td>
                                                                                                <td><h5 class="m-0 font-weight-normal">{{$t('general.totalAmount')}}</h5></td>
                                                                                                <td v-html="totalTransferAmount" class="amount-red"></td>
                                                                                                <td></td>
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
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <label>{{$t('general.TheDifference')}}</label>
                                                                                    <input type="text"
                                                                                           class="form-control mb-1 input-Sender"
                                                                                           v-model="remainingAmount"
                                                                                           disabled
                                                                                    >
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </form>
                                        </b-modal>
                                        <!--  /edit   -->
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <button @mousemove="log(data.id)" @mouseover="log(data.id)" type="button"
                                                class="btn" :id="`tooltip-${data.id}`"
                                                :data-placement="$i18n.locale == 'en' ? 'left' : 'right'"
                                                :title="Tooltip">
                                            <i class="fe-info" style="font-size: 22px"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="7">
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

<style lang="scss">
.page-content {
    width: 100%;
}

.total {
    color: #343a40 !important;
    font-weight: bold;
}

.text-secondary-d1 {
    color: #728299 !important;
}

.page-header {
    margin: 0 0 1rem;
    padding-bottom: 1rem;
    padding-top: .5rem;
    border-bottom: 1px dotted #e2e2e2;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -ms-flex-align: center;
    align-items: center;
}

.page-title {
    padding: 0;
    margin: 0;
    font-size: 1.75rem;
    font-weight: 300;
}

.brc-default-l1 {
    border-color: #dce9f0 !important;
}

.ml-n1,
.mx-n1 {
    margin-left: -.25rem !important;
}

.mr-n1,
.mx-n1 {
    margin-right: -.25rem !important;
}

.mb-4,
.my-4 {
    margin-bottom: 1.5rem !important;
}

hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
    border-top: 1px solid rgba(0, 0, 0, .1);
}

.text-grey-m2 {
    color: #888a8d !important;
}

.text-success-m2 {
    color: #86bd68 !important;
}

.font-bolder,
.text-600 {
    font-weight: 600 !important;
}

.text-110 {
    font-size: 110% !important;
}

.text-blue {
    color: #478fcc !important;
}

.pb-25,
.py-25 {
    padding-bottom: .75rem !important;
}

.pt-25,
.py-25 {
    padding-top: .75rem !important;
}

.bgc-default-tp1 {
    background-color: rgba(121, 169, 197, .92) !important;
}

.bgc-default-l4,
.bgc-h-default-l4:hover {
    background-color: #f3f8fa !important;
}

.page-header .page-tools {
    -ms-flex-item-align: end;
    align-self: flex-end;
}

.btn-light {
    color: #757984;
    background-color: #f5f6f9;
    border-color: #dddfe4;
}

.w-2 {
    width: 1rem;
}

.text-120 {
    font-size: 120% !important;
}

.text-primary-m1 {
    color: #4087d4 !important;
}

.text-danger-m1 {
    color: #dd4949 !important;
}

.text-blue-m2 {
    color: #68a3d5 !important;
}

.text-150 {
    font-size: 150% !important;
}

.text-60 {
    font-size: 60% !important;
}

.text-grey-m1 {
    color: #7b7d81 !important;
}

.align-bottom {
    vertical-align: bottom !important;
}
.amount-red{
    color: red;
}
.total-amount{
    background-color: rgba(0,0,0,.075);
}
</style>
