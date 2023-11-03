<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import { required, minLength, maxLength, integer } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import ArchDoc from "../../../components/create/arch/gen-arch-doc-type";
import DocField from "../../../components/create/arch/doc-field";
import translation from "../../../helper/mixin/translation-mixin";
import permissionGuard from "../../../helper/permission";


/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Arch Doc Type Field",
        meta: [{ name: "description", content: "Arch Doc Type Field" }],
    },
    mixins: [translation],
    components: {
        Layout,
        PageHeader,
        ErrorMessage,
        loader,
        Multiselect,
        ArchDoc,
        DocField,
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Archiving Document Type Field", "all Store");
    });


    },
    updated() {
        $(".englishInput").keypress(function (event) {
            var ew = event.which;
            if (ew == 32) return true;
            if (48 <= ew && ew <= 57) return true;
            if (65 <= ew && ew <= 90) return true;
            if (97 <= ew && ew <= 122) return true;
            return false;
        });
        $(".arabicInput").keypress(function (event) {
            var ew = event.which;
            if (ew == 32) return true;
            if (48 <= ew && ew <= 57) return false;
            if (65 <= ew && ew <= 90) return false;
            if (97 <= ew && ew <= 122) return false;
            return true;
        });
    },
    data() {
        return {
            per_page: 50,
            search: "", //Search table column
            debounce: {},
            dataTablePaginations: {},
            storedData: [],
            genArchDocData: [],
            archDocFieldData: [],
            enabled3: true,
            isLoader: false,
            create: {
                gen_doc_type_id: "",
                doc_field_id: "",
                field_order: "",
                is_required: 1,
                field_characters: "",
            }, //Create form
            edit: {
                gen_doc_type_id: "",
                doc_field_id: "",
                field_order: "",
                is_required: 1,
                field_characters: "",
            }, //Edit form
            setting: {
                gen_doc_type_id: true,
                doc_field_id: true,
                field_order: true,
                is_required: true,
                field_characters: true,
            }, //Table columns
            filterSetting: [
                "gen_doc_type_id",
                "doc_field_id",
            ],
            errors: {}, //Server Side Validation Errors
            isCheckAll: false,
            checkAll: [],
            is_disabled: false,
            current_page: 1,
            printLoading: true,
            company_id:null,
            printObj: {
                id: "printField",
            },
        };
    },
    validations: {
        create: {
            gen_doc_type_id: { required },
            doc_field_id: { required },
            field_order: { required, integer },
            is_required: { required },
            field_characters: { required, integer },
        },
        edit: {
            gen_doc_type_id: { required },
            doc_field_id: { required },
            field_order: { required, integer },
            is_required: { required },
            field_characters: { required, integer },
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
                this.storedData.forEach((el) => {
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
        /**
         *  get Data document field
         */
        getData(page = 1) {
            this.isLoader = true;
            let _filterSetting = [...this.filterSetting];
            let index = this.filterSetting.indexOf("gen_doc_type_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "genArchDocType.name" : "genArchDocType.name_e";
            }
            index = this.filterSetting.indexOf("doc_field_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "archDocType.name" : "archDocType.name_e";
            }
            let filter = "";
            for (let i = 0; i < _filterSetting.length; ++i) {
                filter += `columns[${i}]=${_filterSetting[i]}&`;
            }

            adminApi
                .get(
                    `/arch-doc-type-field?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.storedData = l.data;
                    this.dataTablePaginations = l.pagination;
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
                this.current_page <= this.dataTablePaginations.last_page &&
                this.current_page != this.dataTablePaginations.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let _filterSetting = [...this.filterSetting];
                let index = this.filterSetting.indexOf("gen_doc_type_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "genArchDocType.name" : "genArchDocType.name_e";
                }
                index = this.filterSetting.indexOf("doc_field_id");
                if (index > -1) {
                    _filterSetting[index] =
                        this.$i18n.locale == "ar" ? "archDocType.name" : "archDocType.name_e";
                }
                let filter = "";
                for (let i = 0; i < _filterSetting.length; ++i) {
                    filter += `columns[${i}]=${_filterSetting[i]}&`;
                }
                adminApi
                    .get(
                        `/arch-doc-type-field?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.storedData = l.data;
                        this.dataTablePaginations = l.pagination;
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
         *  delete document field
         */
        singleDelete(id) {
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
                        .delete(`/arch-doc-type-field/${id}`)
                        .then((res) => {
                            this.getData();
                            this.checkAll = [];
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
        },
        /**
         *  Bulk delete document fields
         */
        bulkDelete(id) {
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
                        .post(`/arch-doc-type-field/bulk-delete`, {
                            ids: id,
                        })
                        .then((res) => {
                            this.getData();
                            this.checkAll = [];
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
        },

        /**
         *  get gen doc type data
         */
        async getGenDocType() {
            await adminApi
                .get(`/gen-arch-doc-type`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({
                        id: 0,
                        name: "Add Gen Arch Doc Type",
                        name_e: "Add Gen Arch Doc Type",
                    });
                    this.genArchDocData = l;
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
         *  get arch doc field data
         */
        async getArchDocType() {
            await adminApi
                .get(`/document-field`)
                .then((res) => {
                    let l = res.data.data;
                    console.log(l);
                    l.unshift({ id: 0, name: "Add Document Field", name_e: "Add Document Field" });
                    this.archDocFieldData = l;
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
         *  reset Modal (create)
         */
        resetModalHidden() {
            this.create = {
                gen_doc_type_id: "",
                doc_field_id: "",
                field_order: "",
                is_required: 1,
                field_characters: "",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  hidden Modal (create)
         */
        async resetModal() {
            // call api
            await this.getGenDocType();
            await this.getArchDocType();
            this.create = {
                gen_doc_type_id: "",
                doc_field_id: "",
                field_order: "",
                is_required: 1,
                field_characters: "",
            };
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  create document field
         */
        resetForm() {
            this.create = {
                gen_doc_type_id: "",
                doc_field_id: "",
                field_order: "",
                is_required: 1,
                field_characters: "",
            };
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
        },
        /**
         *  add document field
         */
        AddSubmit() {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                adminApi
                    .post(`/arch-doc-type-field`, {
                        ...this.create,
                        is_required: this.create.is_required == "1" ? 1 : 0,
                        company_id: this.company_id
                    })
                    .then((res) => {
                        this.getData();
                        this.is_disabled = true;
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
         *  edit document field
         */
        editSubmit(id) {
            this.$v.edit.$touch();
            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                let {
                    gen_doc_type_id,
                    doc_field_id,
                    field_order,
                    is_required,
                    field_characters,
                } = this.edit;
                adminApi
                    .put(`/arch-doc-type-field/${id}`, {
                        gen_doc_type_id,
                        doc_field_id,
                        field_order,
                        is_required: is_required == "1" ? 1 : 0,
                        field_characters,
                        company_id: this.company_id
                    })
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
            await this.getGenDocType();
            await this.getArchDocType();
            let editGenDocType = this.storedData.find((e) => id == e.id);
            this.edit.gen_doc_type_id = editGenDocType.gen_doc_type_id
                ? editGenDocType.gen_doc_type_id.id
                : "";
            this.edit.doc_field_id = editGenDocType.doc_field_id
                ? editGenDocType.doc_field_id.id
                : "";
            this.edit.field_order = editGenDocType.field_order;
            this.edit.is_required = editGenDocType.is_required;
            this.edit.field_characters = editGenDocType.field_characters;
            this.errors = {};
        },
        /**
         *  hidden Modal (edit)
         */
        resetModalHiddenEdit(id) {
            this.errors = {};
            this.edit = {
                gen_doc_type_id: "",
                doc_field_id: "",
                field_order: "",
                is_required: 1,
                field_characters: "",
            };
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
        showArchGenModal() {
            if (this.create.gen_doc_type_id == 0) {
                this.$bvModal.show("arch-doc-type");
                this.create.gen_doc_type_id = null;
            }
        },
        showArchGenModalEdit() {
            if (this.edit.gen_doc_type_id == 0) {
                this.$bvModal.show("arch-doc-type");
                this.edit.gen_doc_type_id = null;
            }
        },
        showDocFieldModal() {
            if (this.create.doc_field_id == 0) {
                this.$bvModal.show("create-doc-field");
                this.create.doc_field_id = null;
            }
        },
        showDocFieldModalEdit() {
            if (this.edit.doc_field_id == 0) {
                this.$bvModal.show("create-doc-field");
                this.edit.doc_field_id = null;
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
                    XLSX.write(wb, { bookType: type, bookSST: true, type: "base64" });
                } else {
                    XLSX.writeFile(
                        wb,
                        fn ||
                        ("Arch Doc Type Field" + "." || "SheetJSTableExport.") + (type || "xlsx")
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
        <PageHeader />
        <ArchDoc :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @create="getGenDocType" />
        <DocField :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @create="getArchDocType" />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">{{ $t("general.arch_doc_type_field_table") }}</h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <!-- Start search button -->
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown
                                        variant="primary"
                                        :text="$t('general.searchSetting')"
                                        ref="dropdown"
                                        class="btn-block setting-search"
                                    >
                                        <b-form-checkbox
                                            v-model="filterSetting"
                                            value="gen_doc_type_id"
                                            class="mb-1"
                                        >
                                            {{ getCompanyKey("arch_doc_type") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-model="filterSetting"
                                            value="doc_field_id"
                                            class="mb-1"
                                        >
                                            {{ getCompanyKey("arch_doc_field") }}
                                        </b-form-checkbox>
                                    </b-dropdown>

                                    <!-- Basic dropdown -->
                                </div>
                                <!-- End search button -->
                                <!-- Start Search TB -->
                                <div class="d-inline-block position-relative" style="width: 77%">
                  <span
                      :class="[
                      'search-custom position-absolute',
                      $i18n.locale == 'ar' ? 'search-custom-ar' : '',
                    ]"
                  >
                    <i class="fe-search"></i>
                  </span>
                                    <input
                                        class="form-control"
                                        style="display: block; width: 93%; padding-top: 3px"
                                        type="text"
                                        v-model.trim="search"
                                        :placeholder="`${$t('general.Search')}...`"
                                    />
                                </div>
                                <!-- End Search TB -->
                            </div>
                        </div>

                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <!-- start create modal  -->
                                <b-button
                                    v-b-modal.create
                                    variant="primary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <!-- end create modal  -->
                                <div class="d-inline-flex">
                                    <button @click="ExportExcel('xlsx')" class="custom-btn-dowonload">
                                        <i class="fas fa-file-download"></i>
                                    </button>
                                    <button v-print="'#printField'" class="custom-btn-dowonload">
                                        <i class="fe-printer"></i>
                                    </button>
                                    <!-- Start one edit -->
                                    <button
                                        class="custom-btn-dowonload"
                                        @click="$bvModal.show(`modal-edit-${checkAll[0]}`)"
                                        v-if="checkAll.length == 1"
                                    >
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- End one edit -->
                                    <!-- start mult delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="checkAll.length > 1"
                                        @click.prevent="bulkDelete(checkAll)"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="checkAll.length == 1"
                                        @click.prevent="singleDelete(checkAll)"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!--  end one delete  -->
                                </div>
                            </div>
                            <div
                                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end"
                            >
                                <div>
                                    <!-- Start filter -->
                                    <b-button class="mx-1 custom-btn-background">
                                        {{ $t("general.filter") }}
                                        <i class="fas fa-filter"></i>
                                    </b-button>
                                    <!-- End filter -->
                                    <!-- Start group -->
                                    <b-button class="mx-1 custom-btn-background">
                                        {{ $t("general.group") }}
                                        <i class="fe-menu"></i>
                                    </b-button>
                                    <!-- End group -->
                                    <!-- Start setting dropdown -->
                                    <b-dropdown
                                        variant="primary"
                                        :html="`${$t('general.setting')} <i class='fe-settings'></i>`"
                                        ref="dropdown"
                                        class="dropdown-custom-ali"
                                    >
                                        <b-form-checkbox v-model="setting.gen_doc_type_id" class="mb-1"
                                        >{{ getCompanyKey("arch_doc_type") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="setting.doc_field_id" class="mb-1">
                                            {{ getCompanyKey("arch_doc_field") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="setting.field_order" class="mb-1">
                                            {{ getCompanyKey("arch_doc_field_order") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="setting.is_required" class="mb-1">
                                            {{ getCompanyKey("is_required") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox v-model="setting.field_characters" class="mb-1">
                                            {{ getCompanyKey("arch_doc_field_character") }}
                                        </b-form-checkbox>
                                        <div class="d-flex justify-content-end">
                                            <a href="javascript:void(0)" class="btn btn-primary btn-sm">{{
                                                $t("general.Apply")
                                                }}</a>
                                        </div>
                                    </b-dropdown>
                                    <!-- End setting dropdown -->
                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom">
                                        <div class="d-inline-block" style="font-size: 15px">
                                            {{ dataTablePaginations.from }}-{{ dataTablePaginations.to }} /
                                            {{ dataTablePaginations.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{
                          'pointer-events':
                            dataTablePaginations.current_page == 1 ? 'none' : '',
                        }"
                                                @click.prevent="getData(dataTablePaginations.current_page - 1)"
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
                            dataTablePaginations.last_page ==
                            dataTablePaginations.current_page
                              ? 'none'
                              : '',
                        }"
                                                @click.prevent="getData(dataTablePaginations.current_page + 1)"
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
                            :title="getCompanyKey('arch_doc_type_field_create_form')"
                            title-class="font-18"
                            body-class="p-4 "
                            :hide-footer="true"
                            size="lg"
                            @show="resetModal"
                            @hidden="resetModalHidden"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <!-- Start Add New Record Button -->
                                    <b-button
                                        variant="success"
                                        :disabled="!is_disabled"
                                        @click.prevent="resetForm"
                                        type="button"
                                        :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
                                    >
                                        {{ $t("general.AddNewRecord") }}
                                    </b-button>
                                    <!-- End Add New Record Button -->
                                    <!-- Emulate built in modal footer ok and cancel button actions -->
                                    <template v-if="!is_disabled">
                                        <!-- Start Add Button -->
                                        <b-button
                                            variant="success"
                                            type="submit"
                                            class="mx-1"
                                            v-if="!isLoader"
                                            @click.prevent="AddSubmit"
                                        >
                                            {{ $t("general.Add") }}
                                        </b-button>
                                        <!-- End Add Button -->
                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                        </b-button>
                                    </template>
                                    <!-- Start Cancel Button Modal -->
                                    <b-button
                                        variant="danger"
                                        type="button"
                                        @click.prevent="$bvModal.hide(`create`)"
                                    >
                                        {{ $t("general.Cancel") }}
                                    </b-button>
                                    <!-- End Cancel Button Modal -->
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>
                                                {{ getCompanyKey("arch_doc_type") }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <multiselect
                                                v-model="create.gen_doc_type_id"
                                                @input="showArchGenModal"
                                                :options="genArchDocData.map((type) => type.id)"
                                                :custom-label="
                          (opt) =>
                            $i18n.locale
                              ? genArchDocData.find((x) => x.id == opt).name
                              : genArchDocData.find((x) => x.id == opt).name_e
                        "
                                            >
                                            </multiselect>

                                            <template v-if="errors.gen_doc_type_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.gen_doc_type_id"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>
                                                {{ getCompanyKey("arch_doc_field") }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <multiselect
                                                @input="showDocFieldModal"
                                                v-model="create.doc_field_id"
                                                :options="archDocFieldData.map((type) => type.id)"
                                                :custom-label="
                          (opt) =>
                            $i18n.locale
                              ? archDocFieldData.find((x) => x.id == opt).name
                              : archDocFieldData.find((x) => x.id == opt).name_e
                        "
                                            >
                                            </multiselect>

                                            <template v-if="errors.doc_field_id">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.doc_field_id"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-1" class="control-label">
                                                {{ getCompanyKey("arch_doc_field_order") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control englishInput"
                                                v-model="$v.create.field_order.$model"
                                                :class="{
                          'is-invalid':
                            $v.create.field_order.$error || errors.field_order,
                          'is-valid':
                            !$v.create.field_order.$invalid && !errors.field_order,
                        }"
                                                id="field-1"
                                            />
                                            <div v-if="!$v.create.field_order.integer" class="invalid-feedback">
                                                {{ $t("general.numericValidation") }}
                                            </div>
                                            <template v-if="errors.field_order">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.field_order"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="mr-2">
                                                {{ getCompanyKey("is_required") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <b-form-group
                                                :class="{
                          'is-invalid':
                            $v.create.is_required.$error || errors.is_required,
                          'is-valid':
                            !$v.create.is_required.$invalid && !errors.is_required,
                        }"
                                            >
                                                <b-form-radio
                                                    class="d-inline-block"
                                                    v-model="$v.create.is_required.$model"
                                                    name="some-radios"
                                                    value="1"
                                                >{{ $t("general.isReferenceYes") }}
                                                </b-form-radio>
                                                <b-form-radio
                                                    class="d-inline-block m-1"
                                                    v-model="$v.create.is_required.$model"
                                                    name="some-radios"
                                                    value="0"
                                                >{{ $t("general.isReferenceNo") }}
                                                </b-form-radio>
                                            </b-form-group>
                                            <template v-if="errors.is_required">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.is_required"
                                                    :key="index"
                                                >{{ errorMessage }}
                                                </ErrorMessage>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="field-2" class="control-label">
                                                {{ getCompanyKey("arch_doc_field_character") }}
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control englishInput"
                                                v-model="$v.create.field_characters.$model"
                                                :class="{
                          'is-invalid':
                            $v.create.field_characters.$error || errors.field_characters,
                          'is-valid':
                            !$v.create.field_characters.$invalid &&
                            !errors.field_characters,
                        }"
                                                id="field-2"
                                            />
                                            <div
                                                v-if="!$v.create.field_characters.integer"
                                                class="invalid-feedback"
                                            >
                                                {{ $t("general.numericValidation") }}
                                            </div>
                                            <template v-if="errors.field_characters">
                                                <ErrorMessage
                                                    v-for="(errorMessage, index) in errors.field_characters"
                                                    :key="index"
                                                >{{ errorMessage }}
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
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->

                            <table
                                class="table table-borderless table-hover table-centered m-0"
                                ref="exportable_table"
                                id="printField"
                            >
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
                                    <th v-if="setting.gen_doc_type_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("arch_doc_type") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="storedData.sort(sortString('gen_doc_type_id'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="storedData.sort(sortString('-gen_doc_type_id'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.doc_field_id">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("arch_doc_field") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="storedData.sort(sortString('doc_field_id'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="storedData.sort(sortString('-doc_field_id'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.field_order">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("arch_doc_field_order") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="storedData.sort(sortString('field_order'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="storedData.sort(sortString('-field_order'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.is_required">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("is_required") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="storedData.sort(sortString('is_required'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="storedData.sort(sortString('-is_required'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="setting.field_characters">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("arch_doc_field_character") }}</span>
                                            <div class="arrow-sort">
                                                <i
                                                    class="fas fa-arrow-up"
                                                    @click="storedData.sort(sortString('field_characters'))"
                                                ></i>
                                                <i
                                                    class="fas fa-arrow-down"
                                                    @click="storedData.sort(sortString('-field_characters'))"
                                                ></i>
                                            </div>
                                        </div>
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        {{ $t("general.Action") }}
                                    </th>
                                    <th v-if="enabled3" class="do-not-print">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </th>
                                </tr>
                                </thead>
                                <tbody v-if="storedData.length > 0">
                                <tr
                                    @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                                    v-for="(data, index) in storedData"
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
                                    <td v-if="setting.gen_doc_type_id">
                                        <h5 class="m-0 font-weight-normal">
                                            {{
                                            data.gen_doc_type_id
                                            ? $i18n.locale == "ar"
                                            ? data.gen_doc_type_id.name
                                            : data.gen_doc_type_id.name_e
                                            : ""
                                            }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.doc_field_id">
                                        <h5 class="m-0 font-weight-normal">
                                            {{
                                            data.doc_field_id
                                            ? $i18n.locale == "ar"
                                            ? data.doc_field_id.name
                                            : data.doc_field_id.name_e
                                            : ""
                                            }}
                                        </h5>
                                    </td>
                                    <td v-if="setting.field_order">
                                        <h5 class="m-0 font-weight-normal">{{ data.field_order }}</h5>
                                    </td>
                                    <td v-if="setting.is_required">
                      <span
                          :class="[
                          data.is_required == 1 ? 'text-success' : 'text-danger',
                          'badge',
                        ]"
                      >
                        {{
                          data.is_required == 1
                            ? `${$t("general.Yes")}`
                            : `${$t("general.No")}`
                        }}
                      </span>
                                    </td>
                                    <td v-if="setting.field_characters">
                                        <h5 class="m-0 font-weight-normal">{{ data.field_characters }}</h5>
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
                                                <a
                                                    class="dropdown-item"
                                                    href="#"
                                                    @click="$bvModal.show(`modal-edit-${data.id}`)"
                                                >
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black"
                                                    >
                                                        <span>{{ $t("general.edit") }}</span>
                                                        <i class="mdi mdi-square-edit-outline text-info"></i>
                                                    </div>
                                                </a>
                                                <a
                                                    class="dropdown-item text-black"
                                                    href="#"
                                                    @click.prevent="singleDelete(data.id)"
                                                >
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black"
                                                    >
                                                        <span>{{ $t("general.delete") }}</span>
                                                        <i class="fas fa-times text-danger"></i>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>

                                        <!--  edit   -->
                                        <b-modal
                                            :id="`modal-edit-${data.id}`"
                                            :title="getCompanyKey('arch_doc_type_field_edit_form')"
                                            title-class="font-18"
                                            body-class="p-4"
                                            size="lg"
                                            :ref="`edit-${data.id}`"
                                            :hide-footer="true"
                                            @show="resetModalEdit(data.id)"
                                            @hidden="resetModalHiddenEdit(data.id)"
                                        >
                                            <form>
                                                <div class="mb-3 d-flex justify-content-end">
                                                    <!-- Emulate built in modal footer ok and cancel button actions -->
                                                    <b-button
                                                        variant="success"
                                                        type="submit"
                                                        class="mx-1"
                                                        v-if="!isLoader"
                                                        @click.prevent="editSubmit(data.id)"
                                                    >
                                                        {{ $t("general.Edit") }}
                                                    </b-button>

                                                    <b-button variant="success" class="mx-1" disabled v-else>
                                                        <b-spinner small></b-spinner>
                                                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                    </b-button>

                                                    <b-button
                                                        variant="danger"
                                                        type="button"
                                                        @click.prevent="$bvModal.hide(`modal-edit-${data.id}`)"
                                                    >
                                                        {{ $t("general.Cancel") }}
                                                    </b-button>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="inlineFormCustomSelectPref">
                                                                {{ getCompanyKey("arch_doc_type") }}
                                                            </label>

                                                            <multiselect
                                                                @input="showArchGenModalEdit"
                                                                v-model="edit.gen_doc_type_id"
                                                                :options="genArchDocData.map((type) => type.id)"
                                                                :custom-label="
                                    (opt) =>
                                      $i18n.locale
                                        ? genArchDocData.find((x) => x.id == opt).name
                                        : genArchDocData.find((x) => x.id == opt).name_e
                                  "
                                                            >
                                                            </multiselect>

                                                            <template v-if="errors.gen_doc_type_id">
                                                                <ErrorMessage
                                                                    v-for="(
                                      errorMessage, index
                                    ) in errors.gen_doc_type_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="inlineFormCustomSelectPref">
                                                                {{ getCompanyKey("arch_doc_field") }}
                                                            </label>

                                                            <multiselect
                                                                @input="showDocFieldModalEdit"
                                                                v-model="edit.doc_field_id"
                                                                :options="archDocFieldData.map((type) => type.id)"
                                                                :custom-label="
                                    (opt) =>
                                      $i18n.locale
                                        ? archDocFieldData.find((x) => x.id == opt).name
                                        : archDocFieldData.find((x) => x.id == opt).name_e
                                  "
                                                            >
                                                            </multiselect>

                                                            <template v-if="errors.doc_field_id">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.doc_field_id"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="field-u-1" class="control-label">
                                                                {{ getCompanyKey("arch_doc_field_order") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="text"
                                                                class="form-control englishInput"
                                                                v-model="$v.edit.field_order.$model"
                                                                :class="{
                                    'is-invalid':
                                      $v.edit.field_order.$error || errors.field_order,
                                    'is-valid':
                                      !$v.edit.field_order.$invalid &&
                                      !errors.field_order,
                                  }"
                                                                :placeholder="$t('general.fieldOrder')"
                                                                id="field-u-1"
                                                            />
                                                            <div
                                                                v-if="!$v.edit.field_order.integer"
                                                                class="invalid-feedback"
                                                            >
                                                                {{ $t("general.numericValidation") }}
                                                            </div>
                                                            <template v-if="errors.field_order">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.field_order"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="mr-2">
                                                                {{ getCompanyKey("is_required") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <b-form-group
                                                                :class="{
                                    'is-invalid':
                                      $v.edit.is_required.$error || errors.is_required,
                                    'is-valid':
                                      !$v.edit.is_required.$invalid &&
                                      !errors.is_required,
                                  }"
                                                            >
                                                                <b-form-radio
                                                                    class="d-inline-block"
                                                                    v-model="$v.edit.is_required.$model"
                                                                    name="some-radios"
                                                                    value="1"
                                                                >
                                                                    {{ $t("general.isReferenceYes") }}
                                                                </b-form-radio>
                                                                <b-form-radio
                                                                    class="d-inline-block m-1"
                                                                    v-model="$v.edit.is_required.$model"
                                                                    name="some-radios"
                                                                    value="0"
                                                                >
                                                                    {{ $t("general.isReferenceNo") }}
                                                                </b-form-radio>
                                                            </b-form-group>
                                                            <template v-if="errors.is_required">
                                                                <ErrorMessage
                                                                    v-for="(errorMessage, index) in errors.is_required"
                                                                    :key="index"
                                                                >{{ errorMessage }}
                                                                </ErrorMessage>
                                                            </template>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="field-u-2" class="control-label">
                                                                {{ getCompanyKey("arch_doc_field_character") }}
                                                                <span class="text-danger">*</span>
                                                            </label>
                                                            <input
                                                                type="text"
                                                                class="form-control englishInput"
                                                                v-model="$v.edit.field_characters.$model"
                                                                :class="{
                                    'is-invalid':
                                      $v.edit.field_characters.$error ||
                                      errors.field_characters,
                                    'is-valid':
                                      !$v.edit.field_characters.$invalid &&
                                      !errors.field_characters,
                                  }"
                                                                :placeholder="$t('general.fieldCharacter')"
                                                                id="field-u-2"
                                                            />
                                                            <div
                                                                v-if="!$v.edit.field_characters.integer"
                                                                class="invalid-feedback"
                                                            >
                                                                {{ $t("general.numericValidation") }}
                                                            </div>
                                                            <template v-if="errors.field_characters">
                                                                <ErrorMessage
                                                                    v-for="(
                                      errorMessage, index
                                    ) in errors.field_characters"
                                                                    :key="index"
                                                                >{{ errorMessage }}
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
                                        <i class="fe-info" style="font-size: 22px"></i>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="6">
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
