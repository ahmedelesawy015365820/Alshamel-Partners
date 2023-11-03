<script>
import Layout from "../../../layouts/main";
import permissionGuard from "../../../../helper/permission";

import PageHeader from "../../../../components/general/Page-header";
import adminApi from "../../../../api/adminAxios";
import Switches from "vue-switches";
import Multiselect from "vue-multiselect";
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
    title: "Inventories Report",
    meta: [{ name: "description", content: "Inventories Report" }],
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
      from_date: "",
      to_date: "",
      sales_type_id: "",
      salesTypes: [],
      payment_type: "",
      paymentTypes: [],
      category_id: "",
      categories: [],
      brand_id: "",
      brands: [],
      group_d: "",
      groups: [],
      branch_id: "",
      branches: [],
      reorders: [],
      total: "",
      reorder: "",
      customer_id: "",
      customers: [],
      employee_id: "",
      employees: [],
      owners: [],
      owner_id: "",
      buildings: [],
      building_id: "",
      wallets: [],
      wallet_id: "",
      properties: [],
      property_id: "",
      units: [],
      unit_id: "",
      sales: [],
      salesman_id: "",
      lng: "",
      lat: "",
      from_price: "",
      to_price: "",
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
        id: true,
        sku: true,
        barcode: true,
        item: true,
        variant: true,
        category: true,
        group: true,
        brand: true,
        purchase_price: true,
        selling_price: true,
        inventory: true,
      },
      is_disabled: false,
      filterSetting: ["id"],
      printLoading: true,
      printObj: {
        id: "printData",
      },
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
  async mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
    await this.getTotal();
    await this.getData(1);
    this.getBranches();
    this.getReorders();
    this.getCategories();
    this.getBrands();
    this.getGroups();
  },
  // beforeRouteEnter(to, from, next) {
  //       next((vm) => {
  //   return permissionGuard(vm, "POS inventories report", "all sold_Unit RealState");
  // });

  //  },
  methods: {
    getItemsSumOf(items, field) {
      let sum = 0;
      items.forEach((item) => (sum += item[field]));
      return sum;
    },
    async getTotal() {
      this.isLoader = true;
      await adminApi
        .get(
          `/point-of-sale/inventories/grandTotal?end_date=${this.to_date ?? ""}
          &start_date=${this.from_date ?? ""}
          &branch_id=${this.branch_id ?? ""}
          &category_id=${this.category_id ?? ""}
          &group_id=${this.group_d ?? ""}`
        )
        .then((res) => {
          let l = res.data;
          this.total = l;
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
    async showReport() {
      this.$bvModal.hide(`filter`);
      await this.getTotal();

      await this.getData(1);
    },
    createBackup() {
      setTimeout(() => {
        let bar = document.getElementById("progress-bar");
        let self = this;
        const config = {
          onUploadProgress: function (progressEvent) {
            const percentCompleted = Math.round(
              (progressEvent.loaded / progressEvent.total) * 100
            );
            self.progress = percentCompleted;
            bar.innerHTML = `${percentCompleted}%`;
            bar.style.width = `${percentCompleted}%`;
          },
        };
        adminApi
          .post(`/backups`, {}, config)
          .then((res) => {
            this.getData();
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
      }, 500);
    },

    formatDate(value) {
      return formatDateOnly(value);
    },
    getData(page = 1) {
      this.isLoader = true;
      let filter = "";
      for (let i = 0; i < this.filterSetting.length; ++i) {
        filter += `columns[${i}]=${this.filterSetting[i]}&`;
      }
      adminApi
        .get(
          `/point-of-sale/inventories?page=${page}
          &per_page=${this.per_page}
          &end_date=${this.to_date ?? ""}
          &start_date=${this.from_date ?? ""}
          &branch_id=${this.branch_id ?? ""}
          &category_id=${this.category_id ?? ""}
          &group_id=${this.group_d ?? ""}`
        )
        .then((res) => {
          let l = res.data;
          let items = res.data.data;
          let self = this;
          items.push({
            bold: true,
            name: "اجمالي",
            name_e: "Total",
            purchase_price: self.getItemsSumOf(items, "purchase_price"),
            selling_price: self.getItemsSumOf(items, "selling_price"),
            quantity: self.getItemsSumOf(items, "quantity"),
          });
          items.push({
            bold: true,
            name: "المجموع الاجمالي",
            name_e: "Grand total",
            purchase_price: self.total.purchase_price,
            selling_price: self.total.selling_price,
            quantity: self.total.quantity,
          });

          this.items = items;
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
            `/point-of-sale/inventories?page=${this.current_page}
          &per_page=${this.per_page}
          &end_date=${this.to_date ?? ""}
          &start_date=${this.from_date ?? ""}
          &branch_id=${this.branch_id ?? ""}
          &category_id=${this.category_id ?? ""}
          &group_id=${this.group_d ?? ""}`
          )
          .then((res) => {
            let l = res.data;
            let self = this;
            let items = res.data.data;
            items.push({
              bold: true,
              name: "اجمالي",
              name_e: "Total",
              purchase_price: self.getItemsSumOf(items, "purchase_price"),
              selling_price: self.getItemsSumOf(items, "selling_price"),
              quantity: self.getItemsSumOf(items, "quantity"),
            });
            items.push({
              bold: true,
              name: "المجموع الاجمالي",
              name_e: "Grand total",
              purchase_price: self.total.purchase_price,
              selling_price: self.total.selling_price,
              quantity: self.total.quantity,
            });
            this.items = items;
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
    async getOwners() {
      this.isLoader = true;

      await adminApi
        .get(`/real-estate/owners`)
        .then((res) => {
          let l = res.data.data;
          this.owners = l;
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
    async getCustomers() {
      this.isLoader = true;
      await adminApi
        .get(`/general-customer`)
        .then((res) => {
          let l = res.data.data;
          this.customers = l;
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
    async getEmployees() {
      this.isLoader = true;
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
        })
        .finally(() => {
          this.isLoader = false;
        });
    },

    async getCategories() {
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
        });
    },
    async getBrands() {
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
        });
    },
    async getBranches() {
      this.isLoader = true;
      await adminApi
        .get(`/branches`)
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
    async getGroups() {
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
        });
    },

    async getSalesTypes() {
      this.isLoader = true;
      await adminApi
        .get(`/salesmen-types`)
        .then((res) => {
          let l = res.data.data;
          this.salesTypes = l;
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
    getReorders() {
      this.reorders = [
        { value: "yes", name: "نعم", name_e: "Yes" },
        { value: "no", name: "لا", name_e: "No" },
      ];
    },
    async getWallets() {
      this.isLoader = true;

      await adminApi
        .get(`/real-estate/wallets`)
        .then((res) => {
          let l = res.data.data;
          this.wallets = l;
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
    async getProperties() {
      this.isLoader = true;

      await adminApi
        .get(`/tree-properties`)
        .then((res) => {
          let l = res.data.data;
          this.properties = l;
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
    async getSales() {
      this.isLoader = true;

      await adminApi
        .get(`/salesmen`)
        .then((res) => {
          let l = res.data.data;
          this.sales = l;
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
    async getUnits() {
      this.isLoader = true;

      await adminApi
        .get(`/real-estate/units`)
        .then((res) => {
          let l = res.data.data;
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
        let wb = XLSX.utils.table_to_book(elt, { sheet: "Sheet JS" });
        if (dl) {
          XLSX.write(wb, { bookType: type, bookSST: true, type: "base64" });
        } else {
          XLSX.writeFile(
            wb,
            fn ||
              ("Sales Report" + "." || "SheetJSTableExport.") + (type || "xlsx")
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
    <b-modal
      dialog-class="modal-full-width"
      id="filter"
      :title="$t('general.filter')"
      title-class="font-18"
      body-class="p-4 "
      :hide-footer="true"
    >
      <div class="row mb-2 px-2">
        <div class="col-md-2">
          <label>{{ $t("general.from_date") }}</label>
          <input class="form-control" type="date" v-model="from_date" />
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.to_date") }}</label>
          <input type="date" class="form-control" v-model="to_date" />
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.branch") }}</label>
          <multiselect
            v-model="branch_id"
            :options="branches.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? branches.find((x) => x.id == opt).name
                  : branches.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.brand") }}</label>
          <multiselect
            v-model="brand_id"
            :options="brands.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? brands.find((x) => x.id == opt).name
                  : brands.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>

        <div class="col-md-2">
          <label>{{ $t("general.category") }}</label>
          <multiselect
            v-model="category_id"
            :options="categories.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? categories.find((x) => x.id == opt).name
                  : categories.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.group") }}</label>
          <multiselect
            v-model="group_d"
            :options="groups.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? groups.find((x) => x.id == opt).name
                  : groups.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
      </div>
      <!-- <hr class="mx-2" />
      <div class="row px-2 mb-4">
        <div class="col-md-2">
          <label>{{ $t("general.reorder") }}</label>
          <multiselect
            v-model="reorder"
            :options="reorders.map((type) => type.value)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? reorders.find((x) => x.value == opt).name
                  : reorders.find((x) => x.value == opt).name_e
            "
          >
          </multiselect>
        </div>
      </div> -->
      <div class="d-flex">
        <b-button
          variant="success"
          type="submit"
          class="mx-2"
          v-if="!isLoader"
          @click.prevent="showReport"
        >
          {{ $t("general.filter") }}
        </b-button>

        <b-button variant="success" class="mx-1" disabled v-else>
          <b-spinner small></b-spinner>
          <span class="sr-only">{{ $t("login.Loading") }}...</span>
        </b-button>
      </div>
    </b-modal>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">
                {{ $t("general.inventoriesReport") }}
              </h4>
              <div class="col-xs-10 col-md-9 col-lg-7" style="font-weight: 500">
                <div class="d-inline-block" style="width: 22.2%">
                  <!-- Basic dropdown -->
                  <b-dropdown
                    variant="primary"
                    :text="$t('general.searchSetting')"
                    ref="dropdown"
                    class="btn-block setting-search dropdown-menu-custom-company"
                  >
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="id"
                      class="mb-1"
                    >
                      {{ getCompanyKey("id") }}
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

            <div
              class="row justify-content-between align-items-center mb-2 px-1"
            >
              <div class="col-md-3 d-flex align-items-center mb-1 mt-2 mb-xl-0">
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
                  <!-- start mult delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length > 1"
                    @click.prevent="deleteBranch(checkAll)"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!-- end mult delete  -->
                  <!--  start one delete  -->
                  <button
                    class="custom-btn-dowonload"
                    v-if="checkAll.length == 1"
                    @click.prevent="deleteBranch(checkAll[0])"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                  <!--  end one delete  -->
                </div>
                <!-- end create and printer -->
              </div>
              <div
                class="col-xs-10 col-md-9 col-lg-7 d-flex align-items-center justify-content-end"
              >
                <div class="d-fex">
                  <!-- start filter and setting -->
                  <div class="d-inline-block">
                    <b-button
                      v-b-modal.filter
                      class="mx-1 custom-btn-background"
                    >
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
                      <b-form-checkbox v-model="setting.id" class="mb-1">
                        {{ getCompanyKey("id") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.sku" class="mb-1">
                        {{ getCompanyKey("sku") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.barcode" class="mb-1">
                        {{ getCompanyKey("barcode") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.item" class="mb-1">
                        {{ getCompanyKey("item") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.variant" class="mb-1">
                        {{ getCompanyKey("variant") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.category" class="mb-1">
                        {{ getCompanyKey("category") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.group" class="mb-1">
                        {{ getCompanyKey("group") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.brand" class="mb-1">
                        {{ getCompanyKey("brand") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.purchase_price"
                        class="mb-1"
                      >
                        {{ getCompanyKey("purchase_price") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.selling_price"
                        class="mb-1"
                      >
                        {{ getCompanyKey("selling_price") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.inventory" class="mb-1">
                        {{ getCompanyKey("inventory") }}
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
                    <th v-if="setting.id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("id") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.sku">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sku") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('start_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-start_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.barcode">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("barcode") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('end_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-end_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.item">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("item") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('serial_number'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-serial_number'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.variant">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("variant") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('prefix'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-prefix'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.category">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("category") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('customer'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-customer'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.group">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("group") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('customer'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-customer'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.brand">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("brand") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('customer'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-customer'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.purchase_price">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("purchase_price") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('phone'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-phone'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.selling_price">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("selling_price") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('phone'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-phone'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.inventory">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("inventory") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('phone'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-phone'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                  </tr>
                </thead>
                <tbody v-if="items.length > 0">
                  <tr
                    :style="data.bold ? 'font-weight:bold' : ''"
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in items"
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
                          :value="data.id"
                          v-model="checkAll"
                        />
                      </div>
                    </td>
                    <td v-if="setting.id">
                      <template v-if="!data.bold">
                        {{ data.id }}
                      </template>
                      <template v-else>{{
                        $i18n.locale == "ar" ? data.name : data.name_e
                      }}</template>
                    </td>
                    <td v-if="setting.sku">
                      {{ data.sku }}
                    </td>
                    <td v-if="setting.barcode">
                      {{ data.bar_code }}
                    </td>
                    <td v-if="setting.item">
                      {{ $i18n.locale == "ar" ? data.title : data.title_e }}
                    </td>
                    <td v-if="setting.variant">
                      {{ data.variant_title }}
                    </td>
                    <td v-if="setting.category">
                      {{
                        $i18n.locale == "ar"
                          ? data.category_name
                          : data.category_name_e
                      }}
                    </td>
                    <td v-if="setting.group">
                      {{
                        $i18n.locale == "ar"
                          ? data.group_name
                          : data.group_name_e
                      }}
                    </td>
                    <td v-if="setting.brand">
                      {{
                        $i18n.locale == "ar"
                          ? data.brand_name
                          : data.brand_name_e
                      }}
                    </td>
                    <td v-if="setting.purchase_price">
                      {{ data.purchase_price }}
                    </td>

                    <td v-if="setting.selling_price">
                      {{ data.selling_price }}
                    </td>
                    <td v-if="setting.inventory">
                      {{ data.quantity }}
                    </td>
                  </tr>
                </tbody>
                <tbody v-else>
                  <tr>
                    <th class="text-center" colspan="50">
                      {{ $t("general.notDataFound") }}
                    </th>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>

<style scoped>
.boldTd {
  font-weight: bold;
}
table th,
table td {
  white-space: nowrap !important;
}
@media print {
  .do-not-print {
    display: none;
  }

  .arrow-sort {
    display: none;
  }
}
hr {
  border-bottom: 1px solid #fff;
}
</style>
