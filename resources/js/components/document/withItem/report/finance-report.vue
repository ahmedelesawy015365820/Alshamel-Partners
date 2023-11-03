<script>
import adminApi from "../../../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength, integer, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../../components/widgets/errorMessage";
import loader from "../../../general/loader";
import {dynamicSortString} from "../../../../helper/tableSort";
import {formatDateOnly} from "../../../../helper/startDate";
import translation from "../../../../helper/mixin/translation-mixin";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";

/**
 * Advanced Table component
 */
export default {
    name: "Finance-Report",
    props: {
        document_detail_type: {
            default: '',
        },
    },
    mixins: [translation],
    components: {
        Switches,
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
    },
    data() {
        return {
            per_page: 50,
            search: '',
            debounce: {},
            installmentStatusPagination: {},
            installmentStatus: [],
            governorates: [],
            categories: [],
            packages: [],
            salesmen: [],
            customers: [],
            sellMethods: [],
            isLoader: false,
            create: {
                date_from: this.formatDate(new Date()),
                date_to: this.formatDate(new Date()),
                governorate_id: null,
                category_id: null,
                is_stripe: null,
                package_id: null,
                employee_id: null,
                customer_id: null,
                sell_method_id: null,
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
                serial_number: true,
                employee_id: true,
                external_sales_id: true,
                customer_id: true,
                category_id: true,
                governorate_id: true,
                package_id: true,

                date_from: true,
                date_to: true,

                unit_type: true,
                quantity: true,
                price_per_uint: true,
                total: true,
                invoice_discount: true,
                net_invoice: true,
                sell_method_discount: true,
                external_commission: true,

                rent_days_month: true,
                executed_days_month: true,
                remaining_days_month: true,
                previously_used_days_month: true,

                unrealized_revenue: true,
                current_revenue_month: true,
                used_previous_revenue_month: true,
                remaning_revenue_month: true,

                unrealized_sell_method_discount_month: true,
                previous_sell_method_discount_month: true,
                current_sell_method_discount_month: true,
                remaning_sell_method_discount_month: true,

                unrealized_external_commission_month: true,
                previous_external_commission_month: true,
                current_external_commission_month: true,
                remaning_external_commission_month: true,

                unrealized_commission_month: true,
                used_previous_commission_month: true,
                current_commission_month: true,
                remaning_commission_month: true,

                payments: true,
                debit_note: true,
                returns: true,
            },
            filterSetting: [
                this.$i18n.locale == 'ar'? 'category.name': 'category.name_e',
                this.$i18n.locale == 'ar'? 'governorate.name': 'governorate.name_e',
                this.$i18n.locale == 'ar'? 'package.name': 'package.name_e',
                'date_from',
                'rent_days',
                'date_to',
                'quantity',
                'price_per_uint',
                'total',
                'invoice_discount',
                'net_invoice',
                'sell_method_discount',
                'unrealized_revenue',
                'external_commission',
            ],
            Tooltip: '',
            mouseEnter: null,
        }
    },
    validations: {
        create: {
            date_from: {required},
            date_to: {required},
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
    methods: {
        /**
         *  start get Data module && pagination
         */
        getData(page = 1) {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                let _filterSetting = [...this.filterSetting];
                let filter = '';
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }

                adminApi.get(`/document-header-details/report-document?document_detail_type=${this.document_detail_type}&date_from=${this.create.date_from}&date_to=${this.create.date_to}&governorate_id=${this.create.governorate_id??''}&category_id=${this.create.category_id??''}&is_stripe=${this.create.is_stripe??''}&package_id=${this.create.package_id??''}&employee_id=${this.create.employee_id??''}&customer_id=${this.create.customer_id??''}&sell_method_id=${this.create.sell_method_id??''}&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
                    .then((res) => {
                        let l = res.data;
                        this.installmentStatus = l.data;
                        // this.installmentStatusPagination = l.pagination;
                        // this.current_page = l.pagination.current_page;
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
        getDataCurrentPage(page = 1) {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                if (this.current_page <= this.installmentStatusPagination.last_page && this.current_page != this.installmentStatusPagination.current_page && this.current_page) {
                    this.isLoader = true;
                    let _filterSetting = [...this.filterSetting];
                    let filter = "";
                    for (let i = 0; i < _filterSetting.length; ++i) {
                        filter += `columns[${i}]=${_filterSetting[i]}&`;
                    }

                    adminApi.get(`/document-header-details/report-document?document_detail_type=${this.document_detail_type}&date_from=${this.create.date_from}&date_to=${this.create.date_to}&governorate_id=${this.create.governorate_id??''}&category_id=${this.create.category_id??''}&is_stripe=${this.create.is_stripe??''}&package_id=${this.create.package_id??''}&employee_id=${this.create.employee_id??''}&customer_id=${this.create.customer_id??''}&sell_method_id=${this.create.sell_method_id??''}&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
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
            }
        },
        /**
         *  end get Data module && pagination
         */
        /**
         *  get Governorate
         */
        async getGovernorate() {
            this.isLoader = true;
            await adminApi
                .get(`/governorates`)
                .then((res) => {
                    let l = res.data.data;
                    this.governorates = l;
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
         *  get Category
         */
        async getCategory() {
            this.isLoader = true;
            await adminApi
                .get(`/categories`)
                .then((res) => {
                    let l = res.data.data;
                    this.categories = l;
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
         *  get Package
         */
        async getPackage() {
            this.isLoader = true;
            await adminApi
                .get(`/boards-rent/packages`)
                .then((res) => {
                    let l = res.data.data;
                    this.packages = l;
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
         *  get Salesmen
         */
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
        /**
         *  get customer
         */
        async getCustomerSalesman(e)
        {
            let employee_id = e;
            if (employee_id)
            {
                await this.getCustomers(employee_id);
            }
        },

        async searchCustomer(e) {
            let search = e??'';
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getCustomers(this.create.employee_id,search);
            }, 500);

        },
        async getCustomers(employee_id=null,search='') {
            this.isLoader = true;
            await adminApi
                .get(`/general-customer?limet=10&employee_id=${employee_id??''}&search=${search}&columns[0]=name&columns[1]=name_e&columns[2]=id`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
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
         *  get Sell Method
         */
        async getSellMethod() {
            this.isLoader = true;
            await adminApi
                .get(`/boards-rent/sell-methods?company_id=${this.company_id}`)
                .then((res) => {
                    let l = res.data.data;
                    this.sellMethods = l;
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
         *  reset Modal (create)
         */
        resetModalHidden() {
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
            await this.getGovernorate();
            await this.getCategory();
            await this.getPackage();
            await this.getSalesmen();
            await this.getCustomers();
            await this.getSellMethod();
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
        },

        /**
         *  start  dynamicSortString
         */
        sortString(value) {
            return dynamicSortString(value);
        },
        moveInput(tag, c, index) {
            document.querySelector(`${tag}[data-${c}='${index}']`).focus()
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
                if (dl) {
                    XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
                } else {
                    XLSX.writeFile(wb, fn || (('Payment Report' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },
        colspanTable(val1,val2,val3=false,val4=false,val5=false,val6=false,val7=false,val8=false)
        {
            let num = 0;
            if (val1)
            {
                num += 1;
            }
            if (val2)
            {
                num += 1;
            }
            if (val3)
            {
                num += 1;
            }
            if (val4)
            {
                num += 1;
            }
            if (val5)
            {
                num += 1;
            }
            if (val6)
            {
                num += 1;
            }
            if (val7)
            {
                num += 1;
            }
            if (val8)
            {
                num += 1;
            }
            return num;
        },
        calcReturns(data)
        {
            let toDate = new Date(this.create.date_to).getTime();
            let fromDate = new Date(this.create.date_from).getTime();
            let toDateRow = new Date(data.date_to).getTime();
            let fromDateRow = new Date(data.date_from).getTime();
            let amountPerDay = data.total / data.rent_days;
            let revenue = 0;
            let TotalDays = 0;
            let difference = 0;

            if (fromDate <= fromDateRow && toDate >= toDateRow)
            {
                revenue = amountPerDay * data.rent_days;
            }else if (fromDate >= fromDateRow && toDate >= toDateRow){
                difference = toDateRow - fromDate;
                TotalDays = Math.ceil(difference / (1000 * 3600 * 24));
                revenue = amountPerDay * TotalDays;
            }else if (fromDate <= fromDateRow && toDate <= toDateRow){
                difference = toDate - fromDateRow;
                TotalDays = Math.ceil(difference / (1000 * 3600 * 24));
                revenue = amountPerDay * TotalDays;
            }else if (fromDate >= fromDateRow && toDate <= toDateRow){
                difference = toDate - fromDate;
                TotalDays = Math.ceil(difference / (1000 * 3600 * 24));
                revenue = amountPerDay * TotalDays;
            }
            return revenue;
        }
    },
};
</script>

<template>
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title"> {{ $t('general.FinanceReportTable') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">

                                <div class="d-inline-block" style="width: 22.2%;">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown" class="btn-block setting-search dropdown-menu-custom-company">
                                        <b-form-checkbox v-model="filterSetting" :value="$i18n.locale == 'ar' ? 'category.name' : 'category.name_e'" class="mb-1">
                                            {{ $t('general.category') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" :value="$i18n.locale == 'ar' ? 'governorate.name' : 'governorate.name_e'" class="mb-1">
                                            {{ $t('general.governorate') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" :value="$i18n.locale == 'ar' ? 'package.name' : 'package.name_e'" class="mb-1">
                                            {{ $t('general.package') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="date_from" class="mb-1">
                                            {{ $t('general.from_date') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="rent_days" class="mb-1">
                                            {{ $t('general.rent_days') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="date_to" class="mb-1">
                                            {{ $t('general.to_date') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="quantity" class="mb-1">
                                            {{ $t('general.quantity') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="price_per_uint" class="mb-1">
                                            {{ $t('general.PricePerUnit') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="total" class="mb-1">
                                            {{ $t('general.Total') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="invoice_discount" class="mb-1">
                                            {{ $t('general.InvoiceDiscount') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="net_invoice" class="mb-1">
                                            {{ $t('general.NetInvoice') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="sell_method_discount" class="mb-1">
                                            {{ $t('general.sellMethodDiscount') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="unrealized_revenue" class="mb-1">
                                            {{ $t('general.unrelaized_revenue') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="external_commission" class="mb-1">
                                            {{ $t('general.externalCommission') }}
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
                                    v-b-modal.create
                                    variant="primary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t('general.Search') }}
                                    <i class="fe-search"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button class="custom-btn-dowonload" @click="ExportExcel('xlsx')">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button class="custom-btn-dowonload" v-print="'#printCustom'">
                                        <i class="fe-printer"></i>
                                    </button>

                                </div>
                                <!-- end create and printer -->
                            </div>
                            <div
                                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center  justify-content-center"
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
                                                    ref="dropdown" class="dropdown-custom-ali dropdown-menu-custom-company">
                                            <!--details-->
                                            <b-form-checkbox v-model="setting.serial_number" class="mb-1">{{ $t('general.serial_number')}}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.employee_id" class="mb-1">{{ $t('general.documentSalesmen')}}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.external_sales_id" class="mb-1">{{ $t('general.externalSalesmen')}}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.customer_id" class="mb-1">{{ $t('general.documentCustomer')}}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.category_id" class="mb-1">{{ $t('general.category')}}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.governorate_id" class="mb-1">{{ $t('general.governorate') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.package_id" class="mb-1">{{ $t('general.package')}}</b-form-checkbox>
                                            <!--date-->
                                            <b-form-checkbox v-model="setting.date_from" class="mb-1">{{ $t('general.from_date')}}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.date_to" class="mb-1">{{ $t('general.to_date')}}</b-form-checkbox>
                                            <!--Financial Data-->
                                            <b-form-checkbox v-model="setting.unit_type" class="mb-1">{{ $t('general.Unit_type')}}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.quantity" class="mb-1">{{ $t('general.quantity') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.price_per_uint" class="mb-1">{{ $t('general.PricePerUnit') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.total" class="mb-1">{{ $t('general.Total') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.invoice_discount" class="mb-1">{{ $t('general.InvoiceDiscount') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.net_invoice" class="mb-1">{{ $t('general.NetInvoice') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.sell_method_discount" class="mb-1">{{ $t('general.sellMethodDiscount') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.external_commission" class="mb-1">{{ $t('general.externalCommission') }}</b-form-checkbox>
                                            <!-- days-->
                                            <b-form-checkbox v-model="setting.rent_days_month" class="mb-1">{{ $t('general.rent_days') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.executed_days_month" class="mb-1">{{ $t('general.executed_days') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.remaining_days_month" class="mb-1">{{ $t('general.remaining_days') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.previously_used_days_month" class="mb-1">{{ $t('general.previously_used_days') }}</b-form-checkbox>
                                            <!-- revenue-->
                                            <b-form-checkbox v-model="setting.unrealized_revenue" class="mb-1">{{ $t('general.unrelaized_revenue') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.current_revenue_month" class="mb-1">{{ $t('general.current_revenue') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.used_previous_revenue_month" class="mb-1">{{ $t('general.used_previous_revenue') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.remaning_revenue_month" class="mb-1">{{ $t('general.remaining_revenue') }}</b-form-checkbox>
                                            <!-- Sell Method Discount-->
                                            <b-form-checkbox v-model="setting.unrealized_sell_method_discount_month" class="mb-1">{{ $t('general.line_unrealized_sell_method_discount') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.current_sell_method_discount_month" class="mb-1">{{ $t('general.line_current_sell_method_discount') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.previous_sell_method_discount_month" class="mb-1">{{ $t('general.line_used_previous_sell_method_discount') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.remaning_sell_method_discount_month" class="mb-1">{{ $t('general.line_remaining_sell_method_discount') }}</b-form-checkbox>
                                            <!-- External Commission-->
                                            <b-form-checkbox v-model="setting.unrealized_external_commission_month" class="mb-1">{{ $t('general.line_unrealized_external_commission') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.current_external_commission_month" class="mb-1">{{ $t('general.line_current_external_commission') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.previous_external_commission_month" class="mb-1">{{ $t('general.line_used_previous_external_commission') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.remaning_external_commission_month" class="mb-1">{{ $t('general.line_remaining_external_commission') }}</b-form-checkbox>
                                            <!-- Commission-->
                                            <b-form-checkbox v-model="setting.unrealized_commission_month" class="mb-1">{{ $t('general.line_unrealized_commission') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.current_commission_month" class="mb-1">{{ $t('general.line_current_commission') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.used_previous_commission_month" class="mb-1">{{ $t('general.line_used_previous_commission') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.remaning_commission_month" class="mb-1">{{ $t('general.line_remaining_commission') }}</b-form-checkbox>

                                            <b-form-checkbox v-model="setting.payments" class="mb-1">{{ $t('general.payments') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.debit_note" class="mb-1">{{ $t('general.debit_note') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.returns" class="mb-1">{{ $t('general.returns') }}</b-form-checkbox>
                                            <div class="d-flex justify-content-end">
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm">Apply</a>
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>
                                    <!-- end filter and setting -->

                                    <!-- start Pagination -->
<!--                                    <div class="d-inline-flex align-items-center pagination-custom">-->
<!--                                        <div class="d-inline-block" style="font-size:13px;">-->
<!--                                            {{ installmentStatusPagination.from }}-{{ installmentStatusPagination.to }}-->
<!--                                            /-->
<!--                                            {{ installmentStatusPagination.total }}-->
<!--                                        </div>-->
<!--                                        <div class="d-inline-block">-->
<!--                                            <a-->
<!--                                                href="javascript:void(0)"-->
<!--                                                :style="{'pointer-events':installmentStatusPagination.current_page == 1 ? 'none': ''}"-->
<!--                                                @click.prevent="getData(installmentStatusPagination.current_page - 1)"-->
<!--                                            >-->
<!--                                                <span>&lt;</span>-->
<!--                                            </a>-->
<!--                                            <input-->
<!--                                                type="text"-->
<!--                                                @keyup.enter="getDataCurrentPage()"-->
<!--                                                v-model="current_page"-->
<!--                                                class="pagination-current-page"-->
<!--                                            />-->
<!--                                            <a-->
<!--                                                href="javascript:void(0)"-->
<!--                                                :style="{'pointer-events':installmentStatusPagination.last_page == installmentStatusPagination.current_page ? 'none': ''}"-->
<!--                                                @click.prevent="getData(installmentStatusPagination.current_page + 1)"-->
<!--                                            >-->
<!--                                                <span>&gt;</span>-->
<!--                                            </a>-->
<!--                                        </div>-->
<!--                                    </div>-->
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!--  create   -->
                        <b-modal
                            id="create"
                            :title="$t('general.Search')"
                            title-class="font-18"
                            body-class="p-4"
                            size="lg"
                            :hide-footer="true"
                            @show="resetModal"
                            @hidden="resetModalHidden"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <template v-if="!is_disabled">
                                        <b-button
                                            variant="success"
                                            type="button" class="mx-1"
                                            v-if="!isLoader"
                                            @click.prevent="getData"
                                        >
                                            {{ $t('general.Search') }}
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
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.fromDate') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="$v.create.date_from.$model"
                                                format="YYYY-MM-DD"
                                                valueType="format"
                                                :confirm="false"
                                                :class="{'is-invalid':$v.create.date_from.$error ||errors.date_from,'is-valid':!$v.create.date_from.$invalid &&!errors.date_from,}"
                                            ></date-picker>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.toDate') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="$v.create.date_to.$model"
                                                format="YYYY-MM-DD"
                                                valueType="format"
                                                :confirm="false"
                                                :class="{'is-invalid':$v.create.date_to.$error ||errors.date_to,'is-valid':!$v.create.date_to.$invalid &&!errors.date_to,}"
                                            ></date-picker>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group position-relative">
                                            <label class="control-label">{{ $t('general.is_stripe') }}</label>
                                            <b-form-group>
                                                <b-form-radio class="d-inline-block" v-model="create.is_stripe" name="some-radios"
                                                              value="1">{{ $t("general.Yes") }}</b-form-radio>
                                                <b-form-radio class="d-inline-block m-1" v-model="create.is_stripe" name="some-radios"
                                                              value="0">{{ $t("general.No") }}</b-form-radio>
                                            </b-form-group>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group position-relative">
                                            <label
                                                class="control-label">{{ $t('general.governorate') }}
                                            </label>
                                            <multiselect
                                                v-model="create.governorate_id"
                                                :options="governorates.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ?governorates.find((x) => x.id == opt).name:governorates.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group position-relative">
                                            <label
                                                class="control-label">{{ $t('general.category') }}
                                            </label>
                                            <multiselect
                                                v-model="create.category_id"
                                                :options="categories.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ?categories.find((x) => x.id == opt).name:categories.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group position-relative">
                                            <label
                                                class="control-label">{{ $t('general.package') }}
                                            </label>
                                            <multiselect
                                                v-model="create.package_id"
                                                :options="packages.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ?packages.find((x) => x.id == opt).name:packages.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group position-relative">
                                            <label class="control-label">{{ $t('general.documentSalesmen') }} </label>
                                            <multiselect
                                                @input="getCustomerSalesman"
                                                v-model="create.employee_id"
                                                :options="salesmen.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ?salesmen.find((x) => x.id == opt).name:salesmen.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group position-relative">
                                            <label
                                                class="control-label">{{ $t('general.documentCustomer') }}
                                            </label>
                                            <multiselect
                                                :internalSearch="false"
                                                @search-change="searchCustomer"
                                                v-model="create.customer_id"
                                                :options="customers.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ?customers.find((x) => x.id == opt).name:customers.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group position-relative">
                                            <label
                                                class="control-label">{{ $t('general.sellMethod') }}
                                            </label>
                                            <multiselect
                                                v-model="create.sell_method_id"
                                                :options="sellMethods.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ?sellMethods.find((x) => x.id == opt).name:sellMethods.find((x) => x.id == opt).name_e"
                                            >
                                            </multiselect>
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
                            <loader size="large" v-if="isLoader"/>
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0">
                                <thead>
                                <tr>
                                    <th  v-if="setting.serial_number || setting.employee_id || setting.external_sales_id || setting.customer_id || setting.category_id || setting.governorate_id || setting.package_id"
                                         :colspan="colspanTable(setting.serial_number,setting.employee_id,setting.external_sales_id,setting.customer_id,setting.category_id,setting.governorate_id,setting.package_id)">{{$t("general.details")}}</th>
                                    <th v-if="setting.date_from || setting.date_to"
                                        :colspan="colspanTable(setting.date_from,setting.date_to)">{{$t("general.date")}}</th>
                                    <th v-if="setting.unit_type || setting.quantity || setting.price_per_uint || setting.total || setting.invoice_discount || setting.net_invoice || setting.sell_method_discount || setting.external_commission"
                                        :colspan="colspanTable(setting.unit_type,setting.quantity,setting.price_per_uint,setting.total,setting.invoice_discount,setting.net_invoice,setting.sell_method_discount,setting.external_commission)">{{$t("general.FinancialData")}}</th>
                                    <th v-if="setting.rent_days_month || setting.executed_days_month || setting.remaining_days_month || setting.previously_used_days_month"
                                        :colspan="colspanTable(setting.rent_days_month,setting.executed_days_month,setting.remaining_days_month,setting.previously_used_days_month)">{{ $t("general.days") }}</th>
                                    <th v-if="setting.unrealized_revenue || setting.current_revenue_month || setting.used_previous_revenue_month || setting.remaning_revenue_month"
                                        :colspan="colspanTable(setting.unrealized_revenue,setting.current_revenue_month,setting.used_previous_revenue_month,setting.remaning_revenue_month)">{{$t("general.revenue")}}</th>
                                    <th v-if="setting.unrealized_sell_method_discount_month || setting.previous_sell_method_discount_month || setting.current_sell_method_discount_month || setting.remaning_sell_method_discount_month"
                                        :colspan="colspanTable(setting.unrealized_sell_method_discount_month,setting.previous_sell_method_discount_month,setting.current_sell_method_discount_month,setting.remaning_sell_method_discount_month)">{{$t("general.sellMethodDiscount")}}</th>
                                    <th v-if="setting.unrealized_external_commission_month || setting.previous_external_commission_month || setting.current_external_commission_month || setting.remaning_external_commission_month"
                                        :colspan="colspanTable(setting.unrealized_external_commission_month,setting.previous_external_commission_month,setting.current_external_commission_month,setting.remaning_external_commission_month)">{{$t("general.externalCommission")}}</th>
                                    <th v-if="setting.unrealized_commission_month || setting.used_previous_commission_month || setting.current_commission_month || setting.remaning_commission_month"
                                        :colspan="colspanTable(setting.unrealized_commission_month,setting.used_previous_commission_month,setting.current_commission_month,setting.remaning_commission_month)">{{$t("general.commission")}}</th>
                                    <th v-if="setting.payments || setting.debit_note || setting.returns"
                                        :colspan="colspanTable(setting.payments,setting.debit_note,setting.returns)"></th>
                                </tr>
                                <tr>
                                    <!--details-->
                                    <th v-if="setting.serial_number">{{ $t("general.serial_number") }}</th>
                                    <th v-if="setting.employee_id">{{ $t("general.documentSalesmen") }}</th>
                                    <th v-if="setting.external_sales_id">{{ $t("general.externalSalesmen") }}</th>
                                    <th v-if="setting.customer_id">{{ $t("general.documentCustomer") }}</th>
                                    <th v-if="setting.category_id">{{ $t("general.category") }}</th>
                                    <th v-if="setting.governorate_id">{{ $t("general.governorate") }}</th>
                                    <th v-if="setting.package_id">{{ $t("general.package") }}</th>
                                    <!--date-->
                                    <th v-if="setting.date_from">{{ $t("general.from") }}</th>
                                    <th v-if="setting.date_to">{{ $t("general.to") }}</th>
                                    <!--Financial Data-->
                                    <th v-if="setting.unit_type">{{ $t("general.Unit_type") }}</th>
                                    <th v-if="setting.quantity">{{ $t("general.quantity") }}</th>
                                    <th v-if="setting.price_per_uint">{{ $t("general.PricePerUnit") }}</th>
                                    <th v-if="setting.total">{{ $t("general.Total") }}</th>
                                    <th v-if="setting.invoice_discount">{{ $t("general.InvoiceDiscount") }}</th>
                                    <th v-if="setting.net_invoice">{{ $t("general.NetInvoice") }}</th>
                                    <th v-if="setting.sell_method_discount">{{ $t("general.sellMethodDiscount") }}</th>
                                    <th v-if="setting.external_commission">{{ $t("general.externalCommission") }}</th>
                                    <!-- days-->
                                    <th v-if="setting.rent_days_month">{{ $t("general.rent_days") }}</th>
                                    <th v-if="setting.executed_days_month">{{ $t("general.executed_days") }}</th>
                                    <th v-if="setting.remaining_days_month">{{ $t("general.Previous") }}</th>
                                    <th v-if="setting.previously_used_days_month">{{ $t("general.Remaining") }}</th>
                                    <!-- revenue-->
                                    <th v-if="setting.unrealized_revenue">{{ $t("general.Unrealized") }}</th>
                                    <th v-if="setting.current_revenue_month">{{ $t("general.Current") }}</th>
                                    <th v-if="setting.used_previous_revenue_month">{{ $t("general.Previous") }}</th>
                                    <th v-if="setting.remaning_revenue_month">{{ $t("general.Remaining") }}</th>
                                    <!-- Sell Method Discount-->
                                    <th v-if="setting.unrealized_sell_method_discount_month">{{ $t("general.Unrealized") }}</th>
                                    <th v-if="setting.current_sell_method_discount_month">{{ $t("general.Current") }}</th>
                                    <th v-if="setting.previous_sell_method_discount_month">{{ $t("general.Previous") }}</th>
                                    <th v-if="setting.remaning_sell_method_discount_month">{{ $t("general.Remaining") }}</th>
                                    <!-- External Commission-->
                                    <th v-if="setting.unrealized_external_commission_month">{{ $t("general.Unrealized") }}</th>
                                    <th v-if="setting.current_external_commission_month">{{ $t("general.Current") }}</th>
                                    <th v-if="setting.previous_external_commission_month">{{ $t("general.Previous") }}</th>
                                    <th v-if="setting.remaning_external_commission_month">{{ $t("general.Remaining") }}</th>
                                    <!-- Commission-->
                                    <th v-if="setting.unrealized_commission_month">{{ $t("general.Unrealized") }}</th>
                                    <th v-if="setting.current_commission_month">{{ $t("general.Current") }}</th>
                                    <th v-if="setting.used_previous_commission_month">{{ $t("general.Previous") }}</th>
                                    <th v-if="setting.remaning_commission_month">{{ $t("general.Remaining") }}</th>

                                    <th v-if="setting.payments"><span>{{ $t('general.payments') }}</span></th>
                                    <th v-if="setting.debit_note"><span>{{ $t('general.debit_note') }}</span></th>
                                    <th v-if="setting.returns"><span>{{ $t('general.returns') }}</span></th>
                                </tr>
                                </thead>
                                <tbody v-if="installmentStatus.length > 0">
                                <tr
                                    v-for="(item,index) in installmentStatus"
                                    :key="item.id"
                                    class="body-tr-custom"
                                >
                                    <!--details-->
                                    <td v-if="setting.serial_number"><h5 class="m-0 font-weight-normal">{{ item.document_header.prefix }}</h5></td>
                                    <td v-if="setting.employee_id"><h5 class="m-0 font-weight-normal">{{ $i18n.locale == 'ar' ? item.document_header.employee.name: item.document_header.employee.name_e }}</h5></td>
                                    <td v-if="setting.external_sales_id"><h5 class="m-0 font-weight-normal">{{ $i18n.locale == 'ar' ? item.document_header.external_salesmen?item.document_header.external_salesmen.name:'---': item.document_header.external_salesmen?item.document_header.external_salesmen.name_e:'---' }}</h5></td>
                                    <td v-if="setting.customer_id"><h5 class="m-0 font-weight-normal">{{ $i18n.locale == 'ar' ? item.document_header.customer.name: item.document_header.customer.name_e }}</h5></td>
                                    <td v-if="setting.category_id"><h5 v-if="item.category_id" class="m-0 font-weight-normal">{{ $i18n.locale == 'ar' ? item.category.name: item.category.name_e }}</h5></td>
                                    <td v-if="setting.governorate_id"><h5 v-if="item.governorate_id" class="m-0 font-weight-normal">{{ $i18n.locale == 'ar'? item.governorate.name : item.governorate.name_e }}</h5></td>
                                    <td v-if="setting.package_id"><h5 v-if="item.package_id" class="m-0 font-weight-normal">{{ $i18n.locale == 'ar' ? item.package.name: item.package.name_e}}</h5></td>
                                    <!--date-->
                                    <td v-if="setting.date_from"><h5 class="m-0 font-weight-normal">{{ item.date_from }}</h5></td>
                                    <td v-if="setting.date_to"><h5 class="m-0 font-weight-normal">{{ item.date_to }}</h5></td>
                                    <!--Financial Data-->
                                    <td v-if="setting.unit_type"><h5 class="m-0 font-weight-normal">{{ item.unit_type }}</h5></td>
                                    <td v-if="setting.quantity"><h5 class="m-0 font-weight-normal">{{ item.quantity }}</h5></td>
                                    <td v-if="setting.price_per_uint"><h5 class="m-0 font-weight-normal">{{ item.price_per_uint }}</h5></td>
                                    <td v-if="setting.total"><h5 class="m-0 font-weight-normal">{{ item.total }}</h5></td>
                                    <td v-if="setting.invoice_discount"><h5 class="m-0 font-weight-normal">{{ item.invoice_discount }}</h5></td>
                                    <td v-if="setting.net_invoice"><h5 class="m-0 font-weight-normal">{{ item.net_invoice }}</h5></td>
                                    <td v-if="setting.sell_method_discount"><h5 class="m-0 font-weight-normal">{{ item.sell_method_discount }}</h5></td>
                                    <td v-if="setting.external_commission"><h5 class="m-0 font-weight-normal">{{ item.external_commission }}</h5></td>
                                    <!-- days-->
                                    <td v-if="setting.rent_days_month"><h5 class="m-0 font-weight-normal">{{ item.rent_days }}</h5></td>
                                    <td v-if="setting.executed_days_month"><h5 class="m-0 font-weight-normal">{{ item.line_executed_days }}</h5></td>
                                    <td v-if="setting.remaining_days_month"><h5 class="m-0 font-weight-normal">{{ item.line_previously_used_days }}</h5></td>
                                    <td v-if="setting.previously_used_days_month"><h5 class="m-0 font-weight-normal">{{ item.line_remaining_days }}</h5></td>
                                    <!-- revenue-->
                                    <td v-if="setting.unrealized_revenue"><h5 class="m-0 font-weight-normal">{{ item.line_unrealized_revenue }}</h5></td>
                                    <td v-if="setting.current_revenue_month"><h5 class="m-0 font-weight-normal">{{ item.line_current_revenue }}</h5></td>
                                    <td v-if="setting.used_previous_revenue_month"><h5 class="m-0 font-weight-normal">{{ item.line_used_previous_revenue }}</h5></td>
                                    <td v-if="setting.remaning_revenue_month"><h5 class="m-0 font-weight-normal">{{ item.line_remaning_revenue }}</h5></td>
                                    <!-- Sell Method Discount-->
                                    <td v-if="setting.unrealized_sell_method_discount_month"><h5 class="m-0 font-weight-normal">{{ item.line_unrealized_sell_method_discount }}</h5></td>
                                    <td v-if="setting.current_sell_method_discount_month"><h5 class="m-0 font-weight-normal">{{ item.line_current_sell_method_discount }}</h5></td>
                                    <td v-if="setting.previous_sell_method_discount_month"><h5 class="m-0 font-weight-normal">{{ item.line_used_previous_sell_method_discount }}</h5></td>
                                    <td v-if="setting.remaning_sell_method_discount_month"><h5 class="m-0 font-weight-normal">{{ item.line_remaning_sell_method_discount }}</h5></td>
                                    <!-- External Commission-->
                                    <td v-if="setting.unrealized_external_commission_month"><h5 class="m-0 font-weight-normal">{{ item.line_unrealized_external_commission }}</h5></td>
                                    <td v-if="setting.current_external_commission_month"><h5 class="m-0 font-weight-normal">{{ item.line_current_external_commission }}</h5></td>
                                    <td v-if="setting.previous_external_commission_month"><h5 class="m-0 font-weight-normal">{{ item.line_used_previous_external_commission }}</h5></td>
                                    <td v-if="setting.remaning_external_commission_month"><h5 class="m-0 font-weight-normal">{{ item.line_remaning_external_commission }}</h5></td>
                                    <!-- Commission-->
                                    <td v-if="setting.unrealized_commission_month"><h5 class="m-0 font-weight-normal">{{ item.line_unrealized_commission }}</h5></td>
                                    <td v-if="setting.current_commission_month"><h5 class="m-0 font-weight-normal">{{ item.line_current_commission }}</h5></td>
                                    <td v-if="setting.used_previous_commission_month"><h5 class="m-0 font-weight-normal">{{ item.line_used_previous_commission }}</h5></td>
                                    <td v-if="setting.remaning_commission_month"><h5 class="m-0 font-weight-normal">{{ item.line_remaning_commission }}</h5></td>

                                    <td v-if="setting.payments"><h5 class="m-0 font-weight-normal">{{ item.line_payments }}</h5></td>
                                    <td v-if="setting.debit_note"><h5 class="m-0 font-weight-normal">{{ item.line_total_debit_note }}</h5></td>
                                    <td v-if="setting.returns"><h5 class="m-0 font-weight-normal">{{ item.line_total_returns }}</h5></td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="40">{{ $t('general.notDataFound') }}</th>
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

<style>
.dropdown-menu-custom-company.dropdown .dropdown-menu {
    padding: 5px 10px !important;
    overflow-y: scroll;
    height: 300px;
}
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
.td5{
    font-size: 16px !important;
}
th{
    border: 1px solid !important;
}
</style>



