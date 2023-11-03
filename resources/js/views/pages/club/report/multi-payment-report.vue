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
import PrintRenewal from "../print/print-renewal";

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
        PrintRenewal
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "multi payment report", "all multi payment report");
    });

    },
    data() {
        return {
            per_page: 50,
            search: '',
            dataInv:"",
            debounce: {},
            installmentStatusPagination: {},
            installmentStatus: [],
            sponsors: [],
            isLoader: false,
            create: {
                sponsor_id: null,
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
                serial_number: true,
                cm_member_id: true,
                document_no: true,
                date: true,
                serial_id: true,
                year: true,
                amount: true,
            },
            filterSetting: [
                "cm_member_id",
                "document_no",
                "serial_id",
                "amount",
                "prefix",
                "year",
            ],
            Tooltip: '',
            mouseEnter: null,
        }
    },
    validations: {
        create: {
            sponsor_id: {required},
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

                let start_date = this.create.start_date;
                let parts_start_date = start_date.split("-");
                let converted_start_date = parts_start_date[2] + "-" + parts_start_date[1] + "-" + parts_start_date[0];

                let end_date = this.create.end_date;
                let parts_end_date = end_date.split("-");
                let converted_end_date = parts_end_date[2] + "-" + parts_end_date[1] + "-" + parts_end_date[0];

                let _filterSetting = [...this.filterSetting];
                let index = this.filterSetting.indexOf("cm_member_id");
                if (index > -1) {
                    _filterSetting[index] ="member.full_name";
                }
                index = this.filterSetting.indexOf("serial_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "serial.name" : "serial.name_e";
                }
                let filter = "";
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }

                adminApi.get(`/club-members/transactions?sponsor_id=${this.create.sponsor_id??''}&start_date=${converted_start_date}&end_date=${converted_end_date}&page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
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

                    let start_date = this.create.start_date;
                    let parts_start_date = start_date.split("-");
                    let converted_start_date = parts_start_date[2] + "-" + parts_start_date[1] + "-" + parts_start_date[0];

                    let end_date = this.create.end_date;
                    let parts_end_date = end_date.split("-");
                    let converted_end_date = parts_end_date[2] + "-" + parts_end_date[1] + "-" + parts_end_date[0];

                    let _filterSetting = [...this.filterSetting];
                    let index = this.filterSetting.indexOf("cm_member_id");
                    if (index > -1) {
                        _filterSetting[index] ="member.full_name";
                    }
                    index = this.filterSetting.indexOf("serial_id");
                    if (index > -1) {
                        _filterSetting[index] =
                            this.$i18n.locale == "ar" ? "serial.name" : "serial.name_e";
                    }
                    let filter = "";
                    for (let i = 0; i < _filterSetting.length; ++i) {
                        filter += `columns[${i}]=${_filterSetting[i]}&`;
                    }
                    adminApi.get(`/club-members/transactions?sponsor_id=${this.create.sponsor_id??''}&start_date=${converted_start_date}&end_date=${converted_end_date}&page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`)
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
         *  get Sponsors
         */
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
            await this.getSponsors();
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
                    XLSX.writeFile(wb, fn || (('multi Payment Report' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },
        printInv(data){
            this.dataInv = data
        },
        changeDate()
        {
            console.log(123)
            if (this.create.start_date) {
                const parts = this.create.start_date.split('-');
                const day = parts[0].padStart(2, '0');
                const month = parts[1].padStart(2, '0');
                const year = parts[2];

                this.create.start_date = `${day}-${month}-${year}`;
            } else {
                this.create.start_date = '';
            }
        }
    },
};
</script>

<template>
    <Layout>
        <PageHeader/>
        <div v-if="dataInv" style="display:none;">
            <PrintRenewal :data_row="dataInv"/>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title"> {{ $t('general.GroupPaymentReport') }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">

                                <div class="d-inline-block" style="width: 22.2%;">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting" value="cm_member_id"
                                                         class="mb-1"
                                        >{{ getCompanyKey("member") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="document_no"
                                                         class="mb-1"
                                        >{{ $t("general.DocumentNumber") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting" value="serial_id"
                                                         class="mb-1"
                                        >{{ $t("general.serialName") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting"
                                                         value="amount" class="mb-1"
                                        >{{ getCompanyKey("subscription_amount") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="filterSetting"
                                                         value="year" class="mb-1"
                                        >{{ $t("general.ForAYear") }}
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
                                            <b-form-checkbox v-model="setting.cm_member_id" class="mb-1">
                                                {{ getCompanyKey("member") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.serial_number" class="mb-1">
                                                {{ $t("general.serial_number") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.document_no" class="mb-1">
                                                {{ $t("general.DocumentNumber") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.date" class="mb-1">
                                                {{ $t("general.PaymentDate") }}
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
                                                {{ getCompanyKey("sponsor") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <multiselect v-model="create.sponsor_id"
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
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ $t('general.fromDate') }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <date-picker
                                                type="date"
                                                v-model="$v.create.start_date.$model"
                                                placeholder="DD-MM-YYYY"
                                                format="DD-MM-YYYY"
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
                                                placeholder="DD-MM-YYYY"
                                                format="DD-MM-YYYY"
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
                                    <th v-if="setting.cm_member_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("member") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="transactions.sort(sortString('full_name'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="transactions.sort(sortString('-full_name'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.serial_number">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.serial_number") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up" @click="transactions.sort(sortString('prefix'))"></i>
                                                <i class="fas fa-arrow-down" @click="transactions.sort(sortString('-prefix'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.document_no">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.DocumentNumber") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="transactions.sort(sortString('document_no'))"
                                                ></i>
                                                <i class="fas fa-arrow-down"
                                                   @click=" transactions.sort(sortString('-document_no'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.date">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.PaymentDate") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="transactions.sort(sortString('date'))"
                                                ></i>
                                                <i class="fas fa-arrow-down"
                                                   @click=" transactions.sort(sortString('-date'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.serial_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.serialName") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="
                                                      transactions.sort(
                                                        sortString(($i18n.locale = 'ar' ? 'name' : 'name_e'))
                                                      )
                                                    "
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="
                                                      transactions.sort(
                                                        sortString(($i18n.locale = 'ar' ? '-name' : '-name_e'))
                                                      )
                                                    "
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.amount">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("subscription_amount") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="transactions.sort(sortString('amount'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="transactions.sort(sortString('-amount'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.year">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ $t("general.ForAYear") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="transactions.sort(sortString('year'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="transactions.sort(sortString('-year'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t("general.Action") }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody v-if="installmentStatus.length > 0">
                                <tr
                                    v-for="(data,index) in installmentStatus"
                                    :key="data.id"
                                    class="body-tr-custom"
                                >

                                    <td v-if="setting.cm_member_id">
                                        <h5 class="m-0 font-weight-normal">
                                            {{
                                                data.member ? data.member.first_name +' '+ data.member.second_name +' '+ data.member.third_name +' '+ data.member.last_name:''
                                            }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.serial_number">
                                        <h5 class="m-0 font-weight-normal">
                                            {{ data.prefix }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.document_no">
                                        <h5 class="m-0 font-weight-normal">{{ data.document_no }}</h5>
                                    </td>
                                    <td v-if="setting.date">
                                        <h5 class="m-0 font-weight-normal">{{ formatDate(data.date) }}</h5>
                                    </td>
                                    <td v-if="setting.serial_id">
                                        <h5 class="m-0 font-weight-normal">
                                            {{ data.serial ? $i18n.locale == 'ar' ? data.serial.name : data.serial.name : '---' }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.amount">
                                        <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>
                                    </td>
                                    <td v-if="setting.year">
                                        <h5 class="m-0 font-weight-normal">{{ data.year ? data.year : data.year_to}}</h5>
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="btn-group">
                                            <button
                                                type="button"
                                                class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                data-toggle="dropdown"
                                                aria-expanded="false"
                                            >
                                                {{ $t("general.commands") }}
                                                <i class="fas fa-angle-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-custom">
                                                <a class="dropdown-item"  v-print="'#printInv'" href="#" @click="printInv(data)" >
                                                    <div class="d-flex justify-content-between align-items-center text-black">
                                                        {{ $t("general.print") }}
                                                        <i class="fe-printer"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="8">{{ $t('general.notDataFound') }}</th>
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



