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
        title: "Duplicate bonds Report",
        meta: [{name: "Duplicate bonds", content: 'Duplicate bonds Report'}],
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
            return permissionGuard(vm, "Duplicate Bonds", "all paid member");
        });
    },
    data() {
        return {
            per_page: 100,
            search: '',
            showMembers: [],
            debounce: {},
            installmentStatusPagination: {},
            installmentStatus: [],
            customers: [],
            payment_types: [],
            isLoader: false,
            create: {
                start_date: '',
                end_date: '',
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
        resetModalShow(document){

            let dateStartArray = this.create.start_date.split("-");
            let dateEndArray = this.create.end_date.split("-");

            let data = '?start_date='+dateStartArray[2] + "-" + dateStartArray[1] + "-" + dateStartArray[0];
            data += '&end_date='+dateEndArray[2] + "-" + dateEndArray[1] + "-" + dateEndArray[0];
            data += '&document_no='+document;

            adminApi.get(`/club-members/transactions/get-member-is-document${data}&per_page=${this.per_page}`)
                .then((res) => {
                    let l = res.data;
                    this.showMembers = l.data.data

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
        resetModalShowHidden(document){
            this.showMembers = [];
            this.$bvModal.hide(`show-${document}`);
        },
        getData(page = 1) {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                let dateStartArray = this.create.start_date.split("-");
                let dateEndArray = this.create.end_date.split("-");

                let data = '?start_date='+dateStartArray[2] + "-" + dateStartArray[1] + "-" + dateStartArray[0];
                data += '&end_date='+dateEndArray[2] + "-" + dateEndArray[1] + "-" + dateEndArray[0];

                adminApi.get(`/club-members/transactions/member-transaction-defore-after-date${data}&per_page=${this.per_page}`)
                    .then((res) => {
                        let l = res.data;
                        this.installmentStatus = l.data.data;
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

                    let data = '?start_date='+this.create.start_date;
                    data += '&end_date='+this.create.end_date;

                    adminApi.get(`/club-members/transactions/member-transaction-defore-after-date${data}&per_page=${this.per_page}`)
                        .then((res) => {
                            let l = res.data;
                            this.installmentStatus = l.data.data;
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
            adminApi.post(`/club-members/transactions/check-date-member-transaction-update`,{
                date: this.create.date,
                year: this.create.date
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
                            <h4 class="header-title"> {{ $t('general.DuplicatbondsReport') }}</h4>
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
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.startDate') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="create.start_date"
                                                placeholder="DD-MM-YYYY"
                                                format="DD-MM-YYYY"
                                                valueType="format"
                                                :confirm="false"
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
                                                {{ $t('general.endDate') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="create.end_date"
                                                placeholder="DD-MM-YYYY"
                                                format="DD-MM-YYYY"
                                                valueType="format"
                                                :confirm="false"
                                            ></date-picker>
                                            <template v-if="errors.end_date">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.end_date"
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
                                            <span>{{ $t('general.DocumentNumber')  }}</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.duplicate')  }}</span>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody v-if="installmentStatus.length > 0">
                                <tr
                                    v-for="(data,index) in installmentStatus"
                                    :key="data.document_no"
                                    @dblclick.prevent="$bvModal.show(`show-${data.document_no}`)"
                                    class="body-tr-custom"
                                >
                                    <td>
                                        {{ data.document_no }}
                                    </td>
                                    <td>
                                        {{ data.total }}
                                    </td>

                                    <!--  show   -->
                                    <b-modal
                                        :id="`show-${data.document_no}`"
                                        :title="$t('general.Searchdd')"
                                        title-class="font-18"
                                        body-class="p-4"
                                        size="lg"
                                        :hide-footer="true"
                                        @show="resetModalShow(data.document_no)"
                                        @hidden="resetModalShowHidden(data.document_no)"
                                    >
                                        <form>
                                            <div class="mb-3 d-flex justify-content-end">
                                                <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`show-${data.document_no}`)">
                                                    {{ $t('general.Cancel') }}
                                                </b-button>
                                            </div>
                                            <div class="row">
                                                <div class="table-responsive mb-3 custom-table-theme position-relative">
                                                    <!--       start loader       -->
                                                    <loader size="large" v-if="isLoader"/>
                                                    <!--       end loader       -->
                                                    <table class="table table-borderless table-hover table-centered m-0">
                                                    <thead>
                                                    <tr>
                                                        <th>
                                                            <div class="d-flex justify-content-center">
                                                                <span>{{ $t('general.date')  }}</span>
                                                            </div>
                                                        </th>
                                                        <th>
                                                            <div class="d-flex justify-content-center">
                                                                <span>{{ getCompanyKey("member_membership_number") }}</span>
                                                            </div>
                                                        </th>
                                                        <th>
                                                            <div class="d-flex justify-content-center">
                                                                <span>{{ $t('general.name')  }}</span>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody v-if="showMembers.length > 0">
                                                        <tr v-for="(data1,index) in showMembers">
                                                            <td>
                                                                {{ data1.date }}
                                                            </td>
                                                            <td>
                                                                {{ data1.member_number }}
                                                            </td>
                                                            <td>
                                                                {{ data1.member.full_name }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                </div>
                                            </div>
                                        </form>
                                    </b-modal>
                                    <!--  /show   -->

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



