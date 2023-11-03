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
        title: "Change of member rights",
        meta: [{name: "description", content: 'Change of member rights'}],
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
            return permissionGuard(vm, "Change of member rights", "all paid member");
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
                membership_numbers: '',
                year: '',
                financial_status_id: null
            },
            edit: {
                member_type_id: null,
                status_id: null
            },
            financialStatuses: [],
            statuses: [],
            errors: {},
            is_disabled: false,
            isCheckAll: false,
            memberTypes: [],
            checkAll: [],
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
            financial_status_id: {required},
            membership_numbers: {required},
            year: {required}
        },
        edit: {
            member_type_id: {required},
            status_id: {required}
        }
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
        this.getFinancialStatus();
        this.getStatus();
        this.getMemberType();
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

                adminApi.get(`/club-members/transactions/get-member-voting?financial_status_id=${this.create.financial_status_id}&membership_numbers=${this.create.membership_numbers}&year=${this.create.year}&page=${page}&per_page=${this.per_page}`)
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

                    adminApi.get(`/club-members/transactions/get-member-voting?financial_status_id=${this.create.financial_status_id}&membership_numbers=${this.create.membership_numbers}&year=${this.create.year}&page=${page}&per_page=${this.per_page}`)
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
        getFinancialStatus(page = 1) {

            adminApi
                .get(
                    `/club-members/financial-status`
                )
                .then((res) => {
                    let l = res.data;
                    this.financialStatuses = l.data;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })

        },
        getStatus() {

            adminApi
                .get(
                    `/club-members/cm-status?member_type_id=1`
                )
                .then((res) => {
                    let l = res.data;
                    this.statuses = l.data;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })

        },
        getMemberType() {

            adminApi
                .get(
                    `/club-members/members-types?normal_member=1`
                )
                .then((res) => {
                    let l = res.data;
                    this.memberTypes = l.data;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })

        },
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
            this.$v.edit.$touch();
            if (this.$v.edit.$invalid) {
                return;
            } else {
                adminApi.put(`/club-members/transactions/update-member-voting`, {
                    membership_numbers: this.create.membership_numbers,
                    year: this.create.year,
                    financial_status_id: this.create.financial_status_id,
                    status_id: this.edit.status_id,
                    member_type_id: this.edit.member_type_id,
                })
                    .then((res) => {
                        this.installmentStatus = [];
                        this.installmentStatusPagination = {};
                        this.current_page = 1;
                        this.create = {
                                membership_numbers: '',
                                year: '',
                                financial_status_id: null
                        };
                        this.edit = {
                                member_type_id: null,
                                status_id: null
                        };
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
            }
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
                            <h4 class="header-title"> {{ $t('general.Changeofmemberrights') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500"></div>
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
                                <b-button
                                    variant="secondary"
                                    v-if="installmentStatus.length > 0"
                                    :disabled="isLoader"
                                    class="btn-sm mx-1 font-weight-bold"
                                    v-b-modal.changeStatus
                                >
                                    {{ $t('general.changeStatus') }}
                                    <i class="mdi mdi-square-edit-outline"></i>
                                </b-button>
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label">
                                            {{ getCompanyKey("financial_status") }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            v-model="$v.create.financial_status_id.$model"
                                            :options="financialStatuses.map((type) => type.id)"
                                            :custom-label="
                                                (opt) =>
                                                  financialStatuses.find((x) => x.id == opt)
                                                  ? $i18n.locale == 'ar'
                                                    ? financialStatuses.find((x) => x.id == opt).name
                                                    : financialStatuses.find((x) => x.id == opt).name_e
                                                  : null
                                            "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.create.financial_status_id.$error || errors.financial_status_id"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.financial_status_id">
                                            <ErrorMessage v-for="(errorMessage,index) in errors.financial_status_id"
                                                          :key="index">
                                                {{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.yearsOfMembership') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="$v.create.membership_numbers.$model"
                                                :class="{ 'is-invalid':  $v.create.membership_numbers.$error || errors.membership_numbers,
                                                    'is-valid':!$v.create.membership_numbers.$invalid &&!errors.membership_numbers,
                                                    }"
                                            >
                                            <template v-if="errors.membership_numbers">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.membership_numbers"
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

                        <!--  chang eStatus   -->
                        <b-modal
                            id="changeStatus"
                            :title="$t('general.Search')"
                            title-class="font-18"
                            body-class="p-4"
                            size="lg"
                            :hide-footer="true"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <template v-if="!is_disabled">
                                        <b-button
                                            variant="success"
                                            type="button" class="mx-1"
                                            v-if="!isLoader"
                                            @click.prevent="changeStatus"
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
                                        <label class="control-label">
                                            {{ getCompanyKey("member_type_id") }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            v-model="$v.edit.member_type_id.$model"
                                            :options="memberTypes.map((type) => type.id)"
                                            :custom-label="
                                                (opt) =>
                                                  memberTypes.find((x) => x.id == opt)
                                                  ? $i18n.locale == 'ar'
                                                    ? memberTypes.find((x) => x.id == opt).name
                                                    : memberTypes.find((x) => x.id == opt).name_e
                                                  : null
                                            "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.edit.member_type_id.$error || errors.member_type_id"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.member_type_id">
                                            <ErrorMessage v-for="(errorMessage,index) in errors.member_type_id"
                                                          :key="index">
                                                {{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label">
                                            {{ getCompanyKey("status") }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            v-model="$v.edit.status_id.$model"
                                            :options="statuses.map((type) => type.id)"
                                            :custom-label="
                                                (opt) =>
                                                  statuses.find((x) => x.id == opt)
                                                  ? $i18n.locale == 'ar'
                                                    ? statuses.find((x) => x.id == opt).name
                                                    : statuses.find((x) => x.id == opt).name_e
                                                  : null
                                            "
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.edit.status_id.$error || errors.status_id"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.status_id">
                                            <ErrorMessage v-for="(errorMessage,index) in errors.status_id"
                                                          :key="index">
                                                {{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /change Status   -->

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

                                </tr>
                                </thead>
                                <tbody v-if="installmentStatus.length > 0">
                                <tr
                                    v-for="(data,index) in installmentStatus"
                                    :key="data.id"
                                    class="body-tr-custom"
                                >
                                    <td>
                                        {{ data.membership_number }}
                                    </td>
                                    <td>
                                        {{ formatDate(data.membership_date)  }}
                                    </td>
                                    <td>
                                        <h5 class="m-0 font-weight-normal td5">
                                            {{data.first_name +' '+ data.second_name +' '+ data.third_name +' '+ data.last_name }}
                                        </h5>
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



