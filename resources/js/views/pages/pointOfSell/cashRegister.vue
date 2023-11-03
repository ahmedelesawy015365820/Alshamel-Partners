<script>
import permissionGuard from "../../../helper/permission";
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength, integer, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import alphaArabic from "../../../helper/alphaArabic";
import alphaEnglish from "../../../helper/alphaEnglish";
import {dynamicSortString, dynamicSortNumber} from "../../../helper/tableSort";
import translation from "../../../helper/mixin/translation-mixin";
import {formatDateOnly} from "../../../helper/startDate";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import Multiselect from "vue-multiselect";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Cash Register",
        meta: [{name: "Cash Register", content: "Cash Register"}],
    },
    mixins: [translation],
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect
    },
    data() {
        return {
            fields: [],
            per_page: 50,
            search: "",
            debounce: {},
            enabled3: true,
            unitStatusPagination: {},
            unitStatuses: [],
            branches: [],
            isLoader: false,
            Tooltip: "",
            mouseEnter: "",
            create: {
                title: "",
                title_e: "",
                branch_id: null,
                multiple_access: 0,
            },
            edit: {
                title: "",
                title_e: "",
                branch_id: null,
                multiple_access: 0,
            },
            company_id: null,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            setting: {
                title: true,
                title_e: true,
                branch_id: true,
                multiple_access: true,
                created_by: true,
            },
            is_disabled: false,
            filterSetting: ["title", "title_e"],
            printLoading: true,
            printObj: {
                id: "print",
            }
        };
    },
    validations: {
        create: {
            title: {
                required: requiredIf(function (model) {
                    return this.isRequired("title");
                }), minLength: minLength(2), maxLength: maxLength(100)
            },
            title_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("title_e");
                }), minLength: minLength(2), maxLength: maxLength(100)
            },
            branch_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("branch_id");
                })
            },
            multiple_access: {
                required: requiredIf(function (model) {
                    return this.isRequired("multiple_access");
                })
            },
        },
        edit: {
            title: {
                required: requiredIf(function (model) {
                    return this.isRequired("title");
                }), minLength: minLength(2), maxLength: maxLength(100)
            },
            title_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("title_e");
                }), minLength: minLength(2), maxLength: maxLength(100)
            },
            branch_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("branch_id");
                })
            },
            multiple_access: {
                required: requiredIf(function (model) {
                    return this.isRequired("multiple_access");
                })
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
                this.unitStatuses.forEach((el) => {
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
        this.getCustomTableFields();
        this.getData();
    },
     beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "POS Cash Register", "all POS Cash Register");
    });
  },

    methods: {
        isPermission(item) {
            if (this.$store.state.auth.type == 'user') {
                return this.$store.state.auth.permissions.includes(item)
            }
            return true;
        },
        getCustomTableFields() {
            adminApi
                .get(`/customTable/table-columns/pos_cash_registers`)
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
        arabicValue(txt) {
            this.create.title = arabicValue(txt);
            this.edit.title = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.title_e = englishValue(txt);
            this.edit.title_e = englishValue(txt);
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`point-of-sale/cash-register/logs/${id}`)
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
                    `point-of-sale/cash-register?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.unitStatuses = l.data;
                    this.unitStatusPagination = l.pagination;
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
                this.current_page <= this.unitStatusPagination.last_page &&
                this.current_page != this.unitStatusPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let filter = "";
                for (let i = 0; i < this.filterSetting.length; ++i) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `point-of-sale/cash-register?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}&company_id=${this.company_id}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.unitStatuses = l.data;
                        this.unitStatusPagination = l.pagination;
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
         *  start delete countrie
         */
        deleteBranch(id, index) {
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
                            .post(`point-of-sale/cash-register/bulk-delete`, {ids: id})
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
                            .delete(`point-of-sale/cash-register/${id}`)
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
         *  end delete countrie
         */
        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.create = {
                title: "",
                title_e: "",
                branch_id: null,
                multiple_access: 0,
            };
            this.isLoader= false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.$bvModal.hide(`create`);
        },
        /**
         *  hidden Modal (create)
         */
        resetModal() {
            this.create = {
                title: "",
                title_e: "",
                branch_id: null,
                multiple_access: 0,
            };
            if (this.isVisible("branch_id")) this.getBranch();
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  create countrie
         */
        resetForm() {
            this.create = {
                title: "",
                title_e: "",
                branch_id: null,
                multiple_access: 0,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
        },
        getCreateBy(){
            let created_by = this.$store.state.auth.type == 'admin' ?
                this.$store.state.auth.partner ? this.$store.state.auth.partner.name:this.$store.state.auth.partner.name_e:
                this.$store.state.auth.user ? this.$store.state.auth.user.name:this.$store.state.auth.user.name_e;
            return created_by;
        },
        AddSubmit() {
            if (!this.create.title) {
                this.create.title = this.create.title_e;
            }
            if (!this.create.title_e) {
                this.create.title_e = this.create.title;
            }
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                let data = {
                    title: this.create.title,
                    title_e: this.create.title_e,
                    branch_id: this.create.branch_id,
                    created_by:this.getCreateBy(),
                    multiple_access: this.create.multiple_access,
                };
                adminApi
                    .post(`point-of-sale/cash-register`, {
                        ...data,
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
         *  edit countrie
         */
        editSubmit(id) {
            if (!this.edit.title) {
                this.edit.title = this.edit.title_e;
            }
            if (!this.edit.title_e) {
                this.edit.title_e = this.edit.title;
            }
            this.$v.edit.$touch();

            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                let data = {
                    title: this.edit.title,
                    title_e: this.edit.title_e,
                    branch_id: this.edit.branch_id,
                    created_by:this.getCreateBy(),
                    multiple_access: this.edit.multiple_access,
                };
                adminApi
                    .put(`point-of-sale/cash-register/${id}`, data)
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
        async resetModalEdit(id) {
            if (this.isVisible("branch_id")) await this.getBranch();
            let unitStatus = this.unitStatuses.find((e) => id == e.id);
            this.edit.title = unitStatus.title;
            this.edit.title_e = unitStatus.title_e;
            this.edit.branch_id = unitStatus.branch_id;
            this.edit.multiple_access = unitStatus.multiple_access;
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.errors = {};
            this.edit = {
                title: "",
                title_e: "",
                branch_id: null,
                multiple_access: 0,
            };
        },
        async getBranch() {
            this.isLoader = true;

            await adminApi
                .get(`/branches?company_id=${this.company_id}&notParent=1`)
                .then((res) => {
                    let l = res.data.data;
                    this.branches = l;
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
        moveInput(tag, c, index) {
            document.querySelector(`${tag}[data-${c}='${index}']`).focus();
        },
        ExportExcel(type, fn, dl) {
            this.enabled3 = false;
            setTimeout(() => {
                let elt = this.$refs.exportable_table;
                let wb = XLSX.utils.table_to_book(elt, {sheet: "Sheet JS"});
                if (dl) {
                    XLSX.write(wb, {bookType: type, bookSST: true, type: 'base64'});
                } else {
                    XLSX.writeFile(wb, fn || (('cash-register' + '.' || 'SheetJSTableExport.') + (type || 'xlsx')));
                }
                this.enabled3 = true;
            }, 100);
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
                            <h4 class="header-title">{{ $t("general.CashRegisterTable") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown variant="primary" :text="$t('general.searchSetting')" ref="dropdown"
                                                class="btn-block setting-search">
                                        <b-form-checkbox v-if="isVisible('title')" v-model="filterSetting" value="title" class="mb-1">
                                            {{getCompanyKey("customer_groups_title_ar")}}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-if="isVisible('title_e')" v-model="filterSetting" value="title_e" class="mb-1">
                                            {{ getCompanyKey("customer_groups_title_en")}}
                                        </b-form-checkbox>
                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                </div>

                                <div class="d-inline-block position-relative" style="width: 77%">
                                    <span :class="[ 'search-custom position-absolute',$i18n.locale == 'ar' ? 'search-custom-ar' : '',]">
                                        <i class="fe-search"></i>
                                    </span>
                                    <input class="form-control" style="display: block; width: 93%; padding-top: 3px" type="text" v-model.trim="search" :placeholder="`${$t('general.Search')}...`"/>
                                </div>
                            </div>
                        </div>
                        <!-- end search -->

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <!-- start create and printer -->
                                <b-button v-b-modal.create v-if="isPermission('create document status')" variant="primary" class="btn-sm mx-1 font-weight-bold">
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#print'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <button class="custom-btn-dowonload"
                                            @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                            v-if="checkAll.length == 1 && isPermission('update document status')">
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button class="custom-btn-dowonload"
                                            v-if="checkAll.length > 1 && isPermission('delete document status')"
                                            @click.prevent="deleteBranch(checkAll)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button class="custom-btn-dowonload"
                                            v-if="checkAll.length == 1 && isPermission('delete document status')"
                                            @click.prevent="deleteBranch(checkAll[0])">
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
                                        <b-dropdown variant="primary" :html="`${$t('general.setting')} <i class='fe-settings'></i>`" ref="dropdown" class="dropdown-custom-ali">
                                            <b-form-checkbox v-if="isVisible('title')" v-model="setting.title" class="mb-1">{{ getCompanyKey("customer_groups_title_ar") }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('title_e')" v-model="setting.title_e" class="mb-1">{{ getCompanyKey("customer_groups_title_en") }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('branch_id')" v-model="setting.branch_id" class="mb-1">{{ getCompanyKey("cash_register_branch") }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('multiple_access')" v-model="setting.multiple_access" class="mb-1">{{ getCompanyKey("cash_register_multiple_access") }}</b-form-checkbox>
                                            <b-form-checkbox v-if="isVisible('created_by')" v-model="setting.created_by" class="mb-1">{{ getCompanyKey("cash_register_created_by") }}</b-form-checkbox>
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
                                            {{ unitStatusPagination.from }}-{{ unitStatusPagination.to }} /
                                            {{ unitStatusPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                               :style="{'pointer-events':unitStatusPagination.current_page == 1 ? 'none' : '',}"
                                               @click.prevent="getData(unitStatusPagination.current_page - 1)">
                                                <span>&lt;</span>
                                            </a>
                                            <input type="text" @keyup.enter="getDataCurrentPage()" v-model="current_page" class="pagination-current-page"/>
                                            <a
                                                href="javascript:void(0)"
                                               :style="{'pointer-events':unitStatusPagination.last_page ==unitStatusPagination.current_page? 'none': '',}"
                                               @click.prevent="getData(unitStatusPagination.current_page + 1)"
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
                            :title="getCompanyKey('cash_register_create_form')"
                            title-class="font-18"
                            body-class="p-4 "
                            :hide-footer="true"
                            @show="resetModal"
                            @hidden="resetModalHidden"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <b-button variant="success" :disabled="!is_disabled" @click.prevent="resetForm"
                                              type="button"
                                              :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                                        {{ $t("general.AddNewRecord") }}
                                    </b-button>
                                    <template v-if="!is_disabled">
                                        <b-button variant="success" type="button" class="mx-1" v-if="!isLoader"
                                                  @click.prevent="AddSubmit">
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
                                <div class="row">
                                    <div class="col-md-12" v-if="isVisible('branch_id')">
                                        <div class="form-group">
                                            <label class="mr-2">
                                                {{ getCompanyKey("cash_register_branch") }}
                                                <span v-if="isRequired('branch_id')" class="text-danger">*</span>
                                            </label>
                                            <multiselect
                                                v-model="create.branch_id"
                                                :options="branches.map((type) => type.id)"
                                                :custom-label="(opt) =>$i18n.locale == 'ar'? branches.find((x) => x.id == opt).name: branches.find((x) => x.id == opt).name_e"
                                                :class="{'is-invalid':$v.create.branch_id.$error || errors.branch_id,}"
                                            >
                                            </multiselect>
                                            <div
                                                v-if="!$v.create.branch_id.required"
                                                class="invalid-feedback"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                            <template v-if="errors.branch_id">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.branch_id" :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-12" v-if="isVisible('title')">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("cash_register_title_ar") }}
                                                <span v-if="isRequired('title')" class="text-danger">*</span>
                                            </label>
                                            <div dir="rtl">
                                                <input
                                                    @keyup="arabicValue(create.title)"
                                                    type="text"
                                                    class="form-control"
                                                    data-create="1"
                                                    @keypress.enter="moveInput('input', 'create', 2)"
                                                    v-model="$v.create.title.$model"
                                                    :class="{
                                                       'is-invalid': $v.create.title.$error || errors.title,
                                                       'is-valid': !$v.create.title.$invalid && !errors.title,
                                                    }"
                                                />
                                            </div>
                                            <div v-if="!$v.create.title.minLength" class="invalid-feedback">
                                                {{ $t("general.Itmustbeatleast") }}
                                                {{ $v.create.title.$params.minLength.min }}
                                                {{ $t("general.letters") }}
                                            </div>
                                            <div v-if="!$v.create.title.maxLength" class="invalid-feedback">
                                                {{ $t("general.Itmustbeatmost") }}
                                                {{ $v.create.title.$params.maxLength.max }}
                                                {{ $t("general.letters") }}
                                            </div>
                                            <template v-if="errors.title">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.title" :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-12" v-if="isVisible('title_e')">
                                        <div class="form-group">
                                            <label class="control-label">
                                                {{ getCompanyKey("cash_register_title_en") }}
                                                <span v-if="isRequired('title_e')" class="text-danger">*</span>
                                            </label>
                                            <div dir="ltr">
                                                <input
                                                    @keyup="englishValue(create.title_e)" type="text"
                                                    class="form-control englishInput"
                                                    data-create="2"
                                                    @keypress.enter="moveInput('select', 'create', 3)"
                                                    v-model="$v.create.title_e.$model"
                                                    :class="{
                                                      'is-invalid': $v.create.title_e.$error || errors.title_e,
                                                      'is-valid': !$v.create.title_e.$invalid && !errors.title_e,
                                                    }"
                                                />
                                            </div>
                                            <div v-if="!$v.create.title_e.minLength" class="invalid-feedback">
                                                {{ $t("general.Itmustbeatleast") }}
                                                {{ $v.create.title_e.$params.minLength.min }}
                                                {{ $t("general.letters") }}
                                            </div>
                                            <div v-if="!$v.create.title_e.maxLength" class="invalid-feedback">
                                                {{ $t("general.Itmustbeatmost") }}
                                                {{ $v.create.title_e.$params.maxLength.max }}
                                                {{ $t("general.letters") }}
                                            </div>
                                            <template v-if="errors.title_e">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.title_e" :key="index">
                                                    {{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6" v-if="isVisible('multiple_access')">
                                        <div class="form-group">
                                            <label class="mr-2">
                                                {{ getCompanyKey("cash_register_multiple_access") }}
                                                <span v-if="isRequired('multiple_access')" class="text-danger">*</span>
                                            </label>
                                            <b-form-group
                                                :class="{
                                                    'is-invalid': $v.create.multiple_access.$error || errors.multiple_access,
                                                    'is-valid': !$v.create.multiple_access.$invalid && !errors.multiple_access,
                                                }"
                                            >
                                                <b-form-radio class="d-inline-block"
                                                              v-model="$v.create.multiple_access.$model"
                                                              name="create_def_some-radios" value="1">
                                                    {{ $t("general.Yes") }}
                                                </b-form-radio>
                                                <b-form-radio class="d-inline-block m-1"
                                                              v-model="$v.create.multiple_access.$model"
                                                              name="create_def_some-radios" value="0">
                                                    {{ $t("general.No") }}
                                                </b-form-radio>
                                            </b-form-group>
                                            <template v-if="errors.multiple_access">
                                                <ErrorMessage v-for="(errorMessage, index) in errors.multiple_access"
                                                              :key="index">{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div class="table-responsive mb-3 custom-table-theme position-relative">
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader"/>
                            <!--       end loader       -->
                            <table class="table table-borderless table-hover table-centered m-0" ref="exportable_table"
                                   id="print">
                                <thead>
                                <tr>
                                    <th scope="col" style="width: 0" v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control">
                                            <input class="form-check-input" type="checkbox" v-model="isCheckAll"
                                                   style="width: 17px; height: 17px"/>
                                        </div>
                                    </th>
                                    <th v-if="setting.title && isVisible('title')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("cash_register_title_ar") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="unitStatuses.sort(sortString('title'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="unitStatuses.sort(sortString('-title'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.title_e && isVisible('title_e')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("cash_register_title_en") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="unitStatuses.sort(sortString('title_e'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="unitStatuses.sort(sortString('-title_e'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.branch_id && isVisible('branch_id')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("cash_register_branch") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="unitStatuses.sort(sortString('discount'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="unitStatuses.sort(sortString('-discount'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.multiple_access && isVisible('multiple_access')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("cash_register_multiple_access") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="unitStatuses.sort(sortString('multiple_access'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="unitStatuses.sort(sortString('-multiple_access'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.created_by && isVisible('created_by')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("cash_register_created_by") }}</span>
                                            <div class="arrow-sort">
                                                <i class="fas fa-arrow-up"
                                                   @click="unitStatuses.sort(sortString('created_by'))"></i>
                                                <i class="fas fa-arrow-down"
                                                   @click="unitStatuses.sort(sortString('-created_by'))"></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t("general.Action") }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print"><i class="fas fa-ellipsis-v"></i></th>
                                </tr>
                                </thead>
                                <tbody v-if="unitStatuses.length > 0">
                                <tr
                                    @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="isPermission('update document status')? $bvModal.show(`modal-edit-${data.id}`): false"
                                    v-for="(data, index) in unitStatuses" :key="data.id"
                                    class="body-tr-custom"
                                >
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="form-check custom-control" style="min-height: 1.9em">
                                            <input style="width: 17px; height: 17px" class="form-check-input" type="checkbox" :value="data.id" v-model="checkAll"/>
                                        </div>
                                    </td>
                                    <td v-if="setting.title && isVisible('title')">
                                        <h5 class="m-0 font-weight-normal">{{ data.title }}</h5>
                                    </td>
                                    <td v-if="setting.title_e && isVisible('title_e')">
                                        <h5 class="m-0 font-weight-normal">{{ data.title_e }}</h5>
                                    </td>
                                    <td v-if="setting.branch_id && isVisible('branch_id')">
                                        <h5 class="m-0 font-weight-normal">
                                            {{data.branch ? $i18n.locale == "ar" ? data.branch.name : data.branch.name_e : " - "}}
                                        </h5>
                                    </td>
                                    <td v-if="setting.multiple_access && isVisible('multiple_access')">
                                        <span :class="[data.multiple_access == 1 ? 'text-success' : 'text-danger','badge',]">
                                            {{data.multiple_access == 1? `${$t("general.Yes")}`: `${$t("general.No")}`}}
                                        </span>
                                    </td>
                                    <td v-if="setting.created_by && isVisible('created_by')">
                                        <h5 class="m-0 font-weight-normal">{{ data.created_by }}</h5>
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <div class="btn-group">
                                            <button type="button"
                                                    class="btn btn-sm dropdown-toggle dropdown-coustom"
                                                    data-toggle="dropdown"
                                                    aria-expanded="false">
                                                {{ $t("general.commands") }}
                                                <i class="fas fa-angle-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-custom">
                                                <a class="dropdown-item" v-if="isPermission('update document status')"
                                                   href="#" @click="$bvModal.show(`modal-edit-${data.id}`)">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black">
                                                        <span>{{ $t("general.edit") }}</span>
                                                        <i class="mdi mdi-square-edit-outline text-info"></i>
                                                    </div>
                                                </a>
                                                <a class="dropdown-item text-black"
                                                   v-if="isPermission('delete document status')" href="#"
                                                   @click.prevent="deleteBranch(data.id)">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black">
                                                        <span>{{ $t("general.delete") }}</span>
                                                        <i class="fas fa-times text-danger"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>

                                        <!--  edit   -->
                                        <b-modal :id="`modal-edit-${data.id}`"
                                                 :title="getCompanyKey('cash_register_edit_form')"
                                                 title-class="font-18" body-class="p-4" :ref="`edit-${data.id}`"
                                                 :hide-footer="true"
                                                 @show="resetModalEdit(data.id)"
                                                 @hidden="resetModalHiddenEdit(data.id)">
                                            <form>
                                                <div class="mb-3 d-flex justify-content-end">
                                                    <!-- Emulate built in modal footer ok and cancel button actions -->
                                                    <b-button variant="success" type="submit" class="mx-1"
                                                              v-if="!isLoader"
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
                                                    <div class="col-md-12" v-if="isVisible('branch_id')">
                                                        <div class="form-group">
                                                            <label class="mr-2">
                                                                {{ getCompanyKey("cash_register_branch") }}
                                                                <span v-if="isRequired('branch_id')" class="text-danger">*</span>
                                                            </label>
                                                            <multiselect
                                                                v-model="edit.branch_id"
                                                                :options="branches.map((type) => type.id)"
                                                                :custom-label="(opt) =>$i18n.locale == 'ar'? branches.find((x) => x.id == opt).name: branches.find((x) => x.id == opt).name_e"
                                                                :class="{'is-invalid':$v.edit.branch_id.$error || errors.branch_id,}"
                                                            >
                                                            </multiselect>
                                                            <div
                                                                v-if="!$v.edit.branch_id.required"
                                                                class="invalid-feedback"
                                                            >
                                                                {{ $t("general.fieldIsRequired") }}
                                                            </div>
                                                            <template v-if="errors.branch_id">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.branch_id" :key="index">
                                                                    {{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12" v-if="isVisible('title')">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("cash_register_title_ar") }}
                                                                <span v-if="isRequired('title')" class="text-danger">*</span>
                                                            </label>
                                                            <div dir="rtl">
                                                                <input
                                                                    @keyup="arabicValue(edit.title)"
                                                                    type="text"
                                                                    class="form-control"
                                                                    data-edit="1"
                                                                    @keypress.enter="moveInput('input', 'edit', 2)"
                                                                    v-model="$v.edit.title.$model"
                                                                    :class="{
                                                                        'is-invalid': $v.edit.title.$error || errors.title,
                                                                        'is-valid': !$v.edit.title.$invalid && !errors.title,
                                                                    }"
                                                                />
                                                            </div>
                                                            <div v-if="!$v.edit.title.minLength"
                                                                 class="invalid-feedback">
                                                                {{ $t("general.Itmustbeatleast") }}
                                                                {{ $v.edit.title.$params.minLength.min }}
                                                                {{ $t("general.letters") }}
                                                            </div>
                                                            <div v-if="!$v.edit.title.maxLength"
                                                                 class="invalid-feedback">
                                                                {{ $t("general.Itmustbeatmost") }}
                                                                {{ $v.edit.title.$params.maxLength.max }}
                                                                {{ $t("general.letters") }}
                                                            </div>
                                                            <template v-if="errors.title">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.title" :key="index">
                                                                    {{ errorMessage}}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12" v-if="isVisible('title_e')">
                                                        <div class="form-group">
                                                            <label class="control-label">
                                                                {{ getCompanyKey("cash_register_title_en") }}
                                                                <span v-if="isRequired('title_e')"
                                                                      class="text-danger">*</span>
                                                            </label>
                                                            <div dir="ltr">
                                                                <input
                                                                    @keyup="englishValue(edit.title_e)"
                                                                    type="text"
                                                                    class="form-control englishInput"
                                                                    data-edit="2"
                                                                    @keypress.enter="moveInput('select', 'edit', 3)"
                                                                    v-model="$v.edit.title_e.$model"
                                                                    :class="{
                                                                        'is-invalid': $v.edit.title_e.$error || errors.title_e,
                                                                        'is-valid':!$v.edit.title_e.$invalid && !errors.title_e,
                                                                    }"
                                                                />
                                                            </div>
                                                            <div v-if="!$v.edit.title_e.minLength"
                                                                 class="invalid-feedback">
                                                                {{ $t("general.Itmustbeatleast") }}
                                                                {{ $v.edit.title_e.$params.minLength.min }}
                                                                {{ $t("general.letters") }}
                                                            </div>
                                                            <div v-if="!$v.edit.title_e.maxLength"
                                                                 class="invalid-feedback">
                                                                {{ $t("general.Itmustbeatmost") }}
                                                                {{ $v.edit.title_e.$params.maxLength.max }}
                                                                {{ $t("general.letters") }}
                                                            </div>
                                                            <template v-if="errors.title_e">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.title_e" :key="index">
                                                                    {{errorMessage}}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6" v-if="isVisible('multiple_access')">
                                                        <div class="form-group">
                                                            <label class="mr-2">
                                                                {{ getCompanyKey("cash_register_multiple_access") }}
                                                                <span v-if="isRequired('multiple_access')" class="text-danger">*</span>
                                                            </label>
                                                            <b-form-group
                                                                :class="{
                                                                    'is-invalid': $v.edit.multiple_access.$error || errors.multiple_access,
                                                                    'is-valid': !$v.edit.multiple_access.$invalid && !errors.multiple_access,
                                                                }"
                                                            >
                                                                <b-form-radio class="d-inline-block"
                                                                              v-model="$v.edit.multiple_access.$model"
                                                                              name="edit_def_some-radios" value="1">
                                                                    {{ $t("general.Yes") }}
                                                                </b-form-radio>
                                                                <b-form-radio class="d-inline-block m-1"
                                                                              v-model="$v.edit.multiple_access.$model"
                                                                              name="edit_def_some-radios" value="0">
                                                                    {{ $t("general.No") }}
                                                                </b-form-radio>
                                                            </b-form-group>
                                                            <template v-if="errors.multiple_access">
                                                                <ErrorMessage v-for="(errorMessage, index) in errors.multiple_access"
                                                                              :key="index">{{ errorMessage }}
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
                                        <button @mouseover="log(data.id)" @mousemove="log(data.id)" type="button"
                                                class="btn"
                                                data-toggle="tooltip"
                                                :data-placement="$i18n.locale == 'en' ? 'left' : 'right'"
                                                :title="Tooltip">
                                            <i class="fe-info" style="font-size: 22px"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="11">
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
