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
        title: "Installment opening balance",
        meta: [{name: "Installment opening balance", content: 'Installment opening balance'}],
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
      return permissionGuard(vm, "Opening Balance RP", "all openingBalance RP");
    });

    },
    data() {
        return {
            is_Date: false,
            per_page: 50,
            search: '',
            debounce: {},
            installmentStatusPagination: {},
            installmentStatus: [],
            customers: [],
            currencies: [],
            isLoader: false,
            create: [],
            edit: [],
            errors: {},
            dropDownSenders: [],
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
                date: true,
                count: true,
                total_local_debit: true,
                total_local_credit: true,
                net: true,
            },
            filterSetting: [
                'date',
                'count',
                'total_local_debit',
                'total_local_credit',
                'net',
            ],
            Tooltip: '',
            mouseEnter: null,
            allDrop: [],
        }
    },
    validations: {
        create: {
            $each: {
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
        edit: {
            $each: {
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
                this.installmentStatus.forEach(el => {
                    if (!this.checkAll.includes(el.id)) {
                        this.checkAll.push(el.id);
                    }
                });
            } else {
                this.checkAll = [];
            }
        }
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
        onChange(e) {
            let id = e;
            let serverData = this.installmentStatus[0];

            this.create.date = serverData
                ? serverData.date
                : new Date().toISOString().slice(0, 10);
            this.is_Date = serverData ? true : false;

            this.allDataArr.forEach((item) => {
                if (item.wallet_id == e) {
                    this.arr.push(item.stock.id);
                }
            });
            let stockArr = [];
            this.stockTypeAr.forEach((list) => {
                if (!this.arr.includes(list.id)) stockArr.push(list);
            });
            this.stockSelectAr = stockArr;
        },
        showCustomerModal(index) {
            this.orderDropChangeCreate(index);
            if (this.create[index].customer_id == 0) {
                this.$bvModal.show("customer-general-create");
                this.create[index].customer_id = null;
            }
        },
        showCustomerModalEdit(index) {
            this.orderDropChangeEdit(index);
            if (this.edit[index].customer_id == 0) {
                this.$bvModal.show("customer-general-create");
                this.edit[index].customer_id = null;
            }
        },
        showCurrencyModal(index) {
            if (this.create[index].currency_id == 0) {
                this.$bvModal.show("currency-create");
                this.create[index].currency_id = null;
            }
        },
        showCurrencyEditModal(index) {
            if (this.edit[index].currency_id == 0) {
                this.$bvModal.show("currency-create");
                this.edit[index].currency_id = null;
            }
        },

        addNewFieldCreate() {
            let date = this.installmentStatus.length > 0 ? this.installmentStatus[0].date :  new Date().toISOString().slice(0, 10);
            this.create.push({
                id:null,
                date: date,
                customer_id: null,
                currency_id: 1,
                rate: 1,
                debit: 0,
                credit: 0,
                local_debit: 0,
                local_credit: 0,
            });
            this.allDrop.push({order: true});
        },
        removeNewFieldCreate(index) {
            if (this.create.length > 1) {
                this.create.splice(index, 1);
                this.allDrop.splice(index, 1);
            }
        },

        removeFieldEdit(index) {
            this.edit.splice(index, 1);
            this.allDrop.splice(index, 1);
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

            adminApi.get(`/recievable-payable/rp_opening_balance?group=1&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
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

                adminApi.get(`/recievable-payable/rp_opening_balance?group=1&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
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
                    this.isLoader = false;
                    let l = res.data.data;
                    if(this.isPermission('create Customer')){
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
        /**
         *  get currency
         */
        async getCurrencies() {
            this.isLoader = true;

            await adminApi
                .get(
                    `/currencies`
                )
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Currency')){
                        l.unshift({id: 0, name: "اضف عمله جديده  ", name_e: "Add New Currency"});
                    }
                    this.currencies = l;
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
         *  start delete module
         */
        deleteModule(id, index) {
            if (Array.isArray(id)) {
              return ;
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
                            .delete(`/recievable-payable/rp_opening_balance/${id}`)
                            .then((res) => {
                                this.checkAll = [];
                                this.removeFieldEdit(index);
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
            this.create = [];
            let date = this.installmentStatus.length > 0 ? this.installmentStatus[0].date :  new Date().toISOString().slice(0, 10);
            this.create.push({
                id:null,
                date: date,
                customer_id: null,
                currency_id: 1,
                rate: 1,
                debit: 0,
                credit: 0,
                local_debit: 0,
                local_credit: 0,
            });
            this.is_disabled = false;
            this.is_Date = this.installmentStatus.length > 0 ? true : false;
            this.allDrop = [];
            this.allDrop.push({order: true});
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
            this.is_disabled = false;
            this.$bvModal.hide(`create`);
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            this.create = [];
            await this.getCustomers(1);
            await this.getCurrencies();
            let date = this.installmentStatus.length > 0 ? this.installmentStatus[0].date :  new Date().toISOString().slice(0, 10);
            this.create.push({
                id:null,
                date: date,
                customer_id: null,
                currency_id: 1,
                rate: 1,
                debit: 0,
                credit: 0,
                local_debit: 0,
                local_credit: 0,
            });
            this.is_Date = this.installmentStatus.length > 0 ? true : false;
            this.allDrop = [];
            this.is_disabled = false;
            this.allDrop.push({order: true});
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
            let isTrueDrop = this.allDrop.some((el) => el.order == false);
            if (this.$v.create.$invalid || isTrueDrop) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                let creatArr = [];
                this.create.forEach((el)=>{
                    if (!el.id)
                    {
                        creatArr.push(el)
                    }
                });
                let opening_balances={
                    opening_balances:creatArr
                }
                adminApi.post(`/recievable-payable/rp_opening_balance`, opening_balances)
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
        editSubmit(id,index) {
            this.$v.edit.$touch();
            let isTrueDrop = this.allDrop.some((el) => el.order == false);
            if (this.$v.edit.$invalid || isTrueDrop) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi.put(`/recievable-payable/rp_opening_balance/${id}`, this.edit[index])
                    .then((res) => {
                        this.getData();
                        this.closeEdit(index);
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                text: `${this.$t('general.Editsuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }, 500);
                        this.showBreakEdit(index);
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

        showBreakEdit(index){
            if (this.edit[index].id) {
                this.openingBreak = this.edit[index];
                this.openingBreak.is_update = true;
                this.openingBreak.break_type = 'openingBalance';
                this.openingBreak.document_id = 1;
                this.$bvModal.show("opening-balance-break-create");
            }
        },

        /**
         *   show Modal (edit)
         */
        async resetModalEdit(index) {
            await this.getCustomers();
            await this.getCurrencies();
            let date = this.installmentStatus[index].date;
           await adminApi.get(`/recievable-payable/rp_opening_balance?date=${date}`)
                .then((res) => {
                    let l = res.data;
                    this.allDrop = [];
                    this.edit = [];
                    this.is_disabled = false;
                     l.data.forEach((el) => {
                         this.edit.push({
                             id: el.id,
                             date: el.date,
                             customer_id: el.customer_id,
                             currency_id: el.currency_id,
                             rate: el.rate,
                             debit: el.debit,
                             credit: el.credit,
                             local_debit: el.local_debit,
                             local_credit: el.local_credit,
                             is_edit:false,
                         });
                         this.allDrop.push({order: true});
                    });
                    this.errors = {};
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
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.is_disabled = false;
            this.allDrop = [];
            this.errors = {};
            this.edit = [];
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
                    .get(`/recievable-payable/rp_opening_balance/logs/${id}`)
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
                    XLSX.writeFile(wb, fn || (('InstallmentStatus' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },

        /**
         *  account create
         */
        accountRateCreate(index) {
            if (this.create[index].local_credit && this.create[index].local_credit > 0) {
                this.accountLocalCreditForCreate(index);
            }
            if (this.create[index].local_debit && this.create[index].local_debit > 0) {
                this.accountLocalDebitForCreate(index);
            }
        },
        accountLocalCreditForCreate(index) {
            this.create[index].credit = this.create[index].local_credit * this.create[index].rate;
        },
        accountLocalDebitForCreate(index) {
            this.create[index].debit = this.create[index].local_debit * this.create[index].rate;
        },
        /**
         *  end create
         */

        orderDropChangeCreate(index) {
            let fill = this.create.filter(
                (el, ind) => index != ind && el.customer_id == this.create[index].customer_id
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

        changeDateForCreate(index){
            let dateNew = '';
            dateNew = this.create[index].date;
            this.create.forEach((el) => {
                el.date = dateNew;
            });
        },

        /**
         *  account edit
         */
        accountRateEdit(index) {
            if (this.edit[index].local_credit && this.edit[index].local_credit > 0) {
                this.accountLocalCreditForEdit(index);
            }
            if (this.edit[index].local_debit && this.edit[index].local_debit > 0) {
                this.accountLocalDebitForEdit(index);
            }
        },
        accountLocalCreditForEdit(index) {
            this.openEdit(index);
            this.edit[index].credit = this.edit[index].local_credit * this.edit[index].rate;
        },
        accountLocalDebitForEdit(index) {
            this.openEdit(index);
            this.edit[index].debit = this.edit[index].local_debit * this.edit[index].rate;
        },
        openEdit(index)
        {
            this.edit[index].is_edit = true
        },
        closeEdit(index)
        {
            this.edit[index].is_edit = false
        },
        /**
         *  end edit
         */

        orderDropChangeEdit(index) {
            let fill = this.edit.filter(
                (el, ind) => index != ind && el.customer_id == this.edit[index].customer_id
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

        /**
         *  Start create Open Balance And Open Break
         */
       async saveOpenAndBreak(index){
            let isTrueDrop = this.allDrop.some((el) => el.order == false);
            this.$v.create.$touch();
            if (isTrueDrop && this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                let opening_balances={
                    opening_balances:[this.create[index]]
                }
               await adminApi.post(`/recievable-payable/rp_opening_balance`, opening_balances)
                    .then((res) => {
                        this.is_disabled = true;
                        this.getData();
                        this.create[index].id = res.data[0].id;
                        this.showBreakCreate(index);
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
        showBreakCreate(index){
            if (this.create[index].id) {
                this.openingBreak = this.create[index];
                this.openingBreak.break_type = 'openingBalance';
                this.openingBreak.document_id = 1;
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
                            <h4 class="header-title"> {{ $t('general.installmentCustomerOpeningBalanceTable') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">

                                <div class="d-inline-block" style="width: 22.2%;">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting" value="date" class="mb-1">
                                            {{ getCompanyKey('installment_opening_balance_date') }}
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
                                    v-if="isPermission('create openingBalance RP')"
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
                                        v-if="checkAll.length == 1 && isPermission('edit openingBalance RP')"
                                    >
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
<!--                                    <button-->
<!--                                        class="custom-btn-dowonload"-->
<!--                                        v-if="checkAll.length > 1"-->
<!--                                        @click.prevent="deleteModule(checkAll)"-->
<!--                                    >-->
<!--                                        <i class="fas fa-trash-alt"></i>-->
<!--                                    </button>-->
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
<!--                                    <button-->
<!--                                        class="custom-btn-dowonload"-->
<!--                                        v-if="checkAll.length == 1"-->
<!--                                        @click.prevent="deleteModule(checkAll[0])"-->
<!--                                    >-->
<!--                                        <i class="fas fa-trash-alt"></i>-->
<!--                                    </button>-->
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
                                            <b-form-checkbox v-model="setting.date" class="mb-1">{{
                                                    getCompanyKey('installment_opening_balance_date')
                                                }}
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
                            :title="getCompanyKey('installment_opening_balance_create_form')"
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
                                        @click.prevent="addNewFieldCreate"
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

                                <template v-for="(item, index) in create">
                                    <div class="row" v-if="index == 0">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ getCompanyKey('installment_opening_balance_date') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <date-picker
                                                    type="date"
                                                    v-model="$v.create.$each[index].date.$model"
                                                    format="YYYY-MM-DD"
                                                    valueType="format"
                                                    :confirm="false"
                                                    @input="changeDateForCreate(index)"
                                                    :class="{
                                                    'is-invalid':
                                                        $v.create.$each[index].date.$error ||
                                                        errors.date,
                                                    'is-valid':
                                                        !$v.create.$each[index].date
                                                            .$invalid &&
                                                        !errors.date,
                                                }"
                                                    :disabled="is_Date"
                                                    :disabled-date="
                                                    (date) => date >= new Date()
                                                "
                                                ></date-picker>

                                                <template v-if="errors.date">
                                                    <ErrorMessage v-for="(errorMessage,index) in errors.date"
                                                                  :key="index">
                                                        {{ errorMessage }}
                                                    </ErrorMessage>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group position-relative">
                                                <label v-if="index == 0"
                                                       class="control-label">{{ getCompanyKey("customer") }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <multiselect
                                                    :disabled="create[index].id?true:false"
                                                    @input="showCustomerModal(index)"
                                                    v-model="$v.create.$each[index].customer_id.$model"
                                                    :options="customers.map((type) => type.id)"
                                                    :custom-label="(opt) => $i18n.locale == 'ar' ?customers.find((x) => x.id == opt).name:customers.find((x) => x.id == opt).name_e"
                                                    :class="{
                                                            'is-invalid': $v.create.$each[index].customer_id.$error || errors.customer_id || !allDrop[index].order,
                                                            'is-valid': !$v.create.$each[index].customer_id.$invalid && !errors.customer_id && allDrop[index].order,
                                                          }"
                                                >
                                                </multiselect>
                                                <div v-if="!allDrop[index].order" class="invalid-feedback">
                                                    {{ $t("general.thisFieldIsUniq") }}
                                                </div>

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
                                        <div class="col-md-1">
                                            <div class="form-group position-relative">
                                                <label v-if="index == 0" class="control-label">
                                                    {{ getCompanyKey('installment_opening_balance_currency') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <multiselect
                                                    :disabled="true"
                                                    @input="showCurrencyModal(index)"
                                                    v-model="$v.create.$each[index].currency_id.$model"
                                                    :options="currencies.map((type) => type.id)"
                                                    :custom-label="(opt) => $i18n.locale == 'ar' ?currencies.find((x) => x.id == opt).name:currencies.find((x) => x.id == opt).name_e"
                                                    :class="{
                                                            'is-invalid': $v.create.$each[index].currency_id.$error || errors.currency_id,
                                                            'is-valid': !$v.create.$each[index].currency_id.$invalid && !errors.currency_id,
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
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label v-if="index == 0" class="control-label">
                                                    {{ getCompanyKey('installment_opening_balance_rate') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div dir="rtl">
                                                    <input
                                                        :disabled="true"
                                                        type="number"
                                                        class="form-control"
                                                        step="any"
                                                        @input="accountRateCreate(index)"
                                                        v-model="$v.create.$each[index].rate.$model"
                                                        :class="{
                                                            'is-invalid': $v.create.$each[index].rate.$error || errors.rate,
                                                            'is-valid': !$v.create.$each[index].rate.$invalid && !errors.rate,
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
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label v-if="index == 0" class="control-label">
                                                    {{ getCompanyKey('installment_opening_balance_local_debit') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    :disabled="(create[index].local_credit && create[index].local_credit > 0) ? true : create[index].id?true:false "
                                                    @input="accountLocalDebitForCreate(index)"
                                                    type="number"
                                                    step="any"
                                                    class="form-control"
                                                    data-create="3"
                                                    v-model="$v.create.$each[index].local_debit.$model"
                                                    :class="{
                                                        'is-invalid': $v.create.$each[index].local_debit.$error || errors.local_debit,
                                                        'is-valid': !$v.create.$each[index].local_debit.$invalid && !errors.local_debit,
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
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label v-if="index == 0" class="control-label">
                                                    {{ getCompanyKey('installment_opening_balance_local_credit') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    :disabled="(create[index].local_debit && create[index].local_debit > 0) ? true :create[index].id?true:false"
                                                    type="number"
                                                    step="any"
                                                    class="form-control"
                                                    @input="accountLocalCreditForCreate(index)"
                                                    data-create="3"
                                                    v-model="$v.create.$each[index].local_credit.$model"
                                                    :class="{
                                                        'is-invalid': $v.create.$each[index].local_credit.$error || errors.local_credit,
                                                        'is-valid': !$v.create.$each[index].local_credit.$invalid && !errors.local_credit,
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
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label v-if="index == 0" class="control-label" style="font-size: 12px">
                                                    {{ getCompanyKey('installment_opening_balance_debit') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    :disabled="true"
                                                    type="number"
                                                    step="any"
                                                    class="form-control arabicInput"
                                                    data-create="3"

                                                    v-model="$v.create.$each[index].debit.$model"
                                                    :class="{
                                                        'is-invalid': $v.create.$each[index].debit.$error || errors.debit,
                                                        'is-valid': !$v.create.$each[index].debit.$invalid && !errors.debit,
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
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label v-if="index == 0" class="control-label" style="font-size: 12px">
                                                    {{ getCompanyKey('installment_opening_balance_credit') }}
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input
                                                    :disabled="true"
                                                    type="number"
                                                    step="any"
                                                    class="form-control"
                                                    data-create="3"
                                                    v-model="$v.create.$each[index].credit.$model"
                                                    :class="{
                                                        'is-invalid': $v.create.$each[index].credit.$error || errors.credit,
                                                        'is-valid': !$v.create.$each[index].credit.$invalid && !errors.credit,
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

                                        <div :class="[index == 0 ? ' pt-3':'' ,'col-md-1']">
                                            <b-button v-if="!create[index].id && create[index].customer_id &&(create[index].local_credit > 0 || create[index].local_debit > 0 )"
                                                variant="primary"
                                                class="btn btn-primary"
                                                @click="saveOpenAndBreak(index)"
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

                                        <div :class="[index == 0 ? ' pt-3':'' ,'col-md-1']">
                                            <button v-if="create.length > 1 && !create[index].id"
                                                type="button"
                                                @click.prevent="removeNewFieldCreate(index)"
                                                class="custom-btn-dowonload"
                                            >
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            <b-button
                                                variant="success"
                                                @click.prevent="addNewFieldCreate"
                                                type="button" :class="['font-weight-bold px-2']"
                                            >
                                                <i class="fas fa-plus"></i>
                                            </b-button>
                                        </div>
                                    </div>
                                </template>
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
                                    <th v-if="setting.count">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('installment_count_opening_balance') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('count'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-count'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.total_local_debit">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('installment_opening_total_local_debit') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('total_local_debit'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-total_local_debit'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.total_local_credit">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('installment_opening_total_local_credit') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('total_local_credit'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-total_local_credit'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.net">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey('installment_opening_net') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('net'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-net'))"></i>
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
                                    @dblclick.prevent="isPermission('edit openingBalance RP') ?
                                    $bvModal.show(`modal-edit-${data.date}`):false"
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
                                                :value="data.date"
                                                v-model="checkAll"
                                            >
                                        </div>
                                    </td>
                                    <td v-if="setting.date">
                                        <h5 class="m-0 font-weight-normal">{{ data.date }}</h5>
                                    </td>
                                    <td v-if="setting.count">
                                        <h5 class="m-0 font-weight-normal">{{ data.count }}</h5>
                                    </td>
                                    <td v-if="setting.total_local_debit">
                                        <h5 class="m-0 font-weight-normal">{{ data.total_local_debit }}</h5>
                                    </td>
                                    <td v-if="setting.total_local_credit">
                                        <h5 class="m-0 font-weight-normal">{{ data.total_local_credit }}</h5>
                                    </td>

                                    <td v-if="setting.net">
                                        <h5 class="m-0 font-weight-normal">{{ data.net }}</h5>
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
                                                    v-if="isPermission('edit openingBalance RP')"
                                                    class="dropdown-item"
                                                    href="#"
                                                    @click="$bvModal.show(`modal-edit-${data.date}`)"
                                                >
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black"
                                                    >
                                                        <span>{{ $t('general.edit') }}</span>
                                                        <i class="mdi mdi-square-edit-outline text-info"></i>
                                                    </div>
                                                </a>
<!--                                                <a-->
<!--                                                    class="dropdown-item text-black"-->
<!--                                                    href="#"-->
<!--                                                    @click.prevent="deleteModule(data.id)"-->
<!--                                                >-->
<!--                                                    <div-->
<!--                                                        class="d-flex justify-content-between align-items-center text-black">-->
<!--                                                        <span>{{ $t('general.delete') }}</span>-->
<!--                                                        <i class="fas fa-times text-danger"></i>-->
<!--                                                    </div>-->
<!--                                                </a>-->
                                            </div>
                                        </div>

                                        <!--  edit   -->
                                        <b-modal
                                            :id="`modal-edit-${data.date}`"
                                            :title="getCompanyKey('installment_opening_balance_edit_form')"
                                            title-class="font-18"
                                            dialog-class="modal-full-width"
                                            scrollable
                                            body-class="p-4"
                                            :ref="`edit-${data.id}`"
                                            :hide-footer="true"
                                            @show="resetModalEdit(index)"
                                            @hidden="resetModalHiddenEdit(data.id)"
                                        >
                                            <form>
                                                <div class="mb-3 d-flex justify-content-end">
                                                    <!-- Emulate built in modal footer ok and cancel button actions -->
<!--                                                    <b-button variant="success" type="submit" class="mx-1"-->
<!--                                                              v-if="!isLoader"-->
<!--                                                              @click.prevent="editSubmit(data.date)"-->
<!--                                                    >-->
<!--                                                        {{ $t('general.Edit') }}-->
<!--                                                    </b-button>-->

<!--                                                    <b-button variant="success" class="mx-1" disabled v-else>-->
<!--                                                        <b-spinner small></b-spinner>-->
<!--                                                        <span class="sr-only">{{ $t('login.Loading') }}...</span>-->
<!--                                                    </b-button>-->

                                                    <b-button
                                                        variant="danger"
                                                        type="button"
                                                        @click.prevent="$bvModal.hide(`modal-edit-${data.date}`)"
                                                    >
                                                        {{ $t('general.Cancel') }}
                                                    </b-button>
                                                </div>
                                                <template v-for="(item, index) in edit">
                                                    <div class="row" v-if="index == 0">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label class="control-label">
                                                                    {{ getCompanyKey('installment_opening_balance_date') }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <date-picker
                                                                    type="date"
                                                                    v-model="$v.edit.$each[index].date.$model"
                                                                    format="YYYY-MM-DD"
                                                                    valueType="format"
                                                                    :confirm="false"
                                                                    @input="changeDateForEdit(index)"
                                                                    :class="{
                                                                        'is-invalid':
                                                                            $v.edit.$each[index].date.$error ||
                                                                            errors.date,
                                                                        'is-valid':
                                                                            !$v.edit.$each[index].date
                                                                                .$invalid &&
                                                                            !errors.date,
                                                                    }"
                                                                      :disabled="true"
                                                                ></date-picker>

                                                                <template v-if="errors.date">
                                                                    <ErrorMessage v-for="(errorMessage,index) in errors.date"
                                                                                  :key="index">
                                                                        {{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <div class="form-group position-relative">
                                                                <label v-if="index == 0"
                                                                       class="control-label">{{ getCompanyKey("customer") }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <multiselect
                                                                    :disabled="true"
                                                                    @input="showCustomerModalEdit(index)"
                                                                    v-model="$v.edit.$each[index].customer_id.$model"
                                                                    :options="customers.map((type) => type.id)"
                                                                    :custom-label="(opt) => $i18n.locale == 'ar' ?customers.find((x) => x.id == opt).name:customers.find((x) => x.id == opt).name_e"
                                                                    :class="{
                                                                        'is-invalid': $v.edit.$each[index].customer_id.$error || errors.customer_id || !allDrop[index].order,
                                                                        'is-valid': !$v.edit.$each[index].customer_id.$invalid && !errors.customer_id && allDrop[index].order,
                                                                      }"
                                                                >
                                                                </multiselect>
                                                                <div v-if="!allDrop[index].order" class="invalid-feedback">
                                                                    {{ $t("general.thisFieldIsUniq") }}
                                                                </div>

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
                                                        <div class="col-md-1">
                                                            <div class="form-group position-relative">
                                                                <label v-if="index == 0" class="control-label">
                                                                    {{ getCompanyKey('installment_opening_balance_currency') }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <multiselect
                                                                    :disabled="true"
                                                                    @input="showCurrencyEditModal(index)"
                                                                    v-model="$v.edit.$each[index].currency_id.$model"
                                                                    :options="currencies.map((type) => type.id)"
                                                                    :custom-label="(opt) => $i18n.locale == 'ar' ?currencies.find((x) => x.id == opt).name:currencies.find((x) => x.id == opt).name_e"
                                                                    :class="{
                                                                        'is-invalid': $v.edit.$each[index].currency_id.$error || errors.currency_id,
                                                                        'is-valid': !$v.edit.$each[index].currency_id.$invalid && !errors.currency_id,
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
                                                        <div class="col-md-1">
                                                            <div class="form-group">
                                                                <label v-if="index == 0" class="control-label">
                                                                    {{ getCompanyKey('installment_opening_balance_rate') }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <div dir="rtl">
                                                                    <input
                                                                        :disabled="true"
                                                                        type="number"
                                                                        class="form-control"
                                                                        step="any"
                                                                        @input="accountRateEdit(index)"
                                                                        v-model="$v.edit.$each[index].rate.$model"
                                                                        :class="{
                                                                            'is-invalid': $v.edit.$each[index].rate.$error || errors.rate,
                                                                            'is-valid': !$v.edit.$each[index].rate.$invalid && !errors.rate,
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
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label v-if="index == 0" class="control-label">
                                                                    {{ getCompanyKey('installment_opening_balance_local_debit') }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input
                                                                    :disabled="(edit[index].local_credit && edit[index].local_credit > 0) ? true :false"
                                                                    @input="accountLocalDebitForEdit(index)"
                                                                    type="number"
                                                                    step="any"
                                                                    class="form-control"
                                                                    v-model="$v.edit.$each[index].local_debit.$model"
                                                                    :class="{
                                                                    'is-invalid': $v.edit.$each[index].local_debit.$error || errors.local_debit,
                                                                    'is-valid': !$v.edit.$each[index].local_debit.$invalid && !errors.local_debit,
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
                                                        <div class="col-md-2">
                                                            <div class="form-group">
                                                                <label v-if="index == 0" class="control-label">
                                                                    {{ getCompanyKey('installment_opening_balance_local_credit') }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input
                                                                    :disabled="(edit[index].local_debit && edit[index].local_debit > 0) ? true :false"
                                                                    type="number"
                                                                    step="any"
                                                                    class="form-control"
                                                                    @input="accountLocalCreditForEdit(index)"
                                                                    v-model="$v.edit.$each[index].local_credit.$model"
                                                                    :class="{
                                                                        'is-invalid': $v.edit.$each[index].local_credit.$error || errors.local_credit,
                                                                        'is-valid': !$v.edit.$each[index].local_credit.$invalid && !errors.local_credit,
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
                                                        <div class="col-md-1">
                                                            <div class="form-group">
                                                                <label v-if="index == 0" class="control-label" style="font-size: 12px">
                                                                    {{ getCompanyKey('installment_opening_balance_debit') }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input
                                                                    :disabled="true"
                                                                    type="number"
                                                                    step="any"
                                                                    class="form-control arabicInput"

                                                                    v-model="$v.edit.$each[index].debit.$model"
                                                                    :class="{
                                                                        'is-invalid': $v.edit.$each[index].debit.$error || errors.debit,
                                                                        'is-valid': !$v.edit.$each[index].debit.$invalid && !errors.debit,
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
                                                        <div class="col-md-1">
                                                            <div class="form-group">
                                                                <label v-if="index == 0" class="control-label" style="font-size: 12px">
                                                                    {{ getCompanyKey('installment_opening_balance_credit') }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input
                                                                    :disabled="true"
                                                                    type="number"
                                                                    step="any"
                                                                    class="form-control"
                                                                    v-model="$v.edit.$each[index].credit.$model"
                                                                    :class="{
                                                                        'is-invalid': $v.edit.$each[index].credit.$error || errors.credit,
                                                                        'is-valid': !$v.edit.$each[index].credit.$invalid && !errors.credit,
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

                                                        <div :class="[index == 0 ? ' pt-3':'' ,'col-md-1']">
                                                            <b-button v-if="edit[index].is_edit"
                                                                      variant="primary"
                                                                      class="btn btn-primary"
                                                                      @click.prevent="editSubmit(edit[index].id,index)"
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

                                                        <div :class="[index == 0 ? ' pt-3':'' ,'col-md-1']">
                                                            <button v-if="edit.length > 1"
                                                                    type="button"
                                                                    @click.prevent="deleteModule(edit[index].id,index)"
                                                                    class="custom-btn-dowonload"
                                                            >
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </template>
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
                                    <th class="text-center" colspan="8">{{ $t('general.notDataFound') }}</th>
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



