<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength, integer, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import {dynamicSortString, dynamicSortNumber} from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import {formatDateOnly} from "../../../helper/startDate";
import translation from "../../../helper/mixin/translation-mixin";
import DatePicker from "vue2-datepicker";
import Branch from "../../../components/create/general/branch";
import Sponsor from "../../../components/create/club/sponsor.vue";
import permissionGuard from "../../../helper/permission";
import PrintMultiRenewal from "./print/print-multi-renewal";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Multi Subscription",
        meta: [{name: "description", content: "Multi Subscription"}],
    },
    mixins: [translation],
    components: {
        Branch,
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
        DatePicker,
        Sponsor,
        PrintMultiRenewal
    },
    data() {
        return {
            fields: [],
            per_page: 100,
            search: "",
            dataInv:[],
            debounce: {},
            transactionsPagination: {},
            transactions: [],
            removeMembers: [],
            branches: [],
            renewal: [],
            sponsors: [],
            serials: [],
            enabled3: true,
            is_disabled: false,
            isLoader: false,
            create: {
                sponsor_id: null,
                branch_id: null,
                serial_id: null,
                cm_member_id: null,
                document_id: 8,
                date_from: '',
                date_to: '',
                year: '',
                first_name: '',
                second_name: '',
                third_name: '',
                last_name: '',
                family_name: '',
                full_name: '',
                national_id: '',
                membership_number: '',
                is_sponser: '',
                gender: null,
                type: "renew",
                amount: "",
                module_type:"club",
                date:new Date().toISOString().slice(0, 10),
            },
            company_id: null,
            setting: {
                membership_number: true,
                cm_member_id: true,
                national_id: true,
                serial_id: true,
                year: true,
                amount: true,
            },
            Tooltip: "",
            mouseEnter: null,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            transaction_create: [],
            current_page: 1,
            total:0,
            filterSetting: [
                "cm_member_id",
                "document_no",
                "serial_id",
                "date",
                "amount",
                "prefix",
                "year",
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
            return permissionGuard(vm, "Multi Subscription", "all Multi Subscription club");
        });
    },
    validations: {
        create: {
            branch_id: {required},
            sponsor_id: {required},
            year: {},
            first_name: {},
            second_name: {},
            third_name: {},
            last_name: {},
            family_name: {},
            full_name: {},
            national_id: {},
            membership_number: {},
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
                this.transactions.forEach((el) => {
                    if (!this.checkAll.includes(el.id)) {
                        this.checkAll.push(el.id);
                    }
                    this.addNewRecordTransaction(el.id);
                });
            } else {
                this.checkAll = [];
                this.transaction_create = [];
                this.total = 0 ;
            }
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.getCustomTableFields();
    },
    methods: {
        getCustomTableFields() {
            adminApi
                .get(`/customTable/table-columns/cm_transactions`)
                .then((res) => {
                    this.fields = res.data;
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
        isVisible(fieldName) {
            let res = this.fields.filter((field) => {
                return field.column_name == fieldName;
            });
            return res.length > 0 && res[0].is_visible == 1 ? true : false;
        },
        isRequired(fieldName) {
            let res = this.fields.filter((field) => {
                return field.column_name == fieldName;
            });
            return res.length > 0 && res[0].is_required == 1 ? true : false;
        },
        isPermission(item) {
            if (this.$store.state.auth.type == 'user'){
                return this.$store.state.auth.permissions.includes(item)
            }
            return true;
        },
        async showBranchModal() {
            if (this.create.branch_id == 0) {
                this.$bvModal.show("create_branch");
                this.create.branch_id = null;
            }else{
                await this.getSerials();
            }
        },
        resetForm() {
            this.total = 0;
            this.transaction_create = [];
            this.current_page= 1;
            this.transactionsPagination= {};
            this.transactions= [];
            this.create = {
                sponsor_id: null,
                branch_id: null,
                serial_id: null,
                cm_member_id: null,
                date_from: '',
                date_to: '',
                year: '',
                type: "renew",
                document_id: 8,
                amount: "",
                module_type:"club",
                date:new Date().toISOString().slice(0, 10),
                first_name: '',
                second_name: '',
                third_name: '',
                last_name: '',
                family_name: '',
                full_name: '',
                national_id: '',
                membership_number: '',
                is_sponser: '',
                gender: null,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
        },
        resetFormSearch() {
            this.create.year = '';
            this.create.first_name = '';
            this.create.second_name = '';
            this.create.third_name = '';
            this.create.last_name = '';
            this.create.full_name = '';
            this.create.national_id = '';
            this.create.membership_number = '';
            this.create.is_sponser = '';
            this.create.gender = null;
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
        },
        /**
         *  start get Data && pagination
         */
        getData(page = 1) {
            this.isLoader = true;
            adminApi
                .get(
                    `/club-members/members/getMemberForMultiSubscription?sponsor_id=${this.create.is_sponser?this.create.sponsor_id:''}&hasTransaction=1&member_status_id=1&company_id=${this.company_id}&page=${page}&per_page=${this.per_page}&national_id=${this.create.national_id??''}&membership_number=${this.create.membership_number??''}&full_name=${this.create.full_name??''}&year=${this.create.year??''}&first_name=${this.create.first_name??''}&second_name=${this.create.second_name??''}&third_name=${this.create.third_name}&last_name=${this.create.last_name??''}&family_name=${this.create.family_name}&gender=${this.create.gender}`
                )
                .then((res) => {
                    let l = res.data;
                    this.transactions = l.data;
                    this.transactionsPagination = l.pagination;
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
                this.current_page <= this.transactionsPagination.last_page &&
                this.current_page != this.transactionsPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                adminApi
                    .get(
                        `/club-members/transactions?module_type=club&sponsor=1&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.transactions = l.data;
                        this.transactionsPagination = l.pagination;
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
        async getBranches() {
            this.isLoader = true;
            await adminApi
                .get(`/branches?document_id=${this.create.document_id}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.branches = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getSponsors() {
            this.isLoader = true;
            await adminApi
                .get(`/club-members/sponsers`)
                .then((res) => {
                    let l = res.data.data;
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
        async getSerials() {
            this.isLoader = true;
            await adminApi
                .get(`/serials?branch_id=${this.create.branch_id}&document_id=8`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.serials = l;
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
         *  end get Data && pagination
         */
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            if(this.isVisible('branch_id')) await this.getBranches();
            if(this.isVisible('sponsor_id')) await this.getSponsors();
            await this.getRenewal();
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
        },
        /**
         *  create countrie
         */
        AddSubmit() {
            this.isLoader = true;
            this.errors = {};
            this.is_disabled = false;
            let transactions = this.transaction_create;
            adminApi
                .post(`/club-members/transactions`, {transactions, company_id: this.company_id})
                .then((res) => {
                    this.is_disabled = true;
                    setTimeout(() => {
                        Swal.fire({
                            icon: "success",
                            text: `${this.$t("general.Addedsuccessfully")}`,
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    }, 500);
                    this.printInv(res.data.data)
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
        },
        /*
         *  start  dynamicSortString
         */
        sortString(value) {
            return dynamicSortString(value);
        },
        sortNumber(value) {
            return dynamicSortNumber(value);
        },
        /**
         *  start  ckeckRow
         */
        checkRow(id) {
            if (!this.checkAll.includes(id)) {
                this.checkAll.push(id);
                this.addNewRecordTransaction(id);
            } else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index, 1);
                this.removeRecordTransaction(id);
            }
        },
        /**
         *  end  ckeckRow
         */
        formatDate(value) {
            return formatDateOnly(value);
        },
        async getRenewal()
        {
            await adminApi
                .get(`/club-members/memberships-renewals?date_search=${this.create.date}`)
                .then((res) => {
                    let l = res.data.data;
                    this.renewal = l;
                    if (this.create.type)
                    {
                        this.renewalAmount();
                    }
                    this.DataOfModelFinancialYear();
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.PleaseSelectAMember")}`,
                    });
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },

        renewalAmount()
        {
            if (this.renewal.length > 0)
            {
                if (this.create.type == "subscribe")
                {
                    this.create.amount = this.renewal[0].membership_cost;
                }else {
                    this.create.amount = this.renewal[0].renewal_cost;
                }
            }
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
                    XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
                } else {
                    XLSX.writeFile(wb, fn || (('Multi-Subscription' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },

        addNewRecordTransaction(id) {
            let data = this.create;
            let member = this.transactions.find((el)=> el.id ==id);
            let serial = this.serials.find((el)=> el.gender ==member.gender);
            let member_name = member.full_name;
            let member_transactions = this.transaction_create.find((el)=> el.cm_member_id == id);
            if (!member_transactions)
            {
                this.transaction_create.push({
                    sponsor_id: data.sponsor_id,
                    branch_id: data.branch_id,
                    serial_name: serial.name,
                    serial_id: serial.id,
                    cm_member_id: id,
                    document_id: 8,
                    year_from: new Date(data.date_from).getFullYear(),
                    year_to: new Date(data.date_to).getFullYear(),
                    date_from: data.date_from,
                    date_to: data.date_to,
                    year: parseInt(member.transaction.year) + 1,
                    type: data.type,
                    amount: data.amount,
                    member_name: member_name,
                    membership_number: member.membership_number,
                    module_type:"club",
                    date:new Date().toISOString().slice(0, 10),
                });
                this.total += parseFloat(this.create.amount);
            }

        },
        removeRecordTransaction(id) {
           let index = this.transaction_create.findIndex(obj => obj.cm_member_id === id);
            this.transaction_create.splice(index, 1);
            this.total = 0;
            this.transaction_create.forEach((el) => {
                this.total += parseFloat(el.amount);
            });
        },
        async DataOfModelFinancialYear() {
            this.isLoader = true;
            await adminApi
                .get(`/financial-years/DataOfModelFinancialYear?date=${this.create.date}`)
                .then((res) => {
                    let l = res.data;
                    if(l)
                    {
                        this.create.date_from = l.data.start_date;
                        this.create.date_to = l.data.end_date;
                    }
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
        handelDataPrintInv(data)
        {
            let array = [];
            array.push(data);
            this.printInv(array);
        },
        printInv(data){
            this.dataInv = data
        },
        serialName(data)
        {
          return this.serials.find((el)=> el.gender ==data.gender)
        }
    },
};
</script>

<template>
    <Layout>
        <PageHeader/>
        <div v-if="dataInv" style="display:none;">
            <PrintMultiRenewal :data_row="dataInv"/>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ $t("general.multiSubscriptionSponsorTable") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                            </div>
                        </div>
                        <!-- end search -->

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-5 d-flex align-items-center mb-1 mb-xl-0">
                                <!-- start create and printer -->
                                <b-button
                                    variant="primary"
                                    :disabled="!is_disabled"
                                    @click.prevent="resetForm"
                                    type="button"
                                    :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
                                >
                                    {{ $t("general.AddNewSubscriptions") }}
                                </b-button>
                                <template v-if="!is_disabled">
                                    <b-button
                                        v-b-modal.showTransaction
                                        variant="primary"
                                        type="submit"
                                        class="mx-1"
                                        v-if="!isLoader"
                                    >
                                        {{ $t("general.Create") }}
                                        <i class="fas fa-plus"></i>
                                    </b-button>

                                    <b-button variant="primary" class="mx-1" disabled v-else>
                                        <b-spinner small></b-spinner>
                                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                    </b-button>
                                </template>
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printData'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <b-button
                                        variant="primary"
                                        :disabled="!is_disabled"
                                        type="button"
                                        v-print="'#printInv'"
                                        :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : 'mx-2']"
                                    >
                                        {{ $t("general.PrintReceipts") }}
                                        <i class="fe-printer"></i>
                                    </b-button>
<!--                                    <button-->
<!--                                        class="custom-btn-dowonload"-->
<!--                                        @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"-->
<!--                                        v-if="checkAll.length == 1 && isPermission('update multiSubscription club')"-->
<!--                                    >-->
<!--                                        <i class="mdi mdi-square-edit-outline"></i>-->
<!--                                    </button>-->
                                </div>
                                <!-- end create and printer -->
                            </div>
                            <div class="col-md-2">
                                <div class="d-inline-block">
                                    <!-- Basic dropdown -->
                                    <b-button
                                        v-b-modal.create
                                        variant="primary"
                                        class="btn-block setting-search"
                                    >
                                        {{ $t("general.searchSetting") }}
                                        <i class="fas fa-search"></i>
                                    </b-button>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ $t('general.totalAmount') }}
                                    </label>
                                    <input
                                        :disabled="true"
                                        type="number"
                                        class="form-control"
                                        step="any"
                                        v-model="total"
                                    />
                                </div>
                            </div>
                            <div class="col-xs-4 col-md-4 col-lg-4 d-flex align-items-center justify-content-end">
                                <div class="d-fex">
                                    <!-- start filter and setting -->
                                    <div class="d-inline-block">
                                        <!-- Basic dropdown -->
                                        <b-dropdown
                                            variant="primary"
                                            :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                                            ref="dropdown"
                                            class="dropdown-custom-ali"
                                        >
                                            <b-form-checkbox v-model="setting.membership_number" class="mb-1">
                                                {{ getCompanyKey("member_membership_number") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.cm_member_id" class="mb-1">
                                                {{ getCompanyKey("member") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.national_id" class="mb-1">
                                                {{ getCompanyKey("member_national_id") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.serial_id" class="mb-1">
                                                {{ $t("general.serialName") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.amount" class="mb-1">
                                                {{ getCompanyKey("subscription_amount") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.year" class="mb-1">
                                                {{ $t("general.ForAYear") }}
                                            </b-form-checkbox>
                                            <div class="d-flex justify-content-end">
                                                <a href="javascript:void(0)" class="btn btn-primary btn-sm">{{$t("general.Apply")}}</a>
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>
                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom">
                                        <div class="d-inline-block" style="font-size: 13px">
                                            {{ transactionsPagination.from }}-{{ transactionsPagination.to }} /
                                            {{ transactionsPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{
                                                  'pointer-events':
                                                    transactionsPagination.current_page == 1 ? 'none' : '',
                                                }"
                                                @click.prevent="getData(transactionsPagination.current_page - 1)"
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
                                                  'pointer-events':
                                                    transactionsPagination.last_page == transactionsPagination.current_page
                                                      ? 'none'
                                                      : '',
                                                }"
                                                @click.prevent="getData(transactionsPagination.current_page + 1)"
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
                            body-class="p-4 "
                            size="lg"
                            scrollable
                            :hide-footer="true"
                            @show="resetModal"
                        >
                            <form>
                                <div class="row">
                                    <div class="col-md-12 mb-3 d-flex justify-content-end">
                                        <b-button
                                            variant="success"
                                            @click.prevent="resetFormSearch"
                                            type="button"
                                            :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
                                        >
                                            {{ $t("general.NewSearch") }}
                                        </b-button>
                                        <!-- Emulate built in modal footer ok and cancel button actions -->
                                        <template v-if="!is_disabled">
                                            <b-button
                                                variant="success"
                                                type="submit"
                                                class="mx-1"
                                                v-if="!isLoader"
                                                @click.prevent="getData"
                                            >
                                                {{ $t("general.Search") }}
                                            </b-button>

                                            <b-button variant="success" class="mx-1" disabled v-else>
                                                <b-spinner small></b-spinner>
                                                <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                            </b-button>
                                        </template>
                                        <b-button
                                            variant="danger"
                                            type="button"
                                            @click.prevent="$bvModal.hide('create')"
                                        >
                                            {{ $t("general.Cancel") }}
                                        </b-button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ getCompanyKey("branch") }} <span class="text-danger">*</span></label>
                                            <multiselect :disabled="transaction_create.length > 0" :show-labels="false" @input="showBranchModal" v-model="create.branch_id"
                                                         :options="branches.map((type) => type.id)" :custom-label="
                                                    (opt) =>
                                                        $i18n.locale == 'ar'
                                                            ? branches.find((x) => x.id == opt).name
                                                            : branches.find((x) => x.id == opt).name_e
                                                " :class="{
                                                        'is-invalid':
                                                            $v.create.branch_id.$error || errors.branch_id,
                                                    }">
                                            </multiselect>
                                            <div v-if="!$v.create.branch_id.required" class="invalid-feedback">
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>

                                            <template v-if="errors.branch_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.branch_id"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey("sponsor") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect :show-labels="false" v-model="create.sponsor_id"
                                                         :disabled="transaction_create.length > 0"
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
                                    <div v-if="create.branch_id && create.sponsor_id" class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.LastYearOfPayment') }}
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="yyyy"
                                                v-model="$v.create.year.$model"
                                                :class="{ 'is-invalid':
                                                        $v.create.year.$error ||
                                                        errors.year,
                                                    'is-valid':
                                                        !$v.create.year
                                                            .$invalid &&
                                                        !errors.year,
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
                                    <div v-if="create.branch_id && create.sponsor_id" class="col-md-6">
                                        <div class="form-check mt-3">
                                            <input style="transform: scale(1.5);" type="checkbox" v-model="create.is_sponser" value="true" id="flexCheckDefault">
                                            <label style="padding:9px" class="form-check-label" for="flexCheckDefault">
                                                {{$t('general.SponsorMembersOnly')}}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <hr v-if="create.branch_id && create.sponsor_id" style=" margin: 10px 0 !important; border-top: 1px solid rgb(141 163 159 / 42%);"/>
                                <div class="row" v-if="create.branch_id && create.sponsor_id">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t("general.full_name") }}
                                            </label>
                                            <input
                                                v-model="$v.create.full_name.$model"
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                  'is-invalid':
                                                    $v.create.full_name.$error || errors.full_name,
                                                  'is-valid':
                                                    !$v.create.full_name.$invalid &&
                                                    !errors.full_name,
                                                }"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("member_national_id") }}
                                            </label>
                                            <input
                                                v-model="$v.create.national_id.$model"
                                                class="form-control"
                                                type="number"
                                                :class="{
                                                  'is-invalid':
                                                    $v.create.national_id.$error || errors.national_id,
                                                  'is-valid':
                                                    !$v.create.national_id.$invalid &&
                                                    !errors.national_id,
                                                }"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("member_membership_number") }}
                                            </label>
                                            <input
                                                v-model="$v.create.membership_number.$model"
                                                class="form-control"
                                                type="number"
                                                :class="{
                                                  'is-invalid':
                                                    $v.create.membership_number.$error || errors.membership_number,
                                                  'is-valid':
                                                    !$v.create.membership_number.$invalid &&
                                                    !errors.membership_number,
                                                }"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("member_first_name") }}
                                            </label>
                                            <input
                                                v-model="$v.create.first_name.$model"
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                  'is-invalid':
                                                    $v.create.first_name.$error || errors.first_name,
                                                  'is-valid':
                                                    !$v.create.first_name.$invalid &&
                                                    !errors.first_name,
                                                }"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("member_second_name") }}
                                            </label>
                                            <input
                                                v-model="$v.create.second_name.$model"
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                      'is-invalid':
                                                        $v.create.second_name.$error || errors.second_name,
                                                      'is-valid':
                                                        !$v.create.second_name.$invalid &&
                                                        !errors.second_name,
                                                    }"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("member_third_name") }}
                                            </label>
                                            <input
                                                v-model="$v.create.third_name.$model"
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                  'is-invalid':
                                                    $v.create.third_name.$error || errors.third_name,
                                                  'is-valid':
                                                    !$v.create.third_name.$invalid &&
                                                    !errors.third_name,
                                                }"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("member_last_name") }}
                                            </label>
                                            <input
                                                v-model="$v.create.last_name.$model"
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                  'is-invalid':
                                                    $v.create.last_name.$error || errors.last_name,
                                                  'is-valid':
                                                    !$v.create.last_name.$invalid && !errors.last_name,
                                                }"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("member_family_name") }}
                                            </label>
                                            <input
                                                v-model="$v.create.family_name.$model"
                                                class="form-control"
                                                type="text"
                                                :class="{
                                                  'is-invalid':
                                                    $v.create.family_name.$error || errors.family_name,
                                                  'is-valid':
                                                    !$v.create.family_name.$invalid &&
                                                    !errors.family_name,
                                                }"
                                            />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label mr-2">
                                                {{ getCompanyKey("serial_gender") }}
                                            </label>
                                            <b-form-group>
                                                <b-form-radio
                                                    class="d-inline-block"
                                                    v-model="create.gender"
                                                    name="create_gender"
                                                    value="1"
                                                >{{ $t("general.male") }}
                                                </b-form-radio>
                                                <b-form-radio
                                                    class="d-inline-block m-1"
                                                    v-model="create.gender"
                                                    name="create_gender"
                                                    value="0"
                                                >{{ $t("general.female") }}
                                                </b-form-radio>
                                                <b-form-radio
                                                    class="d-inline-block m-1"
                                                    v-model="create.gender"
                                                    name="create_gender"
                                                    value="null"
                                                >{{ $t("general.all") }}
                                                </b-form-radio>
                                            </b-form-group>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <!-- show member transactions -->
                        <b-modal
                            id="showTransaction"
                            :title="$t('general.transactions')"
                            title-class="font-18"
                            body-class="p-4 "
                            dialog-class="modal-full-width"
                            scrollable
                            :hide-footer="true"
                        >
                            <form>
                                <div class="row">
                                    <div class="col-md-12 mb-3 d-flex justify-content-end">
                                        <!-- Emulate built in modal footer ok and cancel button actions -->
                                        <template v-if="!is_disabled">
                                            <b-button
                                                variant="success"
                                                type="submit"
                                                class="mx-1"
                                                v-if="!isLoader"
                                                @click.prevent="AddSubmit"
                                            >
                                                {{ $t("general.Save") }}
                                            </b-button>

                                            <b-button variant="success" class="mx-1" disabled v-else>
                                                <b-spinner small></b-spinner>
                                                <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                            </b-button>
                                        </template>
                                        <b-button
                                            variant="danger"
                                            type="button"
                                            @click.prevent="$bvModal.hide('showTransaction')"
                                        >
                                            {{ $t("general.Cancel") }}
                                        </b-button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card-header p-0">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <h3 class="card-title">
                                                        {{$t('general.totalAmount')}} : {{total}}
                                                    </h3>
                                                </div>
                                                <div class="col-md-4">
                                                    <h3 class="card-title">
                                                        {{$t('general.sponsor')}} : {{sponsors.find((el)=> el.id ==create.sponsor_id) ? sponsors.find((el)=> el.id ==create.sponsor_id).name:'---'}}
                                                    </h3>
                                                </div>
                                                <div class="col-md-4">
                                                    <h3 class="card-title">
                                                        {{ getCompanyKey("branch") }} : {{branches.find((el)=> el.id ==create.branch_id) ? branches.find((el)=> el.id ==create.branch_id).name:'---'}}
                                                    </h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="table-responsive mb-3 custom-table-theme position-relative">
                                            <!--       start loader       -->
                                            <loader size="large" v-if="isLoader" />
                                            <!--       end loader       -->

                                            <table class="table table-borderless table-hover table-centered m-0" >
                                                <thead>
                                                <tr>
                                                    <th v-if="isVisible('membership_number')">{{ getCompanyKey("member_membership_number") }}</th>
                                                    <th>{{ $t("general.serial_number") }}</th>
                                                    <th>
                                                        <div class="d-flex justify-content-center">
                                                            <span>{{ getCompanyKey("member") }}</span>
                                                            <div class="arrow-sort">
                                                                <i class="fas fa-arrow-up" @click="transaction_create.sort(sortString('full_name'))"></i>
                                                                <i class="fas fa-arrow-down" @click="transaction_create.sort(sortString('-full_name'))"></i>
                                                            </div>
                                                        </div>
                                                    </th>
                                                    <th v-if="isVisible('amount')">{{ getCompanyKey("subscription_amount") }}</th>
                                                    <th v-if="isVisible('year')">{{ $t("general.ForAYear") }}</th>
                                                    <th>{{ $t("general.Action") }}</th>
                                                </tr>
                                                </thead>
                                                <tbody v-if="transaction_create.length > 0">
                                                <tr v-for="(data, index) in transaction_create"
                                                    :key="data.id"
                                                    class="body-tr-custom"
                                                >
                                                    <td v-if="isVisible('membership_number')">
                                                        <h5 class="m-0 font-weight-normal">{{ data.membership_number }}</h5>
                                                    </td>
                                                    <td>
                                                        <h5 class="m-0 font-weight-normal">{{ data.serial_name }}</h5>
                                                    </td>
                                                    <td>
                                                        <h5 class="m-0 font-weight-normal">{{ data.member_name }}</h5>
                                                    </td>
                                                    <td v-if="isVisible('amount')">
                                                        <h5 class="m-0 font-weight-normal">{{data.amount}}</h5>
                                                    </td>
                                                    <td v-if="isVisible('year')"> <h5 class="m-0 font-weight-normal"> {{ data.year }}</h5></td>
                                                    <td>
                                                        <button  type="button"
                                                                 @click.prevent="checkRow(data.cm_member_id)"
                                                                 class="custom-btn-dowonload"
                                                        >
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                </tbody>
                                                <tbody v-else>
                                                <tr>
                                                    <th class="text-center" colspan="9">
                                                        {{ $t("general.notDataFound") }}
                                                    </th>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!-- /show member transactions -->

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader"/>
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                                   id="printData">
                                <thead>
                                <tr>
                                    <th v-if="enabled3" class="do-not-print" scope="col" style="width: 0">
                                        <div class="form-check custom-control">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                v-model="isCheckAll"
                                                style="width: 17px; height: 17px"
                                            />
                                        </div>
                                    </th>
                                    <th v-if="setting.membership_number">
                                        <div class="d-flex justify-content-center">
                                            <span>{{getCompanyKey("member_membership_number")}}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="members.sort(sortString('membership_number'))"></i>
                                                <i class="fas fa-arrow-down" @click="members.sort(sortString('-membership_number'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.cm_member_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="transactions.sort(sortString('full_name'))"></i>
                                                <i class="fas fa-arrow-down" @click="transactions.sort(sortString('-full_name'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.national_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member_national_id") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="members.sort(sortString('national_id'))"></i>
                                                <i class="fas fa-arrow-down" @click="members.sort(sortString('-national_id'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.serial_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.serialName") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.amount">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("subscription_amount") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.year">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.ForAYear") }}</span>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody v-if="transactions.length > 0">
                                     <tr
                                    @click.capture="checkRow(data.id)"
                                    v-for="(data, index) in transactions"
                                    :key="data.id"
                                    class="body-tr-custom"
                                >
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control" style="min-height: 1.9em">
                                            <input
                                                style="width: 17px; height: 17px"
                                                class="form-check-input"
                                                type="checkbox"
                                                :value="data.id"
                                                v-model="checkAll"
                                            />
                                        </div>
                                    </td>
                                     <td v-if="setting.membership_number">
                                         <h5 class="m-0 font-weight-normal">
                                             {{data.membership_number}}
                                         </h5>
                                     </td>
                                     <td v-if="setting.cm_member_id">
                                         <h5 class="m-0 font-weight-normal">
                                             {{data.full_name}}
                                         </h5>
                                     </td>
                                     <td v-if="setting.national_id">
                                         <h5 class="m-0 font-weight-normal">
                                             {{data.national_id}}
                                         </h5>
                                     </td>
                                     <td v-if="setting.serial_id && isVisible('serial_id')">
                                         <h5 class="m-0 font-weight-normal">
                                             {{ serialName(data) ? $i18n.locale == 'ar' ? serialName(data).name : serialName(data).name_e : '---' }}
                                         </h5>
                                     </td>
                                     <td v-if="setting.amount && isVisible('amount')">
                                         <h5 class="m-0 font-weight-normal">{{ create.amount }}</h5>
                                     </td>
                                     <td v-if="setting.year && isVisible('year')">
                                         <h5 class="m-0 font-weight-normal">{{ data.transaction ? parseInt(data.transaction.year) +1 :'---' }}</h5>
                                     </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="7">
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
