<script>
import Layout from "../../layouts/main";
import permissionGuard from "../../../helper/permission";

import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
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
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import alphaArabic from "../../../helper/alphaArabic";
import alphaEnglish from "../../../helper/alphaEnglish";
import {
  dynamicSortString,
  dynamicSortNumber,
} from "../../../helper/tableSort";
import translation from "../../../helper/mixin/translation-mixin";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Sales List",
    meta: [{ name: "description", content: "Sales List" }],
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
      owners: [],
      payment_type: null,
      customer_id: null,
      paymentTypes: [],
      customers: [],
      total: null,
      to_date: "",
      from_date: "",
      owner_id: null,
      buildings: [],
      building_id: null,
      wallets: [],
      wallet_id: null,
      properties: [],
      property_id: null,
      units: [],
      unit_id: null,
      sales: [],
      salesman_id: null,
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
        invoice_id: true,
        date: true,
        type: true,
        sold_by: true,
        sold_to: true,
        status: true,
        item_purchased: true,
        tax: true,
        discount: true,
        total: true,
        due: true,
      },
      is_disabled: false,
      filterSetting: ["invoice_id", "tax", "discount"],
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

    this.getCustomers();
    this.getPaymentTypes();
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "POS sales list", "all sold_Unit RealState");
    });
  },
  methods: {
    getItemsSumOf(items, field) {
      let sum = 0;
      items.forEach((item) => (sum += item[field]));
      return sum;
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
          `/point-of-sale/sales-list?page=${page}&per_page=${this.per_page}
          &customer_id=${this.customer_id ?? ""}
          &payment_method_id=${this.payment_type ?? ""}`
        )
        .then((res) => {
          let l = res.data;
          let self = this;
          let items = l.data;
          items.push({
            bold: true,
            name: "الاجمالي",
            name_e: "total",
            item_purchased: self.getItemsSumOf(items, "item_purchased"),
            tax: self.getItemsSumOf(items, "tax"),
            discount: self.getItemsSumOf(items, "discount"),
            total: self.getItemsSumOf(items, "total").toFixed(2),
            due_amount: self.getItemsSumOf(items, "due_amount"),
          });

          items.push({
            bold: true,
            name: "الاجمالي الكلي",
            name_e: "Grand total",
            item_purchased: self.total.item_purchased_total,
            tax: self.total.tax_total,
            discount: self.total.discount_total,
            total: self.total.total_total.toFixed(2),
            due_amount: self.total.due_amount_total,
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
            `/point-of-sale/sales-list?page=${this.current_page}&per_page=${
              this.per_page
            }
            &customer_id=${this.customer_id ?? ""}
            &payment_method_id=${this.payment_type ?? ""}`
          )
          .then((res) => {
            let l = res.data;
            let self = this;
            let items = l.data;
            items.push({
              bold: true,
              name: "الاجمالي",
              name_e: "total",
              item_purchased: self.getItemsSumOf(items, "item_purchased"),
              tax: self.getItemsSumOf(items, "tax"),
              discount: self.getItemsSumOf(items, "discount"),
              total: self.getItemsSumOf(items, "total"),
              due_amount: self.getItemsSumOf(items, "due_amount"),
            });
            items.push({
              bold: true,
              name: "الاجمالي الكلي",
              name_e: "Grand total",
              item_purchased: self.total.item_purchased_total,
              tax: self.tax_total,
              discount: self.discount_total,
              total: self.total_total,
              due_amount: self.due_amount_total,
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
        .finally(() => {});
    },
    async getTotal() {
      this.isLoader = true;
      await adminApi
        .get(
          `/point-of-sale/sales-list/grandTotal?customer_id=${
            this.customer_id ?? ""
          }
          &payment_method_id=${this.payment_type ?? ""}`
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
    async getPaymentTypes() {
      await adminApi
        .get(`/payment-methods`)
        .then((res) => {
          let l = res.data.data;
          this.paymentTypes = l;
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
            fn || ("Branch" + "." || "SheetJSTableExport.") + (type || "xlsx")
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
      id="filter"
      :title="$t('general.filter')"
      title-class="font-18"
      body-class="p-4 "
      :hide-footer="true"
    >
      <div class="row mb-2 px-2">
        <div class="col-lg-12">
          <label>{{ $t("general.customer") }}</label>
          <multiselect
            v-model="customer_id"
            :options="customers.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? customers.find((x) => x.id == opt).name
                  : customers.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
        <div class="col-lg-12">
          <label>{{ $t("general.paymentType") }}</label>
          <multiselect
            v-model="payment_type"
            :options="paymentTypes.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? paymentTypes.find((x) => x.id == opt).name
                  : paymentTypes.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
      </div>
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
                {{ $t("general.SalesList") }}
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
                      value="invoice_id"
                      class="mb-1"
                    >
                      {{ getCompanyKey("sales_list_invoice_id") }}
                    </b-form-checkbox>
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="discount"
                      class="mb-1"
                    >
                      {{ getCompanyKey("sales_list_discount") }}
                    </b-form-checkbox>
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="tax"
                      class="mb-1"
                    >
                      {{ getCompanyKey("sales_list_tax") }}
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
                      <b-form-checkbox
                        v-model="setting.invoice_id"
                        class="mb-1"
                      >
                        {{ getCompanyKey("sales_list_invoice_id") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.date" class="mb-1">
                        {{ getCompanyKey("sales_list_date") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.type" class="mb-1">
                        {{ getCompanyKey("sales_list_type") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.sold_by" class="mb-1">
                        {{ getCompanyKey("sales_list_sold_by") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.sold_to" class="mb-1">
                        {{ getCompanyKey("sales_list_sold_to") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.status" class="mb-1">
                        {{ getCompanyKey("sales_list_status") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.item_purchased"
                        class="mb-1"
                      >
                        {{ getCompanyKey("sales_list_item_purchased") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.tax" class="mb-1">
                        {{ getCompanyKey("sales_list_tax") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.discount" class="mb-1">
                        {{ getCompanyKey("sales_list_tax") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.discount" class="mb-1">
                        {{ getCompanyKey("sales_list_discount") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.total" class="mb-1">
                        {{ getCompanyKey("sales_list_total") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.due" class="mb-1">
                        {{ getCompanyKey("sales_list_due") }}
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
                    <th v-if="setting.invoice_id">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("sales_list_invoice_id")
                        }}</span>
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
                    <th v-if="setting.date">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sales_list_date") }}</span>
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
                    <th v-if="setting.type">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sales_list_type") }}</span>
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
                    <th v-if="setting.sold_by">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sales_list_sold_by") }}</span>
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
                    <th v-if="setting.sold_to">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sales_list_sold_to") }}</span>
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
                    <th v-if="setting.status">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sales_list_status") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.item_purchased">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("sales_list_item_purchased")
                        }}</span>
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
                    <th v-if="setting.tax">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sales_list_tax") }}</span>
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
                    <th v-if="setting.discount">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sales_list_discount") }}</span>
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
                    <th v-if="setting.total">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sales_list_total") }}</span>
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
                    <th v-if="setting.due">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sales_list_due") }}</span>
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
                    <td v-if="setting.invoice_id">
                      <template v-if="data.bold">
                        {{ $i18n.locale == "ar" ? data.name : data.name_e }}
                      </template>
                      <template v-else>
                        {{ data.invoice_id }}
                      </template>
                    </td>
                    <td v-if="setting.date">
                      {{ data.date }}
                    </td>
                    <td v-if="setting.type">
                      {{ data.type }}
                    </td>
                    <td v-if="setting.sold_by">
                      {{ data.created_by }}
                    </td>

                    <td v-if="setting.sold_to">
                      {{ data.sold_to }}
                    </td>
                    <td v-if="setting.status">
                      {{ data.status }}
                    </td>
                    <td v-if="setting.item_purchased">
                      {{ data.item_purchased }}
                    </td>
                    <td v-if="setting.tax">
                      {{ data.tax }}
                    </td>
                    <td v-if="setting.discount">
                      {{ data.discount }}
                    </td>
                    <td v-if="setting.total">
                      {{ data.total }}
                    </td>
                    <td v-if="setting.due">
                      {{ data.due_amount }}
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
