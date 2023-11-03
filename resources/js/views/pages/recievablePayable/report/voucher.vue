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
import Multiselect from "vue-multiselect";
import permissionGuard from "../../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Voucher Report",
        meta: [{name: "Voucher Report", content: 'Voucher Report'}],
    },
    mixins: [translation],
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Voucher RP", "all voucher RP");
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
            payment_types: [],
            isLoader: false,
            create: {
                start_date: this.formatDate(new Date()),
                end_date: this.formatDate(new Date()),
                customer_id: null,
                instalment_type_id: null,
                amount_status: null,
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
                local_debit: true,
                local_credit: true,
                total: true,
                break_type: true,
                instalment_type_id: true,
                amount_status: true,
                date_status: true,
                serial_number: true,

            },
            filterSetting: [
                'customer_id',
                'instalment_date',
                'currency_id',
                'rate',
                'debit',
                'credit',
                'total',
                'break_type',
                'instalment_type_id',
                'amount_status',
            ],
            Tooltip: '',
            mouseEnter: null,
        }
    },
    validations: {
        create: {
            start_date: {required},
            end_date: {required},
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
                index = this.filterSetting.indexOf("instalment_type_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "installment_payment_type.name" : "installment_payment_type.name_e";
                }
                let filter = '';
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }

                adminApi.get(`/recievable-payable/filter-vourcher?start_date=${this.create.start_date}&end_date=${this.create.end_date}&customer_id=${this.create.customer_id??''}&instalment_type_id=${this.create.instalment_type_id??''}&amount_status=${this.create.amount_status??''}&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
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
        getDataCurrentPage(page = 1) {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
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
                    index = this.filterSetting.indexOf("instalment_type_id");
                    if (index > -1) {
                        _filterSetting[index] =
                            this.$i18n.locale == "ar" ? "installment_payment_type.name" : "installment_payment_type.name_e";
                    }
                    let filter = "";
                    for (let i = 0; i < _filterSetting.length; ++i) {
                        filter += `columns[${i}]=${_filterSetting[i]}&`;
                    }

                    adminApi.get(`/recievable-payable/filter-vourcher?start_date=${this.create.start_date}&end_date=${this.create.end_date}&customer_id=${this.create.customer_id??''}&instalment_type_id=${this.create.instalment_type_id??''}&amount_status=${this.create.amount_status??''}&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
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
         *  get customer
         */
        async getCustomers(opening_balance=0) {
            this.isLoader = true;
            await adminApi
                .get(`/general-customer?opening_balance=${opening_balance}`)
                .then((res) => {
                    let l = res.data.data;
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
         *  get payment type
         */
        async getInstallPaymentTypes() {
            this.isLoader = true;
            await adminApi
                .get(`/recievable-payable/rp_installment_payment_types`)
                .then((res) => {
                    let l = res.data.data;
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
            await this.getCustomers(1);
            await this.getInstallPaymentTypes();
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

        dateStatus(instalment_date_row,inv_date)
        {
            let instalment_date = this.formatDate(instalment_date_row);
            let invoice_date = this.formatDate(inv_date);
            if (instalment_date > invoice_date)
            {
                return 'advancePayment';
            }else if (instalment_date == invoice_date)
            {
                return 'onTimePayment';
            }else if (instalment_date < invoice_date)
            {
                return 'latePayment';
            }else {
                return 'completedPayment'
            }
        }

    },
};
</script>

<template>
    <Layout>
        <PageHeader/>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title"> {{ $t('general.VoucherReportTable') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">

                                <div class="d-inline-block" style="width: 22.2%;">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting"
                                                         :value="$i18n.locale == 'ar' ? 'customer.name' : 'customer.name_e'"
                                                         class="mb-1">{{ getCompanyKey("customer") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="instalment_date" class="mb-1">
                                            {{ $t('general.instalmentDate') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting"
                                                         :value="$i18n.locale == 'ar' ? 'currency.name' : 'currency.name_e'"
                                                         class="mb-1">{{ getCompanyKey("installment_opening_balance_currency") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="rate" class="mb-1">
                                            {{ getCompanyKey('installment_opening_balance_rate') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="debit" class="mb-1">
                                            {{ getCompanyKey('installment_opening_balance_local_debit') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="credit" class="mb-1">
                                            {{ getCompanyKey('installment_opening_balance_local_credit') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="total" class="mb-1">
                                            {{ $t('general.Total') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="break_type" class="mb-1">
                                            {{ $t('general.type') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting"
                                                         :value="$i18n.locale == 'ar' ? 'installment_payment_type.name' : 'installment_payment_type.name_e'"
                                                         class="mb-1">{{ getCompanyKey("installment_payment_type_id") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="amount_status" class="mb-1">
                                            {{ $t("general.amount_status") }}
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
                                            <b-form-checkbox v-model="setting.date" class="mb-1">{{ $t('general.instalmentDate') }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.currency_id" class="mb-1">{{ getCompanyKey("installment_opening_balance_currency")}}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.rate" class="mb-1">{{ getCompanyKey("installment_opening_balance_rate")}}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.local_debit" class="mb-1">{{ getCompanyKey("installment_opening_balance_local_debit")}}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.local_credit" class="mb-1">{{ getCompanyKey("installment_opening_balance_local_credit")}}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.total" class="mb-1">{{ $t('general.Total') }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.break_type" class="mb-1">{{ $t('general.type') }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.instalment_type_id" class="mb-1">{{ getCompanyKey("installment_payment_type_id") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.amount_status" class="mb-1">{{ $t('general.amount_status') }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.date_status" class="mb-1">{{ $t('general.date_status') }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.serial_number" class="mb-1">{{ $t('general.serial_number') }}
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.fromDate') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="$v.create.start_date.$model"
                                                format="YYYY-MM-DD"
                                                valueType="format"
                                                :confirm="false"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.start_date.$error ||
                                                        errors.start_date,
                                                    'is-valid':
                                                        !$v.create.start_date
                                                            .$invalid &&
                                                        !errors.start_date,
                                                    }"
                                            ></date-picker>

                                            <template v-if="errors.start_date">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.start_date"
                                                              :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.toDate') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="$v.create.end_date.$model"
                                                format="YYYY-MM-DD"
                                                valueType="format"
                                                :confirm="false"
                                                :class="{
                                                    'is-invalid':
                                                        $v.create.end_date.$error ||
                                                        errors.end_date,
                                                    'is-valid':
                                                        !$v.create.end_date
                                                            .$invalid &&
                                                        !errors.end_date,
                                                    }"
                                            ></date-picker>

                                            <template v-if="errors.end_date">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.end_date"
                                                              :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label
                                                class="control-label">{{ getCompanyKey("customer") }}
                                            </label>
                                            <multiselect
                                                v-model="create.customer_id"
                                                :options="customers.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ?customers.find((x) => x.id == opt).name:customers.find((x) => x.id == opt).name_e"
                                                :class="{
                                                        'is-invalid': errors.customer_id,
                                                        'is-valid': !errors.customer_id,
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
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label
                                                class="control-label">{{ getCompanyKey("installment_payment_type_id") }}
                                            </label>
                                            <multiselect
                                                v-model="create.instalment_type_id"
                                                :options="payment_types.map((type) => type.id)"
                                                :custom-label="(opt) => $i18n.locale == 'ar' ?payment_types.find((x) => x.id == opt).name:payment_types.find((x) => x.id == opt).name_e"
                                                :class="{
                                                        'is-invalid': errors.instalment_type_id,
                                                        'is-valid': !errors.instalment_type_id,
                                                      }"
                                            >
                                            </multiselect>

                                            <template v-if="errors.instalment_type_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.instalment_type_id"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage
                                                >
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label
                                                class="control-label">{{ $t('general.amount_status') }}
                                            </label>
                                            <select
                                                class="custom-select"
                                                v-model="create.amount_status"
                                            >
                                                <option value="Paid">{{ $t('general.Paid') }}</option>
                                                <option value="Paid_Partially">{{ $t('general.Paid_Partially') }}</option>
                                            </select>

                                            <template v-if="errors.amount_status">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.amount_status"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage
                                                >
                                            </template>
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
                                            <span>{{ $t('general.instalmentDate') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('instalment_date'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-instalment_date'))"></i>
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
                                    <th v-if="setting.total">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Total') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('total'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-total'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.break_type">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.type') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('break_type'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-break_type'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.instalment_type_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("installment_payment_type_id") }}</span>
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
                                    <th v-if="setting.amount_status">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.amount_status') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('amount_status'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-amount_status'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.date_status">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.date_status') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('date_status'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-date_status'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.serial_number">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.serial_number') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('serial_number'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-serial_number'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody v-if="installmentStatus.length > 0">
                                <tr
                                    v-for="(data,index) in installmentStatus"
                                    :key="data.id"
                                    class="body-tr-custom"
                                >

                                    <td v-if="setting.customer_id">
                                        <h5 class="m-0 font-weight-normal td5">
                                            {{ $i18n.locale == "ar" ? data.customer.name : data.customer.name_e}}
                                        </h5>
                                    </td>
                                    <td v-if="setting.date">
                                        <h5 class="m-0 font-weight-normal td5">{{ data.instalment_date }}</h5>
                                    </td>
                                    <td v-if="setting.currency_id">
                                        <h5 class="m-0 font-weight-normal td5">
                                            {{ $i18n.locale == "ar" ? data.currency.name : data.currency.name_e}}
                                        </h5>
                                    </td>
                                    <td v-if="setting.rate">
                                        <h5 class="m-0 font-weight-normal td5">{{ data.rate }}</h5>
                                    </td>
                                    <td v-if="setting.local_debit">
                                        <h5 class="m-0 font-weight-normal td5">{{ data.debit }}</h5>
                                    </td>
                                    <td v-if="setting.local_credit">
                                        <h5 class="m-0 font-weight-normal td5">{{ data.credit }}</h5>
                                    </td>

                                    <td v-if="setting.total">
                                        <h5 class="m-0 font-weight-normal td5">{{ data.total }}</h5>
                                    </td>
                                    <td v-if="setting.break_type">
                                        <h5 v-if="data.document" class="m-0 font-weight-normal">{{$i18n.locale == "ar" ? data.document.name : data.document.name_e}}</h5>
                                        <h5 v-else class="m-0 font-weight-normal">---</h5>
                                    </td>
                                    <td v-if="setting.instalment_type_id">
                                        <h5 class="m-0 font-weight-normal td5">{{ $i18n.locale == "ar" ? data.installment_payment_type.name : data.installment_payment_type.name_e}}</h5>
                                    </td>
                                    <td v-if="setting.amount_status">
                                        <h5 class="m-0 font-weight-normal td5">{{ $t('general.'+data.amount_status)}}</h5>
                                    </td>
                                    <td v-if="setting.date_status">
                                        <h5 class="m-0 font-weight-normal td5">{{$t('general.'+dateStatus(data.instalment_date,data.payment_invoice.date))}}</h5>
                                    </td>
                                    <td v-if="setting.serial_number">
                                        <h5 class="m-0 font-weight-normal td5">{{ data.payment_invoice ? data.payment_invoice.prefix : '---'}}</h5>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="12">{{ $t('general.notDataFound') }}</th>
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
.td5{
    font-size: 16px !important;
}
</style>



