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
        title: "Payment Report",
        meta: [{name: "Payment Report", content: 'Payment Report'}],
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
      return permissionGuard(vm, "report to members", "all report to members");
    });

    },
    data() {
        return {
            per_page: 50,
            search: '',
            debounce: {},
            installmentStatusPagination: {},
            installmentStatus: [],
            financial_status: [],
            typs: [],
            status: [],
            isLoader: false,
            create: {
                financial_status_id: null,
                member_type_id: null,
                status_id: null,
                year_number: '',
                year: '',
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
                gender: true,
                Subscription_receipt_number: true,
                membership_number: true,
                first_name: true,
                second_name: true,
                third_name: true,
                last_name: true,
                family_name: true,
                financial_status_id: true,
                member_type_id: true,
                status_id: true,
                birth_date: true,
                national_id: true,
                nationality_number: true,
                home_phone: true,
                work_phone: true,
                home_address: true,
                work_address: true,
                job: true,
                degree: true,
                membership_date: true,

            },
            filterSetting: [
                "membership_number",
                "first_name",
                "second_name",
                "third_name",
                "last_name",
                "family_name",
                "birth_date",
                "national_id",
                "home_phone",
                "work_phone",
                "home_address",
                "work_address",
                "job",
                "degree",
            ],
            Tooltip: '',
            mouseEnter: null,
        }
    },
    validations: {
        create: {
            year: {required: requiredIf(function (model) {
                    return this.create.year_number;
                }),
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
                let filter = "";
                for (let i = 0; i < this.filterSetting.length; ++i) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }

                adminApi.get(`/club-members/members/report-to-members?financial_status_id=${this.create.financial_status_id??''}&member_type_id=${this.create.member_type_id??''}&status_id=${this.create.status_id??''}&year_number=${this.create.year_number??''}&year=${this.create.year??''}&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
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
                    let filter = "";
                    for (let i = 0; i < this.filterSetting.length; ++i) {
                        filter += `columns[${i}]=${this.filterSetting[i]}&`;
                    }
                    adminApi.get(`/club-members/members/report-to-members?financial_status_id=${this.create.financial_status_id??''}&member_type_id=${this.create.member_type_id??''}&status_id=${this.create.status_id??''}&year_number=${this.create.year_number??''}&year=${this.create.year??''}&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
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
         *  get financial status
         */
        async getFinancialStatus() {
            this.isLoader = true;
            await adminApi
                .get(`/club-members/financial-status`)
                .then((res) => {
                    let l = res.data.data;
                    this.financial_status = l;
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
         *  get type
         */
        async getType() {
            this.isLoader = true;
           await adminApi.get(`/club-members/members-types`)
                .then((res) => {
                    let l = res.data.data;
                    this.typs = l;
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
         *  get status
         */
        async getStatus() {
            this.isLoader = true;
           await adminApi.get(`/club-members/member-status`)
                .then((res) => {
                    let l = res.data.data;
                    this.status = l;
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
            await this.getFinancialStatus();
            await this.getType();
            await this.getStatus();
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
        choseAllFinancialStatus(){
           this.create.financial_status_id = this.financial_status.map((obj) => obj.id);
        },
        choseAllTypes(){
           this.create.member_type_id = this.typs.map((obj) => obj.id);
        },
        choseAllStatus(){
           this.create.status_id = this.status.map((obj) => obj.id);
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
                            <h4 class="header-title"> {{ $t('general.ReportToMembers') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">

                                <div class="d-inline-block" style="width: 22.2%;">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting" value="membership_number" class="mb-1">
                                            {{ getCompanyKey("member_membership_number") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="first_name" class="mb-1">
                                            {{ getCompanyKey("member_first_name") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="second_name" class="mb-1">
                                            {{getCompanyKey("member_second_name")}}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="third_name" class="mb-1">
                                            {{ getCompanyKey("member_third_name") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="last_name" class="mb-1" >
                                            {{ getCompanyKey("member_last_name") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="family_name" class="mb-1">
                                            {{getCompanyKey("member_family_name")}}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="status_id" class="mb-1">
                                            {{ getCompanyKey("status") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="national_id" class="mb-1">
                                            {{ getCompanyKey("member_national_id") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="home_phone" class="mb-1">
                                            {{ getCompanyKey("member_home_phone") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="work_phone" class="mb-1">
                                            {{ getCompanyKey("member_work_phone") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="home_address" class="mb-1">
                                            {{ getCompanyKey("member_home_address") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="work_address" class="mb-1">
                                            {{ getCompanyKey("member_work_address") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="job" class="mb-1">
                                            {{ getCompanyKey("member_job") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="degree" class="mb-1">
                                            {{ getCompanyKey("member_degree") }}
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
                                        <b-button  class="mx-1 custom-btn-background">
                                            {{ $t('general.filter') }}
                                            <i class="fas fa-filter"></i>
                                        </b-button>
                                        <b-button  class="mx-1 custom-btn-background">
                                            {{ $t('general.group') }}
                                            <i class="fe-menu"></i>
                                        </b-button>
                                        <!-- Basic dropdown -->
                                        <b-dropdown variant="primary"
                                                    :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                                                    ref="dropdown" class="dropdown-custom-ali">
                                            <b-form-checkbox v-model="setting.membership_number" class="mb-1">{{ getCompanyKey("member_membership_number") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.first_name" class="mb-1">{{ getCompanyKey("member_first_name") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.second_name" class="mb-1">{{ getCompanyKey("member_second_name") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.third_name" class="mb-1">{{ getCompanyKey("member_third_name") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.last_name" class="mb-1">{{ getCompanyKey("member_last_name") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.family_name" class="mb-1">{{ getCompanyKey("member_family_name") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.birth_date" class="mb-1">{{ getCompanyKey("member_birth_date") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.gender" class="mb-1">{{ getCompanyKey("member_type") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.membership_date" class="mb-1">{{ getCompanyKey("member_membership_date") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.financial_status_id" class="mb-1">{{ $t("general.financial_status") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.member_type_id" class="mb-1">{{ getCompanyKey("member_type") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.status_id" class="mb-1">{{ $t("general.status") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.national_id" class="mb-1">{{ getCompanyKey("member_national_id") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.home_phone" class="mb-1">{{ getCompanyKey("member_home_phone") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.work_phone" class="mb-1">{{ getCompanyKey("member_work_phone") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.home_address" class="mb-1">{{ getCompanyKey("member_home_address") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.work_address" class="mb-1">{{ getCompanyKey("member_work_address") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.job" class="mb-1">{{ getCompanyKey("member_job") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.degree" class="mb-1">{{ getCompanyKey("member_degree") }}</b-form-checkbox>
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
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ $t("general.financial_status") }}
                                            </label>
                                            <b-button variant="primary" class="btn-sm mx-1 font-weight-bold mb-1" @click="choseAllFinancialStatus">
                                                {{ $t('general.choseAll') }}
                                            </b-button>
                                            <multiselect
                                                :multiple="true"
                                                v-model="create.financial_status_id"
                                                :options="financial_status.map((type) => type.id)"
                                                :custom-label="
                                                     (opt) =>
                                                        $i18n.locale == 'ar'
                                                          ? financial_status.find((x) => x.id == opt).name
                                                          : financial_status.find((x) => x.id == opt).name_e
                                                    "
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey("member_type") }}
                                            </label>
                                            <b-button variant="primary" class="btn-sm mx-1 font-weight-bold mb-1" @click="choseAllTypes">
                                                {{ $t('general.choseAll') }}
                                            </b-button>
                                            <multiselect
                                                :multiple="true"
                                                v-model="create.member_type_id"
                                                :options="typs.map((type) => type.id)"
                                                :custom-label="
                                                     (opt) =>
                                                        $i18n.locale == 'ar'
                                                          ? typs.find((x) => x.id == opt).name
                                                          : typs.find((x) => x.id == opt).name_e
                                                    "
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ $t("general.status") }}
                                            </label>
                                            <b-button variant="primary" class="btn-sm mx-1 font-weight-bold mb-1" @click="choseAllStatus">
                                                {{ $t('general.choseAll') }}
                                            </b-button>
                                            <multiselect
                                                :multiple="true"
                                                v-model="create.status_id"
                                                :options="status.map((type) => type.id)"
                                                :custom-label="
                                                     (opt) =>
                                                        $i18n.locale == 'ar'
                                                          ? status.find((x) => x.id == opt).name
                                                          : status.find((x) => x.id == opt).name_e
                                                    "
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ $t("general.NumberOfSubscriptionYears") }}
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                v-model="create.year_number"
                                            />
                                        </div>
                                    </div>
                                    <div v-if="create.year_number" class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.whichYearHeStarted') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="year"
                                                v-model="$v.create.year.$model"
                                                format="YYYY"
                                                valueType="format"
                                                :confirm="false"
                                                :class="{ 'is-invalid': $v.create.year.$error || errors.year,
                                                    'is-valid': !$v.create.year.$invalid && !errors.year,
                                                }"
                                            ></date-picker>
                                            <div v-if="$v.create.year.$error || errors.year" class="text-danger">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>

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
                                    <th v-if="setting.membership_number">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_membership_number") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="installmentStatus.sort(sortString('membership_number'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="installmentStatus.sort(sortString('-membership_number'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.first_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_first_name") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="installmentStatus.sort(sortString('name'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="installmentStatus.sort(sortString('-name'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.second_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_second_name") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="installmentStatus.sort(sortString('name_e'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="installmentStatus.sort(sortString('-name_e'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.third_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_third_name") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="installmentStatus.sort(sortString('name_e'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="installmentStatus.sort(sortString('-name_e'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.last_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_last_name") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="installmentStatus.sort(sortString('name_e'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="installmentStatus.sort(sortString('-name_e'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.family_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_family_name") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="installmentStatus.sort(sortString('name_e'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="installmentStatus.sort(sortString('-name_e'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.birth_date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_birth_date") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="installmentStatus.sort(sortString('birth_date'))"
                                                ></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="installmentStatus.sort(sortString('-birth_date'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.gender">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_gender") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.membership_date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_membership_date")}}</span>
                                            <div class="arrow-sort">
                                                <i  class="fas fa-arrow-up"
                                                    @click="installmentStatus.sort(sortString('membership_date'))"
                                                ></i>
                                                <i class="fas fa-arrow-down"
                                                    @click="installmentStatus.sort(sortString('-membership_date'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.financial_status_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.financial_status") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.member_type_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_type") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.status_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.status") }}</span>
                                        </div>
                                    </th>

                                    <th v-if="setting.national_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_national_id") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="installmentStatus.sort(sortString('national_id'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="installmentStatus.sort(sortString('-national_id'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.home_phone">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_home_phone") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="installmentStatus.sort(sortString('home_phone'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="installmentStatus.sort(sortString('-home_phone'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.work_phone">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_work_phone") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="installmentStatus.sort(sortString('work_phone'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="installmentStatus.sort(sortString('-work_phone'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.home_address">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_home_address") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="installmentStatus.sort(sortString('home_address'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="installmentStatus.sort(sortString('-home_address'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.work_address">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_work_address") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="installmentStatus.sort(sortString('work_address'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="installmentStatus.sort(sortString('-work_address'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.job">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_job") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="installmentStatus.sort(sortString('job'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="installmentStatus.sort(sortString('-job'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.degree">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_degree") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="installmentStatus.sort(sortString('degree'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="installmentStatus.sort(sortString('-degree'))"
                                                ></i>
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

                                    <td v-if="setting.membership_number">
                                        {{ data.membership_number }}
                                    </td>
                                    <td v-if="setting.first_name">
                                        {{ data.first_name }}
                                    </td>
                                    <td v-if="setting.second_name">
                                        {{ data.second_name }}
                                    </td>
                                    <td v-if="setting.third_name">
                                        {{ data.third_name }}
                                    </td>
                                    <td v-if="setting.last_name">
                                        {{ data.last_name }}
                                    </td>
                                    <td v-if="setting.family_name">
                                        {{ data.family_name }}
                                    </td>
                                    <td v-if="setting.birth_date">
                                        {{ data.birth_date }}
                                    </td>
                                    <td v-if="setting.gender">
                                        {{data.gender ? parseInt(data.gender) == 1 ? $t("general.male") : $t("general.female") : '---'}}
                                    </td>
                                    <td v-if="setting.membership_date">
                                        {{ formatDate(data.membership_date) }}
                                    </td>
                                    <td v-if="setting.financial_status_id">
                                        {{data.financial_status ? $i18n.locale == "ar"? data.financial_status.name: data.financial_status.name_e: "---"}}
                                    </td>
                                    <td v-if="setting.member_type_id">
                                        {{data.membersType ? $i18n.locale == "ar"? data.membersType.name: data.membersType.name_e: "---"}}
                                    </td>
                                    <td v-if="setting.status_id">
                                        {{data.status ? $i18n.locale == "ar"? data.status.name: data.status.name_e: "---"}}
                                    </td>
                                    <td v-if="setting.national_id">
                                        {{ data.national_id }}
                                    </td>
                                    <td v-if="setting.home_phone">
                                        {{ data.home_phone }}
                                    </td>
                                    <td v-if="setting.work_phone">
                                        {{ data.work_phone }}
                                    </td>
                                    <td v-if="setting.home_address">
                                        {{ data.home_address }}
                                    </td>
                                    <td v-if="setting.work_address">
                                        {{ data.work_address }}
                                    </td>
                                    <td v-if="setting.job">
                                        {{ data.job }}
                                    </td>
                                    <td v-if="setting.degree">
                                        {{ data.degree }}
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="18">{{ $t('general.notDataFound') }}</th>
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



