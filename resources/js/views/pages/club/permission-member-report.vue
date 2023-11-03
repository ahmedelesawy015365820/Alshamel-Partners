<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import Multiselect from "vue-multiselect";
import permissionGuard from "../../../helper/permission";

import {
    required,
    minLength,
    maxLength,
    integer,
    requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import alphaArabic from "../../../helper/alphaArabic";
import alphaEnglish from "../../../helper/alphaEnglish";
import {
    dynamicSortString,
    dynamicSortNumber,
} from "../../../helper/tableSort";
import translation from "../../../helper/mixin/translation-mixin";
import {formatDateOnly} from "../../../helper/startDate";
import DatePicker from "vue2-datepicker";
import {arabicValue, englishValue} from "../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Permission member report",
        meta: [{name: "description", content: "Permission member report"}],
    },
    mixins: [translation],
    components: {
        Multiselect,
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        DatePicker,
    },
    data() {
        return {
            permissions: [],
            create: {
                date: '',
                cm_permission_id: []
            },
            per_page: 50,
            search: "",
            debounce: {},
            enabled3: true,
            itemsPagination: {},
            progress: 0,
            items: [],
            isLoader: false,
            Tooltip: "",
            mouseEnter: "",
            fields: [],
            company_id: null,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            setting: {
                membership_number: true,
                full_name: true,
                birth_date: true,
                gender: true,
                membership_date: true,
                financial_status_id: true,
                member_status_id: true,
                PaymentDate: true,
                document_no: true,
                ForAYear: true,
                national_id: true,
                home_phone: true,
                home_address: true,
                work_phone: true,
                job: true,
                degree: true,
            },
            is_disabled: false,
            filterSetting: [
                "membership_number",
                "full_name",
                "birth_date",
                "gender",
                "membership_date",
                "financial_status_id",
                "member_status_id",
                "national_id",
                "home_phone",
                "home_address",
                "work_phone",
                "job",
                "degree",
            ],
            printLoading: true,
            printObj: {
                id: "printData",
            },
        };
    },
    validations: {
        create: {
            cm_permission_id: {required},
            date: {required},
        },
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Permission Member Report", "all Permission member club");
        });

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
                this.items.forEach((el) => {
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
        this.getMemberPermissions();
    },
    methods: {
        formatDate(value) {
            return formatDateOnly(value);
        },
        getData(page = 1) {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                let dateStartArray = this.create.date.split("-");
                let startDate = dateStartArray[2] + "-" + dateStartArray[1] + "-" + dateStartArray[0];

                adminApi
                    .get(`/club-members/members/report-cm-member?members_permissions_id=${this.create.cm_permission_id}&dateOfYear=${startDate}&page=${page}&per_page=50`, {
                        params: {
                            members_permissions_id: this.create.cm_permission_id,
                            dateOfYear: this.create.date
                        },
                    })
                    .then((res) => {
                        let l = res.data;
                        this.items = l.data;
                        this.itemsPagination = l.pagination;
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
        getDataCurrentPage(page = 1) {
            if (
                this.current_page <= this.itemsPagination.last_page &&
                this.current_page != this.itemsPagination.current_page &&
                this.current_page
            ) {
                this.$v.create.$touch();
                if (this.$v.create.$invalid) {
                    return;
                } else {
                    this.isLoader = true;
                    let dateStartArray = this.create.date.split("-");
                    let startDate = dateStartArray[2] + "-" + dateStartArray[1] + "-" + dateStartArray[0];

                    adminApi
                        .get(`/club-members/members/report-cm-member?members_permissions_id=${this.create.cm_permission_id}&dateOfYear=${startDate}&page=${this.current_page}&per_page=50&search=${this.search}&${filter}`, {
                            params: {
                                members_permissions_id: this.create.cm_permission_id,
                                dateOfYear: this.create.date
                            },
                        })
                        .then((res) => {
                            let l = res.data;
                            this.items = l.data;
                            this.itemsPagination = l.pagination;
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
            }
        },
        async getMemberPermissions() {
            this.isLoader = true;

            await adminApi
                .get(`/club-members/members-permissions`)
                .then((res) => {
                    let l = res.data.data;
                    this.permissions = l;
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
        moveInput(tag, c, index) {
            document.querySelector(`${tag}[data-${c}='${index}']`).focus();
        },
        /**
         *   Export Excel
         */
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
                if (dl) {
                    XLSX.write(wb, {bookType: type, bookSST: true, type: "base64"});
                } else {
                    XLSX.writeFile(
                        wb,
                        fn || ("Permission Member Report" + "." || "SheetJSTableExport.") + (type || "xlsx")
                    );
                }
                this.enabled3 = true;
            }, 100);
        },
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
                            <h4 class="header-title">
                                {{ $t("general.PermissionMemberReport") }}
                            </h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">

                                </div>

                                <div
                                    class="d-inline-block position-relative"
                                    style="width: 77%"
                                >
                                    <div class="form-group position-relative"></div>
                                </div>
                            </div>
                        </div>
                        <!-- end search -->

                        <div class="row justify-content-between align-items-center mb-2 px-1" >
                            <div class="col-md-3 d-flex align-items-center mb-1 mt-2 mb-xl-0">
                                <b-button
                                    v-b-modal.create
                                    variant="primary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t('general.Search') }}
                                    <i class="fe-search"></i>
                                </b-button>
                                <!-- start create and printer -->
                                <div class="d-inline-flex">
                                    <button
                                        style="margin: 0 15px"
                                        @click="ExportExcel('xlsx')"
                                        class="custom-btn-dowonload"
                                    >
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printData'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
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
                                        <b-dropdown
                                            variant="primary"
                                            :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                                            ref="dropdown"
                                            class="dropdown-custom-ali"
                                        >
                                            <b-form-checkbox v-model="setting.membership_number" class="mb-1">{{ getCompanyKey("member_membership_number") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.full_name" class="mb-1">{{ $t("general.name") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.birth_date" class="mb-1">{{ getCompanyKey("member_birth_date") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.gender" class="mb-1">{{ getCompanyKey("member_type") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.membership_date" class="mb-1">{{ getCompanyKey("member_membership_date") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.financial_status_id" class="mb-1">{{ getCompanyKey("financial_status") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.member_status_id" class="mb-1">{{ $t("general.status") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.PaymentDate" class="mb-1">{{ $t("general.PaymentDate") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.document_no" class="mb-1">{{ $t("general.ReceiptNumber") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.ForAYear" class="mb-1">{{ $t("general.ForAYear") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.national_id" class="mb-1">{{ getCompanyKey("member_national_id") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.home_phone" class="mb-1">{{ getCompanyKey("member_home_phone") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.home_address" class="mb-1">{{ getCompanyKey("member_home_address") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.work_phone" class="mb-1">{{ getCompanyKey("member_work_phone") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.job" class="mb-1">{{ getCompanyKey("member_job") }}</b-form-checkbox>
                                            <b-form-checkbox v-model="setting.degree" class="mb-1">{{ getCompanyKey("member_degree") }}</b-form-checkbox>

                                            <div class="d-flex justify-content-end">
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm">
                                                    Apply
                                                </a>
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>
                                    <!-- end filter and setting -->

                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom">
                                        <div class="d-inline-block" style="font-size: 13px">
                                            {{ itemsPagination.from }}-{{ itemsPagination.to }} /
                                            {{ itemsPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{ 'pointer-events': itemsPagination.current_page == 1 ? 'none' : '',}"
                                                @click.prevent="getData(itemsPagination.current_page - 1)"
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
                                                :style="{
                                                  'pointer-events': itemsPagination.last_page ==itemsPagination.current_page
                                                      ? 'none' : '',
                                                }"
                                                @click.prevent="getData(itemsPagination.current_page + 1)"
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
                            :title="$t('general.execution')"
                            title-class="font-18"
                            body-class="p-4"
                            :hide-footer="true"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <template>
                                        <b-button
                                            variant="success"
                                            type="button" class="mx-1"
                                            v-if="!isLoader"
                                            @click.prevent="getData(1)"
                                        >
                                            {{ $t('general.execution') }}
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
                                    <div class="col-md-12">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ $t('general.permissionsMember') }}
                                            </label>
                                            <multiselect
                                                :multiple="true"
                                                v-model="$v.create.cm_permission_id.$model"
                                                :options="permissions.map((type) => type.id)"
                                                :custom-label="
                                                  (opt) => permissions.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'
                                                      ? permissions.find((x) => x.id == opt).name
                                                      : permissions.find((x) => x.id == opt).name_e:null
                                                "
                                            >
                                            </multiselect>
                                            <div
                                                v-if="
                                  $v.create.cm_permission_id.$error
                                "
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.date') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="$v.create.date.$model"
                                                placeholder="DD-MM-YYYY"
                                                format="DD-MM-YYYY"
                                                valueType="format"
                                                :confirm="false"
                                            ></date-picker>
                                            <div
                                                v-if="
                                                  $v.create.date.$error
                                                "
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <h3 class="text-center" v-if="items.length > 0">
                            {{ $t('general.Theresultofsearchesfomembersrightsisbasedonhistory') + create.date }}
                        </h3>
                        <!-- start .table-responsive-->
                        <div  class="table-responsive mb-3 custom-table-theme position-relative">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader"/>
                            <!--       end loader       -->
                            <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table" id="printData">
                                <thead>
                                <tr>
                                    <th v-if="setting.membership_number">
                                        <div class="d-flex justify-content-center">
                                            <span>{{getCompanyKey("member_membership_number")}}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString('membership_number'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-membership_number'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.full_name">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.name") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString('full_name'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-full_name'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.birth_date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_birth_date") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString('birth_date'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-birth_date'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.gender">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_gender") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString('gender'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-gender'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.membership_date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_membership_date")}}</span>
                                            <div class="arrow-sort">
                                                <i  class="fas fa-arrow-up" @click="items.sort(sortString('membership_date'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-membership_date'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.financial_status_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("financial_status") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.member_status_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.status") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.PaymentDate">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.PaymentDate") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.document_no">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.ReceiptNumber") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.ForAYear">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.ForAYear") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.national_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_national_id") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString('national_id'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-national_id'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.home_phone">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_home_phone") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString('home_phone'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-home_phone'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.home_address">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_home_address") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString('home_address'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-home_address'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.work_phone">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_work_phone") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString('work_phone'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-work_phone'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.job">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_job") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString('job'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-job'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.degree">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_degree") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="items.sort(sortString('degree'))"></i>
                                                <i class="fas fa-arrow-down" @click="items.sort(sortString('-degree'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody v-if="items.length > 0">
                                <tr
                                    v-for="(data, index) in items"
                                    :key="data.id"
                                    class="body-tr-custom"
                                >
                                    <td v-if="setting.membership_number">
                                        {{ data.membership_number }}
                                    </td>
                                    <td v-if="setting.full_name">
                                        {{ data.full_name }}
                                    </td>
                                    <td v-if="setting.birth_date">
                                        {{ data.birth_date?data.birth_date:'---' }}
                                    </td>
                                    <td v-if="setting.gender">
                                        {{data.gender ? parseInt(data.gender) == 1 ? $t("general.male") : $t("general.female") : '---'}}
                                    </td>
                                    <td v-if="setting.membership_date">
                                        {{data.membership_date ? formatDate(data.membership_date) : '---' }}
                                    </td>
                                    <td v-if="setting.financial_status_id">
                                        {{data.financial_status ? $i18n.locale == "ar"? data.financial_status.name: data.financial_status.name_e: "---"}}
                                    </td>
                                    <td v-if="setting.member_status_id">
                                        {{data.status ? $i18n.locale == "ar"? data.status.name: data.status.name_e: "---"}}
                                    </td>
                                    <td v-if="setting.PaymentDate">
                                        {{data.transaction ? formatDate(data.transaction.date) : '---' }}
                                    </td>
                                    <td v-if="setting.document_no">
                                        {{ data.transaction ? data.transaction.document_no : '---' }}
                                    </td>
                                    <td v-if="setting.ForAYear">
                                        {{ data.transaction ? data.transaction.year : '---' }}
                                    </td>
                                    <td v-if="setting.national_id">
                                        {{ data.national_id ? data.national_id : '---' }}
                                    </td>
                                    <td v-if="setting.home_phone">
                                        {{ data.home_phone ? data.home_phone : '---' }}
                                    </td>
                                    <td v-if="setting.home_address">
                                        {{ data.home_address ? data.home_address : '---' }}
                                    </td>
                                    <td v-if="setting.work_phone">
                                        {{ data.work_phone ? data.work_phone : '---' }}
                                    </td>
                                    <td v-if="setting.job">
                                        {{ data.job ? data.job : '---' }}
                                    </td>
                                    <td v-if="setting.degree">
                                        {{ data.degree ? data.degree : '---' }}
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="12">
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

<style scoped>
@media print {
    .do-not-print {
        display: none;
    }

    .arrow-sort {
        display: none;
    }
}
</style>
