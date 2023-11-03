<script>
import adminApi from "../../../api/adminAxios";
import {minValue, required, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../general/loader";
import {dynamicSortString} from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import {formatDateOnly} from "../../../helper/startDate";
import translation from "../../../helper/mixin/translation-mixin";
import DatePicker from "vue2-datepicker";

export default {
    name: "documentWithMoney",
    props: {
        document_id: {
            default: null,
        },
    },
    mixins: [translation],
    components: {
        ErrorMessage,
        loader,
        Multiselect,
        DatePicker
    },
    data() {
        return {
            document:null,
            financial_years_validate : true,
            customer_data_create: "",
            customer_data_edit: "",
            relatedDocuments: [],
            per_page: 50,
            search: "",
            debounce: {},
            invoicesPagination: {},
            invoices: [],
            customers: [],
            salesmen: [],
            paymentMethods: [],
            customerDebits:[],
            customerCredit:[],
            totalDebitSettlement: 0,
            totalCreditSettlement: 0,
            openTransfer:false,
            amountPaid:0,
            transaction:[],
            clientTypes:[],
            with_paid:false,
            open_invoice_details:false,
            enabled3: true,
            isLoader: false,
            invoice_id: null,
            create: {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesmen_id: null,
                payment_method_id: null,
                customer_id: null,
                amount:0,
                document_id:this.document_id,
                client_type_id:1,
            },
            edit: {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesmen_id: null,
                payment_method_id: null,
                customer_id: null,
                amount:0,
                document_id:this.document_id,
                client_type_id:1,
            },
            setting: {
                branch_id: true,
                serial_number: true,
                date: true,
                customer_id: true,
                amount: true,
                payment_method_id: true,
                salesmen_id: true,
            },
            filterSetting: [
                'branch_id',
                'prefix',
                'date',
                'customer_id',
                'amount',
                'payment_method_id',
                'salesmen_id',
            ],
            errors: {},
            branches: [],
            isCheckAll: false,
            checkAll: [],
            is_disabled: false,
            current_page: 1,
            Tooltip: "",
            mouseEnter: null,
            serial_number:'',
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
            salesmen_id: {required},
            payment_method_id: {required},
            amount: {required},
            document_id: {required},
            client_type_id: {required},
        },
        edit: {
            date: {required},
            customer_id: {required},
            branch_id: {required},
            salesmen_id: {required},
            payment_method_id: {required},
            amount: {required},
            document_id: {required},
            client_type_id: {required},
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
        this.getDocumentData();
        this.getData();
    },
    methods: {
        /**
         *  get Document Data
         */
        async getDocumentData() {
            this.isLoader = true;
            await adminApi
                .get(`/document/${this.document_id}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.document = l;
                    this.relatedDocuments = l.document_relateds;
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
            index = this.filterSetting.indexOf("salesmen_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "salesmen.name" : "salesmen.name_e";
            }
            index = this.filterSetting.indexOf("branch_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "branch.name" : "branch.name_e";
            }
            index = this.filterSetting.indexOf("payment_method_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "paymentMethod.name" : "paymentMethod.name_e";
            }
            let filter = "";
            for (let i = 0; i < _filterSetting.length; ++i) {
                filter += `columns[${i}]=${_filterSetting[i]}&`;
            }
            adminApi
                .get(
                    `voucher-headers?document_id=${this.document_id}&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
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
                index = this.filterSetting.indexOf("salesmen_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "salesmen.name" : "salesmen.name_e";
                }
                index = this.filterSetting.indexOf("branch_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "branch.name" : "branch.name_e";
                }
                index = this.filterSetting.indexOf("payment_method_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "paymentMethod.name" : "paymentMethod.name_e";
                }
                let filter = "";
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `voucher-headers?document_id=${this.document_id}&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
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
                            .post(`voucher-headers/bulk-delete`, {ids: id})
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
                            .delete(`voucher-headers/${id}`)
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
            this.serial_number = "";
            this.customer_data_create='';
            this.customer_data_edit='';
            this.customerCredit=[];
            this.customerDebits=[];
            this.totalDebitSettlement= 0;
            this.totalCreditSettlement= 0;
            this.openTransfer=false;
            this.amountPaid=0;
            this.amount=0;
            this.open_invoice_details=false;
            this.financial_years_validate = true;
            this.with_paid = false;
            this.create = {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesmen_id: null,
                payment_method_id: null,
                customer_id: null,
                amount:0,
                document_id:this.document_id,
                client_type_id:1,
            };
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
            await this.resetModalHidden();
            await this.getBranches();
            await this.getClientType();
            await this.getSalesmen();
            await this.getPaymentMethod();
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
            if (this.open_invoice_details)
            {
                if (!this.validationBeforeSubmit()){return;}
            }
            if (this.$v.create.$invalid && !this.financial_years_validate) {
                this.$v.create.$touch();
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                let data_create = this.create;
                let break_settlement = [];
                this.customerDebits.forEach((el,index) => {
                    if (el.settlement_amount > 0) {
                        break_settlement.push({
                            'break_id': el.id,
                            'amount': el.settlement_amount,
                        })
                    }
                });
                this.customerCredit.forEach((el,index) => {
                    if (el.settlement_amount > 0) {
                        break_settlement.push({
                            'break_id': el.id,
                            'amount': el.settlement_amount,
                        })
                    }
                });
                data_create.break_settlement = break_settlement;
                adminApi
                    .post(`voucher-headers`, {...data_create, company_id: this.company_id})
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
            if (this.$v.edit.$invalid  && !this.financial_years_validate) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`voucher-headers/${id}`, {...this.edit})
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
         *  get data
         */
        async getClientType() {
            this.isLoader = true;
            await adminApi
                .get(`/client-types/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    this.clientTypes = l;
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
        async getBranches() {
            this.isLoader = true;
            await adminApi
                .get(`/branches?document_id=${this.document_id}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.branches = l;
                    if (this.branches.length == 1)
                    {
                        this.create.branch_id = this.branches[0].id;
                        this.getSerialNumber('create',this.create.branch_id);
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
        async getSalesmen() {
            this.isLoader = true;
            await adminApi
                .get(`/employees?is_salesman=1&customer_handel=1`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
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
        async getCustomerSalesman(e)
        {
            let employee_id = e;
            if (employee_id)
            {
                await this.getCustomers(employee_id);
            }
        },
        async getCustomers(employee_id=null,search='') {
            this.isLoader = true;
            await adminApi
                .get(`/general-customer?limet=10&company_id=${this.company_id}&employee_id=${employee_id}&search=${search}&columns[0]=name&columns[1]=name_e&columns[2]=id`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.customers = l;
                    if (this.customer_data_edit)
                    {
                        if (!this.customers.find((e) => e.id == this.customer_data_edit.id))
                        {
                            this.customers.push(this.customer_data_edit);
                        }
                    }
                    if (this.customer_data_create)
                    {
                        if (!this.customers.find((e) => e.id == this.customer_data_create.id))
                        {
                            this.customers.push(this.customer_data_create);
                        }
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
            this.customerDebits = [];
            this.customerCredit = [];
            this.customer_data_create = this.customers.find((e) => e.id == this.create.customer_id);
            this.customer_data_edit = this.customers.find((e) => e.id == this.create.customer_id);
            await adminApi
                .get(`/recievable-payable/document-with-money-details?customer_id=${id}&with_paid=${this.with_paid}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.forEach((e) => {
                        if (parseFloat(e.debit) > 0)
                        {
                            this.customerDebits.push({
                                id:e.id,
                                break_type:e.break_type,
                                document:e.document,
                                amount:parseFloat(e.debit),
                                instalment_date:e.instalment_date,
                                // salesman: this.handelSalesman(e),
                                serial_number: this.handelSerial(e),
                                amount_status:e.amount_status,
                                settlement_amount:0,
                                prev_settlement:e.prev_settlement??0,
                            });
                        }else {
                            this.customerCredit.push({
                                id:e.id,
                                break_type:e.break_type,
                                document:e.document,
                                amount:parseFloat(e.credit),
                                instalment_date:e.instalment_date,
                                // salesman: this.handelSalesman(e),
                                serial_number: this.handelSerial(e),
                                amount_status:e.amount_status,
                                settlement_amount:0,
                                prev_settlement:e.prev_settlement??0,
                            });
                        }

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
            this.serial_number = invoice.prefix;
            this.invoice_id = invoice.id;
            await this.getCustomers(invoice.salesmen_id);
            await this.getSalesmen();
            await this.getPaymentMethod();
            await this.getBranches();
            this.edit.date = invoice.date;
            this.edit.customer_id = invoice.customer_id;
            this.edit.salesmen_id = invoice.salesmen_id;
            this.edit.payment_method_id = invoice.payment_method_id;
            this.edit.branch_id = invoice.branch_id;
            this.edit.document_id = invoice.document_id;
            this.edit.amount = invoice.amount;
            this.customer_data_edit = invoice.customer;
            if(this.document && this.document.attributes && this.parseInt(document.attributes.customer) == -1){
                await this.getBreakCustomer(this.edit.customer_id);
                await this.getTransactions(id);
                this.amount= this.totalTransferAmount ;
            }
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.customerBreak=[];
            this.breakTransactions=[];
            this.totalOrderAmount= 0;
            this.customer_data_edit = '';
            this.totalTransferAmount= 0;
            this.openTransfer=false;
            this.amountPaid=0;
            this.remainingAmount=0;
            this.amount=0;
            this.errors = {};
            this.serial_number = "";
            this.financial_years_validate = true;
            this.edit = {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesmen_id: null,
                payment_method_id: null,
                document_id: this.document_id,
                customer_id: null,
                amount:0
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
                    .get(`voucher-headers/logs/${id}`)
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

        async getSerialNumber(type='create',e)
        {
            let date = type=='create'? this.create.date : this.edit.date;
            let shortYear =new Date(date).getFullYear();
            let twoDigitYear = shortYear.toString().substr(-2);
            let branch = this.branches.find((row) => e == row.id);
            let serial = branch.serials.find((row) => this.document_id == row.document_id);
            this.serial_number = `${twoDigitYear}-${branch.id}-${this.document_id}-${serial.perfix}`;
        },
        async changeDateDocument(type='create')
        {
            let date = type=='create' ? this.create.date : this.edit.date;
            let branch_id = type=='create' ? this.create.branch_id : this.edit.branch_id;
            await this.checkFinancialYears(date,branch_id);
        },
        async checkFinancialYears(date,branch_id) {
            this.isLoader = true;
            await adminApi
                .get(`/document-headers/check-date?date=${date}`)
                .then((res) => {
                    let l = res.data;
                    if(!l)
                    {
                        this.financial_years_validate= false;
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.The date is outside the permitted fiscal year")}`,
                        });
                        this.serial_number = '';
                    }else{
                        this.financial_years_validate= true;
                        let shortYear =new Date(date).getFullYear();
                        let twoDigitYear = shortYear.toString().substr(-2);
                        let branch = this.branches.find((row) => branch_id == row.id);
                        let serial = branch.serials.find((row) => this.document_id == row.document_id);
                        this.serial_number = `${twoDigitYear}-${branch.id}-${this.document_id}-${serial.perfix}`;
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
        async searchCustomer(e) {
            let search = e??'';
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getCustomers(this.create.salesmen_id,search);
            }, 500);

        },
        async searchCustomerEdit(e) {
            let search = e??'';
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getCustomers(this.edit.salesmen_id,search);
            }, 500);

        },
        titleModelName(type='create')
        {
            if(this.document) {
                if (type == 'create')
                {
                    return `${this.$t('general.addNew')} ${this.$i18n.locale == 'ar'?this.document.name:this.document.name_e} `;
                }else {
                    return `${this.$t('general.Edit')} ${this.$i18n.locale == 'ar'?this.document.name:this.document.name_e} `;
                }
            }
        },
        checkDebitSettlement(index)
        {
            let settlement_amount = this.customerDebits[index].settlement_amount;
            let prev_settlement = this.customerDebits[index].prev_settlement;
            let amount = this.customerDebits[index].amount;
            if(parseFloat(settlement_amount) < 0){
                this.customerDebits[index].settlement_amount = 0 ;
            }else if ((amount - prev_settlement) >= settlement_amount ){
                this.accountTotalAmount();
            }else if((amount - prev_settlement) < settlement_amount) {
                this.customerDebits[index].settlement_amount = amount - prev_settlement;
                this.accountTotalAmount();
            }

        },
        checkCreditSettlement(index)
        {
            let settlement_amount = this.customerCredit[index].settlement_amount;
            let prev_settlement = this.customerCredit[index].prev_settlement;
            let amount = this.customerCredit[index].amount;
            if(parseFloat(settlement_amount) < 0){
                this.customerCredit[index].settlement_amount = 0 ;
            }else if ((amount - prev_settlement) >= settlement_amount ){
                this.accountTotalAmount();
            }else if((amount - prev_settlement) < settlement_amount) {
                this.customerCredit[index].settlement_amount = amount - prev_settlement;
                this.accountTotalAmount();
            }

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
            this.totalDebitSettlement = 0;
            this.customerDebits.forEach((el,index)=>{
                this.totalDebitSettlement += parseFloat(el.settlement_amount);
            });

            //account total amount transfer order
            this.totalCreditSettlement = 0;
            this.customerCredit.forEach((el,index)=>{
                this.totalCreditSettlement += parseFloat(el.settlement_amount);
            });
        },
        validationBeforeSubmit()
        {
            let amount = this.create.amount;
            if (parseInt(this.document.attributes.customer) == -1){
                if ( amount !=(this.totalDebitSettlement - this.totalCreditSettlement)){
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.There_is_an_error_in_the_calculations")}`,
                    });
                    return false;
                }
            }else if(parseInt(this.document.attributes.customer) == 1){
                if ( amount !=(this.totalCreditSettlement - this.totalDebitSettlement)){
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.There_is_an_error_in_the_calculations")}`,
                    });
                    return false;
                }
            }
            return true;
        },
        resetDebitsAndCredit()
        {
            this.customerDebits.forEach((e) => {
                e.settlement_amount=0
            });
            this.customerCredit.forEach((e) => {
                e.settlement_amount=0
            });
            this.totalDebitSettlement = 0;
            this.totalCreditSettlement = 0;
        },
        autoSettlement()
        {
            this.resetDebitsAndCredit();
            let amount = this.create.amount;
            if (parseInt(this.document.attributes.customer) == -1){
                this.customerDebits.forEach((e,index) => {
                    if ((e.amount - e.prev_settlement) != 0 && amount > 0)
                    {
                        if (amount >=  e.amount - e.prev_settlement)
                        {
                            e.settlement_amount= e.amount - e.prev_settlement;
                            amount -= e.amount - e.prev_settlement;
                        }else{
                            e.settlement_amount= amount;
                            amount -= amount
                        }
                    }
                });
            }else if(parseInt(this.document.attributes.customer) == 1){
                this.customerCredit.forEach((e,index) => {
                    if ((e.amount - e.prev_settlement) != 0 && amount > 0)
                    {
                        if (amount >=  e.amount - e.prev_settlement)
                        {
                            e.settlement_amount= e.amount - e.prev_settlement;
                            amount -= e.amount - e.prev_settlement;
                        }else{
                            e.settlement_amount= amount;
                            amount -= amount
                        }

                    }
                });
            }
            this.accountTotalAmount();
            if (amount)
            {
                Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.TheSettlementAmountIsGreaterThanTheInstallments")}`,
                });
            }

        }
    }
}
</script>

<template>
    <div>
        <div class="invoice-container row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title" v-if="document">{{  $i18n.locale == "ar" ? $t('general.table') + ' ' + document.name : document.name_e+ ' ' +$t('general.table')  }}</h4>
                            <h4 class="header-title" v-else>{{ $t('general.table') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting" value="branch_id" class="mb-1">{{ $t('general.Branch') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="prefix" class="mb-1">{{ $t('general.serial_number') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="date" class="mb-1">{{ $t('general.Date') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="customer_id" class="mb-1">{{ $t('general.client') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="amount" class="mb-1">{{ $t('general.amount') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="payment_method_id" class="mb-1">{{ $t('general.paymentMethod') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="salesmen_id" class="mb-1">{{ $t('general.documentSalesmen') }}</b-form-checkbox>
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
                                <b-button  v-b-modal.create variant="primary" class="btn-sm mx-1 font-weight-bold">
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
<!--                                    <button class="custom-btn-dowonload"-->
<!--                                            @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"-->
<!--                                            v-if="checkAll.length == 1">-->
<!--                                        <i class="mdi mdi-square-edit-outline"></i>-->
<!--                                    </button>-->
                                    <!-- start mult delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length > 1"
                                            @click.prevent="deleteScreenButton(checkAll)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
<!--                                     end mult delete  -->
<!--                                      start one delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length == 1"
                                            @click.prevent="deleteScreenButton(checkAll[0])">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
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
                                        <b-form-checkbox v-model="setting.branch_id" class="mb-1">{{ $t('general.Branch') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="setting.serial_number" class="mb-1"> {{ $t('general.serial_number') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="setting.date" class="mb-1">{{ $t('general.Date') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="setting.customer_id" class="mb-1">{{$t('general.client') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="setting.amount" class="mb-1">{{$t('general.amount') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="setting.payment_method_id" class="mb-1">{{$t('general.paymentMethod') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="setting.salesmen_id" class="mb-1">{{ $t('general.documentSalesmen') }}</b-form-checkbox>

                                        <div class="d-flex justify-content-end">
                                            <a href="javascript:void(0)" class="btn btn-primary btn-sm">{{$t("general.Apply")}}</a>
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
                                 :title="titleModelName('create')"
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">{{ $t('general.Branch') }} <span class="text-danger">*</span></label>

                                            <multiselect
                                                @input="getSerialNumber('create',$event)"
                                                v-model="create.branch_id"
                                                :options="branches.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar'? branches.find((x) => x.id == opt).name: branches.find((x) => x.id == opt).name_e"
                                                :class="{'is-invalid': $v.create.branch_id.$error || errors.branch_id,}"
                                            >
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.Date') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                :disabled="!create.branch_id"
                                                @input="changeDateDocument('create')"
                                                type="date"
                                                v-model="create.date"
                                                format="YYYY-MM-DD"
                                                valueType="format"
                                                :confirm="false"
                                                :class="{'is-invalid': !financial_years_validate}"

                                            ></date-picker>
                                            <div v-if="!financial_years_validate" class="invalid-feedback">
                                                {{ $t("general.The date is outside the permitted fiscal year") }}
                                            </div>
                                            <template v-if="errors.date">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.date" :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{$t('general.serial_number')}}
                                            </label>
                                            <input
                                                :disabled="true"
                                                type="text"
                                                class="form-control"
                                                v-model="serial_number"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">{{ $t('general.documentSalesmen') }} <span class="text-danger">*</span></label>

                                            <multiselect
                                                :disabled="!create.branch_id"
                                                @input="getCustomerSalesman"
                                                v-model="create.salesmen_id"
                                                :options="salesmen.map((type) => type.id)"
                                                :custom-label=" (opt) => $i18n.locale == 'ar' ? salesmen.find((x) => x.id == opt).name: salesmen.find((x) => x.id == opt).name_e"
                                                :class="{'is-invalid':$v.create.salesmen_id.$error || errors.salesmen_id,}"
                                            >
                                            </multiselect>
                                            <div v-if="!$v.create.salesmen_id.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.salesmen_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.salesmen_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{getCompanyKey('money_voucher_client_type')}}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <multiselect
                                                :disabled="true"
                                                v-model="create.client_type_id"
                                                :options="clientTypes.map((type) => type.id)"
                                                :custom-label=" (opt) => clientTypes.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? clientTypes.find((x) => x.id == opt).name: clientTypes.find((x) => x.id == opt).name_e:''"
                                                :class="{'is-invalid':$v.create.client_type_id.$error || errors.client_type_id,}"
                                            >
                                            </multiselect>
                                            <div v-if="!$v.create.client_type_id.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.client_type_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.client_type_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">{{ $t('general.client') }} <span class="text-danger">*</span></label>
                                            <multiselect
                                                :disabled="!create.branch_id"
                                                :internalSearch="false"
                                                @search-change="searchCustomer"
                                                @input="getBreakCustomer(create.customer_id)"
                                                v-model="create.customer_id"
                                                :options="customers.map((type) => type.id)"
                                                :custom-label="(opt) =>customers.find((x) => x.id == opt)?$i18n.locale == 'ar' ? customers.find((x) => x.id == opt).name: customers.find((x) => x.id == opt).name_e:''"
                                                :class="{'is-invalid':$v.create.customer_id.$error || errors.customer_id,}"
                                            >
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ $t("general.paymentMethod") }}</label>
                                            <multiselect
                                                :disabled="!create.branch_id"
                                                v-model="create.payment_method_id"
                                                :options="paymentMethods.map((type) => type.id)" :custom-label="
                                                (opt) => $i18n.locale == 'ar' ? paymentMethods.find((x) => x.id == opt).name : paymentMethods.find((x) => x.id == opt).name_e"
                                                :class="{ 'is-invalid':$v.create.payment_method_id.$error || errors.payment_method_id,}"
                                            >
                                            </multiselect>
                                            <div v-if="!$v.create.payment_method_id.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.payment_method_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.payment_method_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ $t("general.amount") }}</label>
                                            <input
                                                :disabled="!create.branch_id"
                                                type="number"
                                                class="form-control"
                                                step="any"
                                                v-model="create.amount"
                                                :class="{'is-invalid':$v.create.amount.$error || errors.amount,}"
                                            />
                                            <div v-if="!$v.create.amount.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.amount">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.amount"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-3" v-if="document && parseInt(document.is_break) == 2">
                                        <div class="form-check mt-3">
                                            <input style="transform: scale(1.5);" type="checkbox" v-model="open_invoice_details" value="true" id="flexCheckDefault1">
                                            <label style="padding:9px" class="form-check-label" for="flexCheckDefault1">
                                                {{$t('general.showInvoiceDetails')}}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3" v-if="create.customer_id && open_invoice_details">
                                        <div class="form-check mt-3">
                                            <input style="transform: scale(1.5);" @change="getBreakCustomer(create.customer_id)" type="checkbox" v-model="with_paid" value="true" id="flexCheckDefault">
                                            <label style="padding:9px" class="form-check-label" for="flexCheckDefault">
                                                {{$t('general.showPayment')}}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3" v-if="create.amount && create.customer_id && open_invoice_details">
                                        <b-button
                                            variant="primary"
                                            class="mx-1 mt-3"
                                            @click.prevent="autoSettlement"
                                        >
                                            {{ $t("general.AutoSettlement") }}
                                        </b-button>
                                    </div>
                                    <div class="col-md-12" v-if="document && document.attributes && parseInt(document.attributes.customer) != 0 && parseInt(document.is_break) > 0 && open_invoice_details">
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
                                                                                <th>{{ $t("general.documentSerial") }}</th>
                                                                                <!--                                                                <th>{{ $t("general.salesmen") }}</th>-->
                                                                                <th>{{ $t("general.type") }}</th>
                                                                                <th>{{ $t("general.Date") }}</th>
                                                                                <th>{{ $t("general.amount") }}</th>
                                                                                <th>{{ $t("general.prev_settlement") }}</th>
                                                                                <th>{{ $t("general.settlement_amount") }}</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody v-if="customerDebits.length > 0">
                                                                            <tr v-for="(data, index) in customerDebits"
                                                                                :key="data.id"
                                                                                class="body-tr-custom"
                                                                            >
                                                                                <td>
                                                                                    <h5 class="m-0 font-weight-normal">{{ data.serial_number }}</h5>
                                                                                </td>
                                                                                <!--                                                                <td>-->
                                                                                <!--                                                                    <h5 class="m-0 font-weight-normal">{{ data.salesman }}</h5>-->
                                                                                <!--                                                                </td>-->
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
                                                                                <td>
                                                                                    <h5 class="m-0 font-weight-normal">{{ data.prev_settlement }}</h5>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="form-group">
                                                                                        <input
                                                                                            @input="checkDebitSettlement(index)"
                                                                                            type="number"
                                                                                            step="any"
                                                                                            class="form-control englishInput"
                                                                                            v-model="customerDebits[index].settlement_amount"
                                                                                        />
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr v-if="customerDebits.length > 0" class="total-amount">
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td colspan="3"><h5 class="m-0 font-weight-normal">{{$t('general.totalAmountSettlement')}}</h5></td>
                                                                                <td v-html="totalDebitSettlement" class="amount-red"></td>
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
                                                                            {{$t('general.CustomerCredit')}}
                                                                        </h3>
                                                                    </div>
                                                                    <div class="table-responsive mb-3 custom-table-theme position-relative">
                                                                        <!--       start loader       -->
                                                                        <loader size="large" v-if="isLoader" />
                                                                        <!--       end loader       -->

                                                                        <table class="table table-borderless table-hover table-centered m-0" >
                                                                            <thead>
                                                                            <tr>
                                                                                <th>{{ $t("general.documentSerial") }}</th>
                                                                                <!--                                                                <th>{{ $t("general.salesmen") }}</th>-->
                                                                                <th>{{ $t("general.type") }}</th>
                                                                                <th>{{ $t("general.Date") }}</th>
                                                                                <th>{{ $t("general.amount") }}</th>
                                                                                <th>{{ $t("general.prev_settlement") }}</th>
                                                                                <th>{{ $t("general.settlement_amount") }}</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody v-if="customerCredit.length > 0">
                                                                            <tr v-for="(data, index) in customerCredit"
                                                                                :key="data.id"
                                                                                class="body-tr-custom"
                                                                            >
                                                                                <td>
                                                                                    <h5 class="m-0 font-weight-normal">{{ data.serial_number }}</h5>
                                                                                </td>
                                                                                <!--                                                                <td>-->
                                                                                <!--                                                                    <h5 class="m-0 font-weight-normal">{{ data.salesman }}</h5>-->
                                                                                <!--                                                                </td>-->
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
                                                                                <td>
                                                                                    <h5 class="m-0 font-weight-normal">{{ data.prev_settlement }}</h5>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="form-group">
                                                                                        <input
                                                                                            @input="checkCreditSettlement(index)"
                                                                                            type="number"
                                                                                            step="any"
                                                                                            class="form-control englishInput"
                                                                                            v-model="customerCredit[index].settlement_amount"
                                                                                        />
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                            <tr v-if="customerCredit.length > 0" class="total-amount">
                                                                                <td></td>
                                                                                <td></td>
                                                                                <td colspan="3"><h5 class="m-0 font-weight-normal">{{$t('general.totalAmountSettlement')}}</h5></td>
                                                                                <td v-html="totalCreditSettlement" class="amount-red"></td>
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
                                    <th v-if="setting.branch_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Branch') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="invoices.sort( sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="invoices.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.serial_number">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.serial_number") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"  @click="invoices.sort(sortString('prefix'))"></i>
                                                <i class="fas fa-arrow-down" @click="invoices.sort(sortString('-prefix'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Date') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"  @click="invoices.sort(sortString( 'date'))"></i>
                                                <i class="fas fa-arrow-down" @click="invoices.sort(sortString('-date'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.customer_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.client') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="invoices.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="invoices.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.amount">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.amount') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"  @click="invoices.sort(sortString( 'amount'))"></i>
                                                <i class="fas fa-arrow-down" @click="invoices.sort(sortString('-amount'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.payment_method_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.paymentMethod') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="invoices.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="invoices.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.salesmen_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.documentSalesmen') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="invoices.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="invoices.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
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
                                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                                    v-for="(data, index) in invoices" :key="data.id" class="body-tr-custom">
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control" style="min-height: 1.9em">
                                            <input style="width: 17px; height: 17px" class="form-check-input"
                                                   type="checkbox" :value="data.id" v-model="checkAll"/>
                                        </div>
                                    </td>
                                    <td v-if="setting.branch_id">
                                        <h5 class="m-0 font-weight-normal">{{data.branch? $i18n.locale == "ar" ? data.branch.name : data.branch.name_e : ''}}</h5>
                                    </td>
                                    <td v-if="setting.serial_number">
                                        <h5 class="m-0 font-weight-normal">{{ data.prefix }}</h5>
                                    </td>
                                    <td v-if="setting.date">
                                        <h5 class="m-0 font-weight-normal">{{ data.date }}</h5>
                                    </td>
                                    <td v-if="setting.customer_id">
                                        <h5 class="m-0 font-weight-normal">{{$i18n.locale == "ar" ? data.customer.name : data.customer.name_e}}</h5>
                                    </td>
                                    <td v-if="setting.amount">
                                        <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>
                                    </td>
                                    <td v-if="setting.payment_method_id">
                                        <h5 class="m-0 font-weight-normal">{{$i18n.locale == "ar" ? data.paymentMethod.name : data.paymentMethod.name_e}}</h5>
                                    </td>
                                    <td v-if="setting.salesmen_id">
                                        <h5 class="m-0 font-weight-normal">{{ $i18n.locale == "ar" ? data.salesmen.name : data.salesmen.name_e}}</h5>
                                    </td>

                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                {{ $t("general.commands") }}
                                                <i class="fas fa-angle-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-custom">
<!--                                                <a class="dropdown-item" href="#"-->
<!--                                                   @click="$bvModal.show(`modal-edit-${data.id}`)">-->
<!--                                                    <div-->
<!--                                                        class="d-flex justify-content-between align-items-center text-black">-->
<!--                                                        <span>{{ $t("general.edit") }}</span>-->
<!--                                                        <i class="mdi mdi-square-edit-outline text-info"></i>-->
<!--                                                    </div>-->
<!--                                                </a>-->
                                                <a class="dropdown-item text-black" href="#"
                                                   @click.prevent="deleteScreenButton(data.id)">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black">
                                                        <span>{{ $t("general.delete") }}</span>
                                                        <i class="fas fa-times text-danger"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>

                                        <!--  edit   -->
<!--                                        <b-modal dialog-class="modal-full-width" :id="`modal-edit-${data.id}`"-->
<!--                                                 :title="$t('general.editReceiptVoucher')" title-class="font-18"-->
<!--                                                 body-class="p-4" :ref="`edit-${data.id}`" :hide-footer="true"-->
<!--                                                 @show="resetModalEdit(data.id)"-->
<!--                                                 @hidden="resetModalHiddenEdit(data.id)">-->
<!--                                            <form>-->
<!--                                                <div class="mb-3 d-flex justify-content-end">-->
<!--                                                    &lt;!&ndash; Emulate built in modal footer ok and cancel button actions &ndash;&gt;-->
<!--                                                    <b-button variant="success" type="button" class="mx-1"-->
<!--                                                              v-if="!isLoader" @click.prevent="editSubmit(data.id)">-->
<!--                                                        {{ $t("general.Edit") }}-->
<!--                                                    </b-button>-->

<!--                                                    <b-button variant="success" class="mx-1" disabled v-else>-->
<!--                                                        <b-spinner small></b-spinner>-->
<!--                                                        <span class="sr-only">{{ $t("login.Loading") }}...</span>-->
<!--                                                    </b-button>-->

<!--                                                    <b-button variant="danger" type="button"-->
<!--                                                              @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)">-->
<!--                                                        {{ $t("general.Cancel") }}-->
<!--                                                    </b-button>-->
<!--                                                </div>-->
<!--                                                <div class="row">-->
<!--                                                    <div class="col-md-3">-->
<!--                                                        <div class="form-group">-->
<!--                                                            <label class="control-label">{{ $t('general.Branch') }} <span class="text-danger">*</span></label>-->

<!--                                                            <multiselect-->
<!--                                                                @input="getSerialNumber('edit',$event)"-->
<!--                                                                v-model="edit.branch_id"-->
<!--                                                                :options="branches.map((type) => type.id)"-->
<!--                                                                :custom-label="(opt) => $i18n.locale == 'ar'? branches.find((x) => x.id == opt).name: branches.find((x) => x.id == opt).name_e"-->
<!--                                                                :class="{'is-invalid': $v.edit.branch_id.$error || errors.branch_id,}"-->
<!--                                                            >-->
<!--                                                            </multiselect>-->
<!--                                                            <div v-if="!$v.edit.branch_id.required" class="invalid-feedback">-->
<!--                                                                {{ $t("general.fieldIsRequired") }}-->
<!--                                                            </div>-->

<!--                                                            <template v-if="errors.branch_id">-->
<!--                                                                <ErrorMessage v-for="(errorMessage, index) in errors.branch_id"-->
<!--                                                                              :key="index">{{ errorMessage }}-->
<!--                                                                </ErrorMessage>-->
<!--                                                            </template>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                    <div class="col-md-3">-->
<!--                                                        <div class="form-group">-->
<!--                                                            <label class="control-label">-->
<!--                                                                {{ $t('general.Date') }}-->
<!--                                                                <span class="text-danger">*</span>-->
<!--                                                            </label>-->
<!--                                                            <date-picker-->
<!--                                                                :disabled="!edit.branch_id"-->
<!--                                                                @input="changeDateDocument('edit')"-->
<!--                                                                type="date"-->
<!--                                                                v-model="edit.date"-->
<!--                                                                format="YYYY-MM-DD"-->
<!--                                                                valueType="format"-->
<!--                                                                :confirm="false"-->
<!--                                                                :class="{'is-invalid': !financial_years_validate}"-->

<!--                                                            ></date-picker>-->
<!--                                                            <div v-if="!financial_years_validate" class="invalid-feedback">-->
<!--                                                                {{ $t("general.The date is outside the permitted fiscal year") }}-->
<!--                                                            </div>-->
<!--                                                            <template v-if="errors.date">-->
<!--                                                                <ErrorMessage v-for="(errorMessage, index) in errors.date" :key="index">-->
<!--                                                                    {{ errorMessage }}-->
<!--                                                                </ErrorMessage>-->
<!--                                                            </template>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                    <div class="col-md-3">-->
<!--                                                        <div class="form-group">-->
<!--                                                            <label class="control-label">-->
<!--                                                                {{$t('general.serial_number')}}-->
<!--                                                            </label>-->
<!--                                                            <input-->
<!--                                                                :disabled="true"-->
<!--                                                                type="text"-->
<!--                                                                class="form-control"-->
<!--                                                                v-model="serial_number"-->
<!--                                                            />-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                    <div class="col-md-3">-->
<!--                                                        <div class="form-group">-->
<!--                                                            <label class="control-label">{{ $t('general.documentSalesmen') }} <span class="text-danger">*</span></label>-->

<!--                                                            <multiselect-->
<!--                                                                :disabled="!edit.branch_id"-->
<!--                                                                @input="getCustomerSalesman"-->
<!--                                                                v-model="edit.salesmen_id"-->
<!--                                                                :options="salesmen.map((type) => type.id)"-->
<!--                                                                :custom-label=" (opt) => $i18n.locale == 'ar' ? salesmen.find((x) => x.id == opt).name: salesmen.find((x) => x.id == opt).name_e"-->
<!--                                                                :class="{'is-invalid':$v.edit.salesmen_id.$error || errors.salesmen_id,}"-->
<!--                                                            >-->
<!--                                                            </multiselect>-->
<!--                                                            <div v-if="!$v.edit.salesmen_id.required" class="invalid-feedback">-->
<!--                                                                {{ $t("general.fieldIsRequired") }}-->
<!--                                                            </div>-->
<!--                                                            <template v-if="errors.salesmen_id">-->
<!--                                                                <ErrorMessage v-for="(errorMessage, index) in errors.salesmen_id"-->
<!--                                                                              :key="index">{{ errorMessage }}-->
<!--                                                                </ErrorMessage>-->
<!--                                                            </template>-->
<!--                                                        </div>-->
<!--                                                    </div>-->

<!--                                                    <div class="col-md-3">-->
<!--                                                        <div class="form-group">-->
<!--                                                            <label class="control-label">{{ $t('general.client') }} <span class="text-danger">*</span></label>-->
<!--                                                            <multiselect-->
<!--                                                                :disabled="!edit.branch_id"-->
<!--                                                                :internalSearch="false"-->
<!--                                                                @search-change="searchCustomer"-->
<!--                                                                v-model="edit.customer_id"-->
<!--                                                                :options="customers.map((type) => type.id)"-->
<!--                                                                :custom-label="(opt) =>$i18n.locale == 'ar' ? customers.find((x) => x.id == opt).name: customers.find((x) => x.id == opt).name_e"-->
<!--                                                                :class="{'is-invalid':$v.edit.customer_id.$error || errors.customer_id,}"-->
<!--                                                            >-->
<!--                                                            </multiselect>-->
<!--                                                            <div v-if="!$v.edit.customer_id.required" class="invalid-feedback">-->
<!--                                                                {{ $t("general.fieldIsRequired") }}-->
<!--                                                            </div>-->

<!--                                                            <template v-if="errors.customer_id">-->
<!--                                                                <ErrorMessage v-for="(errorMessage, index) in errors.customer_id"-->
<!--                                                                              :key="index">{{ errorMessage }}-->
<!--                                                                </ErrorMessage>-->
<!--                                                            </template>-->
<!--                                                        </div>-->
<!--                                                    </div>-->

<!--                                                    <div class="col-md-3">-->
<!--                                                        <div class="form-group">-->
<!--                                                            <label>{{ $t("general.paymentMethod") }}</label>-->
<!--                                                            <multiselect-->
<!--                                                                :disabled="!edit.branch_id"-->
<!--                                                                v-model="edit.payment_method_id"-->
<!--                                                                :options="paymentMethods.map((type) => type.id)"-->
<!--                                                                :custom-label=" (opt) => $i18n.locale == 'ar' ? paymentMethods.find((x) => x.id == opt).name : paymentMethods.find((x) => x.id == opt).name_e"-->
<!--                                                                :class="{ 'is-invalid':$v.edit.payment_method_id.$error || errors.payment_method_id,}"-->
<!--                                                            >-->
<!--                                                            </multiselect>-->
<!--                                                            <div v-if="!$v.edit.payment_method_id.required" class="invalid-feedback">-->
<!--                                                                {{ $t("general.fieldIsRequired") }}-->
<!--                                                            </div>-->
<!--                                                            <template v-if="errors.payment_method_id">-->
<!--                                                                <ErrorMessage v-for="(errorMessage, index) in errors.payment_method_id"-->
<!--                                                                              :key="index">{{ errorMessage }}-->
<!--                                                                </ErrorMessage>-->
<!--                                                            </template>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                    <div class="col-md-3">-->
<!--                                                        <div class="form-group">-->
<!--                                                            <label>{{ $t("general.amount") }}</label>-->
<!--                                                            <input-->
<!--                                                                :disabled="!edit.branch_id"-->
<!--                                                                type="number"-->
<!--                                                                class="form-control"-->
<!--                                                                step="any"-->
<!--                                                                v-model="edit.amount"-->
<!--                                                                :class="{'is-invalid':$v.edit.amount.$error || errors.amount,}"-->
<!--                                                            />-->
<!--                                                            <div v-if="!$v.edit.amount.required" class="invalid-feedback">-->
<!--                                                                {{ $t("general.fieldIsRequired") }}-->
<!--                                                            </div>-->
<!--                                                            <template v-if="errors.amount">-->
<!--                                                                <ErrorMessage v-for="(errorMessage, index) in errors.amount"-->
<!--                                                                              :key="index">{{ errorMessage }}-->
<!--                                                                </ErrorMessage>-->
<!--                                                            </template>-->
<!--                                                        </div>-->
<!--                                                    </div>-->
<!--                                                    <div class="col-md-12" v-if="document && document.attributes && parseInt(document.attributes.customer) == -1">-->
<!--                                                        <div class="page-content">-->
<!--                                                            <div class="px-0">-->
<!--                                                                <div class="row mt-4">-->
<!--                                                                    <div class="col-12 col-lg-12">-->
<!--                                                                        <div class="row">-->
<!--                                                                            <div class="col-12">-->
<!--                                                                                <div class="text-center text-150">-->
<!--                                                                                    <i style="font-size:20px" class="fa fa-book fa-2x text-success-m2 mr-1"></i>-->
<!--                                                                                    <span class="text-default-d3">{{$t("general.invoice_details")}}</span>-->
<!--                                                                                </div>-->
<!--                                                                            </div>-->
<!--                                                                        </div>-->
<!--                                                                        &lt;!&ndash; .row &ndash;&gt;-->
<!--                                                                        <div class="mt-4">-->
<!--                                                                            <div class="row">-->
<!--                                                                                <div class="col-md-6">-->
<!--                                                                                    <div class="card-header p-0">-->
<!--                                                                                        <h3 class="card-title float-left">-->
<!--                                                                                            {{$t('general.CustomerDebts')}}-->
<!--                                                                                        </h3>-->
<!--                                                                                    </div>-->
<!--                                                                                    <div class="table-responsive mb-3 custom-table-theme position-relative">-->
<!--                                                                                        &lt;!&ndash;       start loader       &ndash;&gt;-->
<!--                                                                                        <loader size="large" v-if="isLoader" />-->
<!--                                                                                        &lt;!&ndash;       end loader       &ndash;&gt;-->

<!--                                                                                        <table class="table table-borderless table-hover table-centered m-0" >-->
<!--                                                                                            <thead>-->
<!--                                                                                            <tr>-->
<!--                                                                                                <th>{{ $t("general.invoiceSerial") }}</th>-->
<!--                                                                                                <th>{{ $t("general.salesmen") }}</th>-->
<!--                                                                                                <th>{{ $t("general.type") }}</th>-->
<!--                                                                                                <th>{{ $t("general.Date") }}</th>-->
<!--                                                                                                <th>{{ $t("general.amount") }}</th>-->
<!--                                                                                                <th>{{ $t("general.Action") }}</th>-->
<!--                                                                                            </tr>-->
<!--                                                                                            </thead>-->
<!--                                                                                            <tbody v-if="customerBreak.length > 0">-->
<!--                                                                                            <tr v-for="(data, index) in customerBreak"-->
<!--                                                                                                :key="data.id"-->
<!--                                                                                                class="body-tr-custom"-->
<!--                                                                                            >-->
<!--                                                                                                <td>-->
<!--                                                                                                    <h5 class="m-0 font-weight-normal">{{ data.serial_number }}</h5>-->
<!--                                                                                                </td>-->
<!--                                                                                                <td>-->
<!--                                                                                                    <h5 class="m-0 font-weight-normal">{{ data.salesman }}</h5>-->
<!--                                                                                                </td>-->
<!--                                                                                                <td>-->
<!--                                                                                                    <h5 v-if="data.document" class="m-0 font-weight-normal">{{$i18n.locale == "ar" ? data.document.name : data.document.name_e}}</h5>-->
<!--                                                                                                    <h5 v-else class="m-0 font-weight-normal">-&#45;&#45;</h5>-->
<!--                                                                                                </td>-->
<!--                                                                                                <td>-->
<!--                                                                                                    <h5 class="m-0 font-weight-normal">-->
<!--                                                                                                        {{ data.instalment_date }}-->
<!--                                                                                                    </h5>-->
<!--                                                                                                </td>-->
<!--                                                                                                <td>-->
<!--                                                                                                    <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>-->
<!--                                                                                                </td>-->
<!--                                                                                                <td v-if="openTransfer">-->
<!--                                                                                                    <button @click.prevent="orderTransferToTransaction(index)" class="btn btn-primary btn-sm">>></button>-->
<!--                                                                                                </td>-->
<!--                                                                                                <td v-else><button :disabled="true" class="btn btn-secondary btn-sm">>></button></td>-->
<!--                                                                                            </tr>-->
<!--                                                                                            <tr v-if="customerBreak.length > 0" class="total-amount">-->
<!--                                                                                                <td></td>-->
<!--                                                                                                <td></td>-->
<!--                                                                                                <td></td>-->
<!--                                                                                                <td>{{$t('general.totalAmount')}}</td>-->
<!--                                                                                                <td v-html="totalOrderAmount" class="amount-red"></td>-->
<!--                                                                                                <td></td>-->
<!--                                                                                            </tr>-->
<!--                                                                                            </tbody>-->
<!--                                                                                            <tbody v-else>-->
<!--                                                                                            <tr>-->
<!--                                                                                                <th class="text-center" colspan="6">-->
<!--                                                                                                    {{ $t("general.notDataFound") }}-->
<!--                                                                                                </th>-->
<!--                                                                                            </tr>-->
<!--                                                                                            </tbody>-->
<!--                                                                                        </table>-->
<!--                                                                                    </div>-->
<!--                                                                                </div>-->
<!--                                                                                <div class="col-md-6">-->
<!--                                                                                    <div class="card-header p-0">-->
<!--                                                                                        <h3 class="card-title float-left">-->
<!--                                                                                            {{$t('general.transactions')}}-->
<!--                                                                                        </h3>-->
<!--                                                                                    </div>-->
<!--                                                                                    <div class="table-responsive mb-3 custom-table-theme position-relative">-->
<!--                                                                                        &lt;!&ndash;       start loader       &ndash;&gt;-->
<!--                                                                                        <loader size="large" v-if="isLoader" />-->
<!--                                                                                        &lt;!&ndash;       end loader       &ndash;&gt;-->

<!--                                                                                        <table class="table table-borderless table-hover table-centered m-0" >-->
<!--                                                                                            <thead>-->
<!--                                                                                            <tr>-->
<!--                                                                                                <th>{{ $t("general.invoiceSerial") }}</th>-->
<!--                                                                                                <th>{{ $t("general.salesmen") }}</th>-->
<!--                                                                                                <th>{{ $t("general.type") }}</th>-->
<!--                                                                                                <th>{{ $t("general.Date") }}</th>-->
<!--                                                                                                <th>{{ $t("general.amount") }}</th>-->
<!--                                                                                                <th>{{ $t("general.Action") }}</th>-->
<!--                                                                                            </tr>-->
<!--                                                                                            </thead>-->
<!--                                                                                            <tbody v-if="breakTransactions.length > 0">-->
<!--                                                                                            <tr v-for="(data, index) in breakTransactions"-->
<!--                                                                                                :key="data.id"-->
<!--                                                                                                class="body-tr-custom"-->
<!--                                                                                            >-->
<!--                                                                                                <td>-->
<!--                                                                                                    <h5 class="m-0 font-weight-normal">{{ data.serial_number }}</h5>-->
<!--                                                                                                </td>-->
<!--                                                                                                <td>-->
<!--                                                                                                    <h5 class="m-0 font-weight-normal">{{ data.salesman }}</h5>-->
<!--                                                                                                </td>-->
<!--                                                                                                <td>-->
<!--                                                                                                    <h5 v-if="data.document" class="m-0 font-weight-normal">{{$i18n.locale == "ar" ? data.document.name : data.document.name_e}}</h5>-->
<!--                                                                                                    <h5 v-else class="m-0 font-weight-normal">-&#45;&#45;</h5>-->
<!--                                                                                                </td>-->
<!--                                                                                                <td>-->
<!--                                                                                                    <h5 class="m-0 font-weight-normal">-->
<!--                                                                                                        {{ data.instalment_date }}-->
<!--                                                                                                    </h5>-->
<!--                                                                                                </td>-->
<!--                                                                                                <td>-->
<!--                                                                                                    <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>-->
<!--                                                                                                </td>-->
<!--                                                                                                <td v-if="openTransfer">-->
<!--                                                                                                    <button @click.prevent="ReturnTransactionToOrder(index)" class="btn btn-primary btn-sm"><<</button>-->
<!--                                                                                                </td>-->
<!--                                                                                                <td v-else><button :disabled="true" class="btn btn-secondary btn-sm"><<</button></td>-->
<!--                                                                                            </tr>-->
<!--                                                                                            <tr v-if="breakTransactions.length > 0" class="total-amount">-->
<!--                                                                                                <td></td>-->
<!--                                                                                                <td></td>-->
<!--                                                                                                <td></td>-->
<!--                                                                                                <td><h5 class="m-0 font-weight-normal">{{$t('general.totalAmount')}}</h5></td>-->
<!--                                                                                                <td v-html="totalTransferAmount" class="amount-red"></td>-->
<!--                                                                                                <td></td>-->
<!--                                                                                            </tr>-->

<!--                                                                                            </tbody>-->
<!--                                                                                            <tbody v-else>-->
<!--                                                                                            <tr>-->
<!--                                                                                                <th class="text-center" colspan="6">-->
<!--                                                                                                    {{ $t("general.notDataFound") }}-->
<!--                                                                                                </th>-->
<!--                                                                                            </tr>-->
<!--                                                                                            </tbody>-->
<!--                                                                                        </table>-->
<!--                                                                                    </div>-->
<!--                                                                                </div>-->
<!--                                                                                <div class="col-md-6">-->
<!--                                                                                    <label>{{$t('general.TheDifference')}}</label>-->
<!--                                                                                    <input type="text"-->
<!--                                                                                           class="form-control mb-1 input-Sender"-->
<!--                                                                                           v-model="remainingAmount"-->
<!--                                                                                           disabled-->
<!--                                                                                    >-->
<!--                                                                                </div>-->
<!--                                                                            </div>-->
<!--                                                                        </div>-->
<!--                                                                    </div>-->
<!--                                                                </div>-->
<!--                                                            </div>-->
<!--                                                        </div>-->

<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </form>-->
<!--                                        </b-modal>-->
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
    </div>
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
