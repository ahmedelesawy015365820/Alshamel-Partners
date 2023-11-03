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
    name: "Annual-Finance-Report",
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
            annualFinance:[],
            governorates: [],
            categories: [],
            packages: [],
            salesmen: [],
            customers: [],
            sellMethods: [],
            isLoader: false,
            create: {
                year: new Date().getFullYear().toString(),
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
                month: true,
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
            ],
            Tooltip: '',
            mouseEnter: null,
        }
    },
    validations: {
        create: {
            year: {required},
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
        getData() {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                adminApi.get(`/document-header-details/annual-financial-report?document_detail_type=${this.document_detail_type}&year=${this.create.year}&governorate_id=${this.create.governorate_id??''}&category_id=${this.create.category_id??''}&is_stripe=${this.create.is_stripe??''}&package_id=${this.create.package_id??''}&employee_id=${this.create.employee_id??''}&customer_id=${this.create.customer_id??''}&sell_method_id=${this.create.sell_method_id??''}`)
                    .then((res) => {
                        let l = res.data;
                        this.annualFinance = l.data;
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
        showDetails(id,page = 1)
        {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                adminApi.get(`/document-header-details/annual-financial-report-detail?document_detail_type=${this.document_detail_type}&month=${id}&year=${this.create.year}&governorate_id=${this.create.governorate_id??''}&category_id=${this.create.category_id??''}&is_stripe=${this.create.is_stripe??''}&package_id=${this.create.package_id??''}&employee_id=${this.create.employee_id??''}&customer_id=${this.create.customer_id??''}&sell_method_id=${this.create.sell_method_id??''}&page=${page}&per_page=${this.per_page}`)
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
        getDataCurrentPage(id,page = 1) {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                if (this.current_page <= this.installmentStatusPagination.last_page && this.current_page != this.installmentStatusPagination.current_page && this.current_page) {
                    this.isLoader = true;
                    adminApi.get(`/document-header-details/annual-financial-report-detail?document_detail_type=${this.document_detail_type}&year=${this.create.year}&month=${id}&page=${this.current_page}&per_page=${this.per_page}`)
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
                    XLSX.writeFile(wb, fn || (('Annual Finance Report' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },
        colspanTable(val1,val2,val3,val4=false,val5=false,val6=false)
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
            return num;
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
                            <h4 class="header-title"> {{ $t('general.AnnualFinanceReportTable') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">

                                <div class="d-inline-block" style="width: 22.2%;">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown" class="btn-block setting-search dropdown-menu-custom-company">

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
                                            <b-form-checkbox v-model="setting.month" class="mb-1">{{ $t('general.month') }}</b-form-checkbox>
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
                                                {{ $t('general.financialYear') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="year"
                                                v-model="$v.create.year.$model"
                                                format="YYYY"
                                                valueType="format"
                                                :confirm="false"
                                                default-panel="year"
                                                :class="{'is-invalid':$v.create.year.$error ||errors.year,'is-valid':!$v.create.year.$invalid &&!errors.year,}"
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
                                    <th v-if="setting.month || setting.total || setting.invoice_discount || setting.net_invoice || setting.sell_method_discount || setting.external_commission"
                                        :colspan="colspanTable(setting.month,setting.total,setting.invoice_discount,setting.net_invoice,setting.sell_method_discount,setting.external_commission)"></th>
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
                                    <th colspan="1"></th>
                                </tr>
                                <tr>
                                    <th v-if="setting.month"> {{ $t('general.month') }} </th>
                                    <th v-if="setting.total"><span>{{ $t('general.Total') }}</span> </th>
                                    <th v-if="setting.invoice_discount"><span>{{ $t('general.InvoiceDiscount') }}</span></th>
                                    <th v-if="setting.net_invoice"><span>{{ $t('general.NetInvoice') }}</span></th>
                                    <th v-if="setting.sell_method_discount"><span>{{ $t('general.sellMethodDiscount') }}</span></th>
                                    <th v-if="setting.external_commission"><span>{{ $t('general.externalCommission') }}</span></th>
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
                                    <th v-if="enabled3" class="do-not-print">{{ $t("general.Action") }}</th>
                                </tr>
                                </thead>
                                <tbody v-if="annualFinance.length > 0">
                                <tr
                                    v-for="(data,index) in annualFinance"
                                    :key="data.month"
                                    class="body-tr-custom"
                                >

                                    <td v-if="setting.month"><h5 class="m-0 font-weight-normal td5">{{ $t('month.'+data.month) }}</h5></td>
                                    <td v-if="setting.total"><h5 class="m-0 font-weight-normal td5">{{data.total_month}}</h5></td>
                                    <td v-if="setting.invoice_discount"><h5 class="m-0 font-weight-normal td5">{{ data.invoice_discount_month }}</h5></td>
                                    <td v-if="setting.net_invoice"> <h5 class="m-0 font-weight-normal td5">{{ data.net_invoice_month }}</h5></td>
                                    <td v-if="setting.sell_method_discount"><h5 class="m-0 font-weight-normal td5">{{ data.sell_method_discount_month }}</h5></td>
                                    <td v-if="setting.external_commission"><h5 class="m-0 font-weight-normal td5">{{ data.external_commission_month }}</h5></td>
                                    <!-- days-->
                                    <td v-if="setting.rent_days_month"><h5 class="m-0 font-weight-normal td5">{{ data.rent_days_month }}</h5></td>
                                    <td v-if="setting.executed_days_month"><h5 class="m-0 font-weight-normal td5">{{ data.executed_days_month }}</h5></td>
                                    <td v-if="setting.remaining_days_month"><h5 class="m-0 font-weight-normal td5">{{ data.remaining_days_month }}</h5></td>
                                    <td v-if="setting.previously_used_days_month"><h5 class="m-0 font-weight-normal td5">{{ data.previously_used_days_month }}</h5></td>
                                    <!-- revenue-->
                                    <td v-if="setting.unrealized_revenue"><h5 class="m-0 font-weight-normal td5">{{ data.unrealized_revenue_month }}</h5></td>
                                    <td v-if="setting.current_revenue_month"><h5 class="m-0 font-weight-normal td5">{{ data.current_revenue_month }}</h5></td>
                                    <td v-if="setting.used_previous_revenue_month"><h5 class="m-0 font-weight-normal td5">{{ data.used_previous_revenue_month }}</h5></td>
                                    <td v-if="setting.remaning_revenue_month"><h5 class="m-0 font-weight-normal td5">{{ data.remaning_revenue_month }}</h5></td>
                                    <!-- sell Method Discount-->
                                    <td v-if="setting.unrealized_sell_method_discount_month"><h5 class="m-0 font-weight-normal td5">{{ data.unrealized_sell_method_discount_month }}</h5></td>
                                    <td v-if="setting.current_sell_method_discount_month"><h5 class="m-0 font-weight-normal td5">{{ data.current_sell_method_discount_month }}</h5></td>
                                    <td v-if="setting.previous_sell_method_discount_month"><h5 class="m-0 font-weight-normal td5">{{ data.previous_sell_method_discount_month }}</h5></td>
                                    <td v-if="setting.remaning_sell_method_discount_month"><h5 class="m-0 font-weight-normal td5">{{ data.remaning_sell_method_discount_month }}</h5></td>
                                    <!-- External Commission-->
                                    <td v-if="setting.unrealized_external_commission_month"><h5 class="m-0 font-weight-normal td5">{{ data.unrealized_external_commission_month }}</h5> </td>
                                    <td v-if="setting.current_external_commission_month"><h5 class="m-0 font-weight-normal td5">{{ data.current_external_commission_month }}</h5> </td>
                                    <td v-if="setting.previous_external_commission_month"><h5 class="m-0 font-weight-normal td5">{{ data.previous_external_commission_month }}</h5> </td>
                                    <td v-if="setting.remaning_external_commission_month"><h5 class="m-0 font-weight-normal td5">{{ data.remaning_external_commission_month }}</h5> </td>
                                    <!-- Commission-->
                                    <td v-if="setting.unrealized_commission_month"><h5 class="m-0 font-weight-normal td5">{{ data.unrealized_commission_month }}</h5> </td>
                                    <td v-if="setting.current_commission_month"><h5 class="m-0 font-weight-normal td5">{{ data.current_commission_month }}</h5> </td>
                                    <td v-if="setting.used_previous_commission_month"><h5 class="m-0 font-weight-normal td5">{{ data.used_previous_commission_month }}</h5> </td>
                                    <td v-if="setting.remaning_commission_month"><h5 class="m-0 font-weight-normal td5">{{ data.remaning_commission_month }}</h5> </td>

                                    <td v-if="setting.payments"><h5 class="m-0 font-weight-normal td5">{{ data.payments_month }}</h5></td>
                                    <td v-if="setting.debit_note"><h5 class="m-0 font-weight-normal td5">{{ data.total_debit_note_month }}</h5></td>
                                    <td v-if="setting.returns"><h5 class="m-0 font-weight-normal td5">{{ data.total_returns_month }}</h5></td>

                                    <td class="do-not-print">
                                        <b-button @click="$bvModal.show(`financial-details-${data.month}`)" variant="purple" type="button" class="btn-sm font-weight-bold px-2 mx-1">
                                            {{ $t("general.FinancialDetails") }}
                                            <i class="fas fa-dollar-sign"></i>
                                        </b-button>

                                        <!--  Financial Details   -->
                                        <b-modal
                                            dialog-class="modal-full-width"
                                            :id="`financial-details-${data.month}`"
                                            :title="$t('general.FinancialDetails')"
                                            title-class="font-18"
                                            body-class="p-4"
                                            :ref="`financial-${data.month}`"
                                            @show="showDetails(data.month)"
                                            :hide-footer="true"
                                        >
                                            <div class="row">
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
                                                            @click.prevent="showDetails(data.id,installmentStatusPagination.current_page - 1)"
                                                        >
                                                            <span>&lt;</span>
                                                        </a>
                                                        <input
                                                            type="text"
                                                            @keyup.enter="getDataCurrentPage(data.id)"
                                                            v-model="current_page"
                                                            class="pagination-current-page"
                                                        />
                                                        <a
                                                            href="javascript:void(0)"
                                                            :style="{'pointer-events':installmentStatusPagination.last_page == installmentStatusPagination.current_page ? 'none': ''}"
                                                            @click.prevent="showDetails(data.id,installmentStatusPagination.current_page + 1)"
                                                        >
                                                            <span>&gt;</span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <!-- end Pagination -->
                                                <div class="table-responsive mb-3 custom-table-theme position-relative">
                                                    <!--       start loader       -->
                                                    <loader size="large" v-if="isLoader" />
                                                    <!--       end loader       -->

                                                    <table class="table table-borderless table-hover table-centered m-0" >
                                                        <thead>
                                                        <tr>
                                                            <th colspan="17"></th>
                                                            <th colspan="4">{{ $t("general.days") }}</th>
                                                            <th colspan="4">{{$t("general.revenue")}}</th>
                                                            <th colspan="4">{{$t("general.sellMethodDiscount")}}</th>
                                                            <th colspan="4">{{$t("general.externalCommission")}}</th>
                                                            <th colspan="4">{{$t("general.commission")}}</th>
                                                            <th colspan="3"></th>
                                                        </tr>
                                                        <tr>
                                                            <th>{{ $t("general.serial_number") }}</th>
                                                            <th>{{ $t("general.documentSalesmen") }}</th>
                                                            <th>{{ $t("general.externalSalesmen") }}</th>
                                                            <th>{{ $t("general.documentCustomer") }}</th>
                                                            <th>{{ $t("general.category") }}</th>
                                                            <th>{{ $t("general.governorate") }}</th>
                                                            <th>{{ $t("general.package") }}</th>
                                                            <th>{{ $t("general.from_date") }}</th>
                                                            <th>{{ $t("general.to_date") }}</th>
                                                            <th>{{ $t("general.Unit_type") }}</th>
                                                            <th>{{ $t("general.quantity") }}</th>
                                                            <th>{{ $t("general.PricePerUnit") }}</th>
                                                            <th>{{ $t("general.Total") }}</th>
                                                            <th>{{ $t("general.InvoiceDiscount") }}</th>
                                                            <th>{{ $t("general.NetInvoice") }}</th>
                                                            <th>{{ $t("general.sellMethodDiscount") }}</th>
                                                            <th>{{ $t("general.externalCommission") }}</th>
                                                            <!-- days-->
                                                            <th>{{ $t("general.rent_days") }}</th>
                                                            <th>{{ $t("general.executed_days") }}</th>
                                                            <th>{{ $t("general.Previous") }}</th>
                                                            <th>{{ $t("general.Remaining") }}</th>
                                                            <!-- revenue-->
                                                            <th>{{ $t("general.Unrealized") }}</th>
                                                            <th>{{ $t("general.Current") }}</th>
                                                            <th>{{ $t("general.Previous") }}</th>
                                                            <th>{{ $t("general.Remaining") }}</th>
                                                            <!-- Sell Method Discount-->
                                                            <th>{{ $t("general.Unrealized") }}</th>
                                                            <th>{{ $t("general.Current") }}</th>
                                                            <th>{{ $t("general.Previous") }}</th>
                                                            <th>{{ $t("general.Remaining") }}</th>
                                                            <!-- External Commission-->
                                                            <th>{{ $t("general.Unrealized") }}</th>
                                                            <th>{{ $t("general.Current") }}</th>
                                                            <th>{{ $t("general.Previous") }}</th>
                                                            <th>{{ $t("general.Remaining") }}</th>
                                                            <!-- Commission-->
                                                            <th>{{ $t("general.Unrealized") }}</th>
                                                            <th>{{ $t("general.Current") }}</th>
                                                            <th>{{ $t("general.Previous") }}</th>
                                                            <th>{{ $t("general.Remaining") }}</th>

                                                            <th>{{ $t("general.payments") }}</th>
                                                            <th>{{ $t("general.debit_note") }}</th>
                                                            <th>{{ $t("general.returns") }}</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody v-if="installmentStatus.length > 0">
                                                        <tr v-for="(item, index) in installmentStatus"
                                                            :key="item.id"
                                                            class="body-tr-custom"
                                                        >
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.document_header.prefix }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ $i18n.locale == 'ar' ? item.document_header.employee.name: item.document_header.employee.name_e }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ $i18n.locale == 'ar' ? item.document_header.external_salesmen?item.document_header.external_salesmen.name:'---': item.document_header.external_salesmen?item.document_header.external_salesmen.name_e:'---' }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ $i18n.locale == 'ar' ? item.document_header.customer.name: item.document_header.customer.name_e }}</h5></td>
                                                            <td><h5 v-if="item.category_id" class="m-0 font-weight-normal">{{ $i18n.locale == 'ar' ? item.category.name: item.category.name_e }}</h5></td>
                                                            <td><h5 v-if="item.governorate_id" class="m-0 font-weight-normal">{{ $i18n.locale == 'ar'? item.governorate.name : item.governorate.name_e }}</h5></td>
                                                            <td><h5 v-if="item.package_id" class="m-0 font-weight-normal">{{ $i18n.locale == 'ar' ? item.package.name: item.package.name_e}}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.date_from }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.date_to }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.unit_type }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.quantity }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.price_per_uint }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.total }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.invoice_discount }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.net_invoice }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.sell_method_discount }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.external_commission }}</h5></td>
                                                            <!-- days-->
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.rent_days }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_executed_days }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_previously_used_days }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_remaining_days }}</h5></td>
                                                            <!-- revenue-->
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_unrealized_revenue }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_current_revenue }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_used_previous_revenue }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_remaning_revenue }}</h5></td>
                                                            <!-- Sell Method Discount-->
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_unrealized_sell_method_discount }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_current_sell_method_discount }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_used_previous_sell_method_discount }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_remaning_sell_method_discount }}</h5></td>
                                                            <!-- External Commission-->
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_unrealized_external_commission }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_current_external_commission }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_used_previous_external_commission }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_remaning_external_commission }}</h5></td>
                                                            <!-- Commission-->
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_unrealized_commission }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_current_commission }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_used_previous_commission }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_remaning_commission }}</h5></td>

                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_payments }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_total_debit_note }}</h5></td>
                                                            <td><h5 class="m-0 font-weight-normal">{{ item.line_total_returns }}</h5></td>
                                                        </tr>
                                                        </tbody>
                                                        <tbody v-else>
                                                        <tr>
                                                            <th class="text-center" colspan="40">
                                                                {{ $t("general.notDataFound") }}
                                                            </th>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </b-modal>
                                        <!--  /Financial Details   -->
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="30">{{ $t('general.notDataFound') }}</th>
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



