<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import adminApi from "../../../../api/adminAxios";
import Switches from "vue-switches";
import Multiselect from "vue-multiselect";
import permissionGuard from "../../../../helper/permission";

import {
    required,
    minLength,
    maxLength,
    integer,
    requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../../components/widgets/errorMessage";
import loader from "../../../../components/general/loader";
import alphaArabic from "../../../../helper/alphaArabic";
import alphaEnglish from "../../../../helper/alphaEnglish";
import {
    dynamicSortString,
    dynamicSortNumber,
} from "../../../../helper/tableSort";
import translation from "../../../../helper/mixin/translation-mixin";
import { formatDateOnly } from "../../../../helper/startDate";
import { arabicValue, englishValue } from "../../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "HouseKeeping",
        meta: [{ name: "description", content: "HouseKeeping" }],
    },
    mixins: [translation],
    components: {
        Multiselect,
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
    },
    data() {
        return {
            member_type_id: null,
            member_types: [],
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
                name: true,
                name_e: true,
            },
            is_disabled: false,
            filterSetting: ["name"],
            printLoading: true,
            printObj: {
                id: "printData",
            },
            total: 0
        };
    },
    validations: {},
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
        this.getMemberTypes();
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "House Keeping Daily", "all payer member club");
        });
    },
    methods: {
        formatDate(value) {
            return formatDateOnly(value);
        },
        getData(page = 1) {
            this.isLoader = true;
            let filter = "";
            for (let i = 0; i < this.filterSetting.length; ++i) {
                filter += `columns[${i}]=${this.filterSetting[i]}&`;
            }
            this.total = 0;
            adminApi
                .get(
                    `/booking/report/check-in?page=${page}&per_page=${this.per_page}&branch_id=${this.member_type_id}`
                )
                .then((res) => {
                    let l = res.data;
                    this.items = l.data;
                    // this.itemsPagination = l.pagination;
                    // this.current_page = l.pagination.current_page;
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
                this.current_page <= this.itemsPagination.last_page &&
                this.current_page != this.itemsPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let filter = "";
                for (let i = 0; i < this.filterSetting.length; ++i) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `/booking/report/house-keeping?page=${this.current_page}&per_page=${this.per_page}&branch_id=${this.member_type_id}`
                    )
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
        sortString(value) {
            return dynamicSortString(value);
        },
        SortNumber(value) {
            return dynamicSortNumber(value);
        },
        checkRow(id) {
            if (!this.checkAll.includes(id)) {
                this.checkAll.push(id);
            } else {
                let index = this.checkAll.indexOf(id);
                this.checkAll.splice(index, 1);
            }
        },
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
                if (dl) {
                    XLSX.write(wb, { bookType: type, bookSST: true, type: "base64" });
                } else {
                    XLSX.writeFile(
                        wb,
                        fn || ("Branch" + "." || "SheetJSTableExport.") + (type || "xlsx")
                    );
                }
                this.enabled3 = true;
            }, 100);
        },
        getMemberTypes() {
            adminApi
                .get(`/branches?notParent=1`)
                .then((res) => {
                    this.member_types = res.data.data;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })

        },
        getVoucherHeaders(items){
            let sumItem = 0,didi= 0;

            if(items.length > 0){
                sumItem = items.reduce((n, {amount}) => n + amount, 0);
                didi += sumItem;
                this.total = didi;
                return sumItem;
            }
            return sumItem;
        }
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">
                                {{ $t("general.dailyCheckedIn") }}
                            </h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <!-- <b-dropdown
                                      variant="primary"
                                      :text="$t('general.searchSetting')"
                                      ref="dropdown"
                                      class="btn-block setting-search"
                                    >
                                      <b-form-checkbox
                                        v-model="filterSetting"
                                        value="id"
                                        class="mb-1"
                                      >
                                        {{ $t("general.id") }}
                                      </b-form-checkbox>
                                      <b-form-checkbox
                                        v-model="filterSetting"
                                        value="path"
                                        class="mb-1"
                                      >
                                        {{ $t("general.path") }}
                                      </b-form-checkbox>
                                      <b-form-checkbox
                                        v-model="filterSetting"
                                        value="created_at"
                                        class="mb-1"
                                      >
                                        {{ $t("general.created_at") }}
                                      </b-form-checkbox>
                                    </b-dropdown> -->
                                    <!-- Basic dropdown -->
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

                        <div
                            class="row justify-content-between align-items-center mb-2 px-1"
                        >
                            <div class="col-md-3 d-flex align-items-center mb-1 mt-2 mb-xl-0">
                                <div style="width: 100%">
                                    <multiselect
                                        @input="getData(1)"
                                        v-model="member_type_id"
                                        :options="member_types.map((type) => type.id)"
                                        :custom-label="
                              (opt) => member_types.find((x) => x.id == opt)?
                                $i18n.locale == 'ar'
                                  ? member_types.find((x) => x.id == opt).name
                                  : member_types.find((x) => x.id == opt).name_e: null
                            "
                                    >
                                    </multiselect>
                                </div>
                                <!-- start create and printer -->
                                <!-- <b-button
                                  v-b-modal.progress
                                  variant="primary"
                                  class="btn-sm mx-1 font-weight-bold"
                                >
                                  {{ $t("general.Create") }}
                                  <i class="fas fa-plus"></i>
                                </b-button> -->
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
                                    <button
                                        class="custom-btn-dowonload"
                                        @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                        v-if="checkAll.length == 1"
                                    >
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                </div>
                                <!-- end create and printer -->
                            </div>
                            <div
                                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end"
                            >
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
                                            :html="`${$t(
                        'general.setting'
                      )} <i class='fe-settings'></i>`"
                                            ref="dropdown"
                                            class="dropdown-custom-ali"
                                        >
                                            <b-form-checkbox v-model="setting.name" class="mb-1">
                                                {{ getCompanyKey("member") }}
                                            </b-form-checkbox>
                                            <div class="d-flex justify-content-end">
                                                <a
                                                    href="javascript:void(0)"
                                                    class="btn btn-primary btn-sm"
                                                >Apply</a
                                                >
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>
                                    <!-- end filter and setting -->

                                    <!-- start Pagination -->
                                    <div
                                        v-if="0"
                                        class="d-inline-flex align-items-center pagination-custom"
                                    >
                                        <div class="d-inline-block" style="font-size: 13px">
                                            {{ itemsPagination.from }}-{{ itemsPagination.to }} /
                                            {{ itemsPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{
                          'pointer-events':
                            itemsPagination.current_page == 1 ? 'none' : '',
                        }"
                                                @click.prevent="
                          getData(itemsPagination.current_page - 1)
                        "
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
                            itemsPagination.last_page ==
                            itemsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                                                @click.prevent="
                          getData(itemsPagination.current_page + 1)
                        "
                                            >
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!-- start .table-responsive-->
                        <div ref="exportable_table" id="printData">
                            <div class="head-branch text-center">
                                <h2>
                                    DIVONA HOTAL
                                    <span v-if="member_types.find(el => el.id == member_type_id)">
                                        <template v-if="member_types.find(el => el.id == member_type_id).media">
                                            <img :src="member_types.find(el => el.id == member_type_id).media[0].url" />
                                        </template>
                                    </span>
                                    فندق ديفونا
                                </h2>
                                <p class="font-weight-bold">- DAILY CHECKED-IN LIST REPORT -</p>
                            </div>

                            <div
                                class="table-responsive mb-3 custom-table-theme position-relative"
                            >
                                <!--       start loader       -->
                                <loader size="large" v-if="isLoader" />
                                <!--       end loader       -->
                                <table
                                    class="table table-borderless table-hover table-centered m-0"
                                >
                                    <thead>
                                    <tr>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{getCompanyKey("houseKeepingDaily_rome")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{getCompanyKey("dailyCheckedIn_guest_name")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{getCompanyKey("dailyCheckedIn_nationality")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{getCompanyKey("dailyCheckedIn_pax")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{getCompanyKey("dailyCheckedIn_type")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{getCompanyKey("dailyCheckedIn_Arrival")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{getCompanyKey("dailyCheckedIn_departure")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{getCompanyKey("dailyCheckedIn_totalDays")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{getCompanyKey("dailyCheckedIn_discount_on_rate")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{getCompanyKey("dailyCheckedIn_accomodationNetRate")}}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{getCompanyKey("dailyCheckedIn_payments")}}</span>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="items.length > 0">
                                    <tr
                                        @click.capture="checkRow(data.id)"
                                        v-for="(data, index) in items"
                                        :key="data.id"
                                        class="body-tr-custom"
                                    >
                                        <td>
                                            {{
                                                data.document_header_details.length > 0?
                                                    data.document_header_details[0].unit ?
                                                        data.document_header_details[0].unit.code
                                                        : '-'
                                                : '-'
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                data.customer ?
                                                    $i18n.locale == 'ar'? data.customer.name : data.customer.name_e
                                                : '-'
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                data.customer ?
                                                    data.customer.nationality
                                                    : '-'
                                            }}
                                        </td>
                                        <td>{{ data.attendans_num }}</td>
                                        <td>
                                            {{
                                                data.document_header_details?
                                                    data.document_header_details[0] ?
                                                        data.document_header_details[0].category_booking
                                                        : '-'
                                                    : '-'
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                data.document_header_details?
                                                    data.document_header_details[0] ?
                                                        data.document_header_details[0].date_from
                                                        : '-'
                                                    : '-'
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                data.document_header_details?
                                                    data.document_header_details[0] ?
                                                        data.document_header_details[0].date_to
                                                        : '-'
                                                    : '-'
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                data.document_header_details?
                                                    data.document_header_details[0] ?
                                                        data.document_header_details[0].rent_days
                                                        : '-'
                                                    : '-'
                                            }}
                                        </td>
                                        <td>{{data.invoice_discount }}</td>
                                        <td>{{data.net_invoice }}</td>
                                        <td>
                                            {{
                                                data.customer ?
                                                    getVoucherHeaders(data.customer.voucher_headers)
                                                    : '0'
                                            }}
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tbody v-else>
                                    <tr>
                                        <th class="text-center" colspan="15">
                                            {{ $t("general.notDataFound") }}
                                        </th>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>

                            <div class="row justify-content-end">
                                <div class="col-2">{{ $t('general.totalPayment') }}: {{ total }}</div>
                            </div>
                        </div>
                        <!-- end .table-responsive-->
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
.signature {
    display: none;
}
.head-branch {
    display: none;
}
@media print {
    .head-branch {
        margin-top: 50px;
        display: block;
    }
    .head-branch h2{
        text-decoration: underline;
    }
    .head-branch img{
        width: 100px;
        height: 100px;
    }
    .head-branch span {
        display: inline-block;
        position: relative;
        top: -49px;
    }
    .do-not-print {
        display: none;
    }

    .arrow-sort {
        display: none;
    }
    table thead tr th {
        color: #000;
        border: 1px solid #000;
    }
    table tbody tr td {
        color: #000;
        border: 1px solid #000;
    }
    hr {
        transform: rotate(90deg);
        background-color: #0000008a;
        border: 1px solid #4444449c;
    }
    .custom-table-theme thead {
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
    }
    .custom-table-theme tbody {
        border: 1px solid #000;
    }
    .signature {
        display: block;
    }
    .signature h4 {
        text-decoration: underline;
        margin: 3px;
    }
}
</style>
