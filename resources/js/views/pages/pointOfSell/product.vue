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
    alpha,
    email,
    requiredIf,
    minValue, decimal
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import Multiselect from "vue-multiselect";
import Group from "../../../components/create/general/group";
import Category from "../../../components/create/general/category";
import Brand from "../../../components/create/general/brand";
import Unit from "../../../components/create/general/unit";
import Attribute from "../../../components/create/general/variant-attributes";
import { formatDateOnly } from "../../../helper/startDate";
import translation from "../../../helper/mixin/translation-mixin";
import permissionGuard from "../../../helper/permission";
import {arabicValue, englishValue} from "../../../helper/langTransform";

/**
 * Advanced Table component
 */

export default {
    page: {
        title: "Product",
        meta: [{ name: "description", content: "Product" }],
    },
    mixins: [translation],
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
        Group,
        Category,
        Attribute,
        Brand,
        Unit
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "POS product", "all pointOfSell product");
        });
    },
    data() {
        return {
            isShow: false,
            fields: [],
            per_page: 50,
            search: "",
            Tooltip: "",
            mouseEnter: null,
            chipArray: [],
            productVariant: [],
            productShow: {},
            productStandard: {
                purchase_price: 0,
                selling_price: 0,
                quantity: 0,
                sku: '',
                bar_code: '',
                re_order: 0
            },
            isVariant: false,
            tempAttributeList: [],
            debounce: {},
            productsPagination: {},
            products: [],
            enabled3: true,
            isLoader: false,
            company_id: null,
            categories: [],
            brands: [],
            groups: [],
            units: [],
            taxes: [],
            allAttributes: [],
            branches: [],
            create: {
                title: '',
                title_e: '',
                description: '',
                description_e: '',
                category_id: null,
                brand_id: null,
                group_id: null,
                unit_id: null,
                tax_id: null,
                branch_id: null,
                product_type: 0,
                is_quantity: 0,
            },
            edit: {
                title: '',
                title_e: '',
                description: '',
                description_e: '',
                category_id: null,
                brand_id: null,
                group_id: null,
                unit_id: null,
                tax_id: null,
                branch_id: null,
                product_type: 0,
                is_quantity: 0,
                old_media: [],
            },
            errors: {},
            isCheckAll: false,
            checkAll: [],
            images: [],
            media: {},
            product_id: null,
            saveImageName: [],
            attributes: [],
            current_page: 1,
            showPhoto: "./images/img-1.png",
            setting: {
                title: true,
                title_e: true,
                description: true,
                description_e: true,
                category_id: true,
                brand_id: true,
                group_id: true,
                unit_id: true,
                tax_id: true,
                product_type: true,
            },
            idDelete: null,
            filterSetting: [
                'title',
                'title_e',
                'description',
                'description_e',
                'category_id',
                'brand_id',
                'group_id',
                'unit_id',
                'tax_id',
            ],
            banks: [],
            printLoading: true,
            printObj: {
                id: "printData",
            },
        };
    },
    validations: {
        create: {
            category_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("category_id");
                }),
                integer,
            },
            brand_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("brand_id");
                }),
                integer,
            },
            unit_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("unit_id");
                }),
                integer,
            },
            group_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("group_id");
                }),
                integer,
            },
            tax_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("tax_id");
                }),
            },
            branch_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("branch_id");
                }),
                integer,
            },
            title: {
                required: requiredIf(function (model) {
                    return this.isRequired("title");
                }),
                minLength: minLength(2),
                maxLength: maxLength(100),
            },
            title_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("title_e");
                }),
                minLength: minLength(2),
                maxLength: maxLength(100),
            },
            description: {
                required: requiredIf(function (model) {
                    return this.isRequired("description");
                }),
                maxLength: maxLength(1000),
            },
            description_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("description_e");
                }),
                maxLength: maxLength(1000),
            },
            product_type: {
                required: requiredIf(function (model) {
                    return this.isRequired("product_type");
                }),
                integer,
            },
            is_quantity: {
                required: requiredIf(function (model) {
                    return this.isRequired("is_quantity");
                }),
                integer,
            },
            media: {},
        },
        edit: {
            category_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("category_id");
                }),
                integer,
            },
            brand_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("brand_id");
                }),
                integer,
            },
            unit_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("unit_id");
                }),
                integer,
            },
            group_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("group_id");
                }),
                integer,
            },
            tax_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("tax_id");
                }),
            },
            branch_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("branch_id");
                }),
                integer,
            },
            title: {
                required: requiredIf(function (model) {
                    return this.isRequired("title");
                }),
                minLength: minLength(2),
                maxLength: maxLength(100),
            },
            title_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("title_e");
                }),
                minLength: minLength(2),
                maxLength: maxLength(100),
            },
            description: {
                required: requiredIf(function (model) {
                    return this.isRequired("description");
                }),
                maxLength: maxLength(1000),
            },
            description_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("description_e");
                }),
                maxLength: maxLength(1000),
            },
            product_type: {
                required: requiredIf(function (model) {
                    return this.isRequired("product_type");
                }),
                integer,
            },
            is_quantity: {
                required: requiredIf(function (model) {
                    return this.isRequired("is_quantity");
                }),
                integer,
            },
            purchase_price: {minValue: minValue(.00)},
            selling_price: {minValue: minValue(.00)},
            sku: {},
            quantity: {integer},
            bar_code: {},
            re_order: {integer},
            media: {},
        },
        productStandard: {
            purchase_price: {minValue: minValue(.00)},
            selling_price: {minValue: minValue(.00)},
            sku: {},
            quantity: {integer},
            bar_code: {},
            re_order: {integer},
        },
        productVariant: {
            $each: {
                purchase_price: {minValue: minValue(.00)},
                selling_price: {minValue: minValue(.00)},
                sku: {},
                quantity: {integer},
                bar_code: {},
                re_order: {integer},
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
                this.products.forEach((el) => {
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
    methods: {
        productShowFun(id){
            this.productShow = this.products.find(el => el.id == id);
            this.$bvModal.show(`show`);
            this.isShow = true;
        },
        async getEmployees() {
            await adminApi
                .get(`/employees`)
                .then((res) => {
                    let l = res.data.data;
                    this.employees = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        isPermission(item) {
            if (this.$store.state.auth.type == "user") {
                return this.$store.state.auth.permissions.includes(item);
            }
            return true;
        },
        getCustomTableFields() {
            adminApi
                .get(`/customTable/table-columns/pos_products`)
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
        showCategoryModal() {
            if (this.create.category_id == 0) {
                this.$bvModal.show("category-create-product");
                this.create.category_id = null;
            }
        },
        showCategoryModalEdit() {
            if (this.edit.category_id == 0) {
                this.$bvModal.show("category-create-product");
                this.edit.category_id = null;
            }
        },
        showBrandModal() {
            if (this.create.brand_id == 0) {
                this.$bvModal.show("brand-create-product");
                this.create.brand_id = null;
            }
        },
        showBrandModalEdit() {
            if (this.edit.brand_id == 0) {
                this.$bvModal.show("brand-create-product");
                this.edit.brand_id = null;
            }
        },
        showTaxModal() {
            if (this.create.tax_id == 0) {
                this.$bvModal.show("tax-create");
                this.create.tax_id = null;
            }
        },
        showTaxModalEdit() {
            if (this.edit.tax_id == 0) {
                this.$bvModal.show("tax-create");
                this.edit.tax_id = null;
            }
        },
        showGroupModal() {
            if (this.create.group_id == 0) {
                this.$bvModal.show("group-create-product");
                this.create.group_id = null;
            }
        },
        showGroupModalEdit() {
            if (this.edit.group_id == 0) {
                this.$bvModal.show("group-create-product");
                this.edit.group_id = null;
            }
        },
        showUnitModal() {
            if (this.create.unit_id == 0) {
                this.$bvModal.show("unit-create-product");
                this.create.unit_id = null;
            }
        },
        showUnitModalEdit() {
            if (this.edit.unit_id == 0) {
                this.$bvModal.show("unit-create-product");
                this.edit.unit_id = null;
            }
        },
        arabicValueDescription(txt) {
            this.create.description = arabicValue(txt);
            this.edit.description = arabicValue(txt);
        },
        englishValueDescription(txt) {
            this.create.description_e = englishValue(txt);
            this.edit.description_e = englishValue(txt);
        },
        showAttributeModal(e) {
            if(e == 0) {
                this.$bvModal.show("attribute-create-product");
            }else {
                if(!this.attributes.find( el => el.id == e)){
                    this.attributes.push(this.allAttributes.find(el => el.id == e));
                    this.chipArray.push({id: e,names: [],len: 0});
                }
            }
        },
        arabicValue(txt) {
            this.create.title = arabicValue(txt);
            this.edit.title = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.title_e = englishValue(txt);
            this.edit.title_e = englishValue(txt);
        },
        async getBrand() {
            this.isLoader = true;
            await adminApi
                .get(`/brands`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف الماركه", name_e: "Add Brand" });
                    this.brands = l;
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
        async getCategory() {
            this.isLoader = true;
            await adminApi
                .get(`/categories`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف الفئه", name_e: "Add category" });
                    this.categories = l;
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
        async getGroup() {
            this.isLoader = true;
            await adminApi
                .get(`/groups`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف جروب", name_e: "Add Groups" });
                    this.groups = l;
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
        async getUnit() {
            this.isLoader = true;
            await adminApi
                .get(`/units`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف وحده", name_e: "Add units" });
                    this.units = l;
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
        async getAttribute(){
            this.isLoader = true;
            await adminApi
                .get(`/attributes`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضف ميزه", name_e: "Add Attributes" });
                    this.allAttributes = l;
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
        async getBranch(type){
                this.isLoader = true;
                await adminApi
                    .get(`/branches?notParent=1`)
                    .then((res) => {
                        let l = res.data.data;
                        this.branches = l;
                        if(type != 'update' && this.branches.length > 0){
                            this.create.branch_id = this.branches[this.branches.length  - 1].id;
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
        async getTax(){
            this.isLoader = true;
            await adminApi
                .get(`/taxes`)
                .then((res) => {
                    let l = res.data.data;
                    this.taxes = l;
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
         *  start get Data countrie && pagination
         */
        getData(page = 1) {
            this.isLoader = true;

            let _filterSetting = [...this.filterSetting];
            let index = this.filterSetting.indexOf("customer_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
            }
            index = this.filterSetting.indexOf("currency_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar" ? "currency.name" : "currency.name_e";
            }
            index = this.filterSetting.indexOf("instalment_type_id");
            if (index > -1) {
                _filterSetting[index] =
                    this.$i18n.locale == "ar"
                        ? "installment_payment_type.name"
                        : "installment_payment_type.name_e";
            }
            let filter = "";
            for (let i = 0; i < _filterSetting.length; ++i) {
                filter += `columns[${i}]=${_filterSetting[i]}&`;
            }

            adminApi
                .get(
                    `/point-of-sale/product?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.products = l.data;
                    this.productsPagination = l.pagination;
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
                this.current_page <= this.productsPagination.last_page &&
                this.current_page != this.productsPagination.current_page &&
                this.current_page
            ) {
                this.isLoader = true;
                let filter = "";
                for (let i = 0; i < this.filterSetting.length; ++i) {
                    filter += `columns[${i}]=${this.filterSetting[i]}&`;
                }

                adminApi
                    .get(
                        `/point-of-sale/product?page=${page}&per_page=${this.per_page}&search=${this.search}&${filter}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.products = l.data;
                        this.productsPagination = l.pagination;
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
        deleteproduct(id, index) {
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
                            .post(`/point-of-sale/product/bulk-delete`, { ids: id })
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
                            .delete(`/point-of-sale/product/${id}`)
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
        resetModalHidden() {
            this.create = {
                title: '',
                title_e: '',
                description: '',
                description_e: '',
                category_id: null,
                brand_id: null,
                group_id: null,
                unit_id: null,
                tax_id: null,
                branch_id: null,
                product_type: 0,
                is_quantity: 0,
            };
            this.productVariant= [];
            this.productStandard = {
                purchase_price: 0,
                    selling_price: 0,
                    quantity: 0,
                    sku: '',
                bar_code: '',
                    re_order: 0
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.images = [];
            this.chipArray = [];
            this.attributes = [];
            this.isVariant = false;
            this.product_id = null;
            this.errors = {};
            this.$bvModal.hide(`create`);
        },
        async resetModal() {
            this.create = {
                title: '',
                title_e: '',
                description: '',
                description_e: '',
                category_id: null,
                brand_id: null,
                group_id: null,
                unit_id: null,
                tax_id: null,
                branch_id: null,
                product_type: 0,
                is_quantity: 0,
            };
            this.productVariant= [];
            this.productStandard = {
                purchase_price: 0,
                selling_price: 0,
                quantity: 0,
                sku: '',
                bar_code: '',
                re_order: 0
            };
            if (this.isVisible("category_id")) await this.getCategory();
            if (this.isVisible("brand_id")) await this.getBrand();
            if (this.isVisible("group_id")) await this.getGroup();
            if (this.isVisible("unit_id")) await this.getUnit();
            if(this.isVisible("tax_id")) await this.getTax();
            if(this.isVisible("branch_id")) await this.getBranch('create');
            this.showPhoto = "./images/img-1.png";
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.product_id = null;
            this.media = {};
            this.images = [];
            this.errors = {};
        },
        async resetForm() {
            this.create = {
                title: '',
                title_e: '',
                description: '',
                description_e: '',
                category_id: null,
                brand_id: null,
                group_id: null,
                unit_id: null,
                tax_id: null,
                branch_id: null,
                product_type: 0,
                is_quantity: 0,
            };
            this.productVariant= [];
            this.productStandard = {
                purchase_price: 0,
                selling_price: 0,
                quantity: 0,
                sku: '',
                bar_code: '',
                re_order: 0
            };
            this.chipArray = [];
            this.attributes = [];
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.product_id = null;
            this.isVariant = false;
            this.media = {};
            this.images = [];
            this.errors = {};
        },
        AddSubmit() {
             if (!this.create.title) {
                 this.create.title = this.create.title_e;
             }
             if (!this.create.title_e) {
                 this.create.title_e = this.create.title;
             }

             if (!this.create.description) {
                 this.create.description = this.create.description_e;
             }
             if (!this.create.description_e) {
                 this.create.description_e = this.create.description;
             }

            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.create.created_by = this.$store.state.auth.type == 'admin' ? this.$store.state.auth.partner ? this.$store.state.auth.partner.name:this.$store.state.auth.partner.name_e:
                    this.$store.state.auth.user ? this.$store.state.auth.user.name:this.$store.state.auth.user.name_e;
                adminApi
                    .post(`/point-of-sale/product`, {
                        ...this.create,
                        'product_type': this.create.product_type == 0 ? 'standard' : 'variant',
                        company_id: this.company_id,
                    })
                    .then(async (res) => {
                        this.product_id = res.data.data.id;
                        if(this.create.product_type != 0 ){
                            await this.getAttribute();
                            let attr = [];
                            if(this.attributes.length < 1){
                                this.allAttributes.forEach(item =>{
                                    if(item.id != 0) {
                                        this.attributes.push(item);
                                        attr.push({id: item.id, names: [],len: 0});
                                    }
                                });
                                this.chipArray = attr;
                            }
                        }
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Addedsuccessfully")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }, 200);
                        this.getData();
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
        AddProductVartial() {
            this.$v.create.$touch();

            if (this.$v.productStandard.$invalid || this.$v.productVariant.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                let data = {};
                data.company_id = this.company_id;
                data.product_type = this.create.product_type == 0 ? 'standard' : 'variant';
                let formDate = new FormData();
                if(this.create.product_type == 1 ){
                    formDate.append("company_id", this.company_id);
                    formDate.append("branch_id", this.create.branch_id);
                    formDate.append("document_id", 4);
                    formDate.append("product_type", 'variant');
                    this.productVariant.forEach((el,ind) => {
                        formDate.append(`product_variant[${ind}][product_id]`, this.product_id);
                        formDate.append(`product_variant[${ind}][purchase_price]`, el.purchase_price?el.purchase_price:0);
                        formDate.append(`product_variant[${ind}][selling_price]`, el.selling_price?el.selling_price:0);
                        formDate.append(`product_variant[${ind}][attribute_values]`, el.variant_title?el.attribute_values:'');
                        formDate.append(`product_variant[${ind}][variant_title]`, el.variant_title?el.variant_title:'');
                        formDate.append(`product_variant[${ind}][bar_code]`, el.bar_code?el.bar_code:'');
                        formDate.append(`product_variant[${ind}][sku]`, el.sku?el.sku:'');
                        formDate.append(`product_variant[${ind}][isNotify]`, 0);
                        formDate.append(`product_variant[${ind}][enabled]`, el.enabled?1:0);
                        formDate.append(`product_variant[${ind}][re_order]`, el.re_order?el.re_order:0);
                        formDate.append(`product_variant[${ind}][quantity]`, el.quantity?el.quantity:0);
                        formDate.append(`product_variant[${ind}][media]`,[]);
                        el.media.forEach(e => {
                            formDate.append(`product_variant[${ind}][media][]`, e);
                        });
                        el.product_attributes.forEach((x,indChild) => {
                            formDate.append(
                                `product_variant[${ind}][product_attributes][${indChild}][product_id]`, this.product_id
                            );
                            formDate.append(
                                `product_variant[${ind}][product_attributes][${indChild}][attribute_id]`, x.attribute_id
                            );
                            formDate.append(
                                `product_variant[${ind}][product_attributes][${indChild}][values]`, x.values
                            );
                        });
                    })
                }else {
                    data = {
                        ...data,
                        product_id: this.product_id,
                        ...this.productStandard,
                        branch_id: this.create.branch_id,
                        document_id: 4
                    };
                }

                adminApi
                    .post(`/point-of-sale/product-variants`, this.create.product_type == 1 ?  formDate : data)
                    .then(async (res) => {
                        this.isVariant = true;
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Addedsuccessfully")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }, 200);
                        this.getData();
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
        editSubmit(id) {
            if (!this.edit.title) {
                this.edit.title = this.edit.title_e;
            }
            if (!this.edit.title_e) {
                this.edit.title_e = this.edit.title;
            }
            if (!this.edit.description) {
                this.edit.description = this.edit.description_e;
            }
            if (!this.edit.description_e) {
                this.edit.description_e = this.edit.description;
            }

            this.$v.edit.$touch();
            this.images.forEach((e) => {
                this.edit.old_media.push(e.id);
            });
            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                let data = {};
                data.product_type = this.edit.product_type == 0 ? 'standard' : 'variant';
                let formDate = new FormData();
                if(this.edit.product_type == 1 ){
                    formDate.append("company_id", this.company_id);
                    formDate.append("document_id", 4);
                    formDate.append("product_type", this.edit.product_type == 0 ? 'standard' : 'variant');
                    this.productVariant.forEach((el,ind) => {
                        formDate.append(`product_variant[${ind}][id]`, el.id?el.id:'');
                        formDate.append(`product_variant[${ind}][product_id]`, this.product_id);
                        formDate.append(`product_variant[${ind}][purchase_price]`, el.purchase_price?el.purchase_price:0);
                        formDate.append(`product_variant[${ind}][selling_price]`, el.selling_price?el.selling_price:0);
                        formDate.append(`product_variant[${ind}][variant_title]`, el.variant_title?el.variant_title:'');
                        formDate.append(`product_variant[${ind}][attribute_values]`, el.variant_title?el.attribute_values:'');
                        formDate.append(`product_variant[${ind}][bar_code]`, el.bar_code?el.bar_code:'');
                        formDate.append(`product_variant[${ind}][sku]`, el.sku?el.sku:'');
                        formDate.append(`product_variant[${ind}][isNotify]`, 0);
                        formDate.append(`product_variant[${ind}][enabled]`, el.enabled?1:0);
                        formDate.append(`product_variant[${ind}][re_order]`, el.re_order?el.re_order:0);
                        formDate.append(`product_variant[${ind}][quantity]`, el.quantity?el.quantity:0);
                        formDate.append(`product_variant[${ind}][media]`,[]);
                        el.media.forEach(e => {
                            formDate.append(`product_variant[${ind}][media][]`, e);
                        });
                        el.product_attributes.forEach((x,indChild) => {
                            formDate.append(
                                `product_variant[${ind}][product_attributes][${indChild}][product_id]`, this.product_id
                            );
                            formDate.append(
                                `product_variant[${ind}][product_attributes][${indChild}][attribute_id]`, x.attribute_id
                            );
                            formDate.append(
                                `product_variant[${ind}][product_attributes][${indChild}][values]`, x.values
                            );
                        });
                    });
                    formDate.append("title", this.edit.title);
                    formDate.append("title_e", this.edit.title_e);
                    formDate.append("description", this.edit.description? this.edit.description:'');
                    formDate.append("description_e", this.edit.description_e? this.edit.description_e:'');
                    formDate.append("category_id", this.edit.category_id? this.edit.category_id:'');
                    formDate.append("brand_id", this.edit.brand_id? this.edit.brand_id:'');
                    formDate.append("group_id", this.edit.group_id? this.edit.group_id:'');
                    formDate.append("unit_id", this.edit.unit_id? this.edit.unit_id:'');
                    formDate.append("branch_id", this.edit.branch_id? this.edit.branch_id:'');
                    formDate.append("tax_id", this.edit.tax_id? this.edit.tax_id:'');
                    formDate.append("is_quantity", this.edit.is_quantity);
                }else {
                    data = {
                        ...this.edit,
                        product_type: this.edit.product_type == 0 ? 'standard' : 'variant',
                        product_id: this.product_id,
                        document_id: 4,
                        ...this.productStandard,
                    };
                }

                adminApi
                    .post(`/point-of-sale/product/${id}`, this.edit.product_type == 1 ?  formDate : data)
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
        formatDate(value) {
            return formatDateOnly(value);
        },
        log(id) {
            if (this.mouseEnter != id) {
                this.Tooltip = "";
                this.mouseEnter = id;
                adminApi
                    .get(`/point-of-sale/product/logs/${id}`)
                    .then((res) => {
                        let l = res.data.data;
                        l.forEach((e) => {
                            this.Tooltip += `Created By: ${e.causer_type}; Event: ${
                                e.event
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
        async resetModalEdit(id) {
            let product = this.products.find((e) => id == e.id);
            this.product_id = id;
            this.edit.title = product.title;
            this.edit.title_e = product.title_e;
            this.edit.description = product.description;
            this.edit.description_e = product.description_e;
            this.edit.product_type = product.product_type == 'variant'? 1 : 0;
            this.edit.is_quantity = product.is_quantity;
            if(product.product_type == 'variant'){
                await this.getAttribute();
                product.product_variant.forEach((el) => {
                    let product_attributes = [];

                    el.product_attributes.forEach(item =>{
                        if(item.attribute_id != 0 && !this.attributes.find((e) => e.id == item.attribute_id)) {
                            this.attributes.push(this.allAttributes.find(e => e.id == item.attribute_id));
                            this.chipArray.push({id: item.attribute_id, names: [],len: 0});
                        }
                        let i = this.chipArray.find((e) => e.id == item.attribute_id);
                        if(!i.names.includes(item.values)){
                            i.names.push(item.values);
                        }
                        product_attributes.push({
                            attribute_id: item.attribute_id,
                            values: item.values
                        });
                    });

                    this.productVariant.push({
                        id: el.id,
                        variant_title: el.variant_title,
                        purchase_price: el.purchase_price,
                        attribute_values: el.attribute_values,
                        selling_price: el.selling_price,
                        sku: el.sku,
                        quantity: parseInt(el.product_quantity),
                        bar_code: el.bar_code,
                        re_order: el.re_order,
                        enabled: el.enabled ? true:false,
                        media: [],
                        product_attributes
                    });

                    product_attributes = [];
                });
            }else {
                if(product.product_variant.length > 0){
                    this.productStandard.product_standard_id = product.product_variant[0].id;
                    this.productStandard.purchase_price = product.product_variant[0].purchase_price;
                    this.productStandard.selling_price = product.product_variant[0].selling_price;
                    this.productStandard.quantity = parseInt(product.product_variant[0].product_quantity);
                    this.productStandard.sku = product.product_variant[0].sku;
                    this.productStandard.bar_code = product.product_variant[0].bar_code;
                    this.productStandard.re_order = product.product_variant[0].re_order;
                }
            }
            if (this.isVisible("category_id")) await this.getCategory();
            this.edit.category_id = product.category?product.category.id:null;
            if (this.isVisible("brand_id")) await this.getBrand();
            this.edit.brand_id = product.brand?product.brand.id:null;
            if (this.isVisible("group_id")) await this.getGroup();
            this.edit.group_id = product.group?product.group.id:null;
            if (this.isVisible("unit_id")) await this.getUnit();
            this.edit.unit_id = product.unit?product.unit.id:null;
            if(this.isVisible("tax_id")) await this.getTax();
            this.edit.tax_id = product.tax_id? typeof product.tax_id == 'string'? product.tax_id: product.tax_id:null;
            if(product.is_quantity == 1 && this.isVisible("branch_id")) await this.getBranch('update');
            this.edit.branch_id = product.branch? product.branch.id : null;

            this.images = product.media ?? [];
            if (this.images && this.images.length > 0) {
                this.showPhoto = this.images[this.images.length - 1].webp;
            } else {
                this.showPhoto = "./images/img-1.png";
            }
            this.errors = {};
        },
        resetModalHiddenEdit(id) {
            this.errors = {};
            this.edit = {
                title: '',
                title_e: '',
                description: '',
                description_e: '',
                category_id: null,
                brand_id: null,
                group_id: null,
                unit_id: null,
                tax_id: null,
                branch_id: null,
                product_type: 0,
                is_quantity: 0,
                old_media: [],
            };
            this.productVariant= [];
            this.chipArray = [];
            this.attributes = [];
            this.productStandard = {
                purchase_price: 0,
                selling_price: 0,
                quantity: 0,
                sku: '',
                bar_code: '',
                re_order: 0
            };
            this.product_id = null;
            this.images = [];
        },
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
        changePhoto() {
            document.getElementById("uploadImageCreate").click();
        },
        changePhotoEdit() {
            document.getElementById("uploadImageEdit").click();
        },
        onImageChanged(e) {
            const file = e.target.files[0];
            this.addImage(file);
        },
        addImage(file) {
            this.media = file; //upload
            if (file) {
                this.idDelete = null;
                let is_media = this.images.find(
                    (e) => e.name == file.name.slice(0, file.name.indexOf("."))
                );
                this.idDelete = is_media ? is_media.id : null;
                if (!this.idDelete) {
                    this.isLoader = true;
                    let formDate = new FormData();
                    formDate.append("media[0]", this.media);
                    adminApi
                        .post(`/media`, formDate)
                        .then((res) => {
                            let old_media = [];
                            this.images.forEach((e) => old_media.push(e.id));
                            let new_media = [];
                            res.data.data.forEach((e) => new_media.push(e.id));

                            adminApi
                                .post(`/point-of-sale/product/${this.product_id}`, {
                                    old_media,
                                    media: new_media,
                                })
                                .then((res) => {
                                    this.images = res.data.data.media ?? [];
                                    if (this.images && this.images.length > 0) {
                                        this.showPhoto = this.images[this.images.length - 1].webp;
                                    } else {
                                        this.showPhoto = "./images/img-1.png";
                                    }
                                    this.getData();
                                })
                                .catch((err) => {
                                    Swal.fire({
                                        icon: "error",
                                        title: `${this.$t("general.Error")}`,
                                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                                    });
                                });
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
                } else {
                    Swal.fire({
                        title: `${this.$t("general.Thisfilehasalreadybeenuploaded")}`,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: `${this.$t("general.Replace")}`,
                        cancelButtonText: `${this.$t("general.Nocancel")}`,
                        confirmButtonClass: "btn btn-success mt-2",
                        cancelButtonClass: "btn btn-danger ml-2 mt-2",
                        buttonsStyling: false,
                    }).then((result) => {
                        if (result.value) {
                            this.isLoader = true;
                            let formDate = new FormData();
                            formDate.append("media[0]", this.media);
                            adminApi
                                .post(`/media`, formDate)
                                .then((res) => {
                                    let old_media = [];
                                    this.images.forEach((e) => old_media.push(e.id));
                                    old_media.splice(old_media.indexOf(this.idDelete), 1);
                                    let new_media = [];
                                    res.data.data.forEach((e) => new_media.push(e.id));

                                    adminApi
                                        .post(`/point-of-sale/product/${this.product_id}`, {
                                            old_media,
                                            media: new_media,
                                        })
                                        .then((res) => {
                                            this.images = res.data.data.media ?? [];
                                            if (this.images && this.images.length > 0) {
                                                this.showPhoto =
                                                    this.images[this.images.length - 1].webp;
                                            } else {
                                                this.showPhoto = "./images/img-1.png";
                                            }
                                            this.getData();
                                        })
                                        .catch((err) => {
                                            Swal.fire({
                                                icon: "error",
                                                title: `${this.$t("general.Error")}`,
                                                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                                            });
                                        });
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
                    });
                }
            }
        },
        deleteImageCreate(id, index) {
            let old_media = [];
            this.images.forEach((e) => {
                if (e.id != id) {
                    old_media.push(e.id);
                }
            });
            adminApi
                .post(`/point-of-sale/product/${this.product_id}`, { old_media })
                .then((res) => {
                    this.products[index] = res.data.data;
                    this.images = res.data.data.media ?? [];
                    if (this.images && this.images.length > 0) {
                        this.showPhoto = this.images[this.images.length - 1].webp;
                    } else {
                        this.showPhoto = "./images/img-1.png";
                    }
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
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
                        fn ||
                        ("Bank Accounts" + "." || "SheetJSTableExport.") +
                        (type || "xlsx")
                    );
                }
                this.enabled3 = true;
            }, 100);
        },
        addChips(event, tempAttributeID,index) {
            let val = document.getElementById(`chip-${index}`);
            if(!this.chipArray.find(el => tempAttributeID == el.id).names.includes(`${val.value}`.trim()) && val.value != ''){
                let chip = this.chipArray.find(el => tempAttributeID == el.id);
                chip.names.push(`${val.value}`.trim());
                chip.len = chip.len + 1;
                this.checkVariant();
            }
            val.value = '';
        },
        deleteChip(event, tempAttributeID, index) {
            this.chipArray.find(el => tempAttributeID == el.id).names.splice(index, 1);
            this.checkVariant();
        },
        removeTempAttribute(index, tempAttributeID) {
            this.attributes.splice(index, 1);
            this.chipArray.splice(this.chipArray.findIndex(el => el.id == tempAttributeID),1);
            this.checkVariant();
        },
        countVariant(id) {
            let chips = this.chipArray.filter(el => el.id != id);
            let variant = this.chipArray.find(el => el.id == id);
            let variants = [];
            variant.names.forEach(x => {
                variants.push({
                    varientTitle:x,
                    product_attributes: [{
                        attribute_id: variant.id,
                        values: x
                    }]
                });
            });
            let attr = [];
            for (let t = 0; t < chips.length; t++){
                attr = [];
                if(chips[t].names.length > 0){
                    chips[t].names.forEach((el) => {
                        variants.forEach(x => {
                            attr.push({
                                varientTitle:`${x.varientTitle},${el}`,
                                product_attributes: [
                                    {
                                        attribute_id: chips[t].id,
                                        values: el
                                    },
                                    ...x.product_attributes
                                ]
                            });
                        });
                    });
                    variants = attr;
                }
            }
            return variants;
        },
        checkVariant(){
            this.productVariant = [];
            if(this.attributes.length > 0){
                let maxId = Math.max(...this.chipArray.map(object => {return object.len;}));
                let id = this.chipArray.find(el => el.len == maxId).id;
                this.countVariant(id).forEach(el => {
                    this.productVariant.push({
                        variant_title: el.varientTitle,
                        purchase_price: 0,
                        selling_price: 0,
                        attribute_values: el.varientTitle,
                        sku: '',
                        quantity: 0,
                        bar_code: '',
                        re_order: 0,
                        enabled: false,
                        media: [],
                        product_attributes: el.product_attributes
                    });
                });
            }
        },
        variantImage(event, index, classID) {
            this.productVariant[index].media = [];
            let input = event.target;
            if (input.files && input.files[0]) {
                let file = event.target.files[0];
                this.productVariant[index].media.push(file);
            } else {
                this.productVariant[index].media = [];
                document.getElementById(classID).value = '';
            }
        },
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <Group
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'group-create-product'"
            :isPage="false" type="create" :isPermission="isPermission" @created="getGroup"
        />
        <Brand
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'brand-create-product'"
            :isPage="false" type="create" :isPermission="isPermission" @created="getBrand"
        />
        <Category
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'category-create-product'"
            :isPage="false" type="create" :isPermission="isPermission" @created="getCategory"
        />
        <Unit
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'unit-create-product'"
            :isPage="false" type="create" :isPermission="isPermission" @created="getUnit"
        />
        <Attribute
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'attribute-create-product'"
            :isPage="false" type="create" :isPermission="isPermission"  @created="getAttribute"
        />
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- start search -->
                        <div class="row justify-content-between align-items-center mb-2">
                            <h4 class="header-title">
                                {{ $t("general.productsTable") }}
                            </h4>
                            <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                                <div class="d-inline-block" style="width: 22.2%">
                                    <!-- Basic dropdown -->
                                    <b-dropdown
                                        variant="primary"
                                        :text="$t('general.searchSetting')"
                                        ref="dropdown"
                                        class="btn-block setting-search"
                                    >
                                        <b-form-checkbox
                                            v-if="isVisible('bank_id')"
                                            v-model="filterSetting"
                                            :value="
                                                $i18n.locale == 'ar' ? 'bank.name' : 'bank.name_e'
                                              "
                                            class="mb-1"
                                        >
                                            {{ getCompanyKey("bank") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-model="filterSetting"
                                            v-if="isVisible('bank_id')"
                                            value="account_number"
                                            class="mb-1"
                                        >
                                            {{ getCompanyKey("bank_account_number") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="isVisible('phone')"
                                            v-model="filterSetting"
                                            value="phone"
                                            class="mb-1"
                                        >
                                            {{ getCompanyKey("bank_account_phone") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="isVisible('address')"
                                            v-model="filterSetting"
                                            value="address"
                                            class="mb-1"
                                        >
                                            {{ getCompanyKey("bank_account_address") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="isVisible('email')"
                                            v-model="filterSetting"
                                            value="email"
                                            class="mb-1"
                                        >
                                            {{ getCompanyKey("bank_account_email") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="isVisible('emp_id')"
                                            v-model="filterSetting"
                                            value="emp_id"
                                            class="mb-1"
                                        >
                                            {{ getCompanyKey("employee") }}
                                        </b-form-checkbox>
                                        <b-form-checkbox
                                            v-if="isVisible('rp_code')"
                                            v-model="filterSetting"
                                            value="rp_code"
                                            class="mb-1"
                                        >{{ getCompanyKey("bank_account_rpcode") }}
                                        </b-form-checkbox>
                                    </b-dropdown>
                                    <!-- Basic dropdown -->
                                </div>

                                <div
                                    class="d-inline-block position-relative"
                                    style="width: 77%"
                                >
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
                            </div>
                        </div>
                        <!-- end search -->
                        <div class="row justify-content-between align-items-center mb-2 px-1">
                            <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                                <!-- start create and printer -->
                                <b-button
                                    v-b-modal.create
                                    v-if="isPermission('create Bank Account')"
                                    variant="primary"
                                    class="btn-sm mx-1 font-weight-bold"
                                >
                                    {{ $t("general.Create") }}
                                    <i class="fas fa-plus"></i>
                                </b-button>
                                <div class="d-inline-flex">
                                    <button
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
                                        v-if="
                                          checkAll.length == 1 &&
                                          isPermission('update Bank Account')
                                        "
                                    >
                                        <i class="mdi mdi-square-edit-outline"></i>
                                    </button>
                                    <!-- start mult delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="
                      checkAll.length > 1 && isPermission('delete Bank Account')
                    "
                                        @click.prevent="deleteproduct(checkAll)"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!-- end mult delete  -->
                                    <!--  start one delete  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="
                      checkAll.length == 1 &&
                      isPermission('delete Bank Account')
                    "
                                        @click.prevent="deleteproduct(checkAll[0])"
                                    >
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <!--  end one delete  -->
                                    <!--  start show  -->
                                    <button
                                        class="custom-btn-dowonload"
                                        v-if="
                                          checkAll.length == 1 &&
                                          isPermission('show Bank Account')
                                        "
                                        @click.prevent="productShowFun(checkAll[0])"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <!--  end show  -->
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
                                            <b-form-checkbox
                                                v-if="isVisible('title')"
                                                v-model="setting.title"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_title") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('title_e')"
                                                v-model="setting.title_e"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_title_e") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('description')"
                                                v-model="setting.description"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_description") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('description_e')"
                                                v-model="setting.description_e"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_description_e") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('category_id')"
                                                v-model="setting.category_id"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_category") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('brand_id')"
                                                v-model="setting.brand_id"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_brand") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('tax_id')"
                                                v-model="setting.tax_id"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_tax") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('unit_id')"
                                                v-model="setting.unit_id"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_unit") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('group_id')"
                                                v-model="setting.group_id"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_group") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('brand_id')"
                                                v-model="setting.brand_id"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_brand") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('product_type')"
                                                v-model="setting.product_type"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_product_type") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('is_quantity')"
                                                v-model="setting.is_quantity"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_is_quantity") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('purchase_price')"
                                                v-model="setting.purchase_price"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_purchase_price") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('selling_price')"
                                                v-model="setting.selling_price"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_selling_price") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('sku')"
                                                v-model="setting.sku"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_sku") }}
                                            </b-form-checkbox>
                                            <b-form-checkbox
                                                v-if="isVisible('bar_code')"
                                                v-model="setting.bar_code"
                                                class="mb-1"
                                            >
                                                {{ getCompanyKey("pointOfSell_product_Barcode") }}
                                            </b-form-checkbox>
                                            <div class="d-flex justify-content-end">
                                                <a
                                                    href="javascript:void(0)"
                                                    class="btn btn-primary btn-sm"
                                                >
                                                    {{ $t('general.Apply') }}
                                                </a>
                                            </div>
                                        </b-dropdown>
                                        <!-- Basic dropdown -->
                                    </div>
                                    <!-- end filter and setting -->

                                    <!-- start Pagination -->
                                    <div
                                        class="d-inline-flex align-items-center pagination-custom"
                                    >
                                        <div class="d-inline-block" style="font-size: 13px">
                                            {{ productsPagination.from }}-{{
                                                productsPagination.to
                                            }}
                                            /
                                            {{ productsPagination.total }}
                                        </div>
                                        <div class="d-inline-block">
                                            <a
                                                href="javascript:void(0)"
                                                :style="{
                          'pointer-events':
                            productsPagination.current_page == 1
                              ? 'none'
                              : '',
                        }"
                                                @click.prevent="
                          getData(productsPagination.current_page - 1)
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
                            productsPagination.last_page ==
                            productsPagination.current_page
                              ? 'none'
                              : '',
                        }"
                                                @click.prevent="
                          getData(productsPagination.current_page + 1)
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

                        <!--  show   -->
                        <b-modal
                            id="show"
                            :title="getCompanyKey('pointOfSell_show_form')"
                            title-class="font-18"
                            size="lg"
                            :hide-footer="true"
                            body-class="bankAccount"
                            @hidden="productShow = {};isShow=false;"
                        >
                            <form v-if="isShow">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="my-1 d-flex justify-content-end">
                                            <b-button
                                                variant="danger"
                                                class="font-weight-bold"
                                                type="button"
                                                @click.prevent="$bvModal.hide(`show`)"
                                            >
                                                {{ $t("general.Cancel") }}
                                            </b-button>
                                        </div>
                                    </div>
                                    <h4 class="text-center mt-3 mb-2" style="font-size: 27px;">{{ $i18n.locale == 'ar' ? productShow.title:productShow.title_e }}</h4>
                                    <div class="row mb-1">
                                        <div class="pxs-0 col-12 col-sm-12 col-md-5 col-lg-5 col-xl-5 text-center" v-if="productShow.media">
                                            <img class="img-thumbnail" @error="src = '../../../assets/images/img-1.png'" :src="productShow.media[0].webp">
                                        </div>
                                        <div class="pxs-0 col-12 col-sm-12 col-md-5 col-lg-5 col-xl-5 text-center" v-else>
                                            <img class="img-thumbnail" src="../../../assets/images/img-1.png">
                                        </div>
                                        <div class="pxs-0 col-xs-12 col-sm-12 col-md-7 col-lg-7 col-xl-7">
                                            <table class="table mb-0 product-detail-table">
                                                <tr v-if="productShow.category">
                                                    <th class="th-size-weight">{{ getCompanyKey("pointOfSell_product_category") }}</th>
                                                    <td class="th-size">{{$i18n.locale == 'ar' ?productShow.category.name:productShow.category.name}}</td>
                                                </tr>
                                                <tr v-if="productShow.brand">
                                                    <th class="th-size-weight">{{ getCompanyKey("pointOfSell_product_brand") }}</th>
                                                    <td class="th-size">{{$i18n.locale == 'ar' ?productShow.brand.name:productShow.brand.name}}</td>
                                                </tr>
                                                <tr v-if="productShow.group">
                                                    <th class="th-size-weight">{{ getCompanyKey("pointOfSell_product_group") }}</th>
                                                    <td class="th-size">{{$i18n.locale == 'ar' ?productShow.group.name:productShow.group.name}}</td>
                                                </tr>
                                                <tr v-if="productShow.unit">
                                                    <th class="th-size-weight">{{ getCompanyKey("pointOfSell_product_unit") }}</th>
                                                    <td class="th-size">{{$i18n.locale == 'ar' ?productShow.unit.name:productShow.unit.name}}</td>
                                                </tr>
                                                <tr v-if="productShow.taxable === 'custom' && productShow.tax">
                                                    <th class="th-size-weight">{{ getCompanyKey("pointOfSell_product_tax") }}</th>
                                                    <td class="th-size">{{ $i18n.locale == 'ar' ?productShow.tax.name:productShow.tax.name_e}} ({{products.tax.percentage}})</td>
                                                </tr>
                                                <tr>
                                                    <th class="th-size-weight">{{ getCompanyKey("pointOfSell_product_product_type") }}</th>
                                                    <td class="th-size">{{productShow.product_type}}</td>
                                                </tr>
                                                <tr v-if="productShow.product_type === 'standard'">
                                                    <th class="th-size-weight">{{ getCompanyKey("pointOfSell_product_selling_price") }}</th>
                                                    <td class="th-size">{{ productShow.selling_price?parseFloat(productShow.selling_price.toFixed(2)): 0}}</td>
                                                </tr>
                                                <tr v-if="products.product_type === 'standard'">
                                                    <th class="th-size-weight">{{ getCompanyKey("pointOfSell_product_selling_price") }}</th>
                                                    <td class="th-size">{{ productShow.selling_price?parseFloat(productShow.purchase_price.toFixed(2)):0}}</td>
                                                </tr>
                                                <tr>
                                                    <th class="th-size-weight">{{ getCompanyKey("pointOfSell_product_descriptionShow") }}</th>
                                                    <td class="th-size">{{$i18n.locale == 'ar' ?productShow.description:productShow.description_e}}</td>
                                                </tr>
                                                <tr>
                                                    <th class="th-size-weight"> {{ getCompanyKey("pointOfSell_product_created_by") }}</th>
                                                    <td class="th-size">{{productShow.created_by}}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row mb-1 justify-content-center"  v-if="productShow.product_type == 'variant'">
                                        <div class="col-11">
                                            <div class="table-responsive" >
                                                <table class="table">
                                                    <thead>
                                                    <tr class="border-0">
                                                        <th class="text-center">{{ $t('general.photo') }}</th>
                                                        <th class="text-center">{{ $t('general.variant') }}</th>
                                                        <th class="text-center">{{ $t('general.attribute_values') }}</th>
                                                        <th class="text-center">{{ getCompanyKey("pointOfSell_product_purchase_price") }}</th>
                                                        <th class="text-center">{{ getCompanyKey("pointOfSell_product_selling_price") }}</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(item, index) in productShow.product_variant">
                                                        <!--Edit Variant combination-->
                                                        <td class="border-0 add-product-padding text-center">
                                                            {{ item.photo }}
                                                        </td>
                                                        <!--Edit price of the variant-->
                                                        <td class="border-0 add-product-padding text-center">
                                                            {{ item.variant_title }}
                                                        </td>
                                                        <td class="border-0 add-product-padding text-center">
                                                            {{ item.attribute_values }}
                                                        </td>
                                                        <td class="border-0 add-product-padding text-center">
                                                            {{ item.purchase_price }}
                                                        </td>
                                                        <!--Edit bar_code of the variant-->
                                                        <td class="border-0 add-product-padding text-center">
                                                            {{ item.selling_price }}
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /show   -->

                        <!--  create   -->
                        <b-modal
                            id="create"
                            :title="getCompanyKey('pointOfSell_create_form')"
                            title-class="font-18"
                            dialog-class="modal-full-width"
                            :hide-footer="true"
                            body-class="bankAccount"
                            @show="resetModal"
                            @hidden="resetModalHidden"
                        >
                            <form>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mt-1 d-flex justify-content-end">
                                            <!-- Emulate built in modal footer ok and cancel button actions -->
                                            <b-button
                                                variant="success"
                                                :disabled="!product_id"
                                                @click.prevent="resetForm"
                                                type="button"
                                                :class="[
                                                  'font-weight-bold px-2',
                                                  product_id ? 'mx-2' : '',
                                                ]"
                                            >
                                                {{ $t("general.AddNewRecord") }}
                                            </b-button>

                                            <template v-if="!product_id">
                                                <b-button
                                                    variant="success"
                                                    type="button"
                                                    class="mx-1 font-weight-bold px-3"
                                                    v-if="!isLoader"
                                                    @click.prevent="AddSubmit"
                                                >
                                                    {{ $t("general.Save") }}
                                                </b-button>

                                                <b-button
                                                    variant="success"
                                                    class="mx-1"
                                                    disabled
                                                    v-else
                                                >
                                                    <b-spinner small></b-spinner>
                                                    <span class="sr-only"
                                                    >{{ $t("login.Loading") }}...</span
                                                    >
                                                </b-button>
                                            </template>

                                            <b-button
                                                variant="danger"
                                                class="font-weight-bold"
                                                type="button"
                                                @click.prevent="resetModalHidden"
                                            >
                                                {{ $t("general.Cancel") }}
                                            </b-button>
                                        </div>
                                    </div>
                                    <b-tabs nav-class="nav-tabs nav-bordered">
                                        <b-tab :title="$t('general.DataEntry')" active>
                                            <div class="row">
                                                <div class="col-md-6" v-if="isVisible('title')">
                                                    <div class="form-group">
                                                        <label for="field-1" class="control-label">
                                                            {{ getCompanyKey("pointOfSell_product_title") }}
                                                            <span
                                                                v-if="isRequired('title')"
                                                                class="text-danger"
                                                            >*</span
                                                            >
                                                        </label>
                                                        <div dir="rtl">
                                                            <input
                                                                @keyup="arabicValue(create.title)"
                                                                type="text"
                                                                class="form-control"
                                                                data-create="1"
                                                                v-model="$v.create.title.$model"
                                                                :class="{
                                  'is-invalid':
                                    $v.create.title.$error || errors.title,
                                  'is-valid':
                                    !$v.create.title.$invalid && !errors.title,
                                }"
                                                                id="field-1"
                                                            />
                                                        </div>
                                                        <div
                                                            v-if="!$v.create.title.minLength"
                                                            class="invalid-feedback"
                                                        >
                                                            {{ $t("general.Itmustbeatleast") }}
                                                            {{ $v.create.title.$params.minLength.min }}
                                                            {{ $t("general.letters") }}
                                                        </div>
                                                        <div
                                                            v-if="!$v.create.title.maxLength"
                                                            class="invalid-feedback"
                                                        >
                                                            {{ $t("general.Itmustbeatmost") }}
                                                            {{ $v.create.title.$params.maxLength.max }}
                                                            {{ $t("general.letters") }}
                                                        </div>
                                                        <template v-if="errors.title">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.title"
                                                                :key="index"
                                                            >{{ errorMessage }}</ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" v-if="isVisible('title_e')">
                                                    <div class="form-group">
                                                        <label for="field-2" class="control-label">
                                                            {{ getCompanyKey("pointOfSell_product_title_e") }}
                                                            <span
                                                                v-if="isRequired('title_e')"
                                                                class="text-danger"
                                                            >*</span
                                                            >
                                                        </label>
                                                        <div dir="ltr">
                                                            <input
                                                                @keyup="englishValue(create.title_e)"
                                                                type="text"
                                                                class="form-control"
                                                                data-create="2"
                                                                v-model="$v.create.title_e.$model"
                                                                :class="{
                                  'is-invalid':
                                    $v.create.title_e.$error || errors.title_e,
                                  'is-valid':
                                    !$v.create.title_e.$invalid &&
                                    !errors.title_e,
                                }"
                                                                id="field-2"
                                                            />
                                                        </div>
                                                        <div
                                                            v-if="!$v.create.title_e.minLength"
                                                            class="invalid-feedback"
                                                        >
                                                            {{ $t("general.Itmustbeatleast") }}
                                                            {{ $v.create.title_e.$params.minLength.min }}
                                                            {{ $t("general.letters") }}
                                                        </div>
                                                        <div
                                                            v-if="!$v.create.title_e.maxLength"
                                                            class="invalid-feedback"
                                                        >
                                                            {{ $t("general.Itmustbeatmost") }}
                                                            {{ $v.create.title_e.$params.maxLength.max }}
                                                            {{ $t("general.letters") }}
                                                        </div>
                                                        <template v-if="errors.title_e">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.title_e"
                                                                :key="index"
                                                            >{{ errorMessage }}</ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                                <div v-if="isVisible('description')" class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="mr-2">
                                                            {{ getCompanyKey("pointOfSell_product_description") }}
                                                            <span v-if="isRequired('description')" class="text-danger">*</span>
                                                        </label>
                                                        <textarea
                                                            @input="arabicValueDescription(create.description)"
                                                            v-model="$v.create.description.$model"
                                                            class="form-control"
                                                            :maxlength="1000"
                                                            rows="4"
                                                        ></textarea>
                                                        <template v-if="errors.description">
                                                            <ErrorMessage
                                                                v-for="(
                                errorMessage, index
                              ) in errors.description"
                                                                :key="index"
                                                            >{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div v-if="isVisible('description_e')" class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="mr-2">
                                                            {{ getCompanyKey("pointOfSell_product_description_e") }}
                                                            <span v-if="isRequired('description_e')"  class="text-danger">*</span>
                                                        </label>
                                                        <textarea
                                                            @input="
                              englishValueDescription(create.description_e)
                            "
                                                            v-model="$v.create.description_e.$model"
                                                            class="form-control"
                                                            :maxlength="1000"
                                                            rows="4"
                                                        ></textarea>
                                                        <template v-if="errors.description_e">
                                                            <ErrorMessage
                                                                v-for="(
                                errorMessage, index
                              ) in errors.description_e"
                                                                :key="index"
                                                            >{{ errorMessage }}</ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr v-if="isVisible('category_id')||isVisible('brand_id')||isVisible('group_id')||isVisible('unit_id')||isVisible('tax_id')" style="
                        margin: 10px 0 !important;
                        border-top: 1px solid rgb(141 163 159 / 42%);
                      "/>
                                            <div class="row">
                                                <div v-if="isVisible('category_id')" class="col-md-3">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("pointOfSell_product_category") }}
                                                            <span v-if="isRequired('category_id')" class="text-danger">*</span>
                                                        </label>
                                                        <multiselect
                                                            @input="showCategoryModal"
                                                            v-model="$v.create.category_id.$model"
                                                            :options="categories.map((type) => type.id)"
                                                            :custom-label="(opt) => categories.find((x) => x.id == opt)?
                                                               $i18n.locale == 'ar' ? categories.find((x) => x.id == opt).name : categories.find((x) => x.id == opt).name_e
                                                              : null
                                                            "
                                                        >
                                                        </multiselect>
                                                        <div
                                                            v-if="
                                                          $v.create.category_id.$error || errors.category_id
                                                        "
                                                            class="text-danger"
                                                        >
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>
                                                        <template v-if="errors.category_id">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.category_id"
                                                                :key="index"
                                                            >{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div v-if="isVisible('brand_id')" class="col-md-3">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("pointOfSell_product_brand") }}
                                                            <span v-if="isRequired('brand_id')" class="text-danger">*</span>
                                                        </label>
                                                        <multiselect
                                                            @input="showBrandModal"
                                                            v-model="$v.create.brand_id.$model"
                                                            :options="brands.map((type) => type.id)"
                                                            :custom-label="(opt) =>  brands.find((x) => x.id == opt)?
                                                                $i18n.locale == 'ar' ? brands.find((x) => x.id == opt).name : brands.find((x) => x.id == opt).name_e
                                                              :null
                                                            "
                                                        >
                                                        </multiselect>
                                                        <div
                                                            v-if="
                                                          $v.create.brand_id.$error || errors.brand_id
                                                        "
                                                            class="text-danger"
                                                        >
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>
                                                        <template v-if="errors.brand_id">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.category_id"
                                                                :key="index"
                                                            >{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div v-if="isVisible('group_id')" class="col-md-3">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("pointOfSell_product_group") }}
                                                            <span v-if="isRequired('group_id')" class="text-danger">*</span>
                                                        </label>
                                                        <multiselect
                                                            @input="showGroupModal"
                                                            v-model="$v.create.group_id.$model"
                                                            :options="groups.map((type) => type.id)"
                                                            :custom-label="(opt) => groups.find((x) => x.id == opt)?
                                                                   $i18n.locale == 'ar' ? groups.find((x) => x.id == opt).name : groups.find((x) => x.id == opt).name_e
                                                                  :null
                                                            "
                                                        >
                                                        </multiselect>
                                                        <div
                                                            v-if="
                                                          $v.create.group_id.$error || errors.group_id
                                                        "
                                                            class="text-danger"
                                                        >
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>
                                                        <template v-if="errors.group_id">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.group_id"
                                                                :key="index"
                                                            >{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div v-if="isVisible('unit_id')" class="col-md-3">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("pointOfSell_product_unit") }}
                                                            <span v-if="isRequired('group_id')" class="text-danger">*</span>
                                                        </label>
                                                        <multiselect
                                                            @input="showUnitModal"
                                                            v-model="$v.create.unit_id.$model"
                                                            :options="units.map((type) => type.id)"
                                                            :custom-label="(opt) => units.find((x) => x.id == opt)?
                                                                   $i18n.locale == 'ar' ? units.find((x) => x.id == opt).name : units.find((x) => x.id == opt).name_e
                                                                  :null
                                                            "
                                                        >
                                                        </multiselect>
                                                        <div
                                                            v-if="
                                                          $v.create.unit_id.$error || errors.unit_id
                                                        "
                                                            class="text-danger"
                                                        >
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>
                                                        <template v-if="errors.unit_id">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.unit_id"
                                                                :key="index"
                                                            >{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div v-if="isVisible('tax_id')" class="col-md-3">
                                                    <label class="control-label">
                                                        {{ getCompanyKey("pointOfSell_product_tax") }}
                                                        <span v-if="isRequired('tax_id')" class="text-danger">*</span>
                                                    </label>
                                                    <select
                                                        @input="showTaxModal"
                                                        v-model="$v.create.tax_id.$model"
                                                        class="custom-select"
                                                    >
                                                        <option value disabled>{{ $t('general.choose') }}</option>
                                                        <option value="no-tax">{{ $t('general.no_tax') }}</option>
                                                        <option value="default-tax">{{ $t('general.default_tax') }}</option>
                                                        <option v-for="tax in taxes" :value="tax.id">{{ $i18n.locale == 'ar'?tax.name:tax.name_e }}</option>
                                                    </select>
                                                    <div
                                                        v-if="
                                                          $v.create.tax_id.$error || errors.tax_id
                                                        "
                                                        class="text-danger"
                                                    >
                                                        {{ $t("general.fieldIsRequired") }}
                                                    </div>
                                                    <template v-if="errors.tax_id">
                                                        <ErrorMessage
                                                            v-for="(errorMessage, index) in errors.tax_id"
                                                            :key="index"
                                                        >{{ errorMessage }}
                                                        </ErrorMessage>
                                                    </template>
                                                </div>
                                            </div>
                                            <hr v-if="isVisible('product_type')||isVisible('is_quantity')||isVisible('branch_id')" style="
                        margin: 10px 0 !important;
                        border-top: 1px solid rgb(141 163 159 / 42%);
                      "/>
                                            <div class="row">
                                                <div class="col-md-3" v-if="isVisible('product_type')">
                                                    <div class="form-group">
                                                        <label class="my-1 mr-2">
                                                            {{ getCompanyKey("pointOfSell_product_product_type") }}
                                                            <span v-if="isRequired('product_type')" class="text-danger">*</span>
                                                        </label>
                                                        <b-form-group>
                                                            <b-form-radio
                                                                class="d-inline-block"
                                                                v-model="$v.create.product_type.$model"
                                                                name="some-create"
                                                                :disabled="product_id ? true:false"
                                                                value="1"
                                                            >{{ $t("general.variant") }}</b-form-radio
                                                            >
                                                            <b-form-radio
                                                                class="d-inline-block m-1"
                                                                :disabled="product_id ? true:false"
                                                                v-model="$v.create.product_type.$model"
                                                                name="some-radios-create"
                                                                value="0"
                                                            >{{ $t("general.standard") }}</b-form-radio
                                                            >
                                                        </b-form-group>
                                                        <template v-if="errors.product_type">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.product_type"
                                                                :key="index"
                                                            >{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-3" v-if="isVisible('is_quantity')">
                                                    <div class="form-group">
                                                        <label class="my-1 mr-2">
                                                            {{ getCompanyKey("pointOfSell_product_is_quantity") }}
                                                            <span v-if="isRequired('is_quantity')" class="text-danger">*</span>
                                                        </label>
                                                        <b-form-group>
                                                            <b-form-radio
                                                                class="d-inline-block"
                                                                v-model="$v.create.is_quantity.$model"
                                                                :disabled="product_id ? true:false"
                                                                @change="getBranch"
                                                                name="some_is_quantity"
                                                                value="1"
                                                            >{{ $t("general.Yes") }}</b-form-radio
                                                            >
                                                            <b-form-radio
                                                                class="d-inline-block m-1"
                                                                v-model="$v.create.is_quantity.$model"
                                                                :disabled="product_id ? true:false"
                                                                name="some_is_quantity"
                                                                value="0"
                                                            >{{ $t("general.No") }}</b-form-radio
                                                            >
                                                        </b-form-group>
                                                        <template v-if="errors.is_quantity">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.is_quantity"
                                                                :key="index"
                                                            >{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                                <div v-if="isVisible('branch_id') && create.is_quantity == 1 && branches.length > 1" class="col-md-3">
                                                    <div class="form-group position-relative">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("pointOfSell_product_branch") }}
                                                            <span v-if="isRequired('branch_id')" class="text-danger">*</span>
                                                        </label>
                                                        <multiselect
                                                            :disabled="product_id ? true:false"
                                                            v-model="$v.create.branch_id.$model"
                                                            :options="branches.map((type) => type.id)"
                                                            :custom-label="
                                                              (opt) => $i18n.locale == 'ar' ? branches.find((x) => x.id == opt).name : branches.find((x) => x.id == opt).name_e
                                                            "
                                                        >
                                                        </multiselect>
                                                        <div
                                                            v-if="
                                                          $v.create.branch_id.$error || errors.branch_id
                                                        "
                                                            class="text-danger"
                                                        >
                                                            {{ $t("general.fieldIsRequired") }}
                                                        </div>
                                                        <template v-if="errors.branch_id">
                                                            <ErrorMessage
                                                                v-for="(errorMessage, index) in errors.branch_id"
                                                                :key="index"
                                                            >{{ errorMessage }}
                                                            </ErrorMessage>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </b-tab>
                                        <b-tab
                                            :disabled="!product_id"
                                            :title="$t('general.ImageUploads')"
                                        >
                                            <div class="row">
                                                <input
                                                    accept="image/png, image/gif, image/jpeg, image/jpg"
                                                    type="file"
                                                    id="uploadImageCreate"
                                                    @change.prevent="onImageChanged"
                                                    class="input-file-upload position-absolute"
                                                    :class="[
                                                    'd-none',
                                                    {
                                                      'is-invalid':
                                                        $v.create.media.$error || errors.media,
                                                      'is-valid':
                                                        !$v.create.media.$invalid && !errors.media,
                                                    },
                                                  ]"
                                                />
                                                <div class="col-md-8 my-1">
                                                    <!-- file upload -->
                                                    <div
                                                        class="row align-content-between"
                                                        style="width: 100%; height: 100%"
                                                    >
                                                        <div class="col-12">
                                                            <div class="d-flex flex-wrap">
                                                                <div
                                                                    :class="[
                                    'dropzone-previews col-4 position-relative mb-2',
                                  ]"
                                                                    v-for="(photo, index) in images"
                                                                    :key="photo.id"
                                                                >
                                                                    <div
                                                                        :class="[
                                      'card mb-0 shadow-none border',
                                      images.length - 1 == index
                                        ? 'bg-primary'
                                        : '',
                                    ]"
                                                                    >
                                                                        <div class="p-2">
                                                                            <div class="row align-items-center">
                                                                                <div
                                                                                    class="col-auto"
                                                                                    @click="showPhoto = photo.webp"
                                                                                >
                                                                                    <img
                                                                                        data-dz-thumbnail
                                                                                        :src="photo.webp"
                                                                                        class="avatar-sm rounded bg-light"
                                                                                        @error="src = './images/img-1.png'"
                                                                                    />
                                                                                </div>
                                                                                <div class="col pl-0">
                                                                                    <a
                                                                                        href="javascript:void(0);"
                                                                                        :class="[
                                              'font-weight-bold',
                                              images.length - 1 == index
                                                ? 'text-white'
                                                : 'text-muted',
                                            ]"
                                                                                        data-dz-name
                                                                                    >
                                                                                        {{ photo.name }}
                                                                                    </a>
                                                                                </div>
                                                                                <!-- Button -->
                                                                                <a
                                                                                    href="javascript:void(0);"
                                                                                    :class="[
                                            'btn-danger dropzone-close',
                                            $i18n.locale == 'ar'
                                              ? 'dropzone-close-rtl'
                                              : '',
                                          ]"
                                                                                    data-dz-remove
                                                                                    @click.prevent="
                                            deleteImageCreate(photo.id, index)
                                          "
                                                                                >
                                                                                    <i class="fe-x"></i>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="footer-image col-12">
                                                            <b-button
                                                                @click="changePhoto"
                                                                variant="success"
                                                                type="button"
                                                                class="mx-1 font-weight-bold px-3"
                                                                v-if="!isLoader"
                                                            >
                                                                {{ $t("general.Add") }}
                                                            </b-button>
                                                            <b-button
                                                                variant="success"
                                                                class="mx-1"
                                                                disabled
                                                                v-else
                                                            >
                                                                <b-spinner small></b-spinner>
                                                                <span class="sr-only"
                                                                >{{ $t("login.Loading") }}...</span
                                                                >
                                                            </b-button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="show-dropzone">
                                                        <img
                                                            :src="showPhoto"
                                                            class="img-thumbnail"
                                                            @error="src = './images/img-1.png'"
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                        </b-tab>
                                        <b-tab :title="$t('general.variant')" :disabled="!product_id">
                                            <div class="mb-1 d-flex justify-content-end">
                                                <b-button
                                                    variant="success"
                                                    type="button"
                                                    class="mx-1 font-weight-bold px-3"
                                                    v-if="!isLoader"
                                                    :disabled="isVariant"
                                                    @click.prevent="AddProductVartial"
                                                >
                                                    {{ $t("general.Save") }}
                                                </b-button>

                                                <b-button
                                                    variant="success"
                                                    class="mx-1"
                                                    disabled
                                                    v-else
                                                >
                                                    <b-spinner small></b-spinner>
                                                    <span class="sr-only"
                                                    >{{ $t("login.Loading") }}...</span
                                                    >
                                                </b-button>
                                            </div>
                                            <div class="row" v-if="isVisible('product_type') && create.product_type == 0">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("pointOfSell_product_purchase_price") }}
                                                        </label>
                                                        <input
                                                            type="number"
                                                            class="form-control"
                                                            step="0.01"
                                                            v-model.number="$v.productStandard.purchase_price.$model"
                                                            :class="{
                                                              'is-invalid':
                                                                $v.productStandard.purchase_price.$error ||
                                                                errors.purchase_price,
                                                              'is-valid':
                                                                !$v.productStandard.purchase_price.$invalid &&
                                                                !errors.purchase_price,
                                                            }"
                                                        />
                                                        <template v-if="errors.purchase_price">
                                                            <ErrorMessage
                                                                v-for="(
                                                                errorMessage, index
                                                              ) in errors.purchase_price"
                                                                :key="index"
                                                            >{{ errorMessage }}</ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("pointOfSell_product_selling_price") }}
                                                        </label>
                                                        <input
                                                            type="number"
                                                            class="form-control"
                                                            step="0.01"
                                                            v-model.number="$v.productStandard.selling_price.$model"
                                                            :class="{
                                                              'is-invalid':
                                                                $v.productStandard.selling_price.$error ||
                                                                errors.purchase_price,
                                                              'is-valid':
                                                                !$v.productStandard.selling_price.$invalid &&
                                                                !errors.selling_price,
                                                            }"
                                                        />
                                                        <template v-if="errors.selling_price">
                                                            <ErrorMessage
                                                                v-for="(
                                                                errorMessage, index
                                                              ) in errors.selling_price"
                                                                :key="index"
                                                            >{{ errorMessage }}</ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("pointOfSell_product_sku") }}
                                                        </label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            v-model="$v.productStandard.sku.$model"
                                                            :class="{
                                                              'is-invalid':
                                                                $v.productStandard.sku.$error ||
                                                                errors.sku,
                                                              'is-valid':
                                                                !$v.productStandard.sku.$invalid &&
                                                                !errors.sku,
                                                            }"
                                                        />
                                                        <template v-if="errors.sku">
                                                            <ErrorMessage
                                                                v-for="(
                                                                errorMessage, index
                                                              ) in errors.sku"
                                                                :key="index"
                                                            >{{ errorMessage }}</ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("pointOfSell_product_Barcode") }}
                                                        </label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            v-model="$v.productStandard.bar_code.$model"
                                                            :class="{
                                                              'is-invalid':
                                                                $v.productStandard.bar_code.$error ||
                                                                errors.sku,
                                                              'is-valid':
                                                                !$v.productStandard.bar_code.$invalid &&
                                                                !errors.sku,
                                                            }"
                                                        />
                                                        <template v-if="errors.bar_code">
                                                            <ErrorMessage
                                                                v-for="(
                                                                errorMessage, index
                                                              ) in errors.bar_code"
                                                                :key="index"
                                                            >{{ errorMessage }}</ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-3" v-if="create.is_quantity == 1">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("pointOfSell_product_quantity") }}
                                                        </label>
                                                        <input
                                                            type="number"
                                                            class="form-control"
                                                            v-model.number="$v.productStandard.quantity.$model"
                                                            :class="{
                                                              'is-invalid':
                                                                $v.productStandard.quantity.$error ||
                                                                errors.sku,
                                                              'is-valid':
                                                                !$v.productStandard.quantity.$invalid &&
                                                                !errors.sku,
                                                            }"
                                                        />
                                                        <template v-if="errors.quantity">
                                                            <ErrorMessage
                                                                v-for="(
                                                                errorMessage, index
                                                              ) in errors.quantity"
                                                                :key="index"
                                                            >{{ errorMessage }}</ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">
                                                            {{ getCompanyKey("pointOfSell_product_re_order") }}
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <input
                                                            type="number"
                                                            class="form-control"
                                                            v-model.number="$v.productStandard.re_order.$model"
                                                            :class="{
                                                              'is-invalid':
                                                                $v.productStandard.re_order.$error ||
                                                                errors.re_order,
                                                              'is-valid':
                                                                !$v.productStandard.re_order.$invalid &&
                                                                !errors.re_order,
                                                            }"
                                                        />
                                                        <template v-if="errors.re_order">
                                                            <ErrorMessage
                                                                v-for="(
                                                                errorMessage, index
                                                              ) in errors.re_order"
                                                                :key="index"
                                                            >{{ errorMessage }}</ErrorMessage
                                                            >
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="isVisible('product_type') && create.product_type == 1" id="addVariantSection" class="mb-3 bg-white rounded p-3">
                                                <div class="row justify-content-between">
                                                    <div class="col-4">
                                                        <h5 class="font-22">{{ $t('general.add_product_variants') }}</h5>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="row no-gutters">
                                                            <div class="col">
                                                                <div>
                                                                    <multiselect
                                                                        @input="showAttributeModal"
                                                                        :options="allAttributes.map((type) => type.id)"
                                                                        :custom-label="
                                                                          (opt) => allAttributes.find((x) => x.id == opt)?
                                                                          $i18n.locale == 'ar' ? allAttributes.find((x) => x.id == opt).name : allAttributes.find((x) => x.id == opt).name_e
                                                                          :null
                                                                        "
                                                                    >
                                                                    </multiselect>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div  v-for="(tempAttribute,index) in attributes">
                                                    <div class="row justify-content-center">
                                                         <div class="col-9">
                                                                <div class="variant-values">
                                                                    <label>{{$i18n.locale == 'ar' ?tempAttribute.name:tempAttribute.name_e }}</label>
                                                                    <div class="chips-container">
                                                                    <span
                                                                        class="chip"
                                                                        v-for="(chips,chipIndex) in chipArray.find(el => tempAttribute.id == el.id).names"
                                                                    >
                                                                        {{ chips }}
                                                                        <i
                                                                            class="fas fa-times"
                                                                            @click.prevent="deleteChip($event,tempAttribute.id,chipIndex)"
                                                                        ></i>
                                                                    </span>
                                                                    </div>
                                                                    <div class="input-group mb-2">
                                                                        <input
                                                                            type="text"
                                                                            :id="`chip-${index}`"
                                                                            class="form-control"
                                                                            @keyup.enter="addChips($event,tempAttribute.id,index)"
                                                                            aria-describedby="basic-addon2"
                                                                            value=""
                                                                        />
                                                                        <div class="input-group-append">
                                                                            <button
                                                                                class="custom-btn-dowonload"
                                                                                type="button"
                                                                                @click.prevent="addChips($event,tempAttribute.id,index)"
                                                                            >
                                                                                <i class="fas fa-plus"></i>
                                                                            </button>
                                                                            <button
                                                                                class="custom-btn-dowonload"
                                                                                type="button"
                                                                                @click.prevent="removeTempAttribute(index,tempAttribute.id)"
                                                                            >
                                                                                <i class="fas fa-trash-alt"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="bg-white rounded p-3" v-if="productVariant.length > 0 && create.product_type == 1">
                                                <div class="form-row">
                                                    <div class="form-group col-md-12">
                                                        <h5 class="m-0 font-22">{{ $t('general.add_variant_details') }}</h5>
                                                    </div>
                                                </div>
                                                <!--For Edit variants-->
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                        <tr class="border-0">
                                                            <th>#</th>
                                                            <th class="text-center">{{ $t('general.variant') }}</th>
                                                            <th class="text-center">{{ getCompanyKey("pointOfSell_product_purchase_price") }}</th>
                                                            <th class="text-center">{{ getCompanyKey("pointOfSell_product_selling_price") }}</th>
                                                            <th class="text-center">{{ getCompanyKey("pointOfSell_product_Barcode") }}</th>
                                                            <th class="text-center">{{ getCompanyKey("pointOfSell_product_sku") }}</th>
                                                            <th v-if="create.is_quantity == 1" class="text-center">{{ getCompanyKey('pointOfSell_product_quantity') }}</th>
                                                            <th class="text-center">{{ getCompanyKey("pointOfSell_product_re_order") }}</th>
                                                            <th class="text-center">{{ $t('general.photo') }}</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(item, index) in productVariant">
                                                            <!--Edit check box-->
                                                            <td class="border-0 add-product-padding" style="vertical-align: middle;">
                                                                    <input
                                                                        class="form-control"
                                                                        type="checkbox"
                                                                        v-model="item.enabled"
                                                                    />
                                                            </td>

                                                            <!--Edit Variant combination-->
                                                            <td class="border-0 add-product-padding">
                                                                <input
                                                                    v-model="item.variant_title"
                                                                    type="text"
                                                                    class="form-control"
                                                                    :disabled="item.enabled"
                                                                    style="width: 100%"
                                                                />
                                                            </td>

                                                            <!--Edit price of the variant-->
                                                            <td class="border-0 add-product-padding">
                                                                <input
                                                                    type="number"
                                                                    v-model.number="item.purchase_price"
                                                                    step=".01"
                                                                    :disabled="item.enabled"
                                                                    class="form-control"
                                                                />
                                                            </td>
                                                            <td class="border-0 add-product-padding">
                                                                <input
                                                                    type="number"
                                                                    step=".01"
                                                                    :disabled="item.enabled"
                                                                    v-model.number="item.selling_price"
                                                                    class="form-control"
                                                                />
                                                            </td>

                                                            <!--Edit bar_code of the variant-->
                                                            <td class="border-0 add-product-padding">
                                                                <input
                                                                    v-model="item.bar_code"
                                                                    :disabled="item.enabled"
                                                                    type="text"
                                                                    class="form-control"
                                                                />
                                                                <template v-if="errors[`product_variant.${index}.bar_code`]">
                                                                    <ErrorMessage
                                                                        v-for="(errorMessage, index) in errors[`product_variant.${index}.bar_code`]"
                                                                        :key="index"
                                                                    >{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </td>

                                                            <!--Edit sku of the variant-->
                                                            <td class="border-0 add-product-padding">
                                                                <input
                                                                    type="text"
                                                                    :disabled="item.enabled"
                                                                    v-model="item.sku"
                                                                    class="form-control"
                                                                />
                                                                <template v-if="errors[`product_variant.${index}.sku`]">
                                                                    <ErrorMessage
                                                                        v-for="(errorMessage, index) in errors[`product_variant.${index}.sku`]"
                                                                        :key="index"
                                                                    >{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </td>

                                                            <!--Edit qty of the variant-->
                                                            <td class="border-0 add-product-padding" v-if="create.is_quantity == 1">
                                                                <input
                                                                    type="number"
                                                                    :disabled="item.enabled"
                                                                    v-model.number="item.quantity"
                                                                    class="form-control"
                                                                />
                                                            </td>

                                                            <td class="border-0 add-product-padding">
                                                                <input
                                                                    type="number"
                                                                    min="0"
                                                                    :disabled="item.enabled"
                                                                    v-model.number="item.re_order"
                                                                    class="form-control"
                                                                />
                                                            </td>
                                                            <td class="border-0 add-product-padding">
                                                                <div class="custom-file" style="padding-right: 100%">
                                                                    <input
                                                                        type="file"
                                                                        class="custom-file-input"
                                                                        :disabled="item.enabled"
                                                                        :id="'variant-image-'+index"
                                                                        accept="image/*"
                                                                        @change="variantImage($event, index, '#variant-image-'+index)"
                                                                    />
                                                                    <label
                                                                        class="custom-file-label text-truncate"
                                                                        :for="'variant-image-'+index"
                                                                    >{{ $t('general.image_only') }}</label>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </b-tab>
                                    </b-tabs>
                                </div>
                            </form>
                        </b-modal>
                        <!--  /create   -->

                        <!-- start .table-responsive-->
                        <div
                            class="table-responsive mb-3 custom-table-theme position-relative"
                        >
                            <!--       start loader       -->
                            <loader size="large" v-if="isLoader" />
                            <!--       end loader       -->

                            <table
                                class="table table-borderless table-hover table-centered m-0"
                                ref="exportable_table"
                                id="printData"
                            >
                                <thead>
                                <tr>
                                    <th
                                        v-if="enabled3"
                                        class="do-not-print"
                                        scope="col"
                                        style="width: 0"
                                    >
                                        <div class="form-check custom-control">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                v-model="isCheckAll"
                                                style="width: 17px; height: 17px"
                                            />
                                        </div>
                                    </th>
                                    <th v-if="setting.title && isVisible('title')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("pointOfSell_product_title") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.title_e && isVisible('title_e')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("pointOfSell_product_title_e") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.category_id && isVisible('category_id')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("pointOfSell_product_category") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.brand_id && isVisible('brand_id')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("pointOfSell_product_brand") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.group_id && isVisible('group_id')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("pointOfSell_product_group") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.unit_id && isVisible('unit_id')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("pointOfSell_product_unit") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.tax_id && isVisible('tax_id')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("pointOfSell_product_tax") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.product_type && isVisible('product_type')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("pointOfSell_product_product_type") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.description && isVisible('description')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("pointOfSell_product_description") }}</span>
                                        </div>
                                    </th>
                                    <th v-if="setting.description_e && isVisible('description_e')">
                                        <div class="d-flex justify-content-center">
                                            <span>{{ getCompanyKey("pointOfSell_product_description_e") }}</span>
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
                                <tbody v-if="products.length > 0">
                                <tr
                                    @click.capture="checkRow(data.id)"
                                    @dblclick.prevent="
                                      isPermission('update Bank Account')
                                        ? $bvModal.show(`modal-edit-${data.id}`)
                                        : false
                                    "
                                    v-for="(data, index) in products"
                                    :key="data.id"
                                    class="body-tr-custom"
                                >
                                    <td v-if="enabled3" class="do-not-print">
                                        <div
                                            class="form-check custom-control"
                                            style="min-height: 1.9em"
                                        >
                                            <input
                                                style="width: 17px; height: 17px"
                                                class="form-check-input"
                                                type="checkbox"
                                                v-model="checkAll"
                                                :value="data.id"
                                            />
                                        </div>
                                    </td>
                                    <th v-if="setting.title && isVisible('title')">
                                        {{ data.title }}
                                    </th>
                                    <th v-if="setting.title_e && isVisible('title_e')">
                                        {{ data.title_e }}
                                    </th>
                                    <th v-if="setting.category_id && isVisible('category_id')">
                                        {{ data.category ? $i18n.locale == 'ar'? data.category.name: data.category.name_e : '-'  }}
                                    </th>
                                    <th v-if="setting.brand_id && isVisible('brand_id')">
                                        {{ data.brand ? $i18n.locale == 'ar'? data.brand.name: data.brand.name_e : '-'  }}
                                    </th>
                                    <th v-if="setting.group_id && isVisible('group_id')">
                                        {{ data.group ? $i18n.locale == 'ar'? data.group.name: data.group.name_e : '-'  }}
                                    </th>
                                    <th v-if="setting.unit_id && isVisible('unit_id')">
                                        {{ data.unit ? $i18n.locale == 'ar'? data.unit.name: data.unit.name_e : '-'  }}
                                    </th>
                                    <th v-if="setting.tax_id && isVisible('tax_id')">
                                        {{ data.tax_id ?
                                            typeof data.tax_id == 'string'? data.tax_id :
                                            $i18n.locale == 'ar'? data.tax.name: data.tax.name_e
                                            : '-'
                                        }}
                                    </th>
                                    <th v-if="setting.product_type && isVisible('product_type')">
                                        <span
                                             :class="[
                                              data.product_type == 'standard' ? 'text-success' : 'text-danger',
                                              'badge',
                                            ]"
                                         >
                                                            {{
                                                     data.product_type == 'standard'
                                                         ? `${$t("general.standard")}`
                                                         : `${$t("general.variant")}`
                                                        }}
                                        </span>
                                    </th>
                                    <th v-if="setting.description && isVisible('description')">
                                        {{ data.description }}
                                    </th>
                                    <th v-if="setting.description_e && isVisible('description_e')">
                                        {{ data.description_e }}
                                    </th>
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
                                                    v-if="isPermission('update Bank Account')"
                                                    @click="$bvModal.show(`modal-edit-${data.id}`)"
                                                >
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black"
                                                    >
                                                        <span>{{ $t("general.edit") }}</span>
                                                        <i
                                                            class="mdi mdi-square-edit-outline text-info"
                                                        ></i>
                                                    </div>
                                                </a>
                                                <a
                                                    class="dropdown-item"
                                                    href="#"
                                                    v-if="isPermission('show Bank Account')"
                                                    @click="productShowFun(data.id)"
                                                >
                                                    <div
                                                        class="d-flex justify-content-between align-items-center text-black"
                                                    >
                                                        <span>{{ $t("general.show") }}</span>
                                                        <i class="fas fa-eye text-secondary"></i>
                                                    </div>
                                                </a>
                                                <a
                                                    v-if="isPermission('delete Bank Account')"
                                                    class="dropdown-item text-black"
                                                    href="#"
                                                    @click.prevent="deleteproduct(data.id)"
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
                                            :title="getCompanyKey('pointOfSell_edit_form')"
                                            title-class="font-18"
                                            dialog-class="modal-full-width"
                                            :ref="`edit-${data.id}`"
                                            :hide-footer="true"
                                            @show="resetModalEdit(data.id)"
                                            @hidden="resetModalHiddenEdit(data.id)"
                                            body-class="bankAccount"
                                        >
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="mt-1 d-flex justify-content-end">
                                                        <!-- Emulate built in modal footer ok and cancel button actions -->
                                                        <b-button
                                                            variant="success"
                                                            @click.prevent="editSubmit(data.id)"
                                                            type="button"
                                                            class="mx-1 font-weight-bold px-3"
                                                            v-if="!isLoader"
                                                        >
                                                            {{ $t("general.edit") }}
                                                        </b-button>

                                                        <b-button
                                                            variant="success"
                                                            class="mx-1"
                                                            disabled
                                                            v-else
                                                        >
                                                            <b-spinner small></b-spinner>
                                                            <span class="sr-only"
                                                            >{{ $t("login.Loading") }}...</span
                                                            >
                                                        </b-button>

                                                        <b-button
                                                            variant="danger"
                                                            class="font-weight-bold"
                                                            type="button"
                                                            @click.prevent="
                                  $bvModal.hide(`modal-edit-${data.id}`)
                                "
                                                        >
                                                            {{ $t("general.Cancel") }}
                                                        </b-button>
                                                    </div>
                                                </div>
                                                <b-tabs nav-class="nav-tabs nav-bordered">
                                                    <b-tab :title="$t('general.DataEntry')" active>
                                                        <div class="row">
                                                            <div class="col-md-6" v-if="isVisible('title')">
                                                                <div class="form-group">
                                                                    <label for="field-edit-1" class="control-label">
                                                                        {{ getCompanyKey("pointOfSell_product_title") }}
                                                                        <span
                                                                            v-if="isRequired('title')"
                                                                            class="text-danger"
                                                                        >*</span
                                                                        >
                                                                    </label>
                                                                    <div dir="rtl">
                                                                        <input
                                                                            @keyup="arabicValue(edit.title)"
                                                                            type="text"
                                                                            class="form-control"
                                                                            data-create="1"
                                                                            v-model="$v.edit.title.$model"
                                                                            :class="{
                                                                              'is-invalid':
                                                                                $v.edit.title.$error || errors.title,
                                                                              'is-valid':
                                                                                !$v.edit.title.$invalid && !errors.title,
                                                                            }"
                                                                            id="field-edit-1"
                                                                        />
                                                                    </div>
                                                                    <div
                                                                        v-if="!$v.edit.title.minLength"
                                                                        class="invalid-feedback"
                                                                    >
                                                                        {{ $t("general.Itmustbeatleast") }}
                                                                        {{ $v.edit.title.$params.minLength.min }}
                                                                        {{ $t("general.letters") }}
                                                                    </div>
                                                                    <div
                                                                        v-if="!$v.edit.title.maxLength"
                                                                        class="invalid-feedback"
                                                                    >
                                                                        {{ $t("general.Itmustbeatmost") }}
                                                                        {{ $v.edit.title.$params.maxLength.max }}
                                                                        {{ $t("general.letters") }}
                                                                    </div>
                                                                    <template v-if="errors.title">
                                                                        <ErrorMessage
                                                                            v-for="(errorMessage, index) in errors.title"
                                                                            :key="index"
                                                                        >{{ errorMessage }}</ErrorMessage
                                                                        >
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6" v-if="isVisible('title_e')">
                                                                <div class="form-group">
                                                                    <label for="field-edit-2" class="control-label">
                                                                        {{ getCompanyKey("pointOfSell_product_title_e") }}
                                                                        <span
                                                                            v-if="isRequired('title_e')"
                                                                            class="text-danger"
                                                                        >*</span
                                                                        >
                                                                    </label>
                                                                    <div dir="ltr">
                                                                        <input
                                                                            @keyup="englishValue(edit.title_e)"
                                                                            type="text"
                                                                            class="form-control"
                                                                            data-edit="2"
                                                                            v-model="$v.edit.title_e.$model"
                                                                            :class="{
                                                                              'is-invalid':
                                                                                $v.edit.title_e.$error || errors.title_e,
                                                                              'is-valid':
                                                                                !$v.edit.title_e.$invalid &&
                                                                                !errors.title_e,
                                                                            }"
                                                                            id="field-edit-2"
                                                                        />
                                                                    </div>
                                                                    <div
                                                                        v-if="!$v.edit.title_e.minLength"
                                                                        class="invalid-feedback"
                                                                    >
                                                                        {{ $t("general.Itmustbeatleast") }}
                                                                        {{ $v.edit.title_e.$params.minLength.min }}
                                                                        {{ $t("general.letters") }}
                                                                    </div>
                                                                    <div
                                                                        v-if="!$v.edit.title_e.maxLength"
                                                                        class="invalid-feedback"
                                                                    >
                                                                        {{ $t("general.Itmustbeatmost") }}
                                                                        {{ $v.edit.title_e.$params.maxLength.max }}
                                                                        {{ $t("general.letters") }}
                                                                    </div>
                                                                    <template v-if="errors.title_e">
                                                                        <ErrorMessage
                                                                            v-for="(errorMessage, index) in errors.title_e"
                                                                            :key="index"
                                                                        >{{ errorMessage }}</ErrorMessage
                                                                        >
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div v-if="isVisible('description')" class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="mr-2">
                                                                        {{ getCompanyKey("pointOfSell_product_description") }}
                                                                        <span v-if="isRequired('description')" class="text-danger">*</span>
                                                                    </label>
                                                                    <textarea
                                                                        @input="arabicValueDescription(edit.description)"
                                                                        v-model="$v.edit.description.$model"
                                                                        class="form-control"
                                                                        :maxlength="1000"
                                                                        rows="4"
                                                                    ></textarea>
                                                                    <template v-if="errors.description">
                                                                        <ErrorMessage
                                                                            v-for="(
                                                                                errorMessage, index
                                                                              ) in errors.description"
                                                                            :key="index"
                                                                        >{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div v-if="isVisible('description_e')" class="col-md-6">
                                                                <div class="form-group">
                                                                    <label class="mr-2">
                                                                        {{ getCompanyKey("pointOfSell_product_description_e") }}
                                                                        <span v-if="isRequired('description_e')"  class="text-danger">*</span>
                                                                    </label>
                                                                    <textarea
                                                                        @input="
                              englishValueDescription(edit.description_e)
                            "
                                                                        v-model="$v.edit.description_e.$model"
                                                                        class="form-control"
                                                                        :maxlength="1000"
                                                                        rows="4"
                                                                    ></textarea>
                                                                    <template v-if="errors.description_e">
                                                                        <ErrorMessage
                                                                            v-for="(
                                errorMessage, index
                              ) in errors.description_e"
                                                                            :key="index"
                                                                        >{{ errorMessage }}</ErrorMessage
                                                                        >
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <hr v-if="isVisible('category_id')||isVisible('brand_id')||isVisible('group_id')||isVisible('unit_id')||isVisible('tax_id')" style="
                        margin: 10px 0 !important;
                        border-top: 1px solid rgb(141 163 159 / 42%);
                      "/>
                                                        <div class="row">
                                                            <div v-if="isVisible('category_id')" class="col-md-3">
                                                                <div class="form-group position-relative">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("pointOfSell_product_category") }}
                                                                        <span v-if="isRequired('category_id')" class="text-danger">*</span>
                                                                    </label>
                                                                    <multiselect
                                                                        @input="showCategoryModalEdit"
                                                                        v-model="$v.edit.category_id.$model"
                                                                        :options="categories.map((type) => type.id)"
                                                                        :custom-label="(opt) => categories.find((x) => x.id == opt)?
                                                                           $i18n.locale == 'ar' ? categories.find((x) => x.id == opt).name : categories.find((x) => x.id == opt).name_e
                                                                          : null
                                                                        "
                                                                    >
                                                                    </multiselect>
                                                                    <div
                                                                        v-if="
                                                          $v.edit.category_id.$error || errors.category_id
                                                        "
                                                                        class="text-danger"
                                                                    >
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>
                                                                    <template v-if="errors.category_id">
                                                                        <ErrorMessage
                                                                            v-for="(errorMessage, index) in errors.category_id"
                                                                            :key="index"
                                                                        >{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div v-if="isVisible('brand_id')" class="col-md-3">
                                                                <div class="form-group position-relative">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("pointOfSell_product_brand") }}
                                                                        <span v-if="isRequired('brand_id')" class="text-danger">*</span>
                                                                    </label>
                                                                    <multiselect
                                                                        @input="showBrandModalEdit"
                                                                        v-model="$v.edit.brand_id.$model"
                                                                        :options="brands.map((type) => type.id)"
                                                                        :custom-label="(opt) =>brands.find((x) => x.id == opt)?
                                                                             $i18n.locale == 'ar' ? brands.find((x) => x.id == opt).name : brands.find((x) => x.id == opt).name_e
                                                                          :null
                                                                        "
                                                                    >
                                                                    </multiselect>
                                                                    <div
                                                                        v-if="
                                                          $v.edit.brand_id.$error || errors.brand_id
                                                        "
                                                                        class="text-danger"
                                                                    >
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>
                                                                    <template v-if="errors.brand_id">
                                                                        <ErrorMessage
                                                                            v-for="(errorMessage, index) in errors.category_id"
                                                                            :key="index"
                                                                        >{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div v-if="isVisible('group_id')" class="col-md-3">
                                                                <div class="form-group position-relative">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("pointOfSell_product_group") }}
                                                                        <span v-if="isRequired('group_id')" class="text-danger">*</span>
                                                                    </label>
                                                                    <multiselect
                                                                        @input="showGroupModalEdit"
                                                                        v-model="$v.edit.group_id.$model"
                                                                        :options="groups.map((type) => type.id)"
                                                                        :custom-label="(opt) => groups.find((x) => x.id == opt)?
                                                                               $i18n.locale == 'ar' ? groups.find((x) => x.id == opt).name : groups.find((x) => x.id == opt).name_e
                                                                              :null
                                                                        "
                                                                    >
                                                                    </multiselect>
                                                                    <div
                                                                        v-if="
                                                          $v.edit.group_id.$error || errors.group_id
                                                        "
                                                                        class="text-danger"
                                                                    >
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>
                                                                    <template v-if="errors.group_id">
                                                                        <ErrorMessage
                                                                            v-for="(errorMessage, index) in errors.group_id"
                                                                            :key="index"
                                                                        >{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div v-if="isVisible('unit_id')" class="col-md-3">
                                                                <div class="form-group position-relative">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("pointOfSell_product_unit") }}
                                                                        <span v-if="isRequired('group_id')" class="text-danger">*</span>
                                                                    </label>
                                                                    <multiselect
                                                                        @input="showUnitModalEdit"
                                                                        v-model="$v.edit.unit_id.$model"
                                                                        :options="units.map((type) => type.id)"
                                                                        :custom-label="(opt) => units.find((x) => x.id == opt)?
                                                                               $i18n.locale == 'ar' ? units.find((x) => x.id == opt).name : units.find((x) => x.id == opt).name_e
                                                                              :null
                                                                        "
                                                                    >
                                                                    </multiselect>
                                                                    <div
                                                                        v-if="
                                                          $v.edit.unit_id.$error || errors.unit_id
                                                        "
                                                                        class="text-danger"
                                                                    >
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>
                                                                    <template v-if="errors.unit_id">
                                                                        <ErrorMessage
                                                                            v-for="(errorMessage, index) in errors.unit_id"
                                                                            :key="index"
                                                                        >{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div v-if="isVisible('tax_id')" class="col-md-3">
                                                                <label class="control-label">
                                                                    {{ getCompanyKey("pointOfSell_product_tax") }}
                                                                    <span v-if="isRequired('tax_id')" class="text-danger">*</span>
                                                                </label>
                                                                <select
                                                                    @input="showTaxModal"
                                                                    v-model="$v.edit.tax_id.$model"
                                                                    class="custom-select"
                                                                >
                                                                    <option value disabled>{{ $t('general.choose') }}</option>
                                                                    <option value="no-tax">{{ $t('general.no_tax') }}</option>
                                                                    <option value="default-tax">{{ $t('general.default_tax') }}</option>
                                                                    <option v-for="tax in taxes" :value="tax.id">{{ $i18n.locale == 'ar'?tax.name:tax.name_e }}</option>
                                                                </select>
                                                                <div
                                                                    v-if="
                                                          $v.edit.tax_id.$error || errors.tax_id
                                                        "
                                                                    class="text-danger"
                                                                >
                                                                    {{ $t("general.fieldIsRequired") }}
                                                                </div>
                                                                <template v-if="errors.tax_id">
                                                                    <ErrorMessage
                                                                        v-for="(errorMessage, index) in errors.tax_id"
                                                                        :key="index"
                                                                    >{{ errorMessage }}
                                                                    </ErrorMessage>
                                                                </template>
                                                            </div>
                                                        </div>
                                                        <hr v-if="isVisible('product_type')||isVisible('is_quantity')||isVisible('branch_id')" style="
                        margin: 10px 0 !important;
                        border-top: 1px solid rgb(141 163 159 / 42%);
                      "/>
                                                        <div class="row">
                                                            <div class="col-md-3" v-if="isVisible('product_type')">
                                                                <div class="form-group">
                                                                    <label class="my-1 mr-2">
                                                                        {{ getCompanyKey("pointOfSell_product_product_type") }}
                                                                        <span v-if="isRequired('product_type')" class="text-danger">*</span>
                                                                    </label>
                                                                    <b-form-group>
                                                                        <b-form-radio
                                                                            class="d-inline-block"
                                                                            v-model="$v.edit.product_type.$model"
                                                                            name="some-edit"
                                                                            value="1"
                                                                        >{{ $t("general.variant") }}</b-form-radio
                                                                        >
                                                                        <b-form-radio
                                                                            class="d-inline-block m-1"
                                                                            v-model="$v.edit.product_type.$model"
                                                                            name="some-edit"
                                                                            value="0"
                                                                        >{{ $t("general.standard") }}</b-form-radio
                                                                        >
                                                                    </b-form-group>
                                                                    <template v-if="errors.product_type">
                                                                        <ErrorMessage
                                                                            v-for="(errorMessage, index) in errors.product_type"
                                                                            :key="index"
                                                                        >{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3" v-if="isVisible('is_quantity')">
                                                                <div class="form-group">
                                                                    <label class="my-1 mr-2">
                                                                        {{ getCompanyKey("pointOfSell_product_is_quantity") }}
                                                                        <span v-if="isRequired('is_quantity')" class="text-danger">*</span>
                                                                    </label>
                                                                    <b-form-group>
                                                                        <b-form-radio
                                                                            class="d-inline-block"
                                                                            v-model="$v.edit.is_quantity.$model"
                                                                            @change="getBranch"
                                                                            name="some_is_quantity"
                                                                            value="1"
                                                                        >{{ $t("general.Yes") }}</b-form-radio
                                                                        >
                                                                        <b-form-radio
                                                                            class="d-inline-block m-1"
                                                                            v-model="$v.edit.is_quantity.$model"
                                                                            name="some_is_quantity"
                                                                            value="0"
                                                                        >{{ $t("general.No") }}</b-form-radio
                                                                        >
                                                                    </b-form-group>
                                                                    <template v-if="errors.is_quantity">
                                                                        <ErrorMessage
                                                                            v-for="(errorMessage, index) in errors.is_quantity"
                                                                            :key="index"
                                                                        >{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div v-if="isVisible('branch_id') && edit.is_quantity == 1 && branches.length > 1" class="col-md-3">
                                                                <div class="form-group position-relative">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("pointOfSell_product_branch") }}
                                                                        <span v-if="isRequired('branch_id')" class="text-danger">*</span>
                                                                    </label>
                                                                    <multiselect
                                                                        v-model="$v.edit.branch_id.$model"
                                                                        :options="branches.map((type) => type.id)"
                                                                        :custom-label="
                                                              (opt) => $i18n.locale == 'ar' ? branches.find((x) => x.id == opt).name : branches.find((x) => x.id == opt).name_e
                                                            "
                                                                    >
                                                                    </multiselect>
                                                                    <div
                                                                        v-if="
                                                          $v.edit.branch_id.$error || errors.branch_id
                                                        "
                                                                        class="text-danger"
                                                                    >
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>
                                                                    <template v-if="errors.branch_id">
                                                                        <ErrorMessage
                                                                            v-for="(errorMessage, index) in errors.branch_id"
                                                                            :key="index"
                                                                        >{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </b-tab>
                                                    <b-tab :title="$t('general.ImageUploads')">
                                                        <div class="row">
                                                            <input
                                                                accept="image/png, image/gif, image/jpeg, image/jpg"
                                                                type="file"
                                                                id="uploadImageEdit"
                                                                @change.prevent="onImageChanged"
                                                                class="input-file-upload position-absolute"
                                                                :class="[
                                    'd-none',
                                    {
                                      'is-invalid':
                                        $v.edit.media.$error || errors.media,
                                      'is-valid':
                                        !$v.edit.media.$invalid &&
                                        !errors.media,
                                    },
                                  ]"
                                                            />
                                                            <div class="col-md-8 my-1">
                                                                <!-- file upload -->
                                                                <div
                                                                    class="row align-content-between"
                                                                    style="width: 100%; height: 100%"
                                                                >
                                                                    <div class="col-12">
                                                                        <div class="d-flex flex-wrap">
                                                                            <div
                                                                                class="dropzone-previews col-4 position-relative mb-2"
                                                                                v-for="(photo, index) in images"
                                                                            >
                                                                                <div
                                                                                    :class="[
                                              'card mb-0 shadow-none border',
                                              images.length - 1 == index
                                                ? 'bg-primary'
                                                : '',
                                            ]"
                                                                                >
                                                                                    <div class="p-2">
                                                                                        <div
                                                                                            class="row align-items-center"
                                                                                        >
                                                                                            <div
                                                                                                class="col-auto"
                                                                                                @click="
                                                    showPhoto = photo.webp
                                                  "
                                                                                            >
                                                                                                <img
                                                                                                    data-dz-thumbnail
                                                                                                    :src="photo.webp"
                                                                                                    class="avatar-sm rounded bg-light"
                                                                                                    @error="
                                                      src = './images/img-1.png'
                                                    "
                                                                                                />
                                                                                            </div>
                                                                                            <div class="col pl-0">
                                                                                                <a
                                                                                                    href="javascript:void(0);"
                                                                                                    :class="[
                                                      'font-weight-bold',
                                                      images.length - 1 == index
                                                        ? 'text-white'
                                                        : 'text-muted',
                                                    ]"
                                                                                                    data-dz-name
                                                                                                >
                                                                                                    {{ photo.name }}
                                                                                                </a>
                                                                                            </div>
                                                                                            <!-- Button -->
                                                                                            <a
                                                                                                href="javascript:void(0);"
                                                                                                :class="[
                                                    'btn-danger text-muted dropzone-close',
                                                    $i18n.locale == 'ar'
                                                      ? 'dropzone-close-rtl'
                                                      : '',
                                                  ]"
                                                                                                data-dz-remove
                                                                                                @click.prevent="
                                                    deleteImageCreate(
                                                      photo.id,
                                                      index
                                                    )
                                                  "
                                                                                            >
                                                                                                <i class="fe-x"></i>
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="footer-image col-12">
                                                                        <b-button
                                                                            @click="changePhotoEdit"
                                                                            variant="success"
                                                                            type="button"
                                                                            class="mx-1 font-weight-bold px-3"
                                                                            v-if="!isLoader"
                                                                        >
                                                                            {{ $t("general.Add") }}
                                                                        </b-button>
                                                                        <b-button
                                                                            variant="success"
                                                                            class="mx-1"
                                                                            disabled
                                                                            v-else
                                                                        >
                                                                            <b-spinner small></b-spinner>
                                                                            <span class="sr-only"
                                                                            >{{ $t("login.Loading") }}...</span
                                                                            >
                                                                        </b-button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="show-dropzone">
                                                                    <img
                                                                        :src="showPhoto"
                                                                        class="img-thumbnail"
                                                                        @error="src = './images/img-1.png'"
                                                                    />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </b-tab>
                                                    <b-tab :title="$t('general.variant')">
                                                        <div class="row" v-if="isVisible('product_type') && edit.product_type == 0">
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("pointOfSell_product_purchase_price") }}
                                                                    </label>
                                                                    <input
                                                                        type="number"
                                                                        class="form-control"
                                                                        step="0.01"
                                                                        v-model.number="$v.productStandard.purchase_price.$model"
                                                                        :class="{
                                                              'is-invalid':
                                                                $v.productStandard.purchase_price.$error ||
                                                                errors.purchase_price,
                                                              'is-valid':
                                                                !$v.productStandard.purchase_price.$invalid &&
                                                                !errors.purchase_price,
                                                            }"
                                                                    />
                                                                    <template v-if="errors.purchase_price">
                                                                        <ErrorMessage
                                                                            v-for="(
                                                                errorMessage, index
                                                              ) in errors.purchase_price"
                                                                            :key="index"
                                                                        >{{ errorMessage }}</ErrorMessage
                                                                        >
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("pointOfSell_product_selling_price") }}
                                                                    </label>
                                                                    <input
                                                                        type="number"
                                                                        class="form-control"
                                                                        step="0.01"
                                                                        v-model.number="$v.productStandard.selling_price.$model"
                                                                        :class="{
                                                              'is-invalid':
                                                                $v.productStandard.selling_price.$error ||
                                                                errors.purchase_price,
                                                              'is-valid':
                                                                !$v.productStandard.selling_price.$invalid &&
                                                                !errors.selling_price,
                                                            }"
                                                                    />
                                                                    <template v-if="errors.selling_price">
                                                                        <ErrorMessage
                                                                            v-for="(
                                                                errorMessage, index
                                                              ) in errors.selling_price"
                                                                            :key="index"
                                                                        >{{ errorMessage }}</ErrorMessage
                                                                        >
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("pointOfSell_product_sku") }}
                                                                    </label>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        v-model="$v.productStandard.sku.$model"
                                                                        :class="{
                                                              'is-invalid':
                                                                $v.productStandard.sku.$error ||
                                                                errors.sku,
                                                              'is-valid':
                                                                !$v.productStandard.sku.$invalid &&
                                                                !errors.sku,
                                                            }"
                                                                    />
                                                                    <template v-if="errors.sku">
                                                                        <ErrorMessage
                                                                            v-for="(
                                                                errorMessage, index
                                                              ) in errors.sku"
                                                                            :key="index"
                                                                        >{{ errorMessage }}</ErrorMessage
                                                                        >
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("pointOfSell_product_Barcode") }}
                                                                    </label>
                                                                    <input
                                                                        type="text"
                                                                        class="form-control"
                                                                        v-model="$v.productStandard.bar_code.$model"
                                                                        :class="{
                                                              'is-invalid':
                                                                $v.productStandard.bar_code.$error ||
                                                                errors.sku,
                                                              'is-valid':
                                                                !$v.productStandard.bar_code.$invalid &&
                                                                !errors.sku,
                                                            }"
                                                                    />
                                                                    <template v-if="errors.bar_code">
                                                                        <ErrorMessage
                                                                            v-for="(
                                                                errorMessage, index
                                                              ) in errors.bar_code"
                                                                            :key="index"
                                                                        >{{ errorMessage }}</ErrorMessage
                                                                        >
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3" v-if="edit.is_quantity == 1">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("pointOfSell_product_quantity") }}
                                                                    </label>
                                                                    <input
                                                                        type="number"
                                                                        class="form-control"
                                                                        v-model.number="$v.productStandard.quantity.$model"
                                                                        :class="{
                                                              'is-invalid':
                                                                $v.productStandard.quantity.$error ||
                                                                errors.sku,
                                                              'is-valid':
                                                                !$v.productStandard.quantity.$invalid &&
                                                                !errors.sku,
                                                            }"
                                                                    />
                                                                    <template v-if="errors.quantity">
                                                                        <ErrorMessage
                                                                            v-for="(
                                                                errorMessage, index
                                                              ) in errors.quantity"
                                                                            :key="index"
                                                                        >{{ errorMessage }}</ErrorMessage
                                                                        >
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="form-group">
                                                                    <label class="control-label">
                                                                        {{ getCompanyKey("pointOfSell_product_re_order") }}
                                                                        <span class="text-danger">*</span>
                                                                    </label>
                                                                    <input
                                                                        type="number"
                                                                        class="form-control"
                                                                        v-model.number="$v.productStandard.re_order.$model"
                                                                        :class="{
                                                              'is-invalid':
                                                                $v.productStandard.re_order.$error ||
                                                                errors.re_order,
                                                              'is-valid':
                                                                !$v.productStandard.re_order.$invalid &&
                                                                !errors.re_order,
                                                            }"
                                                                    />
                                                                    <template v-if="errors.re_order">
                                                                        <ErrorMessage
                                                                            v-for="(
                                                                errorMessage, index
                                                              ) in errors.re_order"
                                                                            :key="index"
                                                                        >{{ errorMessage }}</ErrorMessage
                                                                        >
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div v-if="isVisible('product_type') && edit.product_type == 1" class="mb-3 bg-white rounded p-3">
                                                            <div class="row justify-content-between">
                                                                <div class="col-4">
                                                                    <h5 class="font-22">{{ $t('general.add_product_variants') }}</h5>
                                                                </div>
                                                                <div class="col-4">
                                                                    <div class="row no-gutters">
                                                                        <div class="col">
                                                                            <div>
                                                                                <multiselect
                                                                                    @input="showAttributeModal"
                                                                                    :options="allAttributes.map((type) => type.id)"
                                                                                    :custom-label="
                                                                                      (opt) => allAttributes.find((x) => x.id == opt)?
                                                                                      $i18n.locale == 'ar' ? allAttributes.find((x) => x.id == opt).name : allAttributes.find((x) => x.id == opt).name_e
                                                                                      :null
                                                                                    "
                                                                                >
                                                                                </multiselect>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div  v-for="(tempAttribute,index) in attributes">
                                                                <div class="row justify-content-center">
                                                                    <div class="col-9">
                                                                        <div class="variant-values">
                                                                            <label>{{$i18n.locale == 'ar' ?tempAttribute.name:tempAttribute.name_e }}</label>
                                                                            <div class="chips-container">
                                                                    <span
                                                                        class="chip"
                                                                        v-for="(chips,chipIndex) in chipArray.find(el => tempAttribute.id == el.id).names"
                                                                    >
                                                                        {{ chips }}
                                                                        <i
                                                                            class="fas fa-times"
                                                                            @click.prevent="deleteChip($event,tempAttribute.id,chipIndex)"
                                                                        ></i>
                                                                    </span>
                                                                            </div>
                                                                            <div class="input-group mb-2">
                                                                                <input
                                                                                    type="text"
                                                                                    :id="`chip-${index}`"
                                                                                    class="form-control"
                                                                                    @keyup.enter="addChips($event,tempAttribute.id,index)"
                                                                                    aria-describedby="basic-addon2"
                                                                                    value=""
                                                                                />
                                                                                <div class="input-group-append">
                                                                                    <button
                                                                                        class="custom-btn-dowonload"
                                                                                        type="button"
                                                                                        @click.prevent="addChips($event,tempAttribute.id,index)"
                                                                                    >
                                                                                        <i class="fas fa-plus"></i>
                                                                                    </button>
                                                                                    <button
                                                                                        class="custom-btn-dowonload"
                                                                                        type="button"
                                                                                        @click.prevent="removeTempAttribute(index,tempAttribute.id)"
                                                                                    >
                                                                                        <i class="fas fa-trash-alt"></i>
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="bg-white rounded p-3" v-if="productVariant.length > 0 && edit.product_type == 1">
                                                            <div class="form-row">
                                                                <div class="form-group col-md-12">
                                                                    <h5 class="m-0 font-22">{{ $t('general.add_variant_details') }}</h5>
                                                                </div>
                                                            </div>
                                                            <!--For Edit variants-->
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <thead>
                                                                    <tr class="border-0">
                                                                        <th>#</th>
                                                                        <th class="text-center">{{ $t('general.variant') }}</th>
                                                                        <th class="text-center">{{ getCompanyKey("pointOfSell_product_purchase_price") }}</th>
                                                                        <th class="text-center">{{ getCompanyKey("pointOfSell_product_selling_price") }}</th>
                                                                        <th class="text-center">{{ getCompanyKey("pointOfSell_product_Barcode") }}</th>
                                                                        <th class="text-center">{{ getCompanyKey("pointOfSell_product_sku") }}</th>
                                                                        <th v-if="edit.is_quantity == 1" class="text-center">{{ getCompanyKey('pointOfSell_product_quantity') }}</th>
                                                                        <th class="text-center">{{ getCompanyKey("pointOfSell_product_re_order") }}</th>
                                                                        <th class="text-center">{{ $t('general.photo') }}</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <tr v-for="(item, index) in productVariant">
                                                                        <!--Edit check box-->
                                                                        <td class="border-0 add-product-padding" style="vertical-align: middle;">
                                                                            <input
                                                                                type="checkbox"
                                                                                class="form-control"
                                                                                v-model="item.enabled"
                                                                            />
                                                                        </td>

                                                                        <!--Edit Variant combination-->
                                                                        <td class="border-0 add-product-padding">
                                                                            <input
                                                                                v-model="item.variant_title"
                                                                                type="text"
                                                                                :disabled="item.enabled"
                                                                                class="form-control"
                                                                                style="width: 100%"
                                                                            />
                                                                        </td>

                                                                        <!--Edit price of the variant-->
                                                                        <td class="border-0 add-product-padding">
                                                                            <input
                                                                                type="number"
                                                                                :disabled="item.enabled"
                                                                                v-model.number="item.purchase_price"
                                                                                step=".01"
                                                                                class="form-control"
                                                                            />
                                                                        </td>
                                                                        <td class="border-0 add-product-padding">
                                                                            <input
                                                                                type="number"
                                                                                step=".01"
                                                                                :disabled="item.enabled"
                                                                                v-model.number="item.selling_price"
                                                                                class="form-control"
                                                                            />
                                                                        </td>

                                                                        <!--Edit bar_code of the variant-->
                                                                        <td class="border-0 add-product-padding">
                                                                            <input
                                                                                v-model="item.bar_code"
                                                                                type="text"
                                                                                :disabled="item.enabled"
                                                                                class="form-control"
                                                                            />
                                                                            <template v-if="errors[`product_variant.${index}.bar_code`]">
                                                                                <ErrorMessage
                                                                                    v-for="(errorMessage, index) in errors[`product_variant.${index}.bar_code`]"
                                                                                    :key="index"
                                                                                >{{ errorMessage }}
                                                                                </ErrorMessage>
                                                                            </template>
                                                                        </td>

                                                                        <!--Edit sku of the variant-->
                                                                        <td class="border-0 add-product-padding">
                                                                            <input
                                                                                type="text"
                                                                                v-model="item.sku"
                                                                                :disabled="item.enabled"
                                                                                class="form-control"
                                                                            />
                                                                            <template v-if="errors[`product_variant.${index}.sku`]">
                                                                                <ErrorMessage
                                                                                    v-for="(errorMessage, index) in errors[`product_variant.${index}.sku`]"
                                                                                    :key="index"
                                                                                >{{ errorMessage }}
                                                                                </ErrorMessage>
                                                                            </template>
                                                                        </td>

                                                                        <!--Edit qty of the variant-->
                                                                        <td class="border-0 add-product-padding" v-if="edit.is_quantity == 1">
                                                                            <input
                                                                                type="number"
                                                                                data-vv-as="quantity"
                                                                                :disabled="item.enabled"
                                                                                v-model.number="item.quantity"
                                                                                class="form-control"
                                                                            />
                                                                        </td>

                                                                        <td class="border-0 add-product-padding">
                                                                            <input
                                                                                type="number"
                                                                                min="0"
                                                                                :disabled="item.enabled"
                                                                                v-model.number="item.re_order"
                                                                                class="form-control"
                                                                            />
                                                                        </td>
                                                                        <td class="border-0 add-product-padding">
                                                                            <div class="custom-file" style="padding-right: 100%">
                                                                                <input
                                                                                    type="file"
                                                                                    :disabled="item.enabled"
                                                                                    class="custom-file-input"
                                                                                    :id="'variant-image-'+index"
                                                                                    accept="image/*"
                                                                                    @change="variantImage($event, index, '#variant-image-'+index)"
                                                                                />
                                                                                <label
                                                                                    class="custom-file-label text-truncate"
                                                                                    :for="'variant-image-'+index"
                                                                                >{{ $t('general.image_only') }}</label>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </b-tab>
                                                </b-tabs>
                                            </div>
                                        </b-modal>
                                        <!--  /edit   -->
                                    </td>
                                    <td v-if="enabled3" class="do-not-print">
                                        <button
                                            @mousemove="log(data.id)"
                                            @mouseover="log(data.id)"
                                            type="button"
                                            class="btn"
                                            :id="`tooltip-${data.id}`"
                                            :data-placement="
                          $i18n.locale == 'en' ? 'left' : 'right'
                        "
                                            :title="Tooltip"
                                        >
                                            <i class="fe-info" style="font-size: 22px"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="20">
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
.chips-container .chip{
    padding: 3px 8px;
    display: inline-block;
    background-color: #6dc5f4;
    margin: 0 3px 3px;
    color: #fff;
    border-radius: 13px;
}
.chips-container .chip i{
    cursor: pointer;
}
.modal-dialog .card {
    margin: 0 !important;
}

.bankAccount.modal-body {
    padding: 0 !important;
}

.modal-dialog .card-body {
    padding: 0.5rem 1rem 0  !important;
}

.nav-bordered {
    border: unset !important;
}

.nav {
    background-color: #dff0fe;
}

.tab-content {
    padding: 20px 40px;
    min-height: 300px;
    background-color: #f5f5f5;
    position: relative;
}

.nav-tabs .nav-link {
    border: 1px solid #b7b7b7 !important;
    background-color: #d7e5f2;
    border-bottom: 0 !important;
    margin-bottom: 1px;
}

.nav-tabs .nav-link.active,
.nav-tabs .nav-item.show .nav-link {
    color: #000;
    background-color: hsl(0deg 0% 96%);
    border-bottom: 0 !important;
}

.img-thumbnail {
    max-height: 400px !important;
}

.table.product-detail-table th, .table td {
    border: unset !important;
}

.th-size-weight{
    font-weight: 700;
    font-size: 16px;
}

.th-size{
    font-size: 16px;
}
</style>
