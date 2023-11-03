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
  requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import { dynamicSortString } from "../../../helper/tableSort";
import { formatDateOnly } from "../../../helper/startDate";
import translation from "../../../helper/mixin/translation-mixin";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";
import permissionGuard from "../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "inventories",
    meta: [{ name: "inventories", content: "inventories" }],
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
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "POS inventories", "all POS inventories");
    });
  },
  data() {
    return {
      per_page: 50,
      search: "",
      debounce: {},
      installmentStatusPagination: {},
      installmentStatus: [],
      branches: [],
      brands: [],
      categories: [],
      groups: [],
      isLoader: false,
      create: {
        start_date: '',
        end_date: '',
        branch_id: null,
        brand_id: null,
        category_id: null,
        group_id: null,
        re_order: "all",
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
      openingBreak: "",
      setting: {
        id: true,
        sku: true,
        barcode: true,
        item_name: true,
        variant_name: true,
        category_name: true,
        group_name: true,
        brand_name: true,
        purchase_price: true,
        selling_price: true,
        inventory: true,
      },
      filterSetting: [
        "id",
        "sku",
        "bar_code",
        "title",
        "title_e",
        "category_id",
        "group_id",
        "brand_id",
        "purchase_price",
        "selling_price",
      ],
      Tooltip: "",
      mouseEnter: null,
    };
  },
  validations: {
    create: {
      start_date: {  },
      end_date: {  },
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
  mounted(){
      this.getData();
      this.create.start_date = this.formatDate(new Date());
      this.create.end_date = this.formatDate(new Date());
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
        let _filterSetting = [...this.filterSetting];
        let index = this.filterSetting.indexOf("sku");
        if (index > -1) {
          _filterSetting[index] = "product_variant.sku";
        }
        index = this.filterSetting.indexOf("bar_code");
        if (index > -1) {
          _filterSetting[index] = "product_variant.bar_code";
        }
        index = this.filterSetting.indexOf("category_id");
        if (index > -1) {
          _filterSetting[index] =
            this.$i18n.locale == "ar" ? "category.name" : "category.name_e";
        }
        index = this.filterSetting.indexOf("group_id");
        if (index > -1) {
          _filterSetting[index] =
            this.$i18n.locale == "ar" ? "group.name" : "group.name_e";
        }
        index = this.filterSetting.indexOf("brand_id");
        if (index > -1) {
          _filterSetting[index] =
            this.$i18n.locale == "ar" ? "brand.name" : "brand.name_e";
        }
        index = this.filterSetting.indexOf("purchase_price");
        if (index > -1) {
          _filterSetting[index] = "product_variant.purchase_price";
        }
        index = this.filterSetting.indexOf("selling_price");
        if (index > -1) {
          _filterSetting[index] = "product_variant.selling_price";
        }
        let filter = "";
        for (let i = 0; i < _filterSetting.length; ++i) {
          filter += `columns[${i}]=${_filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/point-of-sale/product/inventories?start_date=${
              this.create.start_date
            }&end_date=${this.create.end_date}&category_id=${
              this.create.category_id ?? ""
            }&brand_id=${this.create.brand_id ?? ""}&group_id=${
              this.create.group_id ?? ""
            }&branch_id=${this.create.branch_id ?? ""}&page=${page}&per_page=${
              this.per_page
            }&search=${this.search}&${filter}`
          )
          .then((res) => {
            let l = res.data;
            this.installmentStatus = l.data;
            this.installmentStatusPagination = l.pagination;
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
      this.$v.create.$touch();
      if (this.$v.create.$invalid) {
        return;
      } else {
        if (
          this.current_page <= this.installmentStatusPagination.last_page &&
          this.current_page != this.installmentStatusPagination.current_page &&
          this.current_page
        ) {
          this.isLoader = true;
          let _filterSetting = [...this.filterSetting];
          let index = this.filterSetting.indexOf("sku");
          if (index > -1) {
            _filterSetting[index] = "product_variant.sku";
          }
          index = this.filterSetting.indexOf("bar_code");
          if (index > -1) {
            _filterSetting[index] = "product_variant.bar_code";
          }
          index = this.filterSetting.indexOf("category_id");
          if (index > -1) {
            _filterSetting[index] =
              this.$i18n.locale == "ar" ? "category.name" : "category.name_e";
          }
          index = this.filterSetting.indexOf("group_id");
          if (index > -1) {
            _filterSetting[index] =
              this.$i18n.locale == "ar" ? "group.name" : "group.name_e";
          }
          index = this.filterSetting.indexOf("brand_id");
          if (index > -1) {
            _filterSetting[index] =
              this.$i18n.locale == "ar" ? "brand.name" : "brand.name_e";
          }
          index = this.filterSetting.indexOf("purchase_price");
          if (index > -1) {
            _filterSetting[index] = "product_variant.purchase_price";
          }
          index = this.filterSetting.indexOf("selling_price");
          if (index > -1) {
            _filterSetting[index] = "product_variant.selling_price";
          }
          let filter = "";
          for (let i = 0; i < _filterSetting.length; ++i) {
            filter += `columns[${i}]=${_filterSetting[i]}&`;
          }

          adminApi
            .get(
              `/point-of-sale/product/inventories?start_date=${
                this.create.start_date
              }&end_date=${this.create.end_date}&category_id=${
                this.create.category_id ?? ""
              }&brand_id=${this.create.brand_id ?? ""}&group_id=${
                this.create.group_id ?? ""
              }&branch_id=${this.create.branch_id ?? ""}&page=${
                this.current_page
              }&per_page=${this.per_page}&search=${this.search}&${filter}`
            )
            .then((res) => {
              let l = res.data;
              this.installmentStatus = l.data;
              this.installmentStatusPagination = l.pagination;
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
    /**
     *  end get Data module && pagination
     */
    /**
     *  get Branches
     */
    async getBranches() {
      this.isLoader = true;
      await adminApi
        .get(`/branches`)
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
    /**
     *  get brands
     */
    async getBrands() {
      this.isLoader = true;
      await adminApi
        .get(`/brands`)
        .then((res) => {
          let l = res.data.data;
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
    /**
     *  get Category
     */
    async getCategory() {
      this.isLoader = true;
      await adminApi
        .get(`/categories`)
        .then((res) => {
          let l = res.data.data;
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
    /**
     *  get groups
     */
    async getGroups() {
      this.isLoader = true;
      await adminApi
        .get(`/groups`)
        .then((res) => {
          let l = res.data.data;
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
    /**
     *  reset Modal (create)
     */
    resetModalHidden() {
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
      });
      this.errors = {};
      this.$bvModal.hide(`create`);
    },
    /**
     *  hidden Modal (create)
     */
    async resetModal() {
      await this.getBranches();
      await this.getBrands();
      await this.getCategory();
      await this.getGroups();
      this.is_disabled = false;
      this.$nextTick(() => {
        this.$v.$reset();
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
      document.querySelector(`${tag}[data-${c}='${index}']`).focus();
    },
    formatDate(value) {
      return formatDateOnly(value);
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
              ("inventories" + "." || "SheetJSTableExport.") + (type || "xlsx")
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
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">{{ $t("general.Inventories") }}</h4>
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
                      v-model="filterSetting"
                      value="id"
                      class="mb-1"
                      >{{ getCompanyKey("inventories_ID") }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="sku"
                      class="mb-1"
                      >{{ getCompanyKey("inventories_SKU") }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="bar_code"
                      class="mb-1"
                      >{{
                        getCompanyKey("inventories_Barcode")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="title"
                      class="mb-1"
                      >{{
                        getCompanyKey("inventories_Item_Name") +
                        " " +
                        $t("general.langAr")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="title_e"
                      class="mb-1"
                      >{{
                        getCompanyKey("inventories_Item_Name") +
                        " " +
                        $t("general.langEn")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="category_id"
                      class="mb-1"
                    >
                      {{
                        getCompanyKey("inventories_Category_Name")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="group_id"
                      class="mb-1"
                    >
                      {{
                        getCompanyKey("inventories_Group_Name")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="brand_id"
                      class="mb-1"
                    >
                      {{
                        getCompanyKey("inventories_Brand_Name")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="purchase_price"
                      class="mb-1"
                      >{{
                        getCompanyKey("inventories_Purchase_Price")
                      }}</b-form-checkbox
                    >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="selling_price"
                      class="mb-1"
                      >{{
                        getCompanyKey("inventories_Selling_Price")
                      }}</b-form-checkbox
                    >
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

            <div
              class="row justify-content-between align-items-center mb-2 px-1"
            >
              <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
                <!-- start create and printer -->
                <b-button
                  v-b-modal.create
                  variant="primary"
                  class="btn-sm mx-1 font-weight-bold"
                >
                  {{ $t("general.Search") }}
                  <i class="fe-search"></i>
                </b-button>
                <div class="d-inline-flex">
                  <button
                    class="custom-btn-dowonload"
                    @click="ExportExcel('xlsx')"
                  >
                    <i class="fas fa-file-download"></i>
                  </button>
                  <button class="custom-btn-dowonload" v-print="'#printCustom'">
                    <i class="fe-printer"></i>
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
                      <b-form-checkbox v-model="setting.id" class="mb-1">{{
                        getCompanyKey("inventories_ID")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.sku" class="mb-1">{{
                        getCompanyKey("inventories_SKU")
                      }}</b-form-checkbox>
                      <b-form-checkbox v-model="setting.barcode" class="mb-1">{{
                        getCompanyKey("inventories_Barcode")
                      }}</b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.item_name"
                        class="mb-1"
                        >{{
                          getCompanyKey("inventories_Item_Name")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-model="setting.variant_name"
                        class="mb-1"
                        >{{
                          getCompanyKey("inventories_Variant_Name")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-model="setting.category_name"
                        class="mb-1"
                        >{{
                          getCompanyKey("inventories_Category_Name")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-model="setting.group_name"
                        class="mb-1"
                        >{{
                          getCompanyKey("inventories_Group_Name")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-model="setting.brand_name"
                        class="mb-1"
                        >{{
                          getCompanyKey("inventories_Brand_Name")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-model="setting.purchase_price"
                        class="mb-1"
                        >{{
                          getCompanyKey("inventories_Purchase_Price")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-model="setting.selling_price"
                        class="mb-1"
                        >{{
                          getCompanyKey("inventories_Selling_Price")
                        }}</b-form-checkbox
                      >
                      <b-form-checkbox
                        v-model="setting.inventory"
                        class="mb-1"
                        >{{
                          getCompanyKey("inventories_Inventory")
                        }}</b-form-checkbox
                      >
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
                    class="d-inline-flex align-items-center pagination-custom"
                  >
                    <div class="d-inline-block" style="font-size: 13px">
                      {{ installmentStatusPagination.from }}-{{
                        installmentStatusPagination.to
                      }}
                      /
                      {{ installmentStatusPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            installmentStatusPagination.current_page == 1
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(installmentStatusPagination.current_page - 1)
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
                            installmentStatusPagination.last_page ==
                            installmentStatusPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(installmentStatusPagination.current_page + 1)
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
                      type="button"
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
                  <!-- Emulate built in modal footer ok and cancel button actions -->

                  <b-button
                    variant="danger"
                    type="button"
                    @click.prevent="resetModalHidden"
                  >
                    {{ $t("general.Cancel") }}
                  </b-button>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">
                        {{ $t("general.fromDate") }}
                        <span class="text-danger">*</span>
                      </label>
                      <date-picker
                        type="date"
                        v-model="$v.create.start_date.$model"
                        format="YYYY-MM-DD"
                        valueType="format"
                        :confirm="false"
                        :class="{
                          'is-invalid':
                            $v.create.start_date.$error || errors.start_date,
                          'is-valid':
                            !$v.create.start_date.$invalid &&
                            !errors.start_date,
                        }"
                      ></date-picker>

                      <template v-if="errors.start_date">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.start_date"
                          :key="index"
                        >
                          {{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="control-label">
                        {{ $t("general.toDate") }}
                        <span class="text-danger">*</span>
                      </label>
                      <date-picker
                        type="date"
                        v-model="$v.create.end_date.$model"
                        format="YYYY-MM-DD"
                        valueType="format"
                        :confirm="false"
                        :class="{
                          'is-invalid':
                            $v.create.end_date.$error || errors.end_date,
                          'is-valid':
                            !$v.create.end_date.$invalid && !errors.end_date,
                        }"
                      ></date-picker>

                      <template v-if="errors.end_date">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.end_date"
                          :key="index"
                        >
                          {{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group position-relative">
                      <label class="control-label">{{
                        getCompanyKey("inventories_branch")
                      }}</label>
                      <multiselect
                        v-model="create.branch_id"
                        :options="branches.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            $i18n.locale == 'ar'
                              ? branches.find((x) => x.id == opt).name
                              : branches.find((x) => x.id == opt).name_e
                        "
                        :class="{
                          'is-invalid': errors.branch_id,
                          'is-valid': !errors.branch_id,
                        }"
                      >
                      </multiselect>

                      <template v-if="errors.branch_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.branch_id"
                          :key="index"
                        >
                          {{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group position-relative">
                      <label class="control-label">{{
                        getCompanyKey("inventories_brand")
                      }}</label>
                      <multiselect
                        v-model="create.brand_id"
                        :options="brands.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            $i18n.locale == 'ar'
                              ? brands.find((x) => x.id == opt).name
                              : brands.find((x) => x.id == opt).name_e
                        "
                        :class="{
                          'is-invalid': errors.brand_id,
                          'is-valid': !errors.brand_id,
                        }"
                      >
                      </multiselect>

                      <template v-if="errors.brand_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.brand_id"
                          :key="index"
                        >
                          {{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group position-relative">
                      <label class="control-label">{{
                        getCompanyKey("inventories_category")
                      }}</label>
                      <multiselect
                        v-model="create.category_id"
                        :options="categories.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            $i18n.locale == 'ar'
                              ? categories.find((x) => x.id == opt).name
                              : categories.find((x) => x.id == opt).name_e
                        "
                        :class="{
                          'is-invalid': errors.category_id,
                          'is-valid': !errors.category_id,
                        }"
                      >
                      </multiselect>

                      <template v-if="errors.category_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.category_id"
                          :key="index"
                        >
                          {{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group position-relative">
                      <label class="control-label">{{
                        getCompanyKey("inventories_group")
                      }}</label>
                      <multiselect
                        v-model="create.group_id"
                        :options="groups.map((type) => type.id)"
                        :custom-label="
                          (opt) =>
                            $i18n.locale == 'ar'
                              ? groups.find((x) => x.id == opt).name
                              : groups.find((x) => x.id == opt).name_e
                        "
                        :class="{
                          'is-invalid': errors.group_id,
                          'is-valid': !errors.group_id,
                        }"
                      >
                      </multiselect>

                      <template v-if="errors.group_id">
                        <ErrorMessage
                          v-for="(errorMessage, index) in errors.group_id"
                          :key="index"
                        >
                          {{ errorMessage }}
                        </ErrorMessage>
                      </template>
                    </div>
                  </div>
                  <!--                                    <div class="col-md-6">-->
                  <!--                                        <div class="form-group position-relative">-->
                  <!--                                            <label class="control-label">{{ getCompanyKey("inventories_re_order") }}</label>-->
                  <!--                                            <select class="custom-select" v-model="create.re_order">-->
                  <!--                                                <option value="all">{{ $t('general.all') }}</option>-->
                  <!--                                                <option value="yes">{{ $t('general.Yes') }}</option>-->
                  <!--                                                <option value="no">{{ $t('general.No') }}</option>-->
                  <!--                                            </select>-->

                  <!--                                            <template v-if="errors.re_order">-->
                  <!--                                                <ErrorMessage v-for="(errorMessage, index) in errors.re_order" :key="index">-->
                  <!--                                                    {{ errorMessage }}-->
                  <!--                                                </ErrorMessage>-->
                  <!--                                            </template>-->
                  <!--                                        </div>-->
                  <!--                                    </div>-->
                </div>
              </form>
            </b-modal>
            <!--  /create   -->

            <!-- start .table-responsive-->
            <div
              class="table-responsive mb-3 custom-table-theme position-relative"
              ref="exportable_table"
              id="printCustom"
            >
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

              <table
                class="table table-borderless table-hover table-centered m-0"
              >
                <thead>
                  <tr>
                    <th v-if="setting.id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("inventories_ID") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="installmentStatus.sort(sortString('id'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="installmentStatus.sort(sortString('-id'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.sku">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("inventories_SKU") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="installmentStatus.sort(sortString('sku'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="installmentStatus.sort(sortString('-sku'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.barcode">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("inventories_Barcode") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="
                              installmentStatus.sort(sortString('barcode'))
                            "
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              installmentStatus.sort(sortString('-barcode'))
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.item_name">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("inventories_Item_Name")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="
                              installmentStatus.sort(
                                sortString(
                                  $i18n.locale == 'ar' ? 'name' : 'name_e'
                                )
                              )
                            "
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              installmentStatus.sort(
                                sortString(
                                  $i18n.locale == 'ar' ? '-name' : '-name_e'
                                )
                              )
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.variant_name">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("inventories_Variant_Name")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="
                              installmentStatus.sort(
                                sortString(
                                  $i18n.locale == 'ar' ? 'name' : 'name_e'
                                )
                              )
                            "
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              installmentStatus.sort(
                                sortString(
                                  $i18n.locale == 'ar' ? '-name' : '-name_e'
                                )
                              )
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.category_name">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("inventories_Category_Name")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="
                              installmentStatus.sort(
                                sortString(
                                  $i18n.locale == 'ar' ? 'name' : 'name_e'
                                )
                              )
                            "
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              installmentStatus.sort(
                                sortString(
                                  $i18n.locale == 'ar' ? '-name' : '-name_e'
                                )
                              )
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.group_name">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("inventories_Group_Name")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="
                              installmentStatus.sort(
                                sortString(
                                  $i18n.locale == 'ar' ? 'name' : 'name_e'
                                )
                              )
                            "
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              installmentStatus.sort(
                                sortString(
                                  $i18n.locale == 'ar' ? '-name' : '-name_e'
                                )
                              )
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.brand_name">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("inventories_Brand_Name")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="
                              installmentStatus.sort(
                                sortString(
                                  $i18n.locale == 'ar' ? 'name' : 'name_e'
                                )
                              )
                            "
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              installmentStatus.sort(
                                sortString(
                                  $i18n.locale == 'ar' ? '-name' : '-name_e'
                                )
                              )
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.purchase_price">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("inventories_Purchase_Price")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="
                              installmentStatus.sort(
                                sortString('purchase_price')
                              )
                            "
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              installmentStatus.sort(
                                sortString('-purchase_price')
                              )
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.selling_price">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("inventories_Selling_Price")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="
                              installmentStatus.sort(
                                sortString('selling_price')
                              )
                            "
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              installmentStatus.sort(
                                sortString('-selling_price')
                              )
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.inventory">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("inventories_Inventory")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="
                              installmentStatus.sort(sortString('inventory'))
                            "
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              installmentStatus.sort(sortString('-inventory'))
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody v-if="installmentStatus.length > 0">
                  <tr
                    v-for="(data, index) in installmentStatus"
                    :key="data.id"
                    class="body-tr-custom"
                  >
                    <td v-if="setting.id">
                      <h5 class="m-0 font-weight-normal td5">{{ data.id }}</h5>
                    </td>
                    <td v-if="setting.sku">
                      <h5 class="m-0 font-weight-normal td5">
                        {{ data.product_variant[0].sku }}
                      </h5>
                    </td>
                    <td v-if="setting.barcode">
                      <h5 class="m-0 font-weight-normal td5">
                        {{ data.product_variant[0].barcode }}
                      </h5>
                    </td>
                    <td v-if="setting.item_name">
                      <h5 class="m-0 font-weight-normal td5">
                        {{ $i18n.locale == "ar" ? data.title : data.title_e }}
                      </h5>
                    </td>
                    <td v-if="setting.variant_name">
                      <h5 class="m-0 font-weight-normal td5">
                        {{
                          data.product_variant.length > 1
                            ? data.product_variant[0].variant_title
                            : "---"
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.category_name">
                      <h5 class="m-0 font-weight-normal td5">
                        {{
                          data.category
                            ? $i18n.locale == "ar"
                              ? data.category.name
                              : data.category.name_e
                            : "---"
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.group_name">
                      <h5 class="m-0 font-weight-normal td5">
                        {{
                          data.group
                            ? $i18n.locale == "ar"
                              ? data.group.name
                              : data.group.name_e
                            : "---"
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.brand_name">
                      <h5 class="m-0 font-weight-normal td5">
                        {{
                          data.brand
                            ? $i18n.locale == "ar"
                              ? data.brand.name
                              : data.brand.name_e
                            : "---"
                        }}
                      </h5>
                    </td>
                    <td v-if="setting.purchase_price">
                      <h5 class="m-0 font-weight-normal td5">
                        {{ data.product_variant[0].purchase_price }}
                      </h5>
                    </td>
                    <td v-if="setting.selling_price">
                      <h5 class="m-0 font-weight-normal td5">
                        {{ data.product_variant[0].selling_price }}
                      </h5>
                    </td>
                    <td v-if="setting.inventory">
                      <h5 class="m-0 font-weight-normal td5">
                        {{
                          data.product_variant.length > 1
                            ? "---"
                            : data.inventory
                        }}
                      </h5>
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

<style>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type="number"] {
  -moz-appearance: textfield;
}
.multiselect__single {
  font-weight: 600 !important;
  color: black !important;
}
.td5 {
  font-size: 16px !important;
}
</style>



