<script>
import Layout from "../../layouts/main";
import permissionGuard from "../../../helper/permission";

import PageHeader from "../../../components/general/Page-header";
import adminApi from "../../../api/adminAxios";
import Switches from "vue-switches";
import Multiselect from "vue-multiselect";
import Swal from "sweetalert2";
import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import {
  dynamicSortString,
  dynamicSortNumber,
} from "../../../helper/tableSort";
import translation from "../../../helper/mixin/translation-mixin";
import { formatDateOnly } from "../../../helper/startDate";

export default {
  page: {
    title: "Admin report",
    meta: [{ name: "description", content: "Admin report" }],
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
        date: true,
        start_date: true,
        end_date: true,
        serial_number: true,
        prefix: true,
        salesman: true,
        customer: true,
        phone: true,
        contact_phone: true,
        branch: true,
        document: true,
        unit: true,
        unit_area: true,
        unit_value: true,
        building: true,
        building_lng: true,
        building_lat: true,
        city: true,
        owner: true,
        owner_phone: true,
      },
      is_disabled: false,
      filterSetting: [],
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
  mounted() {
    this.company_id = this.$store.getters["auth/company_id"];
    this.getOwners();
    this.getBuildings();
    this.getWallets();
    this.getProperties();
    this.getSales();
    this.getUnits();
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Admin Report", "all sold_Unit RealState");
    });
  },
  methods: {
    showReport() {
      this.$bvModal.hide(`filter`);
      this.getData(1);
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
      this.from_date = this.from_date ? this.from_date : this.to_date;
      this.to_date = this.to_date ? this.to_date : this.from_date;
      this.from_price = this.from_price ? this.from_price : this.to_price;
      this.to_price = this.to_price ? this.to_price : this.from_price;
      this.lng = this.lng ? this.lng : this.lat;
      this.lat = this.lat ? this.lat : this.lng;
      adminApi
        .get(
          `/real-estate/contracts/general-filter?page=${page}&per_page=${
            this.per_page
          }${this.building_id ? `&building_id=${this.building_id}` : ""}
          ${this.owner_id ? `&owner_id=${this.owner_id}` : ""}
          ${this.wallet_id ? `&wallet_id=${this.wallet_id}` : ""}
          ${this.from_date ? `&start_date=${this.from_date}` : ""}
          ${this.to_date ? `&end_date=${this.to_date}` : ""}
          ${this.lat ? `&lat=${this.lat}` : ""}
          ${this.lng ? `&lng=${this.lng}` : ""}
          ${this.from_price ? `&unit_value_from=${this.from_price}` : ""}
          ${this.to_price ? `&unit_value_to=${this.to_price}` : ""}
          ${this.property_id ? `&properties=${this.property_id}` : ""}
          ${this.salesman_id ? `&salesman_id=${this.salesman_id}` : ""}
          ${this.unit_id ? `&unit_id=${this.unit_id}` : ""}`
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
    },
    getDataCurrentPage(page = 1) {
      if (
        this.current_page <= this.itemsPagination.last_page &&
        this.current_page != this.itemsPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;

        this.from_date = this.from_date ? this.from_date : this.to_date;
        this.to_date = this.to_date ? this.to_date : this.from_date;
        this.from_price = this.from_price ? this.from_price : this.to_price;
        this.to_price = this.to_price ? this.to_price : this.from_price;
        this.lng = this.lng ? this.lng : this.lat;
        this.lat = this.lat ? this.lat : this.lng;

        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/real-estate/contracts/general-filter?page=${
              this.current_page
            }&per_page=${this.per_page}${
              this.building_id ? `&building_id=${this.building_id}` : ""
            }
          ${this.owner_id ? `&owner_id=${this.owner_id}` : ""}
          ${this.wallet_id ? `&wallet_id=${this.wallet_id}` : ""}
          ${this.from_date ? `&start_date=${this.from_date}` : ""}
          ${this.to_date ? `&end_date=${this.to_date}` : ""}
          ${this.lat ? `&lat=${this.lat}` : ""}
          ${this.lng ? `&lng=${this.lng}` : ""}
          ${this.from_price ? `&unit_value_from=${this.from_price}` : ""}
          ${this.to_price ? `&unit_value_to=${this.to_price}` : ""}
          ${this.property_id ? `&properties=${this.property_id}` : ""}
          ${this.salesman_id ? `&salesman_id=${this.salesman_id}` : ""}
          ${this.unit_id ? `&unit_id=${this.unit_id}` : ""}`
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
    async getBuildings() {
      this.isLoader = true;

      await adminApi
        .get(`/real-estate/buildings`)
        .then((res) => {
          let l = res.data.data;
          this.buildings = l;
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
      dialog-class="modal-full-width"
      id="filter"
      :title="$t('general.filter')"
      title-class="font-18"
      body-class="p-4 "
      :hide-footer="true"
    >
      <div class="row mb-2 px-2">
        <div class="col-md-2">
          <label>{{ $t("general.wallet") }}</label>
          <multiselect
            v-model="wallet_id"
            :options="wallets.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? wallets.find((x) => x.id == opt).name
                  : wallets.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.owner") }}</label>
          <multiselect
            v-model="owner_id"
            :options="owners.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? owners.find((x) => x.id == opt).name
                  : owners.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.building") }}</label>
          <multiselect
            v-model="building_id"
            :options="buildings.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? buildings.find((x) => x.id == opt).name
                  : buildings.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.unit") }}</label>
          <multiselect
            v-model="unit_id"
            :options="units.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? units.find((x) => x.id == opt).name
                  : units.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>

        <div class="col-md-2">
          <label>{{ $t("general.property") }}</label>
          <multiselect
            v-model="property_id"
            :options="properties.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? properties.find((x) => x.id == opt).name
                  : properties.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.saleman") }}</label>
          <multiselect
            v-model="salesman_id"
            :options="sales.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? sales.find((x) => x.id == opt).name
                  : sales.find((x) => x.id == opt).name_e
            "
          >
          </multiselect>
        </div>
      </div>
      <hr class="mx-2" />
      <div class="row px-2 mb-4">
        <div class="col-md-2">
          <label>{{ $t("general.from_date") }}</label>
          <input class="form-control" type="date" v-model="from_date" />
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.to_date") }}</label>
          <input type="date" class="form-control" v-model="to_date" />
        </div>
        <!-- <div class="col-md-2"></div> -->
        <div class="col-md-2">
          <label>{{ $t("general.from_price") }}</label>
          <input class="form-control" type="text" v-model="from_price" />
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.to_price") }}</label>
          <input class="form-control" type="text" v-model="to_price" />
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.lng") }}</label>
          <input class="form-control" type="text" v-model="lng" />
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.lat") }}</label>
          <input class="form-control" type="text" v-model="lat" />
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
                {{ $t("general.AdminReport") }}
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
                      <b-form-checkbox v-model="setting.date" class="mb-1">
                        {{ getCompanyKey("admin_report_date") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.start_date"
                        class="mb-1"
                      >
                        {{ getCompanyKey("admin_report_start_date") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.end_date" class="mb-1">
                        {{ getCompanyKey("admin_report_end_date") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.serial_number"
                        class="mb-1"
                      >
                        {{ getCompanyKey("admin_report_serial_number") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.prefix" class="mb-1">
                        {{ getCompanyKey("admin_report_prefix") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.customer" class="mb-1">
                        {{ getCompanyKey("customer") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.salesman" class="mb-1">
                        {{ getCompanyKey("saleman") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.phone" class="mb-1">
                        {{ getCompanyKey("admin_report_phone") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.contact_phone"
                        class="mb-1"
                      >
                        {{ getCompanyKey("admin_report_contact_phone") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.branch" class="mb-1">
                        {{ getCompanyKey("branch") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.unit_area" class="mb-1">
                        {{ getCompanyKey("admin_report_unit_area") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.unit" class="mb-1">
                        {{ getCompanyKey("unit") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.unit_area" class="mb-1">
                        {{ getCompanyKey("admin_report_unit_area") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.unit_value"
                        class="mb-1"
                      >
                        {{ getCompanyKey("admin_report_unit_value") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.building" class="mb-1">
                        {{ getCompanyKey("building") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.building_lng"
                        class="mb-1"
                      >
                        {{ getCompanyKey("admin_report_building_lng") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.building_lat"
                        class="mb-1"
                      >
                        {{ getCompanyKey("admin_report_building_lat") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.city" class="mb-1">
                        {{ getCompanyKey("city") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.owner" class="mb-1">
                        {{ getCompanyKey("owner") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.owner_phone"
                        class="mb-1"
                      >
                        {{ getCompanyKey("admin_report_owner_phone") }}
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
                    <th v-if="setting.date">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("admin_report_date") }}</span>
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
                    <th v-if="setting.start_date">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("admin_report_start_date")
                        }}</span>
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
                    <th v-if="setting.end_date">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("admin_report_end_date")
                        }}</span>
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
                    <th v-if="setting.serial_number">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("admin_report_serial_number")
                        }}</span>
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
                    <th v-if="setting.prefix">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("admin_report_prefix") }}</span>
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
                    <th v-if="setting.salesman">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("saleman") }}</span>
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
                    <th v-if="setting.customer">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("customer") }}</span>
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
                    <th v-if="setting.phone">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("admin_report_phone") }}</span>
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
                    <th v-if="setting.contact_phone">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("admin_report_contact_phone")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('contact_phone'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-contact_phone'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.branch">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("branch") }}</span>
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
                    <th v-if="setting.document">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("document") }}</span>
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
                    <th v-if="setting.unit">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("unit") }}</span>
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
                    <th v-if="setting.unit_area">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("admin_report_unit_area")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('unit_area'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-unit_area'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.unit_value">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("admin_report_unit_value")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('unit_value'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-unit_value'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.building">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("building") }}</span>
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
                    <th v-if="setting.building_lng">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("admin_report_building_lng")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('lng'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-lng'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.building_lat">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("admin_report_building_lat")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('lat'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-lng'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.city">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("city") }}</span>
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
                    <th v-if="setting.owner">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("owner") }}</span>
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
                    <th v-if="setting.owner_phone">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("admin_report_owner_phone")
                        }}</span>
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
                  </tr>
                </thead>
                <tbody v-if="items.length > 0">
                  <tr
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
                    <td v-if="setting.date">
                      {{ data.date }}
                    </td>
                    <td v-if="setting.start_date">
                      {{ data.start_date }}
                    </td>
                    <td v-if="setting.end_date">
                      {{ data.end_date }}
                    </td>
                    <td v-if="setting.serial_number">
                      {{ data.serial_number }}
                    </td>
                    <td v-if="setting.prefix">
                      {{ data.prefix }}
                    </td>
                    <td v-if="setting.salesman">
                      <template v-if="data.salesman">
                        {{
                          data.salesman
                            ? $i18n.locale == "ar"
                              ? data.salesman.name
                              : data.salesman.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.customer">
                      <template v-if="data.customer">
                        {{
                          data.customer
                            ? $i18n.locale == "ar"
                              ? data.customer.name
                              : data.customer.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.customer">
                      <template v-if="data.customer">
                        {{ data.customer.phone }}
                      </template>
                    </td>
                    <td v-if="setting.customer">
                      <template v-if="data.customer">
                        {{ data.customer.contact_phone }}
                      </template>
                    </td>
                    <td v-if="setting.branch">
                      <template v-if="data.branch">
                        {{
                          data.branch
                            ? $i18n.locale == "ar"
                              ? data.branch.name
                              : data.branch.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.document">
                      <template v-if="data.document">
                        {{
                          data.document
                            ? $i18n.locale == "ar"
                              ? data.document.name
                              : data.document.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.unit">
                      <template v-if="data.unit">
                        {{
                          data.unit
                            ? $i18n.locale == "ar"
                              ? data.unit.name
                              : data.unit.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.unit_area">
                      <template v-if="data.unit">
                        {{ data.unit.unit_area }}
                      </template>
                    </td>
                    <td v-if="setting.unit_value">
                      <template v-if="data.contract_details">
                        {{ data.contract_details.unit_value }}
                      </template>
                    </td>
                    <td v-if="setting.building">
                      <template v-if="data.building">
                        {{
                          data.building
                            ? $i18n.locale == "ar"
                              ? data.building.name
                              : data.building.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.building_lng">
                      <template v-if="data.building">
                        {{ data.building.lng }}
                      </template>
                    </td>
                    <td v-if="setting.building_lat">
                      <template v-if="data.building">
                        {{ data.building.lat }}
                      </template>
                    </td>
                    <td v-if="setting.city">
                      <template v-if="data.city">
                        {{
                          data.city
                            ? $i18n.locale == "ar"
                              ? data.city.name
                              : data.city.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.owner">
                      <template v-if="data.owner">
                        {{
                          $i18n.locale == "ar"
                            ? data.owner.name
                            : data.owner.name_e
                        }}
                      </template>
                    </td>
                    <td v-if="setting.owner_phone">
                      <template v-if="data.owner">
                        {{ data.owner.phone }}
                      </template>
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
