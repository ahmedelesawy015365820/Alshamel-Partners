<script>
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";
import translation from "../../../helper/mixin/translation-mixin";
import {minValue, required, requiredIf} from "vuelidate/lib/validators";
import {formatDateOnly} from "../../../helper/startDate";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import {dynamicSortString} from "../../../helper/tableSort";
import CreateOrUpdatePanel from "./components/create-or-update-panel";
import CreateOrUpdateBooking from "./components/create-or-update-booking";
import CreateOrUpdateItems from "./components/create-or-update-items";
import CreateOrUpdateCheckout from "./components/create-or-update-checkout";
import CreateOrUpdateMaintenance from "./components/create-or-update-maintenance";
import AccountStatementPrint from "./print/account-statement-booking";


/**
 * Advanced Table component
 */
export default {
    name: "documentWithItem",
    props: {
        document_id: {
            default: null,
        },
    },
    mixins: [translation],
    components: {
        ErrorMessage,
        loader,
        DatePicker,
        Multiselect,
        CreateOrUpdatePanel,
        CreateOrUpdateBooking,
        CreateOrUpdateItems,
        CreateOrUpdateCheckout,
        CreateOrUpdateMaintenance,
        AccountStatementPrint
    },
    data() {
        return {
            per_page: 50,
            search: "",
            debounce: {},
            reservationsPagination: {},
            reservations: [],
            document:null,
            enabled3: true,
            isLoader: false,
            setting: {
                date: true,
                customer_id: true,
                salesman_id: true,
                branch_id: true,
                serial_id: true,
            },
            filterSetting: [
                'date',
                'customer_id',
                'employee_id',
                'branch_id',
                'prefix',
            ],
            errors: {},
            isCheckAll: false,
            checkAll: [],
            is_disabled: false,
            current_page: 1,
            company_id: null,
            Tooltip: "",
            mouseEnter: null,
            printLoading: true,
            printObj: {
                id: "printReservation",
            },
            searchFilter: {
                start_date: null,
                end_date: null,
                start_serial_number: null,
                end_serial_number: null,
                is_related_document: null,
            },
        };
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
                this.reservations.forEach((el) => {
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
        this.getDocumentData();
        this.getData();
        this.$store.dispatch('locationIp/getIp');
    },
    methods: {
        /**
         *  get Data reservations
         */
        getData(page = 1) {
            this.isLoader = true;
            let _filterSetting = [...this.filterSetting];
            let index = this.filterSetting.indexOf("customer_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
            }
            index = this.filterSetting.indexOf("employee_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "employee.name" : "employee.name_e";
            }

            index = this.filterSetting.indexOf("branch_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "branch.name" : "branch.name_e";
            }
            let filter = "";
            for (let i = 0; i < _filterSetting.length; ++i) {
                filter += `columns[${i}]=${_filterSetting[i]}&`;
            }
            adminApi
                .get(
                    `document-headers?document_id=${this.document_id}&start_date=${this.searchFilter.start_date??''}&end_date=${this.searchFilter.end_date??''}&start_serial_number=${this.searchFilter.start_serial_number??''}&end_serial_number=${this.searchFilter.end_serial_number??''}&is_related_document=${this.searchFilter.is_related_document??''}&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}&is_quotation=1`
                )
                .then((res) => {
                    let l = res.data;
                    this.reservations = l.data;
                    this.reservationsPagination = l.pagination;
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
        getDataCurrentPage() {
            if (
                this.current_page <= this.reservationsPagination.last_page &&
                this.current_page != this.reservationsPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let _filterSetting = [...this.filterSetting];
                let index = this.filterSetting.indexOf("customer_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
                }
                index = this.filterSetting.indexOf("employee_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "employee.name" : "employee.name_e";
                }

                index = this.filterSetting.indexOf("branch_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "branch.name" : "branch.name_e";
                }
                let filter = "";
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }

                adminApi.get(
                        `document-headers?document_id=${this.document_id}&start_date=${this.searchFilter.start_date??''}&end_date=${this.searchFilter.end_date??''}&start_serial_number=${this.searchFilter.start_serial_number??''}&end_serial_number=${this.searchFilter.end_serial_number??''}&is_related_document=${this.searchFilter.is_related_document??''}&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}&is_quotation=1`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.reservations = l.data;
                        this.reservationsPagination = l.pagination;
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
         *  delete screen button
         */
        deleteScreenButton(id, index) {
            if (Array.isArray(id)) {
                Swal.fire({
                    title: `${this.$t("general.Areyousure")}`,
                    text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                    cancelButtonText: `${this.$t("general.Nocancel")}`,
                    confirmButtonClass: "btn btn-success mt-2",
                    cancelButtonClass: "btn btn-danger ml-2 mt-2",
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.value) {
                        this.isLoader = true;
                        adminApi
                            .post(`document-headers/bulk-delete`, { ids: id })
                            .then((res) => {
                                this.checkAll = [];
                                this.getData();
                                Swal.fire({
                                    icon: "success",
                                    title: `${this.$t("general.Deleted")}`,
                                    text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            })
                            .catch((err) => {
                                if (err.response.status == 400) {
                                    Swal.fire({
                                        icon: "error",
                                        title: `${this.$t("general.Error")}`,
                                        text: `${this.$t("general.CantDeleteRelation")}`,
                                    });
                                    this.getData();
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
                });
            } else {
                Swal.fire({
                    title: `${this.$t("general.Areyousure")}`,
                    text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                    cancelButtonText: `${this.$t("general.Nocancel")}`,
                    confirmButtonClass: "btn btn-success mt-2",
                    cancelButtonClass: "btn btn-danger ml-2 mt-2",
                    buttonsStyling: false,
                }).then((result) => {
                    if (result.value) {
                        this.isLoader = true;

                        adminApi
                            .delete(`document-headers/${id}`)
                            .then((res) => {
                                this.checkAll = [];
                                this.getData();
                                Swal.fire({
                                    icon: "success",
                                    title: `${this.$t("general.Deleted")}`,
                                    text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            })

                            .catch((err) => {
                                if (err.response.status == 400) {
                                    Swal.fire({
                                        icon: "error",
                                        title: `${this.$t("general.Error")}`,
                                        text: `${this.$t("general.CantDeleteRelation")}`,
                                    });
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
                });
            }
        },

        async getDocumentData() {
            this.isLoader = true;
            await adminApi
                .get(`/document/${this.document_id}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.document = l;
                    this.relatedDocuments = l.document_relateds;
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
         *  start  dynamicSortString
         */
        sortString(value) {
            return dynamicSortString(value);
        },
        checkRow(id) {
            if (!this.checkAll.includes(id)) {
                this.checkAll.push(id);
            } else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index, 1);
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
                    .get(`document-headers/${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.forEach((e) => {
                            this.Tooltip += `Created By: ${e.causer_type}; Event: ${e.event
                            }; Description: ${e.description} ;Created At: ${this.formatDate(
                                e.created_at
                            )} \n`;
                        });
                        $(`#tooltip-${id}`).tooltip();
                    })
                    .catch((err) => {
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                        });
                    });
            }
        },
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
                if (dl) {
                    XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' });
                } else {
                    XLSX.writeFile(wb, fn || ((this.document.name + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },

        /**
         *  reset Modal (Search Filter)
         */
        resetModalHiddenSearchFilter() {
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
            this.$bvModal.hide(`searchFilter`);
        },
        /**
         *  hidden Modal (Search Filter)
         */
        async resetModalSearchFilter() {
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset()
            });
            this.errors = {};
        },
    }
};
</script>

<template>
    <div>
        <template v-if="document && document.document_detail_type == 'board_rent'">
            <CreateOrUpdatePanel :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :id="'create'" @created="getData()" />
        </template>
        <template v-if="document && document.document_detail_type == 'booking' && document_id == 34">
            <CreateOrUpdateCheckout :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :id="'create'" @created="getData()" />
        </template>
        <template v-if="document && document.document_detail_type == 'booking' && document_id == 40">
            <CreateOrUpdateMaintenance :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :id="'create'" @created="getData()" />
        </template>
        <template v-if="document && document.document_detail_type == 'booking' && document_id != 34 && document_id != 40">
            <CreateOrUpdateBooking :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :id="'create'" @created="getData()" />
        </template>
        <template v-if="document && document.document_detail_type == 'normal'">
            <CreateOrUpdateItems :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :id="'create'" @created="getData()" />
        </template>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title" v-if="document">{{  $i18n.locale == "ar" ? $t('general.table') + ' ' + document.name : document.name_e+ ' ' +$t('general.table')  }}</h4>
                            <h4 class="header-title" v-else>{{ $t('general.table') }}</h4>
                            <div>
                                <b-button v-b-modal.searchFilter variant="primary" class="btn-sm mx-1 font-weight-bold">
                                    {{ $t("general.searchFilter") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                            </div>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting" value="date" class="mb-1">{{ $t('general.Date') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="customer_id" class="mb-1" v-if="document_id != 40">{{ $t('general.documentCustomer') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="employee_id" class="mb-1">{{document && document.document_detail_type == 'booking' ? document_id == 40 ? $t('general.employee') : $t('general.receptionist') : $t('general.documentSalesmen') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="branch_id" class="mb-1">{{ $t('general.Branch') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="prefix" class="mb-1">{{ $t('general.serial_number') }}</b-form-checkbox>
                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                </div>

                                <div class="d-inline-block position-relative" style="width: 77%">
                                    <span
                                        :class="['search-custom position-absolute', $i18n.locale == 'ar' ? 'search-custom-ar' : '',]">
                                        <i class="fe-search"></i>
                                    </span>
                                    <input class="form-control" style="display: block; width: 93%; padding-top: 3px"
                                           type="text" v-model.trim="search" :placeholder="`${$t('general.Search')}...`" />
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <b-button v-b-modal.create variant="primary" class="btn-sm mx-1 font-weight-bold">
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printReservation'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <button class="custom-btn-dowonload" @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                            v-if="checkAll.length == 1 && document_id != 34">
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length > 1"
                                            @click.prevent="deleteScreenButton(checkAll)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length == 1"
                                            @click.prevent="deleteScreenButton(checkAll[0])">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!--  end one delete  -->
                                </div>
                            </div>
                            <div class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end">
                                <div>
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
                                        <b-form-checkbox v-model="setting.date" class="mb-1">{{ $t('general.Date') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="setting.customer_id" class="mb-1" v-if="document_id != 40">{{$t('general.documentCustomer') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="setting.salesman_id" class="mb-1">{{ document && document.document_detail_type == 'booking' ? document_id == 40 ? $t('general.employee') : $t('general.receptionist') : $t('general.documentSalesmen') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="setting.branch_id" class="mb-1">{{ $t('general.Branch') }}</b-form-checkbox>
                                        <b-form-checkbox v-model="setting.serial_id" class="mb-1"> {{ $t('general.serial_number') }}</b-form-checkbox>
                                        <div class="d-flex justify-content-end">
                                            <a href="javascript:void(0)" class="btn btn-primary btn-sm">{{
                                                $t("general.Apply")
                                                }}</a>
                                        </div>
                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom">
                                        <div class="d-inline-block" style="font-size: 15px">
                                            {{ reservationsPagination.from }}-{{ reservationsPagination.to }}
                                            /
                                            {{ reservationsPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a href="javascript:void(0)" :style="{
                                                'pointer-events':
                                                    reservationsPagination.current_page == 1 ? 'none' : '',
                                            }" @click.prevent="getData(reservationsPagination.current_page - 1)">
                                                <span>&lt;</span>
                                            </a>
                                            <input type="text" @keyup.enter="getDataCurrentPage()" v-model="current_page"
                                                   class="pagination-current-page" />
                                            <a href="javascript:void(0)" :style="{
                                                'pointer-events':
                                                    reservationsPagination.last_page ==
                                                        reservationsPagination.current_page
                                                        ? 'none'
                                                        : '',
                                            }" @click.prevent="getData(reservationsPagination.current_page + 1)">
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!--  search Filter   -->
                        <b-modal
                            id="searchFilter"
                            :title="$t('general.Search')"
                            title-class="font-18"
                            body-class="p-4"
                            size="lg"
                            :hide-footer="true"
                            @show="resetModalSearchFilter"
                            @hidden="resetModalHiddenSearchFilter"
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

                                    <b-button variant="danger" type="button" @click.prevent="resetModalHiddenSearchFilter">
                                        {{ $t('general.Cancel') }}
                                    </b-button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.fromDate') }}
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="searchFilter.start_date"
                                                format="YYYY-MM-DD"
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
                                                {{ $t('general.toDate') }}
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="searchFilter.end_date"
                                                format="YYYY-MM-DD"
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.fromSerialNumber') }}
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                v-model="searchFilter.start_serial_number"
                                            />
                                            <template v-if="errors.start_serial_number">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.start_serial_number"
                                                              :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.toSerialNumber') }}
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                v-model="searchFilter.end_serial_number"
                                            />
                                            <template v-if="errors.end_serial_number">
                                                <ErrorMessage v-for="(errorMessage,index) in errors.end_serial_number"
                                                              :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label
                                                class="control-label">{{ $t('general.displayRelatedDocument') }}
                                            </label>
                                            <select
                                                class="custom-select"
                                                v-model="searchFilter.is_related_document"
                                            >
                                                <option value="null">{{ $t('general.all') }}</option>
                                                <option value="1">{{ $t('general.Yes') }}</option>
                                                <option value="2">{{ $t('general.No') }}</option>
                                            </select>

                                            <template v-if="errors.is_related_document">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.is_related_document"
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
                        <!--  /search Filter   -->

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table" id="printReservation">
                                <thead>
                                <tr>
                                    <th scope="col" style="width: 0" v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control">
                                            <input class="form-check-input" type="checkbox" v-model="isCheckAll"
                                                   style="width: 17px; height: 17px" />
                                        </div>
                                    </th>
                                    <th v-if="setting.date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Date') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"  @click="reservations.sort(sortString( 'date'))"></i>
                                                <i class="fas fa-arrow-down" @click="reservations.sort(sortString('-date'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.customer_id && document_id != 40">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.documentCustomer') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="reservations.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="reservations.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.salesman_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ document && document.document_detail_type == 'booking' ? document_id == 40 ? $t('general.employee') : $t('general.receptionist') : $t('general.documentSalesmen') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="reservations.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="reservations.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.branch_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.Branch') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="reservations.sort(sortString($i18n.locale == 'ar' ? 'name' : 'name_e'))"></i>
                                                <i class="fas fa-arrow-down" @click="reservations.sort(sortString($i18n.locale == 'ar' ? '-name' : '-name_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.serial_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t('general.serial_number') }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"  @click="reservations.sort(sortString('prefix'))"></i>
                                                <i class="fas fa-arrow-down" @click="reservations.sort(sortString('-prefix'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t("general.Action") }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="reservations.length > 0">
                                <tr @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="document_id != 34 ? $bvModal.show(`${data.id+''}`) : $bvModal.show(`${'printStatement'+' '+data.id}`)"
                                    v-for="(data, index) in reservations" :key="data.id" class="body-tr-custom">
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control" style="min-height: 1.9em">
                                            <input style="width: 17px; height: 17px" class="form-check-input"
                                                   type="checkbox" :value="data.id" v-model="checkAll" />
                                        </div>
                                    </td>
                                    <td v-if="setting.date">
                                        <h5 class="m-0 font-weight-normal">
                                            {{ data.date }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.customer_id && document_id != 40">
                                        <h5 class="m-0 font-weight-normal">
                                            {{$i18n.locale == "ar" ? data.customer.name : data.customer.name_e}}
                                        </h5>
                                    </td>
                                    <td v-if="setting.salesman_id">
                                        <h5 class="m-0 font-weight-normal">
                                            {{ $i18n.locale == "ar" ? data.employee.name : data.employee.name_e }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.branch_id">
                                        <h5 class="m-0 font-weight-normal">
                                            {{data.branch? $i18n.locale == "ar" ? data.branch.name : data.branch.name_e : ''}}
                                        </h5>
                                    </td>
                                    <td v-if="setting.serial_id">
                                        <h5 class="m-0 font-weight-normal">
                                            {{ data.prefix }}
                                        </h5>
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                {{ $t("general.commands") }}
                                                <i class="fas fa-angle-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-custom">
                                                <a class="dropdown-item" href="#" v-if="document_id != 34"
                                                   @click="$bvModal.show(`${data.id}`)">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black">
                                                        <span>{{ $t("general.edit") }}</span>
                                                        <i class="mdi mdi-square-edit-outline text-info"></i>
                                                    </div>
                                                </a>
                                                <a class="dropdown-item" href="#" v-else
                                                   @click="$bvModal.show(`${'printStatement'+' '+data.id}`)">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black">
                                                        <span>{{ $t("general.show") }}</span>
                                                        <i class="fas fa-eye text-info"></i>
                                                    </div>
                                                </a>
                                                <a class="dropdown-item text-black" href="#"
                                                   @click.prevent="deleteScreenButton(data.id)">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black">
                                                        <span>{{ $t("general.delete") }}</span>
                                                        <i class="fas fa-times text-danger"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div style="display:none;">
                                            <template v-if="document && document.document_detail_type == 'booking'  && data.id && document_id == 34">
                                                <AccountStatementPrint :id="'printStatement'+' '+data.id" :document_row_id="data.id" />
                                            </template>
                                        </div>

                                        <div style="display:none;">
                                            <template v-if="document && document.document_detail_type == 'board_rent'  && data.id">
                                                <CreateOrUpdatePanel :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :dataRow="data" :id="data.id+''" @created="getData()" />
                                            </template>
                                        </div>
                                        <div style="display:none;">
                                            <template v-if="document && document.document_detail_type == 'booking' && data.id && document_id == 34">
                                                <CreateOrUpdateCheckout :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :dataRow="data" :id="data.id+''" @created="getData()" />
                                            </template>
                                        </div>
                                        <div style="display:none;">
                                            <template v-if="document && document.document_detail_type == 'booking' && data.id && document_id == 40">
                                                <CreateOrUpdateMaintenance :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :dataRow="data" :id="data.id+''" @created="getData()" />
                                            </template>
                                        </div>
                                        <div style="display:none;">
                                            <template v-if="document && document.document_detail_type == 'booking'  && data.id && document_id != 34 && document_id != 40">
                                                <CreateOrUpdateBooking :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :dataRow="data" :id="data.id+''" @created="getData()" />
                                            </template>
                                        </div>
                                        <div style="display:none;">
                                            <template v-if="document && document.document_detail_type == 'normal'  && data.id">
                                                <CreateOrUpdateItems :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :dataRow="data" :id="data.id+''" @created="getData()" />
                                            </template>
                                        </div>
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <button @mousemove="log(data.id)" @mouseover="log(data.id)" type="button"
                                                class="btn" :id="`tooltip-${data.id}`"
                                                :data-placement="$i18n.locale == 'en' ? 'left' : 'right'" :title="Tooltip">
                                            <i class="fe-info" style="font-size: 22px"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="8">
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
    </div>
</template>

<style scoped>
.custom-panel-quotation{
    background-color: #81afca !important;
    color: lemonchiffon;
    font-size: 16px;
    border: none;
}
.page-content {
    width: 100%;
}

.total {
    color: #343a40 !important;
    font-weight: bold;
}

.text-secondary-d1 {
    color: #728299 !important;
}

.page-header {
    margin: 0 0 1rem;
    padding-bottom: 1rem;
    padding-top: .5rem;
    border-bottom: 1px dotted #e2e2e2;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -ms-flex-align: center;
    align-items: center;
}

.page-title {
    padding: 0;
    margin: 0;
    font-size: 1.75rem;
    font-weight: 300;
}

.brc-default-l1 {
    border-color: #dce9f0 !important;
}

.ml-n1,
.mx-n1 {
    margin-left: -.25rem !important;
}

.mr-n1,
.mx-n1 {
    margin-right: -.25rem !important;
}

.mb-4,
.my-4 {
    margin-bottom: 1.5rem !important;
}

hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
    border-top: 1px solid rgba(0, 0, 0, .1);
}

.text-grey-m2 {
    color: #888a8d !important;
}

.text-success-m2 {
    color: #86bd68 !important;
}

.font-bolder,
.text-600 {
    font-weight: 600 !important;
}

.text-110 {
    font-size: 110% !important;
}

.text-blue {
    color: #478fcc !important;
}

.pb-25,
.py-25 {
    padding-bottom: .75rem !important;
}

.pt-25,
.py-25 {
    padding-top: .75rem !important;
}

.bgc-default-tp1 {
    background-color: rgba(121, 169, 197, .92) !important;
}

.bgc-default-l4,
.bgc-h-default-l4:hover {
    background-color: #f3f8fa !important;
}

.page-header .page-tools {
    -ms-flex-item-align: end;
    align-self: flex-end;
}

.btn-light {
    color: #757984;
    background-color: #f5f6f9;
    border-color: #dddfe4;
}

.w-2 {
    width: 1rem;
}

.text-120 {
    font-size: 120% !important;
}

.text-primary-m1 {
    color: #4087d4 !important;
}

.text-danger-m1 {
    color: #dd4949 !important;
}

.text-blue-m2 {
    color: #68a3d5 !important;
}

.text-150 {
    font-size: 150% !important;
}

.text-60 {
    font-size: 60% !important;
}

.text-grey-m1 {
    color: #7b7d81 !important;
}

.align-bottom {
    vertical-align: bottom !important;
}

.face {
    display: inline-block;
    text-align: center;
    margin: 0 5px;
}

.face .face-name {
    background-color: #6dc6f5;
    padding: 0px 8px;
    font-size: 16px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 7px;
    display: block;
}
.row-danger {
    background-color:#f6a9a9 !important;
}
</style>
