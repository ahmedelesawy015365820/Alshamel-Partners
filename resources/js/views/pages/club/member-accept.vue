<script>
import DatePicker from "vue2-datepicker";
import Status from "../../../components/create/general/status.vue";
import Sponsor from "../../../components/create/club/sponsor.vue";
import FinancialStatus from "../../../components/create/club/financialStatus.vue";
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import { required, minLength, maxLength, integer, requiredIf } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import permissionGuard from "../../../helper/permission";

import alphaArabic from "../../../helper/alphaArabic";
import alphaEnglish from "../../../helper/alphaEnglish";
import { dynamicSortString, dynamicSortNumber } from "../../../helper/tableSort";
import translation from "../../../helper/mixin/translation-mixin";
import Multiselect from "vue-multiselect";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Members",
        meta: [{ name: "description", content: "Members" }],
    },
    mixins: [translation],
    components: {
        FinancialStatus,
        Sponsor,
        Status,
        DatePicker,
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
    },
    data() {
        return {
            fields: [],
            per_page: 50,
            search: "",
            debounce: {},
            enabled3: true,
            membersPagination: {},
            members: [],
            isLoader: false,
            Tooltip: "",
            mouseEnter: "",
            statuses: [],
            sponsors: [],
            financialStatuses: [],
            create: {
                id: null,
                status_id: null,
                sponsor_id: null,
                financial_status_id: null,
            },
            company_id: null,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            branches: [],
            current_page: 1,
            setting: {
                gender: true,
                first_name: true,
                second_name: true,
                third_name: true,
                last_name: true,
                family_name: true,
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
                sponsor_id: true,
                membership_number: true
            },
            is_disabled: false,
            filterSetting: [
                "first_name",
                "second_name",
                "third_name",
                "last_name",
                "family_name",
                "status_id",
                "birth_date",
                "national_id",
                "nationality_number",
                "home_phone",
                "work_phone",
                "home_address",
                "work_address",
                "job",
                "degree",
                "sponsor_id",
            ],
            printLoading: true,
            printObj: {
                id: "printData",
            }
        };
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Accepted Member", "all acceptedMembers club");
    });

    },
    validations: {
        create: {
            status_id: { required },
            sponsor_id: {required},
            financial_status_id: { required },
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
                this.members.forEach((el) => {
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
        this.getData();
    },
    methods: {
        isPermission(item) {
            if (this.$store.state.auth.type == 'user'){
                return this.$store.state.auth.permissions.includes(item)
            }
            return true;
        },
        showStatusModal() {
            if (this.create.status_id == 0) {
                this.$bvModal.show("status-create");
                this.create.status_id = null;
            }
        },
        showStatusModalEdit() {
            if (this.edit.status_id == 0) {
                this.$bvModal.show("status-create");
                this.edit.status_id = null;
            }
        },
        showSponsorModal() {
            if (this.create.sponsor_id == 0) {
                this.$bvModal.show("create-sponsor");
                this.create.sponsor_id = null;
            }
        },
        showSponsorModalEdit() {
            if (this.edit.sponsor_id == 0) {
                this.$bvModal.show("create-sponsor");
                this.edit.sponsor_id = null;
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
                    .get(`/club-members/members/logs/${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.forEach((e) => {
                            this.Tooltip += `Created By: ${e.causer_type}; Event: ${e.event
                            }; Description: ${e.description} ;Created At: ${this.formatDate(
                                e.created_at
                            )} \n`;
                        });
                    })
                    .catch((err) => {
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                        });
                    });
            } else {
            }
        },
        /**
         *  start get Data countrie && pagination
         */
        getData(page = 1) {
            this.isLoader = true;

            let filter = "";
            for (let i = 0; i < this.filterSetting.length; ++i) {
                filter += `columns[${i}]=${this.filterSetting[i]}&`;
            }
            adminApi
                .get(
                    `/club-members/members/Acceptance?page=${page}&per_page=${this.per_page}&company_id=${this.company_id}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.members = l.data;
                    this.membersPagination = l.pagination;
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
        getDataCurrentPage(page = 1) {
            if (
                this.current_page <= this.membersPagination.last_page &&
                this.current_page != this.membersPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let filter = "";
                for (let i = 0; i < this.filterSetting.length; ++i) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `/club-members/members/Acceptance?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}&company_id=${this.company_id}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.members = l.data;
                        this.membersPagination = l.pagination;
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
         *  end get Data countrie && pagination
         */

        /**
         *  create countrie
         */
        AddSubmit() {
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`/club-members/members/update-accepted-members/${this.create.id}`, this.create)
                    .then((res) => {
                        this.getData();
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
         *  edit countrie
         */
        /**
         *   show Modal (edit)
         */
        async resetModalEdit(id) {
            let member = this.members.find((e) => id == e.id);
            await this.getStatus();
            await this.getSponsors();
            await this.getFinancialStatus();
            this.create.id = id;
            this.create.financial_status_id = member.financial_status_id ?? null;
            this.create.status_id = member.status_id ?? null;
            this.create.sponsor_id = member.sponsor_id ?? null;
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.errors = {};
            this.create = {
                id: null,
                status_id: null,
                sponsor_id: null,
                financial_status_id: null,
            };
        },
        /*
         *  start  dynamicSortString
         */
        sortString(value) {
            return dynamicSortString(value);
        },
        SortNumber(value) {
            return dynamicSortNumber(value);
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

        async getStatus() {
            this.isLoader = true;

            await adminApi
                .get(`/statuses`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Status')){
                        l.unshift({ id: 0, name: "اضف حاله", name_e: "Add Status" });
                    }
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
        async getSponsors() {
            this.isLoader = true;
            await adminApi
                .get(`/club-members/sponsers`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create sponsor club')){
                        l.unshift({ id: 0, name: "اضف راعي", name_e: "Add sponsor" });
                    }
                    this.sponsors = l;
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
        async getFinancialStatus() {
            this.isLoader = true;
            await adminApi
                .get(`/club-members/financial-status`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create financialStatus club')){
                        l.unshift({ id: 0, name: "اضف حالة مالية", name_e: "Add financial status" });
                    }
                    this.financialStatuses = l;
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
        showFinancialStatusModal() {
            if (this.create.financial_status_id == 0) {
                this.$bvModal.show("create-financial-status");
                this.create.financial_status_id = null;
            }
        },
        showFinancialStatusModalEdit() {
            if (this.edit.financial_status_id == 0) {
                this.$bvModal.show("create-financial-status");
                this.edit.financial_status_id = null;
            }
        },

        /**
         *   Export Excel
         */
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
                if (dl) {
                    XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' });
                } else {
                    XLSX.writeFile(wb, fn || (('Stores' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },
        arabicValue(txt) {
            this.create.name = arabicValue(txt);
            this.edit.name = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.name_e = englishValue(txt);
            this.edit.name_e = englishValue(txt);
        }
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <Status :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getStatus" />
        <Sponsor :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getSponsors" />
        <FinancialStatus :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getFinancialStatus" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ $t("general.memberAcceptTable") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting" value="first_name" class="mb-1">{{
                                                getCompanyKey("member_first_name") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="second_name" class="mb-1">{{
                                                getCompanyKey("member_second_name") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="third_name" class="mb-1">{{
                                                getCompanyKey("member_third_name") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="last_name" class="mb-1">{{
                                                getCompanyKey("member_last_name") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="family_name" class="mb-1">{{
                                                getCompanyKey("member_family_name") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="status_id" class="mb-1">{{
                                                getCompanyKey("status") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="national_id" class="mb-1">{{
                                                getCompanyKey("member_national_id") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="nationality_number" class="mb-1">{{
                                                getCompanyKey("member_nationality_number") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="home_phone" class="mb-1">{{
                                                getCompanyKey("member_home_phone") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="work_phone" class="mb-1">{{
                                                getCompanyKey("member_work_phone") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="home_address" class="mb-1">{{
                                                getCompanyKey("member_home_address") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="work_address" class="mb-1">{{
                                                getCompanyKey("member_work_address") }}
                                        </b-form-checkbox>
                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                </div>

                                <div class="d-inline-block position-relative" style="width: 77%">
                                    <span :class="[
                                        'search-custom position-absolute',
                                        $i18n.locale == 'ar' ? 'search-custom-ar' : '',
                                    ]">
                                        <i class="fe-search"></i>
                                    </span>
                                    <input class="form-control" style="display: block; width: 93%; padding-top: 3px"
                                           type="text" v-model.trim="search" :placeholder="`${$t('general.Search')}...`" />
                                </div>
                            </div>
                        </div>
                        <!-- end search -->

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <!-- start create and printer -->
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printData'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <button class="custom-btn-dowonload" @click="$bvModal.show(`modal-create-${checkAll[0]}`)"
                                            v-if="checkAll.length == 1 && isPermission('update acceptedMembers club')">
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                </div>
                                <!-- end create and printer -->
                            </div>
                            <div class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end">
                                <div class="d-fex">
                                    <!-- start filter and setting -->
                                    <div class="d-inline-block">
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
                                                    :html="`${$t('general.setting')} <i class='fe-settings'></i>`" ref="dropdown"
                                                    class="dropdown-custom-ali">
                                            <b-form-checkbox v-model="setting.first_name" class="mb-1">{{
                                                    getCompanyKey("member_first_name") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.second_name" class="mb-1">{{
                                                    getCompanyKey("member_second_name") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.third_name" class="mb-1">{{
                                                    getCompanyKey("member_third_name") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.last_name" class="mb-1">{{
                                                    getCompanyKey("member_last_name") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.family_name" class="mb-1">{{
                                                    getCompanyKey("member_family_name") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.birth_date" class="mb-1">{{
                                                    getCompanyKey("member_birth_date") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.membership_number" class="mb-1">{{
                                                    getCompanyKey("member_membership_number") }}
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
                                        <div class="d-inline-block" style="font-size: 13px">
                                            {{ membersPagination.from }}-{{ membersPagination.to }} /
                                            {{ membersPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a href="javascript:void(0)" :style="{
                                                'pointer-events':
                                                    membersPagination.current_page == 1 ? 'none' : '',
                                            }" @click.prevent="getData(membersPagination.current_page - 1)">
                                                <span>&lt;</span>
                                            </a>
                                            <input type="text" @keyup.enter="getDataCurrentPage()" v-model="current_page"
                                                   class="pagination-current-page" />
                                            <a href="javascript:void(0)" :style="{
                                                'pointer-events':
                                                    membersPagination.last_page == membersPagination.current_page
                                                        ? 'none'
                                                        : '',
                                            }" @click.prevent="getData(membersPagination.current_page + 1)">
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                                   id="printData">
                                <thead>
                                <tr>
                                    <th v-if="enabled3" class="do-not-print" scope="col" style="width: 0">
                                        <div class="form-check custom-control">
                                            <input class="form-check-input" type="checkbox" v-model="isCheckAll"
                                                   style="width: 17px; height: 17px" />
                                        </div>
                                    </th>
                                    <th v-if="setting.first_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_first_name") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="members.sort(sortString('name'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="members.sort(sortString('-name'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.second_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_second_name") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="members.sort(sortString('name_e'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="members.sort(sortString('-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.third_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_third_name") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="members.sort(sortString('name_e'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="members.sort(sortString('-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.last_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_last_name") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="members.sort(sortString('name_e'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="members.sort(sortString('-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.family_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_family_name") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="members.sort(sortString('name_e'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="members.sort(sortString('-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.birth_date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_birth_date") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="members.sort(sortString('birth_date'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="members.sort(sortString('-birth_date'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.membership_number">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_membership_number") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="members.length > 0">
                                <tr
                                    v-for="(data, index) in members" :key="data.id"
                                    @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="$bvModal.show(`modal-create-${data.id}`)"
                                    class="body-tr-custom"
                                >
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control" style="min-height: 1.9em">
                                            <input style="width: 17px; height: 17px" class="form-check-input"
                                                   type="checkbox" :value="data.id" v-model="checkAll" />
                                        </div>
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
                                    <td v-if="setting.membership_number">
                                        {{ data.membership_number }}
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <button @mouseover="log(data.id)" @mousemove="log(data.id)" type="button"
                                                class="btn" data-toggle="tooltip"
                                                :data-placement="$i18n.locale == 'en' ? 'left' : 'right'" :title="Tooltip">
                                            <i class="fe-info" style="font-size: 22px"></i>
                                        </button>
                                    </td>

                                    <!--  create   -->
                                    <b-modal :id="`modal-create-${data.id}`" :title="getCompanyKey('member_create_form')" title-class="font-18"
                                              body-class="p-4 " :hide-footer="true" @show="resetModalEdit(data.id)"
                                             @hidden="resetModalHiddenEdit">
                                        <form>
                                            <div class="mb-3 d-flex justify-content-end">
                                                <b-button variant="success" type="submit" class="mx-1" v-if="!isLoader"
                                                          @click.prevent="AddSubmit()">
                                                    {{ $t("general.Edit") }}
                                                </b-button>

                                                <b-button variant="success" class="mx-1" disabled v-else>
                                                    <b-spinner small></b-spinner>
                                                    <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                </b-button>
                                                <b-button @click.prevent="$bvModal.hide(`modal-create-${data.id}`)" variant="danger" type="button">
                                                    {{ $t("general.Cancel") }}
                                                </b-button>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("status") }}

                                                        </label>
                                                        <multiselect @input="showStatusModal" v-model="create.status_id"
                                                                     :options="statuses.map((type) => type.id)" :custom-label="(opt) => $i18n.locale == 'ar' ? statuses.find((x) => x.id == opt).name
                                                    : statuses.find((x) => x.id == opt).name_e">
                                                        </multiselect>
                                                        <div v-if="$v.create.status_id.$error || errors.status_id" class="text-danger">
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>
                                                        <template v-if="errors.status_id">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.status_id"
                                                                          :key="index">{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("financial_status") }}

                                                        </label>
                                                        <multiselect @input="showFinancialStatusModal" v-model="create.financial_status_id"
                                                                     :options="financialStatuses.map((type) => type.id)" :custom-label="(opt) => $i18n.locale == 'ar' ? financialStatuses.find((x) => x.id == opt).name
                                                    : financialStatuses.find((x) => x.id == opt).name_e">
                                                        </multiselect>
                                                        <div v-if="$v.create.financial_status_id.$error || errors.financial_status_id"
                                                             class="text-danger">
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>
                                                        <template v-if="errors.financial_status_id">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.financial_status_id"
                                                                          :key="index">{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("sponsor") }}

                                                        </label>
                                                        <multiselect @input="showSponsorModal" v-model="create.sponsor_id"
                                                                     :options="sponsors.map((type) => type.id)"
                                                                     :custom-label="$i18n.locale == 'ar' ? (opt) => sponsors.find((x) => x.id == opt).name : (opt) => sponsors.find((x) => x.id == opt).name_e">
                                                        </multiselect>
                                                        <div v-if="$v.create.sponsor_id.$error || errors.sponsor_id"
                                                             class="text-danger">
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>
                                                        <template v-if="errors.sponsor_id">
                                                            <ErrorMessage v-for="(errorMessage, index) in errors.sponsor_id"
                                                                          :key="index">{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </b-modal>
                                    <!--  /create   -->
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="30">
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
    </Layout>
</template>

<style>
@media print {
    .do-not-print {
        display: none;
    }

    .arrow-sort {
        display: none;
    }

    .text-success {
        background-color: unset;
        color: #6c757d !important;
        border: unset;
    }

    .text-danger {
        background-color: unset;
        color: #6c757d !important;
        border: unset;
    }
}
</style>
