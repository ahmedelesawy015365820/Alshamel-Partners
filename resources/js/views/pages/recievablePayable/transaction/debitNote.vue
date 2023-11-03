<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import adminApi from "../../../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength, integer, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../../components/widgets/errorMessage";
import loader from "../../../../components/general/loader";
import {dynamicSortString} from "../../../../helper/tableSort";
import {formatDateOnly} from "../../../../helper/startDate";
import translation from "../../../../helper/mixin/translation-mixin";
import DatePicker from "vue2-datepicker";
import customerGeneral from "../../../../components/create/general/customerGeneral";
import Multiselect from "vue-multiselect";
import currency from "../../../../components/create/general/currency";
import transactionBreak from "../../../../components/create/receivablePayment/transactionBreak/transactionBreak";
import permissionGuard from "../../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Debit Note",
        meta: [{name: "Debit Note", content: 'Debit Note'}],
    },
    mixins: [translation],
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        DatePicker,
        customerGeneral,
        Multiselect,
        currency,
        transactionBreak
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Depit Note RP", "all creditNote RP");
    });

    },
    data() {
        return {
            per_page: 50,
            search: '',
            debounce: {},
            installmentStatusPagination: {},
            installmentStatus: [],
            customers: [],
            currencies: [],
            isLoader: false,
            create: {
                id:null,
                date: this.formatDate(new Date()),
                customer_id: null,
                currency_id: 1,
                rate: 1,
                debit: 0,
                credit: 0,
                local_debit: 0,
                local_credit: 0,
            },
            edit: {
                id:null,
                date: this.formatDate(new Date()),
                customer_id: null,
                currency_id: 1,
                rate: 1,
                debit: 0,
                credit: 0,
                local_debit: 0,
                local_credit: 0,
            },
            errors: {},
            is_disabled: false,
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            enabled3: true,
            printLoading: true,
            printObj: {
                id: "printCustom",
            },
            openingBreak:'',
            setting: {
                customer_id: true,
                date: true,
                currency_id: true,
                rate: true,
                debit: true,
                credit: true,
                local_debit: true,
                local_credit: true,
            },
            filterSetting: [
                'customer_id',
                'date',
                'currency_id',
                'rate',
                'debit',
                'credit',
                'local_debit',
                'local_credit',
            ],
            Tooltip: '',
            mouseEnter: null,
        }
    },
    validations: {
        create: {
            customer_id: {required},
            currency_id: {required},
            rate: {required},
            date: {required},
            debit: {},
            credit: {},
            local_debit: {},
            local_credit: {},
        },
        edit: {
            customer_id: {required},
            currency_id: {required},
            rate: {required},
            date: {required},
            debit: {},
            credit: {},
            local_debit: {},
            local_credit: {},
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
                this.installmentStatus.forEach((el) => {
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
        isPermission(item) {
            if (this.$store.state.auth.type == 'user'){
                return this.$store.state.auth.permissions.includes(item)
            }
            return true;
        },
        showCustomerModal() {
            if (this.create.customer_id == 0) {
                this.$bvModal.show("customer-general-create");
                this.create.customer_id = null;
            }
        },
        showCustomerModalEdit() {
            if (this.edit.customer_id == 0) {
                this.$bvModal.show("customer-general-create");
                this.edit.customer_id = null;
            }
        },
        showCurrencyModal() {
            if (this.create.currency_id == 0) {
                this.$bvModal.show("currency-create");
                this.create.currency_id = null;
            }
        },
        showCurrencyEditModal() {
            if (this.edit.currency_id == 0) {
                this.$bvModal.show("currency-create");
                this.edit.currency_id = null;
            }
        },

        resetForm() {
            this.create = {
                id:null,
                date: this.formatDate(new Date()),
                customer_id: null,
                currency_id: 1,
                rate: 1,
                debit: 0,
                credit: 0,
                local_debit: 0,
                local_credit: 0,
            };
            this.invoice_id = null;

            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },

        /**
         *  start get Data module && pagination
         */
        getData(page = 1) {
            this.isLoader = true;
            let _filterSetting = [...this.filterSetting];
            let index = this.filterSetting.indexOf("customer_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
            }
            index = this.filterSetting.indexOf("currency_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "currency.name" : "currency.name_e";
            }
            let filter = '';
            for (let i = 0; i < _filterSetting.length; ++i) {
                filter += `columns[${i}]=${_filterSetting[i]}&`;
            }

            adminApi.get(`/recievable-payable/rp_debit_note?&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
                .then((res) => {
                    let l = res.data;
                    this.installmentStatus = l.data;
                    this.installmentStatusPagination = l.pagination;
                    this.current_page = l.pagination.current_page;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: 'error',
                        title: `${this.$t('general.Error')}`,
                        text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                    });
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getDataCurrentPage(page = 1) {
            if (this.current_page <= this.installmentStatusPagination.last_page && this.current_page != this.installmentStatusPagination.current_page && this.current_page) {
                this.isLoader = true;
                let _filterSetting = [...this.filterSetting];
                let index = this.filterSetting.indexOf("customer_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
                }
                index = this.filterSetting.indexOf("currency_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "currency.name" : "currency.name_e";
                }
                let filter = "";
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }

                adminApi.get(`/recievable-payable/rp_debit_note?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
                    .then((res) => {
                        let l = res.data;
                        this.installmentStatus = l.data;
                        this.installmentStatusPagination = l.pagination;
                        this.current_page = l.pagination.current_page;
                    })
                    .catch((err) => {
                        Swal.fire({
                            icon: 'error',
                            title: `${this.$t('general.Error')}`,
                            text: `${this.$t('general.Thereisanerrorinthesystem')}`,
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
         *  get customer
         */
        async getCustomers(opening_balance=0) {
            this.isLoader = true;
            await adminApi
                .get(`/general-customer?opening_balance=${opening_balance}`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Customer')){
                        l.unshift({id: 0, name: "اضافة زبون", name_e: "Add customer"});
                    }
                    this.customers = l;
                    this.isLoader = false;
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
         *  get currency
         */
        async getCurrencies() {
            this.isLoader = true;
            await adminApi
                .get(`/currencies`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Currency')){
                        l.unshift({id: 0, name: "اضف عمله جديده  ", name_e: "Add New Currency"});
                    }
                    this.currencies = l;
                    this.isLoader = false;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })
        },
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
                            .post(`/recievable-payable/rp_debit_note/bulk-delete`, { ids: id })
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
                            .delete(`/recievable-payable/rp_debit_note/${id}`)
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
                id:null,
                date: new Date().toISOString().slice(0, 10),
                customer_id: null,
                currency_id: 1,
                rate: 1,
                debit: 0,
                credit: 0,
                local_debit: 0,
                local_credit: 0,
            };
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
            this.$bvModal.hide(`create`);
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            await this.getCustomers(1);
            await this.getCurrencies();
            this.create = {
                id:null,
                date: new Date().toISOString().slice(0, 10),
                customer_id: null,
                currency_id: 1,
                rate: 1,
                debit: 0,
                credit: 0,
                local_debit: 0,
                local_credit: 0,
            };
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
        },
        /**
         *  create module
         */
        AddSubmit() {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi.post(`/recievable-payable/rp_debit_note`, this.create)
                    .then((res) => {
                        this.is_disabled = true;
                        this.getData();
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                text: `${this.$t('general.Addedsuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: `${this.$t('general.Error')}`,
                                text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                            });
                        }
                    }).finally(() => {
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
                adminApi.put(`/recievable-payable/rp_debit_note/${id}`, this.edit)
                    .then((res) => {
                        this.getData();
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                text: `${this.$t('general.Editsuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }, 500);
                        this.showBreakEdit();
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: `${this.$t('general.Error')}`,
                                text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                            });
                        }
                    }).finally(() => {
                    this.isLoader = false;
                });
            }
        },

        showBreakEdit(){
            if (this.edit.id) {
                this.openingBreak = this.edit;
                this.openingBreak.is_update = true;
                this.openingBreak.break_type = 'debitNote';
                this.openingBreak.document_id = 7;
                this.$bvModal.show("opening-balance-break-create");
            }
        },

        /**
         *   show Modal (edit)
         */
        async resetModalEdit(id) {
            await this.getCustomers();
            await this.getCurrencies();
            let module = this.installmentStatus.find((e) => id == e.id);
            this.edit.id = module.id;
            this.edit.date = module.date;
            this.edit.customer_id = module.customer_id;
            this.edit.currency_id = module.currency_id;
            this.edit.rate = module.rate;
            this.edit.debit = module.debit;
            this.edit.credit = module.credit;
            this.edit.local_debit = module.local_debit;
            this.edit.local_credit = module.local_credit;
            this.errors = {};

        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.is_disabled = false;
            this.errors = {};
            this.edit = {
                id:null,
                date: this.formatDate(new Date()),
                customer_id: null,
                currency_id: 1,
                rate: 1,
                debit: 0,
                credit: 0,
                local_debit: 0,
                local_credit: 0,
            };
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
        moveInput(tag, c, index) {
            document.querySelector(`${tag}[data-${c}='${index}']`).focus()
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`/recievable-payable/rp_debit_note/logs/${id}`)
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
                    XLSX.writeFile(wb, fn || (('DebitNote' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },

        /**
         *  account create
         */
        accountRateCreate() {
            if (this.create.local_credit && this.create.local_credit > 0) {
                this.accountLocalCreditForCreate();
            }
            if (this.create.local_debit && this.create.local_debit > 0) {
                this.accountLocalDebitForCreate();
            }
        },
        accountLocalCreditForCreate() {
            this.create.credit = this.create.local_credit * this.create.rate;
        },
        accountLocalDebitForCreate() {
            this.create.debit = this.create.local_debit * this.create.rate;
        },
        /**
         *  end create
         */


        /**
         *  account edit
         */
        accountRateEdit() {
            if (this.edit.local_credit && this.edit.local_credit > 0) {
                this.accountLocalCreditForEdit();
            }
            if (this.edit.local_debit && this.edit.local_debit > 0) {
                this.accountLocalDebitForEdit();
            }
        },
        accountLocalCreditForEdit() {
            this.edit.credit = this.edit.local_credit * this.edit.rate;
        },
        accountLocalDebitForEdit() {
            this.edit.debit = this.edit.local_debit * this.edit.rate;
        },
        /**
         *  end edit
         */

        /**
         *  Start create Open Balance And Open Break
         */
       async saveOpenAndBreak(){
            this.$v.create.$touch();
            if ( this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
               await adminApi.post(`/recievable-payable/rp_debit_note`, this.create)
                    .then((res) => {
                        this.is_disabled = true;
                        this.getData();
                        this.create.id = res.data.data.id;
                        this.showBreakCreate();
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                text: `${this.$t('general.Addedsuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: `${this.$t('general.Error')}`,
                                text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                            });
                        }
                    }).finally(() => {
                    this.isLoader = false;
                    this.is_disabled= false;
                });
            }
        },
        showBreakCreate(){
            if (this.create.id) {
                this.openingBreak = this.create;
                this.openingBreak.break_type = 'debitNote';
                this.openingBreak.document_id = 7;
                this.$bvModal.show("opening-balance-break-create");
            }
        },
        /**
         *  End create Open Balance And Open Break
         */
    },
};
</script>

<template>
    <Layout>
        <PageHeader/>
        <customerGeneral :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCustomers(1)"/>
        <currency :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getCurrencies"/>
        <transactionBreak :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :opening="openingBreak"/>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title"> {{ getCompanyKey('debit_note_table') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">

                                <div class="d-inline-block" style="width: 22.2%;">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting"
                                                         :value="$i18n.locale == 'ar' ? 'customer.name' : 'customer.name_e'"
                                                         class="mb-1">{{ getCompanyKey("customer") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="date" class="mb-1">
                                            {{ getCompanyKey('installment_opening_balance_date') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting"
                                                         :value="$i18n.locale == 'ar' ? 'currency.name' : 'currency.name_e'"
                                                         class="mb-1">{{ getCompanyKey("installment_opening_balance_currency") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="date" class="mb-1">
                                            {{ getCompanyKey('installment_opening_balance_rate') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="date" class="mb-1">
                                            {{ getCompanyKey('installment_opening_balance_local_debit') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="date" class="mb-1">
                                            {{ getCompanyKey('installment_opening_balance_local_credit') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="date" class="mb-1">
                                            {{ getCompanyKey('installment_opening_balance_debit') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="date" class="mb-1">
                                            {{ getCompanyKey('installment_opening_balance_credit') }}
                                        </b-form-checkbox>
                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                </div>

                                <div class="d-inline-block position-relative" style="width: 77%;">
                                    <span
                                        :class="['search-custom position-absolute',$i18n.locale == 'ar'?'search-custom-ar':'']"
                                    >
                                        <i class="fe-search"></i>
                                    </span>
                                    <input
                                        class="form-control"
                                        style="display:block;width:93%;padding-top: 3px;"
                                        type="text"
                                        v-model.trim="search"
                                        :placeholder="`${$t('general.Search')}...`"
                                    >
                                </div>
                            </div>
                        </div>
                        <!-- end search -->

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <!-- start create and printer -->
                                <b-button
                                    v-if="isPermission('create debitNote RP')"
                                    v-b-modal.create
                                    variant="primary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t('general.Create') }}
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
                                        v-if="checkAll.length == 1 && isPermission('update debitNote RP')"
                                    >
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="checkAll.length > 1 && isPermission('delete debitNote RP')"
                                        @click.prevent="deleteModule(checkAll)"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="checkAll.length == 1 && isPermission('delete debitNote RP')"
                                        @click.prevent="deleteModule(checkAll[0])"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!--  end one delete  -->
                                </div>
                                <!-- end create and printer -->
                            </div>
                            <div
                                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center  justify-content-end"
                            >
                                <div class="d-fex">
                                    <!-- start filter and setting -->
                                    <div class="d-inline-block">
                                        <b-button
                                            class="mx-1 custom-btn-background"
                                        >
                                            {{ $t('general.filter') }}
                                            <i class="fas fa-filter"></i>
                                        </b-button>
                                        <b-button
                                            class="mx-1 custom-btn-background"
                                        >
                                            {{ $t('general.group') }}
                                            <i class="fe-menu"></i>
                                        </b-button>
                                        <!-- Basic dropdown -->
                                        <b-dropdown variant="primary"
                                                    :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                                                    ref="dropdown" class="dropdown-custom-ali">
                                            <b-form-checkbox v-model="setting.customer_id" class="mb-1">{{ getCompanyKey("customer")}}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.date" class="mb-1">{{ getCompanyKey('installment_opening_balance_date')}}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.currency_id" class="mb-1">{{ getCompanyKey("installment_opening_balance_currency")}}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.rate" class="mb-1">{{ getCompanyKey("installment_opening_balance_rate")}}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.local_debit" class="mb-1">{{ getCompanyKey("installment_opening_balance_local_debit")}}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.local_credit" class="mb-1">{{ getCompanyKey("installment_opening_balance_local_credit")}}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.debit" class="mb-1">{{ getCompanyKey("installment_opening_balance_debit")}}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.credit" class="mb-1">{{ getCompanyKey("installment_opening_balance_credit")}}
                                            </b-form-checkbox>
                                            <div class="d-flex justify-content-end">
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm">Apply</a>
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>
                                    <!-- end filter and setting -->

                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom">
                                        <div class="d-inline-block" style="font-size:13px;">
                                            {{ installmentStatusPagination.from }}-{{ installmentStatusPagination.to }}
                                            /
                                            {{ installmentStatusPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{'pointer-events':installmentStatusPagination.current_page == 1 ? 'none': ''}"
                                                @click.prevent="getData(installmentStatusPagination.current_page - 1)"
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
                                                :style="{'pointer-events':installmentStatusPagination.last_page == installmentStatusPagination.current_page ? 'none': ''}"
                                                @click.prevent="getData(installmentStatusPagination.current_page + 1)"
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
                            :title="getCompanyKey('debit_note_create_form')"
                            title-class="font-18"
                            body-class="p-4"
                            dialog-class="modal-full-width"
                            scrollable
                            :hide-footer="true"
                            @show="resetModal"
                            @hidden="resetModalHidden"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">

                                    <b-button
                                        variant="success"
                                        @click.prevent="resetForm"
                                        type="button" :class="['font-weight-bold px-2',is_disabled?'mx-2': '']"
                                    >
                                        {{ $t('general.AddNewRecord') }}
                                    </b-button>
                                    <template v-if="!is_disabled">
                                        <b-button
                                            variant="success"
                                            type="button" class="mx-1"
                                            v-if="!isLoader"
                                            @click.prevent="AddSubmit"
                                        >
                                            {{ $t('general.Add') }}
                                        </b-button>

                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                        </b-button>
                                    </template>
                                    <!-- Emulate built in modal footer ok and cancel button actions -->

                                    <b-button variant="danger" type="button" @click.prevent="resetModalHidden">
                                        {{ $t('general.Cancel') }}
                                    </b-button>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group position-relative">
                                            <label
                                                class="control-label">{{ getCompanyKey("customer") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                @input="showCustomerModal"
                                                v-model="$v.create.customer_id.$model"
                                                :options="customers.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ?customers.find((x) => x.id == opt).name:customers.find((x) => x.id == opt).name_e"
                                                :class="{
                                                        'is-invalid': $v.create.customer_id.$error || errors.customer_id,
                                                        'is-valid': !$v.create.customer_id.$invalid && !errors.customer_id,
                                                      }"
                                            >
                                            </multiselect>

                                            <template v-if="errors.customer_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.customer_id"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey('installment_opening_balance_date') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="$v.create.date.$model"
                                                format="YYYY-MM-DD"
                                                valueType="format"
                                                :confirm="false"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.date.$error ||
                                                        errors.date,
                                                    'is-valid':
                                                        !$v.create.date
                                                            .$invalid &&
                                                        !errors.date,
                                                    }"
                                            ></date-picker>

                                            <template v-if="errors.date">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.date"
                                                              :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey('installment_opening_balance_currency') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                :disabled="true"
                                                @input="showCurrencyModal"
                                                v-model="$v.create.currency_id.$model"
                                                :options="currencies.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ?currencies.find((x) => x.id == opt).name:currencies.find((x) => x.id == opt).name_e"
                                                :class="{
                                                        'is-invalid': $v.create.currency_id.$error || errors.currency_id,
                                                        'is-valid': !$v.create.currency_id.$invalid && !errors.currency_id,
                                                      }"
                                            >
                                            </multiselect>
                                            <template v-if="errors.currency_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.currency_id"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey('installment_opening_balance_rate') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div dir="rtl">
                                                <input
                                                    :disabled="true"
                                                    type="number"
                                                    class="form-control"
                                                    step="any"
                                                    @input="accountRateCreate"
                                                    v-model="$v.create.rate.$model"
                                                    :class="{
                                                        'is-invalid': $v.create.rate.$error || errors.rate,
                                                        'is-valid': !$v.create.rate.$invalid && !errors.rate,
                                                      }"
                                                />
                                            </div>
                                            <template v-if="errors.rate">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.rate"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label  class="control-label">
                                                {{ getCompanyKey('installment_opening_balance_local_debit') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                :disabled="(create.local_credit && create.local_credit > 0) ? true : create.id?true:false "
                                                @input="accountLocalDebitForCreate"
                                                type="number"
                                                step="any"
                                                class="form-control"
                                                data-create="3"
                                                v-model="$v.create.local_debit.$model"
                                                :class="{
                                                    'is-invalid': $v.create.local_debit.$error || errors.local_debit,
                                                    'is-valid': !$v.create.local_debit.$invalid && !errors.local_debit,
                                                  }"
                                            />
                                            <template v-if="errors.local_debit">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.local_debit"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey('installment_opening_balance_local_credit') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                :disabled="(create.local_debit && create.local_debit > 0) ? true :create.id?true:false"
                                                type="number"
                                                step="any"
                                                class="form-control"
                                                @input="accountLocalCreditForCreate"
                                                data-create="3"
                                                v-model="$v.create.local_credit.$model"
                                                :class="{
                                                    'is-invalid': $v.create.local_credit.$error || errors.local_credit,
                                                    'is-valid': !$v.create.local_credit.$invalid && !errors.local_credit,
                                                  }"
                                            />
                                            <template v-if="errors.local_credit">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.local_credit"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" style="font-size: 12px">
                                                {{ getCompanyKey('installment_opening_balance_debit') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                :disabled="true"
                                                type="number"
                                                step="any"
                                                class="form-control arabicInput"
                                                data-create="3"

                                                v-model="$v.create.debit.$model"
                                                :class="{
                                                    'is-invalid': $v.create.debit.$error || errors.debit,
                                                    'is-valid': !$v.create.debit.$invalid && !errors.debit,
                                                  }"
                                            />
                                            <template v-if="errors.debit">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.debit"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label" style="font-size: 12px">
                                                {{ getCompanyKey('installment_opening_balance_credit') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                :disabled="true"
                                                type="number"
                                                step="any"
                                                class="form-control"
                                                data-create="3"
                                                v-model="$v.create.credit.$model"
                                                :class="{
                                                    'is-invalid': $v.create.credit.$error || errors.credit,
                                                    'is-valid': !$v.create.credit.$invalid && !errors.credit,
                                                  }"
                                            />
                                            <template v-if="errors.credit">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.credit"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="pt-3 col-md-1">
                                        <b-button v-if="!create.id && create.customer_id &&(create.local_credit > 0 || create.local_debit > 0 )"
                                            variant="primary"
                                            class="btn btn-primary"
                                            @click="saveOpenAndBreak"
                                        >
                                            {{ $t('general.Break') }}
                                        </b-button>

                                        <b-button v-else
                                            variant="secondary"
                                            class="btn btn-secondary"
                                        >
                                            {{ $t('general.Break') }}
                                        </b-button>
                                    </div>

                                </div>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table"
                             id="printCustom">

                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader"/>
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0">
                                <thead>
                                <tr>
                                    <th scope="col" style="width: 0;" v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control">
                                            <input
                                                class="form-check-input"
                                                type="checkbox" v-model="isCheckAll"
                                                style="width: 17px;height: 17px;"
                                            >
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
                                    <th v-if="setting.date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('installment_opening_balance_date') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('date'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-date'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.currency_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("installment_opening_balance_currency") }}</span>
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
                                    <th v-if="setting.rate">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('installment_opening_balance_rate') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('rate'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-rate'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.local_debit">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('installment_opening_balance_local_debit') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('local_debit'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-local_debit'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.local_credit">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('installment_opening_balance_local_credit') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('local_credit'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-local_credit'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.debit">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('installment_opening_balance_debit') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('debit'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-debit'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.credit">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('installment_opening_balance_credit') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('credit'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-credit'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t('general.Action') }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="installmentStatus.length > 0">
                                <tr
                                    @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="isPermission('update debitNote RP')?
                                    $bvModal.show(`modal-edit-${data.id}`):false"
                                    v-for="(data,index) in installmentStatus"
                                    :key="data.id"
                                    class="body-tr-custom"
                                >
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control" style="min-height: 1.9em;">
                                            <input
                                                style="width: 17px;height: 17px;"
                                                class="form-check-input"
                                                type="checkbox"
                                                :value="data.id"
                                                v-model="checkAll"
                                            >
                                        </div>
                                    </td>
                                    <td v-if="setting.customer_id">
                                        <h5 class="m-0 font-weight-normal">
                                            {{ $i18n.locale == "ar" ? data.customer.name : data.customer.name_e}}
                                        </h5>
                                    </td>
                                    <td v-if="setting.date">
                                        <h5 class="m-0 font-weight-normal">{{ data.date }}</h5>
                                    </td>
                                    <td v-if="setting.currency_id">
                                        <h5 class="m-0 font-weight-normal">
                                            {{ $i18n.locale == "ar" ? data.currency.name : data.currency.name_e}}
                                        </h5>
                                    </td>
                                    <td v-if="setting.rate">
                                        <h5 class="m-0 font-weight-normal">{{ data.rate }}</h5>
                                    </td>
                                    <td v-if="setting.local_debit">
                                        <h5 class="m-0 font-weight-normal">{{ data.local_debit }}</h5>
                                    </td>
                                    <td v-if="setting.local_credit">
                                        <h5 class="m-0 font-weight-normal">{{ data.local_credit }}</h5>
                                    </td>

                                    <td v-if="setting.debit">
                                        <h5 class="m-0 font-weight-normal">{{ data.debit }}</h5>
                                    </td>
                                    <td v-if="setting.credit">
                                        <h5 class="m-0 font-weight-normal">{{ data.credit }}</h5>
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="btn-group">
                                            <button
                                                type="button"
                                                class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                data-toggle="dropdown"
                                                aria-expanded="false"
                                            >
                                                {{ $t('general.commands') }}
                                                <i class="fas fa-angle-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-custom">
                                                <a
                                                    v-if="isPermission('update debitNote RP')"
                                                    class="dropdown-item"
                                                    href="#"
                                                    @click="$bvModal.show(`modal-edit-${data.id}`)"
                                                >
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black"
                                                    >
                                                        <span>{{ $t('general.edit') }}</span>
                                                        <i class="mdi mdi-square-edit-outline text-info"></i>
                                                    </div>
                                                </a>
                                                <a
                                                    v-if="isPermission('delete debitNote RP')"
                                                    class="dropdown-item text-black"
                                                    href="#"
                                                    @click.prevent="deleteModule(data.id)"
                                                >
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black">
                                                        <span>{{ $t('general.delete') }}</span>
                                                        <i class="fas fa-times text-danger"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>

                                        <!--  edit   -->
                                        <b-modal
                                            :id="`modal-edit-${data.id}`"
                                            :title="getCompanyKey('debit_note_edit_form')"
                                            title-class="font-18"
                                            dialog-class="modal-full-width"
                                            scrollable
                                            body-class="p-4"
                                            :ref="`edit-${data.id}`"
                                            :hide-footer="true"
                                            @show="resetModalEdit(data.id)"
                                            @hidden="resetModalHiddenEdit(data.id)"
                                        >
                                            <form>
                                                <div class="mb-3 d-flex justify-content-end">
                                                    <!-- Emulate built in modal footer ok and cancel button actions -->
                                                    <b-button variant="success" type="submit" class="mx-1"
                                                              v-if="!isLoader"
                                                              @click.prevent="editSubmit(data.id)"
                                                    >
                                                        {{ $t('general.Edit') }}
                                                    </b-button>

                                                    <b-button variant="success" class="mx-1" disabled v-else>
                                                        <b-spinner small></b-spinner>
                                                        <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                                    </b-button>

                                                    <b-button
                                                        variant="danger"
                                                        type="button"
                                                        @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)"
                                                    >
                                                        {{ $t('general.Cancel') }}
                                                    </b-button>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group position-relative">
                                                            <label
                                                                class="control-label">{{ getCompanyKey("customer") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                :disabled="true"
                                                                @input="showCustomerModal"
                                                                v-model="$v.edit.customer_id.$model"
                                                                :options="customers.map((type) => type.id)"
                                                                :custom-label="(opt) => $i18n.locale == 'ar' ?customers.find((x) => x.id == opt).name:customers.find((x) => x.id == opt).name_e"
                                                                :class="{
                                                                    'is-invalid': $v.edit.customer_id.$error || errors.customer_id,
                                                                    'is-valid': !$v.edit.customer_id.$invalid && !errors.customer_id,
                                                                  }"
                                                            >
                                                            </multiselect>

                                                            <template v-if="errors.customer_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.customer_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey('installment_opening_balance_date') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <date-picker
                                                                type="date"
                                                                v-model="$v.edit.date.$model"
                                                                format="YYYY-MM-DD"
                                                                valueType="format"
                                                                :confirm="false"
                                                                :class="{
                                                                'is-invalid':
                                                                    $v.edit.date.$error ||
                                                                    errors.date,
                                                                'is-valid':
                                                                    !$v.edit.date
                                                                        .$invalid &&
                                                                    !errors.date,
                                                                }"
                                                            ></date-picker>

                                                            <template v-if="errors.date">
                                                                <ErrorMessage v-for="(errorMessage,index) in errors.date"
                                                                              :key="index">
                                                                    {{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group position-relative">
                                                            <label class="control-label">
                                                                {{ getCompanyKey('installment_opening_balance_currency') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                :disabled="true"
                                                                @input="showCurrencyModal"
                                                                v-model="$v.edit.currency_id.$model"
                                                                :options="currencies.map((type) => type.id)"
                                                                :custom-label="(opt) => $i18n.locale == 'ar' ?currencies.find((x) => x.id == opt).name:currencies.find((x) => x.id == opt).name_e"
                                                                :class="{
                                                                    'is-invalid': $v.edit.currency_id.$error || errors.currency_id,
                                                                    'is-valid': !$v.edit.currency_id.$invalid && !errors.currency_id,
                                                                  }"
                                                            >
                                                            </multiselect>
                                                            <template v-if="errors.currency_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.currency_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey('installment_opening_balance_rate') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <div dir="rtl">
                                                                <input
                                                                    :disabled="true"
                                                                    type="number"
                                                                    class="form-control"
                                                                    step="any"
                                                                    @input="accountRateEdit"
                                                                    v-model="$v.edit.rate.$model"
                                                                    :class="{
                                                                        'is-invalid': $v.edit.rate.$error || errors.rate,
                                                                        'is-valid': !$v.edit.rate.$invalid && !errors.rate,
                                                                      }"
                                                                />
                                                            </div>
                                                            <template v-if="errors.rate">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.rate"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label  class="control-label">
                                                                {{ getCompanyKey('installment_opening_balance_local_debit') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                :disabled="(edit.local_credit && edit.local_credit > 0) ? true :false"
                                                                @input="accountLocalDebitForEdit"
                                                                type="number"
                                                                step="any"
                                                                class="form-control"
                                                                v-model="$v.edit.local_debit.$model"
                                                                :class="{
                                                                    'is-invalid': $v.edit.local_debit.$error || errors.local_debit,
                                                                    'is-valid': !$v.edit.local_debit.$invalid && !errors.local_debit,
                                                                  }"
                                                            />
                                                            <template v-if="errors.local_debit">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.local_debit"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey('installment_opening_balance_local_credit') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                :disabled="(edit.local_debit && edit.local_debit > 0) ? true :false"
                                                                type="number"
                                                                step="any"
                                                                class="form-control"
                                                                @input="accountLocalCreditForEdit"
                                                                v-model="$v.edit.local_credit.$model"
                                                                :class="{
                                                                    'is-invalid': $v.edit.local_credit.$error || errors.local_credit,
                                                                    'is-valid': !$v.edit.local_credit.$invalid && !errors.local_credit,
                                                                  }"
                                                            />
                                                            <template v-if="errors.local_credit">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.local_credit"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label" style="font-size: 12px">
                                                                {{ getCompanyKey('installment_opening_balance_debit') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                :disabled="true"
                                                                type="number"
                                                                step="any"
                                                                class="form-control arabicInput"

                                                                v-model="$v.edit.debit.$model"
                                                                :class="{
                                                                    'is-invalid': $v.edit.debit.$error || errors.debit,
                                                                    'is-valid': !$v.edit.debit.$invalid && !errors.debit,
                                                                  }"
                                                            />
                                                            <template v-if="errors.debit">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.debit"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="control-label" style="font-size: 12px">
                                                                {{ getCompanyKey('installment_opening_balance_credit') }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                :disabled="true"
                                                                type="number"
                                                                step="any"
                                                                class="form-control"
                                                                v-model="$v.edit.credit.$model"
                                                                :class="{
                                                                        'is-invalid': $v.edit.credit.$error || errors.credit,
                                                                        'is-valid': !$v.edit.credit.$invalid && !errors.credit,
                                                                      }"
                                                            />
                                                            <template v-if="errors.credit">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.credit"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage
                                                                >
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="pt-3 col-md-1">
                                                        <b-button
                                                                  variant="primary"
                                                                  class="btn btn-primary"
                                                                  @click="editSubmit(edit.id)"
                                                        >
                                                            {{ $t('general.Break') }}
                                                        </b-button>
                                                    </div>

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
                                    <th class="text-center" colspan="11">{{ $t('general.notDataFound') }}</th>
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

<style>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Firefox */
input[type=number] {
    -moz-appearance: textfield;
}
.multiselect__single{
    font-weight: 600 !important;
    color: black !important;
}
</style>



