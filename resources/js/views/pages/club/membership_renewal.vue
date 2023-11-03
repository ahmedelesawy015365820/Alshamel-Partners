<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {
    required,
    minLength,
    maxLength,
    integer,
    numeric, decimal, minValue, between, requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import Multiselect from "vue-multiselect";
import { formatDateOnly, formatDateTime } from "../../../helper/startDate";
import translation from "../../../helper/mixin/translation-mixin";
import permissionGuard from "../../../helper/permission";
import DatePicker from "vue2-datepicker";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Membership Renewal",
        meta: [{ name: "description", content: "Membership Renewal" }],
    },
    mixins: [translation],
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
        DatePicker,
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "membership renewal", "all membership renewal club");
        });
    },
    data() {
        return {
            fields: [],
            per_page: 50,
            search: "",
            Tooltip: "",
            mouseEnter: null,
            debounce: {},
            membershipRenewalsPagination: {},
            membershipRenewals: [],
            enabled3: true,
            isLoader: false,
            create: {
                memberships_renewals: [
                    {
                        from: this.formatDate(new Date()),
                        to: this.formatDate(new Date()),
                        membership_availability: 0,
                        membership_cost: 0,
                        renewal_availability: 0,
                        renewal_cost: 0,
                    }
                ]
            },
            edit: {
                from: this.formatDate(new Date()),
                to: this.formatDate(new Date()),
                membership_availability: false,
                membership_cost: 0,
                renewal_availability: false,
                renewal_cost: 0,
            },
            errors: {},
            isCheckAll: false,
            checkAll: [],
            wallets: [],
            owners: [],
            current_page: 1,
            is_persentage: true,
            setting: {
                from: true,
                to: true,
                membership_availability: true,
                membership_cost: true,
                renewal_availability: true,
                renewal_cost: true,
            },
            is_disabled: false,
            filterSetting: [
                'membership_cost',
                'renewal_cost'
            ],
            printLoading: true,
            printObj: {
                id: "printWalletOwner",
            }
        };
    },
    validations: {
        create: {
            memberships_renewals: {
                required,
                $each: {
                    from: { required: requiredIf(function (model) {
                            return this.isRequired("from");
                        }) },
                    to: { required: requiredIf(function (model) {
                            return this.isRequired("to");
                        }) },
                    membership_availability: { required: requiredIf(function (model) {
                            return this.isRequired("membership_availability");
                        }) },
                    membership_cost: { required: requiredIf(function (model) {
                            return this.isRequired("membership_cost");
                        }) , decimal, minValue: minValue(0.01)},
                    renewal_availability: { required: requiredIf(function (model) {
                            return this.isRequired("renewal_availability");
                        }) },
                    renewal_cost: { required: requiredIf(function (model) {
                            return this.isRequired("renewal_cost");
                        }) , decimal, minValue: minValue(0.01)},
                }
            }
        },
        edit: {
            from: { required: requiredIf(function (model) {
                    return this.isRequired("from");
                }) },
            to: { required: requiredIf(function (model) {
                    return this.isRequired("to");
                }) },
            membership_availability: { required: requiredIf(function (model) {
                    return this.isRequired("membership_availability");
                }) },
            membership_cost: { required: requiredIf(function (model) {
                    return this.isRequired("membership_cost");
                }) , decimal, minValue: minValue(0.01)},
            renewal_availability: { required: requiredIf(function (model) {
                    return this.isRequired("renewal_availability");
                }) },
            renewal_cost: { required: requiredIf(function (model) {
                    return this.isRequired("renewal_cost");
                }) , decimal, minValue: minValue(0.01)},
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
                this.membershipRenewals.forEach((el) => {
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
        this.getCustomTableFields();
        this.getData();
    },
    methods: {
        getCustomTableFields() {
            adminApi
                .get(`/customTable/table-columns/cm_memberships_renewals`)
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
        getDay(){
            let days = [];
            for(let i = 1;i <= 31; ++i){
                i < 10? days.push(`0${i}`) : days.push(`${i}`);
            }
            return days;
        },
        getMonth(){
            let months = [];
            for(let i = 1;i <= 12; ++i){
                i < 10? months.push(`0${i}`) : months.push(`${i}`);
            }
            return months;
        },
        addNewField() {
            this.create.memberships_renewals.push({
                from: this.formatDate(new Date()),
                to: this.formatDate(new Date()),
                membership_availability: 0,
                membership_cost: 0,
                renewal_availability: 0,
                renewal_cost: 0,
            });
        },
        removeNewField(index) {
            if (this.create.memberships_renewals.length > 1) {
                this.create.memberships_renewals.splice(index, 1);
            }
        },
        /**
         *  start get Data module && pagination
         */
        async getData(page = 1) {
            this.isLoader = true;
            let filter = "";
            for (let i = 0; i < this.filterSetting.length; ++i) {
                filter += `columns[${i}]=${this.filterSetting[i]}&`;
            }
            await adminApi
                .get(
                    `/club-members/memberships-renewals?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.membershipRenewals = l.data;
                    this.membershipRenewalsPagination = l.pagination;
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
                this.current_page <= this.membershipRenewalsPagination.last_page &&
                this.current_page != this.membershipRenewalsPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let filter = "";
                for (let i = 0; i < this.filterSetting.length; ++i) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `/club-members/memberships-renewals?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.membershipRenewals = l.data;
                        this.membershipRenewalsPagination = l.pagination;
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
         *  end get Data module && pagination
         */
        /**
         *  start delete module
         */
        deleteModule(id, index) {
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
                            .post(`/club-members/memberships-renewals/bulk-delete`, { ids: id })
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
                            .delete(`/club-members/memberships-renewals/${id}`)
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
        /**
         *  end delete module
         */
        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.create = {
                memberships_renewals: [
                    {
                        from: this.formatDate(new Date()),
                        to: this.formatDate(new Date()),
                        membership_availability: 0,
                        membership_cost: 0,
                        renewal_availability: 0,
                        renewal_cost: 0,
                    }
                ]
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
            this.is_persentage = true;
            this.$bvModal.hide(`create`);
        },
        /**
         *  hidden Modal (create)
         */

        resetModal() {
            this.create = {
                memberships_renewals: [
                    {
                        from: this.formatDate(new Date()),
                        to: this.formatDate(new Date()),
                        membership_availability: 0,
                        membership_cost: 0,
                        renewal_availability: 0,
                        renewal_cost: 0,
                    }
                ]
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  create module
         */
        resetForm() {
            this.create = {
                memberships_renewals: [
                    {
                        from: this.formatDate(new Date()),
                        to: this.formatDate(new Date()),
                        membership_availability: 0,
                        membership_cost: 0,
                        renewal_availability: 0,
                        renewal_cost: 0,
                    }
                ]
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.is_persentage = true;
            this.errors = {};
        },

        AddSubmit() {
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .post(`/club-members/memberships-renewals`, {
                        'memberships_renewals': this.create.memberships_renewals ,
                        company_id: this.$store.getters["auth/company_id"],
                    })
                    .then((res) => {
                        this.is_disabled = true;
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
         *  edit module
         */
        editSubmit(id) {
            this.$v.edit.$touch();

            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                adminApi
                    .put(`/club-members/memberships-renewals/${id}`, this.edit )
                    .then((res) => {
                        this.$bvModal.hide(`modal-edit-${id}`);
                        this.getData();
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Editsuccessfully")}`,
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
         *   show Modal (edit)
         */
         resetModalEdit(id) {
            let module = this.membershipRenewals.find((e) => id == e.id);
            this.edit.membership_availability =  module.membership_availability;
            this.edit.membership_cost = module.membership_cost;
            this.edit.renewal_availability = module.renewal_availability;
            this.edit.renewal_cost = module.renewal_cost;
            this.edit.from = module.from;
            this.edit.to = module.to;
            this.errors = {};
        },
        resetModalHiddenEdit(id) {
            this.edit = {
                from: this.formatDate(new Date()),
                to: this.formatDate(new Date()),
                membership_availability: 0,
                membership_cost: 0,
                renewal_availability: 0,
                renewal_cost: 0
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
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
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`/club-members/memberships-renewals/logs/${id}`)
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
                    XLSX.writeFile(wb, fn || (('Wallet-Owner' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
        },
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
                            <h4 class="header-title">{{ $t("general.membership_renewalTable") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-model="filterSetting" v-if="isVisible('membership_cost')" value="membership_cost" class="mb-1">
                                            {{ getCompanyKey("membership_renewal_renewal_membership_cost") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('renewal_cost')" v-model="filterSetting" value="renewal_cost" class="mb-1">
                                            {{ getCompanyKey("membership_renewal_renewal_renewal_cost") }}
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
                                    <input class="form-control" style="display: block; width: 93%; padding-top: 3px" type="text"
                                           v-model.trim="search" :placeholder="`${$t('general.Search')}...`" />
                                </div>
                            </div>
                        </div>
                        <!-- end search -->

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <!-- start create and printer -->
                                <b-button v-b-modal.create v-if="isPermission('create membershipRenewal club')" variant="primary" class="btn-sm mx-1 font-weight-bold">
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printWalletOwner'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <button class="custom-btn-dowonload" @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                            v-if="checkAll.length == 1 && isPermission('update membershipRenewal club')">
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length > 1 && isPermission('delete membershipRenewal club')"
                                            @click.prevent="deleteModule(checkAll)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button class="custom-btn-dowonload" v-if="checkAll.length == 1 && isPermission('delete membershipRenewal club')"
                                            @click.prevent="deleteModule(checkAll[0])">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!--  end one delete  -->
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
                                        <b-dropdown variant="primary" :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                                                    ref="dropdown" class="dropdown-custom-ali">
                                            <b-form-checkbox v-model="setting.from" v-if="isVisible('from')" class="mb-1">
                                                {{ getCompanyKey("membership_renewal_from") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.to" v-if="isVisible('to')" class="mb-1">
                                                {{ getCompanyKey("membership_renewal_to") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.membership_availability" v-if="isVisible('membership_availability')" class="mb-1">
                                                {{ getCompanyKey("membership_renewal_membership_availability") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.membership_cost" v-if="isVisible('membership_cost')" class="mb-1">
                                                {{ getCompanyKey("membership_renewal_renewal_membership_cost") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.renewal_availability" v-if="isVisible('renewal_availability')" class="mb-1">
                                                {{ getCompanyKey("membership_renewal_renewal_availability") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox v-model="setting.renewal_cost" v-if="isVisible('renewal_cost')" class="mb-1">
                                                {{ getCompanyKey("membership_renewal_renewal_renewal_cost") }}
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
                                            {{ membershipRenewalsPagination.from }}-{{ membershipRenewalsPagination.to }}
                                            /
                                            {{ membershipRenewalsPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a href="javascript:void(0)" :style="{
                        'pointer-events':
                          membershipRenewalsPagination.current_page == 1 ? 'none' : '',
                      }" @click.prevent="getData(membershipRenewalsPagination.current_page - 1)">
                                                <span>&lt;</span>
                                            </a>
                                            <input type="text" @keyup.enter="getDataCurrentPage()" v-model="current_page"
                                                   class="pagination-current-page" />
                                            <a href="javascript:void(0)" :style="{
                        'pointer-events':
                          membershipRenewalsPagination.last_page ==
                            membershipRenewalsPagination.current_page
                            ? 'none'
                            : '',
                      }" @click.prevent="getData(membershipRenewalsPagination.current_page + 1)">
                                                <span>&gt;</span>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                </div>
                            </div>
                        </div>

                        <!--  create   -->
                        <b-modal id="create" :title="getCompanyKey('membership_renewal_create_form')" title-class="font-18"
                                 body-class="p-4 "  dialog-class="modal-full-width" :hide-footer="true" @show="resetModal" @hidden="resetModalHidden">
                            <form>
                                <div class="d-flex justify-content-end">
                                    <b-button variant="success" :disabled="!is_disabled" @click.prevent="resetForm" type="button"
                                              :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                                        {{ $t("general.AddNewRecord") }}
                                    </b-button>
                                    <template v-if="!is_disabled">
                                        <b-button variant="success" type="button" class="mx-1" v-if="!isLoader" @click.prevent="AddSubmit">
                                            {{ $t("general.Add") }}
                                        </b-button>

                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                        </b-button>
                                    </template>
                                    <!-- Emulate built in modal footer ok and cancel button actions -->

                                    <b-button variant="danger" type="button" @click.prevent="resetModalHidden">
                                        {{ $t("general.Cancel") }}
                                    </b-button>
                                </div>
                                <template v-for="(item, index) in create.memberships_renewals">
                                    <div class="row" :key="index">
                                        <div class="col-md-2" v-if="isVisible('from')">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ $t("general.from_date") }}
                                                </label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    placeholder="yyyy-mm-dd"
                                                    v-model="$v.create.memberships_renewals.$each[index].from.$model"
                                                >
                                                <template v-if="errors.from">
                                                    <ErrorMessage v-for="(errorMessage, index) in errors.from" :key="index">
                                                        {{ errorMessage }}
                                                    </ErrorMessage>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-md-2" v-if="isVisible('to')">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ $t("general.to_date") }}
                                                </label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    placeholder="yyyy-mm-dd"
                                                    v-model="$v.create.memberships_renewals.$each[index].to.$model"
                                                >
                                                <template v-if="errors.to">
                                                    <ErrorMessage v-for="(errorMessage, index) in errors.to" :key="index">
                                                        {{ errorMessage }}
                                                    </ErrorMessage>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-md-1" style="padding: 0 !important;" v-if="isVisible('membership_availability')">
                                            <div class="form-group">
                                                <label class="mr-2">
                                                    {{ getCompanyKey("membership_renewal_membership_availability") }}
                                                </label>
                                                <b-form-group>
                                                    <b-form-radio
                                                        class="d-inline-block"
                                                        v-model="$v.create.memberships_renewals.$each[index].membership_availability.$model"
                                                        :name="`membership-radios-${index}`"
                                                        value="1"
                                                    >{{ $t("general.Yes") }}</b-form-radio
                                                    >
                                                    <b-form-radio
                                                        class="d-inline-block"
                                                        v-model="$v.create.memberships_renewals.$each[index].membership_availability.$model"
                                                        :name="`membership-radios-${index}`"
                                                        value="0"
                                                    >{{ $t("general.No") }}</b-form-radio
                                                    >
                                                </b-form-group>
                                                <template v-if="errors.membership_availability">
                                                    <ErrorMessage
                                                        v-for="(errorMessage, index) in errors.membership_availability"
                                                        :key="index"
                                                    >{{ errorMessage }}
                                                    </ErrorMessage>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-md-2" v-if="isVisible('membership_cost')">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ getCompanyKey("membership_renewal_renewal_membership_cost") }}
                                                </label>
                                                <input type="number" step="0.01" class="form-control"
                                                       v-model="$v.create.memberships_renewals.$each[index].membership_cost.$model" :class="{
                                                        'is-invalid': $v.create.memberships_renewals.$each[index].membership_cost.$error || errors.percentage || !is_persentage,
                                                        'is-valid':
                                                          !$v.create.memberships_renewals.$each[index].membership_cost.$invalid && !errors.membership_cost && is_persentage,
                                                      }" />
                                                <template v-if="errors.membership_cost">
                                                    <ErrorMessage v-for="(errorMessage, index) in errors.percentage" :key="index">{{ errorMessage }}
                                                    </ErrorMessage>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-md-1" style="padding: 0 !important;" v-if="isVisible('renewal_availability')">
                                            <div class="form-group">
                                                <label class="mr-2">
                                                    {{ getCompanyKey("membership_renewal_renewal_availability") }}
                                                </label>
                                                <b-form-group>
                                                    <b-form-radio
                                                        class="d-inline-block"
                                                        v-model="$v.create.memberships_renewals.$each[index].renewal_availability.$model"
                                                        :name="`renewal-radios-${index}`"
                                                        value="1"
                                                    >{{ $t("general.Yes") }}</b-form-radio
                                                    >
                                                    <b-form-radio
                                                        class="d-inline-block"
                                                        v-model="$v.create.memberships_renewals.$each[index].renewal_availability.$model"
                                                        :name="`renewal-radios-${index}`"
                                                        value="0"
                                                    >{{ $t("general.No") }}</b-form-radio
                                                    >
                                                </b-form-group>
                                                <template v-if="errors.renewal_availability">
                                                    <ErrorMessage
                                                        v-for="(errorMessage, index) in errors.renewal_availability"
                                                        :key="index"
                                                    >{{ errorMessage }}
                                                    </ErrorMessage>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-md-2" v-if="isVisible('renewal_cost')">
                                            <div class="form-group">
                                                <label class="control-label">
                                                    {{ getCompanyKey("membership_renewal_renewal_renewal_cost") }}
                                                </label>
                                                <input type="number" step="0.01" class="form-control"
                                                       v-model="$v.create.memberships_renewals.$each[index].renewal_cost.$model" :class="{
                                                        'is-invalid': $v.create.memberships_renewals.$each[index].renewal_cost.$error || errors.renewal_cost,
                                                        'is-valid':
                                                          !$v.create.memberships_renewals.$each[index].renewal_cost.$invalid && !errors.membership_cost,
                                                      }" />
                                                <template v-if="errors.renewal_cost">
                                                    <ErrorMessage v-for="(errorMessage, index) in errors.renewal_cost" :key="index">{{ errorMessage }}
                                                    </ErrorMessage>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-md-2 p-0 pt-3">
                                            <button v-if="(create.memberships_renewals.length - 1) == index" type="button" @click.prevent="addNewField"
                                                    class="custom-btn-dowonload">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                            <button v-if="create.memberships_renewals.length > 1" type="button" @click.prevent="removeNewField(index)"
                                                    class="custom-btn-dowonload">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->

                            <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                                   id="printWalletOwner">
                                <thead>
                                <tr>
                                    <th scope="col" style="width: 0" v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control">
                                            <input class="form-check-input" type="checkbox" v-model="isCheckAll"
                                                   style="width: 17px; height: 17px" />
                                        </div>
                                    </th>
                                    <th v-if="setting.from && isVisible('from')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("membership_renewal_from") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.to && isVisible('to')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("membership_renewal_to") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.membership_availability && isVisible('membership_availability')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("membership_renewal_membership_availability") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.membership_cost && isVisible('membership_cost')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("membership_renewal_renewal_membership_cost") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.renewal_availability && isVisible('renewal_availability')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("membership_renewal_renewal_availability") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.renewal_cost && isVisible('renewal_cost')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("membership_renewal_renewal_renewal_cost") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t("general.Action") }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="membershipRenewals.length > 0">
                                <tr @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="isPermission('update membershipRenewal club')?
                                    $bvModal.show(`modal-edit-${data.id}`):false"
                                    v-for="(data, index) in membershipRenewals" :key="data.id" class="body-tr-custom">
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control" style="min-height: 1.9em">
                                            <input style="width: 17px; height: 17px" class="form-check-input" type="checkbox" :value="data.id"
                                                   v-model="checkAll" />
                                        </div>
                                    </td>
                                    <td v-if="setting.from && isVisible('from')">
                                        {{ data.from }}
                                    </td>
                                    <td v-if="setting.to && isVisible('to')">
                                        {{ data.to }}
                                    </td>
                                    <td v-if="setting.membership_availability && isVisible('membership_availability')">
                                        <span :class="[ data.membership_availability ? 'text-success' : 'text-danger', 'badge', ]">
                                            {{data.membership_availability ? `${$t("general.Active")}` : `${$t("general.Inactive")}`}}
                                        </span>
                                    </td>
                                    <td v-if="setting.membership_cost && isVisible('membership_cost')">
                                        {{ data.membership_cost }}
                                    </td>
                                    <td v-if="setting.renewal_availability && isVisible('renewal_availability')">
                                        <span :class="[ data.renewal_availability ? 'text-success' : 'text-danger', 'badge',]">
                                            {{ data.renewal_availability ? `${$t("general.Active")}` : `${$t("general.Inactive")}`}}
                                        </span>
                                    </td>
                                    <td v-if="setting.renewal_cost && isVisible('renewal_cost')">
                                        {{ data.renewal_cost }}
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm dropdown-toggle dropdown-coustom" data-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{ $t("general.commands") }}
                                                <i class="fas fa-angle-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-custom">
                                                <a class="dropdown-item" v-if="isPermission('update membershipRenewal club')" href="#" @click="$bvModal.show(`modal-edit-${data.id}`)">
                                                    <div class="d-flex justify-content-between align-items-center text-black">
                                                        <span>{{ $t("general.edit") }}</span>
                                                        <i class="mdi mdi-square-edit-outline text-info"></i>
                                                    </div>
                                                </a>
                                                <a class="dropdown-item text-black" v-if="isPermission('delete membershipRenewal club')" href="#" @click.prevent="deleteModule(data.id)">
                                                    <div class="d-flex justify-content-between align-items-center text-black">
                                                        <span>{{ $t("general.delete") }}</span>
                                                        <i class="fas fa-times text-danger"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>

                                        <!--  edit   -->
                                        <b-modal :id="`modal-edit-${data.id}`" :title="getCompanyKey('membership_renewal_edit_form')"
                                                 title-class="font-18" body-class="p-4" size="lg" :ref="`edit-${data.id}`" :hide-footer="true"
                                                 @show="resetModalEdit(data.id)" @hidden="resetModalHiddenEdit(data.id)">
                                            <form>
                                                <div class="d-flex justify-content-end">
                                                    <!-- Emulate built in modal footer ok and cancel button actions -->
                                                    <b-button variant="success" type="submit" class="mx-1" v-if="!isLoader"
                                                              @click.prevent="editSubmit(data.id)">
                                                        {{ $t("general.Edit") }}
                                                    </b-button>

                                                    <b-button variant="success" class="mx-1" disabled v-else>
                                                        <b-spinner small></b-spinner>
                                                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                    </b-button>

                                                    <b-button variant="danger" type="button"
                                                              @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)">
                                                        {{ $t("general.Cancel") }}
                                                    </b-button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6" v-if="isVisible('from')">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ $t("general.from_date") }}
                                                            </label>
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                placeholder="yyyy-mm-dd"
                                                                v-model="$v.edit.from.$model"
                                                            >
                                                            <template v-if="errors.from">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.from" :key="index">
                                                                    {{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" v-if="isVisible('to')">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ $t("general.to_date") }}
                                                            </label>
                                                            <input
                                                                type="text"
                                                                class="form-control"
                                                                placeholder="yyyy-mm-dd"
                                                                v-model="$v.edit.to.$model"
                                                            >
                                                            <template v-if="errors.to">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.to" :key="index">
                                                                    {{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" v-if="isVisible('membership_availability')">
                                                        <div class="form-group">
                                                            <label class="mr-2">
                                                                {{ getCompanyKey("membership_renewal_membership_availability") }}
                                                            </label>
                                                            <b-form-group>
                                                                <b-form-radio
                                                                    class="d-inline-block"
                                                                    v-model="$v.edit.membership_availability.$model"
                                                                    :name="`membership-radios-${index}`"
                                                                    value="1"
                                                                >{{ $t("general.Yes") }}</b-form-radio
                                                                >
                                                                <b-form-radio
                                                                    class="d-inline-block"
                                                                    v-model="$v.edit.membership_availability.$model"
                                                                    :name="`membership-radios-${index}`"
                                                                    value="0"
                                                                >{{ $t("general.No") }}</b-form-radio
                                                                >
                                                            </b-form-group>
                                                            <template v-if="errors.membership_availability">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.membership_availability"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" v-if="isVisible('membership_cost')">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("membership_renewal_renewal_membership_cost") }}
                                                            </label>
                                                            <input type="number" step="0.01" class="form-control"
                                                                   v-model="$v.edit.membership_cost.$model" :class="{
                                                        'is-invalid': $v.edit.membership_cost.$error || errors.percentage || !is_persentage,
                                                        'is-valid':
                                                          !$v.edit.membership_cost.$invalid && !errors.membership_cost && is_persentage,
                                                      }" />
                                                            <template v-if="errors.membership_cost">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.percentage" :key="index">{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" v-if="isVisible('renewal_availability')">
                                                        <div class="form-group">
                                                            <label class="mr-2">
                                                                {{ getCompanyKey("membership_renewal_renewal_availability") }}
                                                            </label>
                                                            <b-form-group>
                                                                <b-form-radio
                                                                    class="d-inline-block"
                                                                    v-model="$v.edit.renewal_availability.$model"
                                                                    :name="`renewal-radios-${index}`"
                                                                    value="1"
                                                                >{{ $t("general.Yes") }}</b-form-radio
                                                                >
                                                                <b-form-radio
                                                                    class="d-inline-block"
                                                                    v-model="$v.edit.renewal_availability.$model"
                                                                    :name="`renewal-radios-${index}`"
                                                                    value="0"
                                                                >{{ $t("general.No") }}</b-form-radio
                                                                >
                                                            </b-form-group>
                                                            <template v-if="errors.renewal_availability">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.renewal_availability"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" v-if="isVisible('renewal_cost')">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("membership_renewal_renewal_renewal_cost") }}
                                                            </label>
                                                            <input type="number" step="0.01" class="form-control"
                                                                   v-model="$v.edit.renewal_cost.$model" :class="{
                                                        'is-invalid': $v.edit.renewal_cost.$error || errors.renewal_cost,
                                                        'is-valid':
                                                          !$v.edit.renewal_cost.$invalid && !errors.membership_cost,
                                                      }" />
                                                            <template v-if="errors.renewal_cost">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.renewal_cost" :key="index">{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </b-modal>
                                        <!--  /edit   -->
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <button @mousemove="log(data.id)" @mouseover="log(data.id)" type="button" class="btn"
                                                :id="`tooltip-${data.id}`" :data-placement="$i18n.locale == 'en' ? 'left' : 'right'"
                                                :title="Tooltip">
                                            <i class="fe-info" style="font-size: 22px"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="10">
                                        {{ $t("general.notDataFound") }}
                                    </th>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- end .table-responsive -->
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
