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
import NavTab from "./components/nav";
import Products from "./components/product";
import Order from "./components/order";
/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Purchase",
    meta: [{ name: "description", content: "Purchase" }],
  },
  mixins: [translation],
  components: {
    Order,
    Layout,
    Products,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    DatePicker,
    Multiselect,
    NavTab,
  },
  beforeRouteEnter(to, from, next) {
          next((vm) => {
    return permissionGuard(vm, "POS purchase", "all POS purchase");
  });

  },
  data() {
    return {
      per_page: 50,
      selectedBranch: null,
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
        start_date: this.formatDate(new Date()),
        end_date: this.formatDate(new Date()),
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
        "customer_id",
        "instalment_date",
        "currency_id",
        "rate",
        "debit",
        "credit",
        "total",
        "break_type",
        "instalment_type_id",
        "amount_status",
      ],
      Tooltip: "",
      mouseEnter: null,
      currency: null
    };
  },
  validations: {
    create: {
      start_date: { required },
      end_date: { required },
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
      this.$store.commit('order/allTurncate');
      this.getCurrency();
  },
  methods: {
      getCurrency() {
          this.isLoader = true;

          adminApi
              .get(
                  `/currencies?is_default=1`
              )
              .then((res) => {
                  let l = res.data.data;
                  this.currency = l.find(el => el.is_default == 1);
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
     *  start get Data module && pagination
     */
    getData(page = 1) {
      this.$v.create.$touch();
      if (this.$v.create.$invalid) {
        return;
      } else {
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
            `/recievable-payable/filterBreak?start_date=${
              this.create.start_date
            }&end_date=${this.create.end_date}&customer_id=${
              this.create.customer_id ?? ""
            }&instalment_type_id=${
              this.create.instalment_type_id ?? ""
            }&amount_status=${
              this.create.amount_status ?? ""
            }&page=${page}&per_page=${this.per_page}&search=${
              this.search
            }&${filter}`
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
              `/recievable-payable/filterBreak?start_date=${
                this.create.start_date
              }&end_date=${this.create.end_date}&customer_id=${
                this.create.customer_id ?? ""
              }&instalment_type_id=${
                this.create.instalment_type_id ?? ""
              }&amount_status=${this.create.amount_status ?? ""}&page=${
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
        .get(`/branches?products=1`)
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
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <NavTab type="purchase" @selectedBranch="selectedBranch = $event" />
    <div class="row">
      <div class="col-lg-7">
        <Products type="purchase" :currency="currency" :selectedBranch="selectedBranch" />
      </div>
      <div
        class="col-lg-5"
        :style="
          $i18n.locale == 'en'
            ? 'padding-left:0;padding-right:22px'
            : 'padding-right:0;padding-left:22px'
        "
      >
        <Order type="purchase" :currency="currency" :selectedBranch="selectedBranch" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" />
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



