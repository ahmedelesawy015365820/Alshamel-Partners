<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength, integer, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import {dynamicSortString} from "../../../helper/tableSort";
import {formatDateOnly} from "../../../helper/startDate";
import translation from "../../../helper/mixin/translation-mixin";
import Multiselect from "vue-multiselect";
import permissionGuard from "../../../helper/permission";
import DatePicker from "vue2-datepicker";
/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Payment Report",
        meta: [{name: "Payment Report", content: 'Payment Report'}],
    },
    mixins: [translation],
    components: {
        Layout,
        PageHeader,
        Switches,
        DatePicker,
        ErrorMessage,
        loader,
        Multiselect,
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Paid After A Specific Date", "all paid member");
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
                date: '',
                year: '',
                member_status_id: 1
            },
            errors: {},
            is_disabled: false,
            isCheckAll: false,
            checkAll: [],
            statuses: [],
            current_page: 1,
            enabled3: true,
            printLoading: true,
            printObj: {
                id: "printCustom",
            },
            openingBreak:'',
            setting: {
                cm_member_id: true,
                branch_id: true,
                prefix: true,
                date: true,
                year_from: true,
                year_to: true,
                number_of_years: true,
            },
            filterSetting: [
                'cm_member_id',
                'membership_number',
            ],
            Tooltip: '',
            mouseEnter: null,
        }
    },
    validations: {
        create: {
            date: {required},
            year: {required},
            member_status_id: {required}
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
    mounted(){
        this.getStatus();
    },
    methods: {
        getStatus() {
            this.isLoader = true;

            adminApi
                .get(`/club-members/cm-status`)
                .then((res) => {
                    let l = res.data.data;
                    this.statuses = l;
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
         *  start get Data module && pagination
         */
        getData(page = 1) {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                let _filterSetting = [...this.filterSetting];
                let index = this.filterSetting.indexOf("cm_member_id");
                let index1 = this.filterSetting.indexOf("membership_number");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "member.first_name" : "member.first_name";
                }
                if (index1 > -1) {
                    _filterSetting[index1] =
                        this.$i18n.locale == "ar" ? "member.membership_number" : "member.membership_number";
                }
                let filter = '';
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }

                adminApi.get(`/club-members/transactions/member-transaction-paid-after-date?date=${this.create.date}&year=${this.create.year}&member_status_id=${this.create.member_status_id}&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
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
                    let index = this.filterSetting.indexOf("cm_member_id");
                    let index1 = this.filterSetting.indexOf("membership_number");
                    if (index > -1) {
                        _filterSetting[index] =
                            this.$i18n.locale == "ar" ? "member.first_name" : "member.first_name";
                    }
                    if (index1 > -1) {
                        _filterSetting[index1] =
                            this.$i18n.locale == "ar" ? "member.membership_number" : "member.membership_number";
                    }
                    let filter = "";
                    for (let i = 0; i < _filterSetting.length; ++i) {
                        filter += `columns[${i}]=${_filterSetting[i]}&`;
                    }

                    adminApi.get(`/club-members/transactions/member-transaction-paid-after-date?date=${this.create.date}&year=${this.create.year}&member_status_id=${this.create.member_status_id}&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
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
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
        },

        /**
         *  start  dynamicSortString
         */
        changeStatus(){
            adminApi.post(`/club-members/transactions/member-transaction-paid-after-date-update`,{
                date: this.create.date,
                year: this.create.year
            })
                .then((res) => {
                    this.installmentStatus = [];
                    this.installmentStatusPagination = {};
                    this.current_page = 1;
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
        sortString(value) {
            return dynamicSortString(value);
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
        dateStatus(date,status) {
            if (status == 'Unpaid')
            {
                let toDay = this.formatDate(new Date());
                let dateRow = this.formatDate(date);
                if (toDay >= dateRow)
                {
                    return 'due';
                }else if (toDay < dateRow)
                {
                    return 'NotDue';
                }else {
                    return 'completedPayment'
                }
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
                            <h4 class="header-title"> {{ $t('general.QueryAboutThePayersAfterASpecificDate') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">

                                <div class="d-inline-block" style="width: 22.2%;">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting"
                                                         value="cm_member_id"
                                                         class="mb-1">{{ $t('general.member') }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="membership_number" class="mb-1">
                                            {{ getCompanyKey("member_membership_number")  }}
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
<!--                                <b-button-->
<!--                                    variant="secondary"-->
<!--                                    v-if="installmentStatus.length > 0"-->
<!--                                    :disabled="isLoader"-->
<!--                                    class="btn-sm mx-1 font-weight-bold"-->
<!--                                    @click="changeStatus"-->
<!--                                >-->
<!--                                    {{ $t('general.changeStatus') }}-->
<!--                                    <i class="mdi mdi-square-edit-outline"></i>-->
<!--                                </b-button>-->
                                <!-- end create and printer -->
                            </div>
                            <div class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center  justify-content-end">
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
                                            <b-form-checkbox v-model="setting.cm_member_id" class="mb-1">{{ $t('general.member')}} </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.branch_id" class="mb-1">{{ $t('general.branch')}} </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.prefix" class="mb-1">{{ $t('general.serial_number') }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.date" class="mb-1">{{ $t('general.date')}}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.year_from" class="mb-1">{{ $t('general.year_from')}}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.year_to" class="mb-1">{{ $t('general.year_to')}}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.number_of_years" class="mb-1">{{ $t('general.number_of_years')}}</b-form-checkbox>

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
                                            @click.prevent="getData(1)"
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
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey("status") }}
                                            </label>
                                            <multiselect
                                                v-model="create.member_status_id"
                                                :options="statuses.map((type) => type.id)"
                                                :custom-label="
                                  (opt) => statuses.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar'
                                      ? statuses.find((x) => x.id == opt).name
                                      : statuses.find((x) => x.id == opt).name_e: null
                                "
                                            >
                                            </multiselect>
                                            <div
                                                v-if="
                                  $v.create.member_status_id.$error || errors.member_status_id
                                "
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.afterDate') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="yyyy-mm-dd"
                                                v-model="$v.create.date.$model"
                                                :class="{ 'is-invalid':  $v.create.date.$error || errors.date,
                                                    'is-valid':!$v.create.date.$invalid &&!errors.date,
                                                    }"
                                            >
                                            <template v-if="errors.date">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.date"
                                                              :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.forYear') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="yyyy"
                                                v-model="$v.create.year.$model"
                                                :class="{ 'is-invalid':  $v.create.year.$error || errors.year,
                                                    'is-valid':!$v.create.year.$invalid &&!errors.year,
                                                    }"
                                            >
                                            <template v-if="errors.year">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.year"
                                                              :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table" id="printCustom">

                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader"/>
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_membership_number") }}</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_membership_date") }}</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.member') }}</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.PaymentVoucherNumber') }}</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Historyofthebond') }}</span>
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
                                    <td>
                                        {{ data.membership_number}}
                                    </td>
                                    <td>
                                        {{ data.membership_date ?formatDate(data.membership_date): '-' }}
                                    </td>
                                    <td>
                                        <h5 class="m-0 font-weight-normal td5">
                                            {{ data.full_name }}
                                        </h5>
                                    </td>
                                    <td>
                                        {{ data.cmTransaction.length > 0?data.cmTransaction[0].document_no: '-' }}
                                    </td>
                                    <td>
                                        {{ data.cmTransaction.length > 0? formatDate(data.cmTransaction[0].date) : '-'  }}
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="10">{{ $t('general.notDataFound') }}</th>
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



