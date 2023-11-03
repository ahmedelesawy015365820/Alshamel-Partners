<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import permissionGuard from "../../../helper/permission";

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
    title: "Unsold unit report",
    meta: [{ name: "description", content: "Unsold unit report" }],
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
      allOwners: [],
      owners: [],
      to_date: "",
      from_date: "",
      owner_id: [],
      allBuildings: [],
      buildings: [],
      building_id: [],
      allWallets: [],
      wallets: [],
      wallet_id: [],
      properties: [],
      property_id: null,
      allCountries: [],
      countries: [],
      country_id: [],
      allGovernorates: [],
      governorates: [],
      governorate_id: [],
      allCities: [],
      cities: [],
      city_id: [],
      unit_ty: [],
      unit_ty_id: null,
      unit_area: "",
      rooms: "",
      path: "",
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
      isBuilding: false,
      checkAll: [],
      current_page: 1,
      setting: {
        building: true,
        country: true,
        owner: true,
        governorate: true,
        city: true,
        wallet: true,
        unit_ty: true,
        unit_area: true,
        rooms: true,
        path: true,
      },
      is_disabled: false,
      filterSetting: [],
      printLoading: true,
      printObj: {
        id: "printData",
      },
      isAll: true
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
    this.getData();
    this.getWallets();
    this.getAllOwners();
    this.getAllBuildings();
    this.getCountries();
    this.getGovernorates();
    this.getCities();
    this.company_id = this.$store.getters["auth/company_id"];
    this.getProperties();
    this.getUnitType();
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(
        vm,
        "Unsold Unit Report",
        "all unSold_Unit RealState"
      );
    });
  },
  methods: {
    async getAllOwners() {
      this.isLoader = true;
      try {
        const res = await adminApi.get(`/real-estate/owners/get-drop-down`);
        let l = res.data.data;
        this.owners = l;
        if(this.isAll){
            this.allOwners = l;
        }
      } catch (err) {
        Swal.fire({
          icon: "error",
          title: `${this.$t("general.Error")}`,
          text: `${this.$t("general.Thereisanerrorinthesystem")}`,
        });
      } finally {
        this.isLoader = false;
      }
    },
    async getAllBuildings() {
      this.isLoader = true;
      try {
        let filter = "";
        for (let i = 0; i < this.country_id.length; ++i) {
            filter += `country_ids[${i}]=${this.country_id[i]}&`;
        }
        for (let i = 0; i < this.governorate_id.length; ++i) {
            filter += `governorate_ids[${i}]=${this.governorate_id[i]}&`;
        }
        for (let i = 0; i < this.city_id.length; ++i) {
            filter += `city_ids[${i}]=${this.city_id[i]}&`;
        }
        const res = await adminApi.get(`/real-estate/buildings/get-drop-down?${filter}`);
        const buildings = res.data.data;
        this.buildings = buildings;
        if(this.isAll){
            this.allBuildings = buildings;
        }else {
            this.countries = this.allCountries;
            this.cities = this.allCities;
            this.governorates = this.allGovernorates;
        }
      } catch (err) {
        Swal.fire({
          icon: "error",
          title: `${this.$t("general.Error")}`,
          text: `${this.$t("general.Thereisanerrorinthesystem")}`,
        });
      } finally {
        this.isLoader = false;
      }
    },
    async getWalletOwnersBuliding(e) {
          if(e){
              this.isLoader = true;
              try {
                  this.wallet_id =[];
                  this.building_id =[]
                  const res = await adminApi.post(`/real-estate/units/building-wallet`,{
                      wallet_ids: this.wallet_id
                  });
                  let l = res.data.data;
                  let own = [],bui = [];
                  l.forEach(el => own.push(...el.owners));
                  l.forEach(el => bui.push(...el.buildings));
                  this.owners = own;
                  this.buildings = bui;
              } catch (err) {
                  Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
              } finally {
                  this.isLoader = false;
              }
          }
      },
    showReport() {
      this.$bvModal.hide(`filter`);
      this.getFilter();
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
        .post(`/real-estate/un-sold-unit-report`, {
                page,
                per_page: this.per_page,
                wallet_id: this.wallet_id,
                building_id: this.building_id,
                owner_id: this.owner_id,
                property_id: this.property_id,
                city_id: this.city_id,
                country_id: this.country_id,
                governorate_id: this.governorate_id,
                rooms: this.rooms,
                unit_ty_id: this.unit_ty_id,
                unit_area: this.unit_area,
                path: this.path,
        })
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
        let filter = "";
        for (let i = 0; i < this.filterSetting.length; ++i) {
          filter += `columns[${i}]=${this.filterSetting[i]}&`;
        }

        adminApi
          .post(
            `/real-estate/un-sold-unit-report`,{
                page: this.current_page,
                per_page: this.per_page,
                wallet_id: this.wallet_id,
                building_id: this.building_id,
                owner_id: this.owner_id,
                property_id: this.property_id,
                city_id: this.city_id,
                country_id: this.country_id,
                governorate_id: this.governorate_id,
                rooms: this.rooms,
                unit_ty_id: this.unit_ty_id,
                unit_area: this.unit_area,
                path: this.path,
           })
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
    async getWallets() {
      this.isLoader = true;

      await adminApi
        .get(`/real-estate/wallets/get-drop-down`)
        .then((res) => {
          let l = res.data.data;
          this.wallets = l;
          if(this.isAll){
              this.allWallets = l;
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
        .get(`/salesmen/get-drop-down`)
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
    async getCountries() {
      this.isLoader = true;
      try {
        const res = await adminApi.get(`/countries/get-drop-down`);
        let countries = res.data.data;
        this.countries = countries;
          if(this.isAll){
              this.allCountries = countries;
          }
      } catch (err) {
        Swal.fire({
          icon: "error",
          title: `${this.$t("general.Error")}`,
          text: `${this.$t("general.Thereisanerrorinthesystem")}`,
        });
      } finally {
        this.isLoader = false;
      }
    },
    async getGovernorates() {
      this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.country_id.length; ++i) {
            filter += `country_ids[${i}]=${this.country_id[i]}&`;
        }
      await adminApi
        .get(`/governorates/get-drop-down?${filter}`)
        .then((res) => {
          let l = res.data.data;
          this.governorates = l;
            if(this.isAll){
                this.allGovernorates = l;
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
    async getCities() {
      this.isLoader = true;
        let filter = "";
        for (let i = 0; i < this.country_id.length; ++i) {
            filter += `country_ids[${i}]=${this.country_id[i]}&`;
        }
        for (let i = 0; i < this.governorate_id.length; ++i) {
            filter += `governorate_ids[${i}]=${this.governorate_id[i]}&`;
        }
      await adminApi
        .get(
          `/cities/get-drop-down?${filter}`
        )
        .then((res) => {
          let l = res.data.data;
            if(this.isAll){
                this.allCities = l;
            }
          this.cities = l;
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
          this.isAll = false;
        });
    },
    async getUnitType() {
      this.isLoader = true;
      try {
        const res = await adminApi.get(`/real-estate/unit-type/get-drop-down`);
        const units = res.data.data;
        this.unit_ty = units;
      } catch (err) {
        Swal.fire({
          icon: "error",
          title: `${this.$t("general.Error")}`,
          text: `${this.$t("general.Thereisanerrorinthesystem")}`,
        });
      } finally {
          this.isLoader = false;
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
    clearFilter() {
      // Clear all filter input values
      this.wallet_id = null;
      this.owner_id = null;
      this.building_id = null;
      this.country_id = null;
      this.governorate_id = null;
      this.city_id = null;
      this.property_id = null;
      this.unit_ty_id = null;
      this.unit_area = "";
      this.rooms = "";
      this.path = "";
      this.getData(); // Uncomment if you want to refresh data after clearing filters
    },
    showModelBuilding(building){
        if(this.building_id.length > 0 && this.building_id){
            this.isBuilding = true;
            this.building_id.forEach(el => {
                let build = this.buildings.find(e => e.id == el);
                if(build){
                    if(!this.country_id.includes(build.country_id) && build.country_id){
                        this.country_id.push(build.country_id);
                    }
                    if(!this.city_id.includes(build.city_id) && build.city_id){
                        this.city_id.push(build.city_id);
                    }
                    if(!this.governorate_id.includes(build.governorate_id) && build.governorate_id){
                        this.governorate_id.push(build.governorate_id);
                    }
                }
            });
        }else {
            this.isBuilding = false;
            this.country_id = [];
            this.city_id = [];
            this.governorate_id = [];
        }
    },
    showCountryModel(countries){
        if(countries.length > 0){
            this.getGovernorates();
            this.getAllBuildings();
        }
    },
    showGovernorateModel(governorates){
        if(governorates.length > 0){
            this.city_id = [];
            governorates.forEach(el => {
                let governorate = this.governorates.find(e => e.id == el);
                if(!this.country_id.includes(governorate.country_id) && governorate.country_id){
                    this.country_id.push(governorate.country_id);
                }
            });
            this.getCities();
            this.getAllBuildings();
        }else {
            this.city_id = [];
        }
    },
    showCityModel(cities){
        if(cities.length > 0){
            cities.forEach(el => {
                let city = this.cities.find(e => e.id == el);
                if(!this.country_id.includes(city.country_id) && city.country_id){
                    this.country_id.push(city.country_id);
                }
                if(!this.governorate_id.includes(city.governorate_id) && city.governorate_id){
                    this.governorate_id.push(city.governorate_id);
                }
            });
            this.getAllBuildings();
        }
    },
    getFilter (){
        this.setting = {
            building: this.building_id.length > 0 ? true: false,
            country: this.country_id.length > 0 ? true: false,
            owner: this.owner_id.length > 0 ? true: false,
            governorate: this.governorate_id.length > 0 ? true: false,
            city: this.city_id.length > 0 ? true: false,
            wallet: this.wallet_id.length > 0 ? true: false,
            unit_ty: this.unit_ty_id ? true: false,
            unit_area: this.unit_area ? true: false,
            rooms: this.rooms? true: false,
            path: this.path ? true: false,
        };
        this.getData();
    }
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
            @input="getWalletOwnersBuliding"
            :multiple="true"
            :options="wallets.map((type) => type.id)"
            :custom-label="
              (opt) => wallets.find((x) => x.id == opt) ?
                $i18n.locale == 'ar'
                  ? wallets.find((x) => x.id == opt).name
                  : wallets.find((x) => x.id == opt).name_e : null
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.owner") }}</label>
          <multiselect
            v-model="owner_id"
            :multiple="true"
            :options="owners.map((type) => type.id)"
            :custom-label="
              (opt) => {
                const owner = owners.find((x) => x.id == opt);
                if (owner) {
                  return $i18n.locale == 'ar' ? owner.name : owner.name_e;
                } else {
                  // Handle the case where no matching owner is found
                  return null;
                }
              }
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.building") }}</label>
          <multiselect
            v-model="building_id"
            :multiple="true"
            @input="showModelBuilding"
            :options="buildings.map((type) => type.id)"
            :custom-label="
              (opt) => {
                const building = buildings.find((x) => x.id == opt);
                if (building) {
                  return $i18n.locale == 'ar' ? building.name : building.name_e;
                } else {
                  // Handle the case where no matching building is found
                  return null;
                }
              }
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.country") }}</label>
          <multiselect
            @input="showCountryModel"
            :multiple="true"
            :disabled="isBuilding"
            v-model="country_id"
            :options="countries.map((type) => type.id)"
            :custom-label="
              (opt) => countries.find((x) => x.id == opt)?
                $i18n.locale == 'ar'
                  ? countries.find((x) => x.id == opt).name
                  : countries.find((x) => x.id == opt).name_e : null
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.governorate") }}</label>
          <multiselect
            @input="showGovernorateModel"
            v-model="governorate_id"
            :disabled="isBuilding"
            :multiple="true"
            :options="governorates.map((type) => type.id)"
            :custom-label="
              (opt) => governorates.find((x) => x.id == opt)?
                $i18n.locale == 'ar'
                  ? governorates.find((x) => x.id == opt).name
                  : governorates.find((x) => x.id == opt).name_e: null
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.city") }}</label>
          <multiselect
            v-model="city_id"
            :options="cities.map((type) => type.id)"
            :multiple="true"
            @input="showCityModel"
            :disabled="isBuilding"
            :custom-label="
              (opt) => cities.find((x) => x.id == opt)?
                $i18n.locale == 'ar'
                  ? cities.find((x) => x.id == opt).name
                  : cities.find((x) => x.id == opt).name_e :null
            "
          >
          </multiselect>
        </div>
      </div>
      <hr class="mx-2" />
      <div class="row px-2 mb-4">
        <div class="col-md-2">
          <label>{{ $t("general.property") }}</label>
          <multiselect
            v-model="property_id"
            :options="properties.map((type) => type.id)"
            :custom-label="
              (opt) => properties.find((x) => x.id == opt)?
                $i18n.locale == 'ar'
                  ? properties.find((x) => x.id == opt).name
                  : properties.find((x) => x.id == opt).name_e: null
            "
          >
          </multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.Unit_type") }}</label>
          <multiselect
            v-model="unit_ty_id"
            :options="unit_ty.map((type) => type.id)"
            :custom-label="
              (opt) =>
                $i18n.locale == 'ar'
                  ? unit_ty.find((x) => x.id == opt).name
                  : unit_ty.find((x) => x.id == opt).name_e
            "
          ></multiselect>
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.Unit_area") }}</label>
          <input class="form-control" type="text" v-model="unit_area" />
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.rooms") }}</label>
          <input class="form-control" type="text" v-model="rooms" />
        </div>
        <div class="col-md-2">
          <label>{{ $t("general.path") }}</label>
          <input class="form-control" type="text" v-model="path" />
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
      <!-- "Clear Filter" button -->
      <div class="text-center">
        <b-button variant="danger" @click="clearFilter">{{
          $t("general.clearFilter")
        }}</b-button>
      </div>
    </b-modal>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <!-- start search -->
            <div class="row justify-content-between align-items-center mb-2">
              <h4 class="header-title">
                {{ $t("general.UnsolidUnitReport") }}
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
                      <b-form-checkbox v-model="setting.unit" class="mb-1">
                        {{ getCompanyKey("unit") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.building" class="mb-1">
                        {{ getCompanyKey("building") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.unit_status"
                        class="mb-1"
                      >
                        {{ getCompanyKey("unit_status") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.owner" class="mb-1">
                        {{ getCompanyKey("owner") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.wallet" class="mb-1">
                        {{ getCompanyKey("wallet") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.unit_ty" class="mb-1">
                        {{ getCompanyKey("admin_report_unit_ty") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.unit_area" class="mb-1">
                        {{ getCompanyKey("admin_report_unit_area") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.rooms" class="mb-1">
                        {{ getCompanyKey("admin_report_rooms") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.path" class="mb-1">
                        {{ getCompanyKey("admin_report_path") }}
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
                    <th>
                          <div class="d-flex justify-content-center">
                              <span>{{ getCompanyKey("realEstate_unit_name_ar") }}</span>
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
                    <th v-if="setting.wallet">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("wallet") }}</span>
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
                    <th v-if="setting.country">
                          <div class="d-flex justify-content-center">
                              <span>{{ getCompanyKey("country") }}</span>
                          </div>
                      </th>
                    <th v-if="setting.governorate">
                          <div class="d-flex justify-content-center">
                              <span>{{ getCompanyKey("governorate") }}</span>
                          </div>
                      </th>
                    <th v-if="setting.city">
                          <div class="d-flex justify-content-center">
                              <span>{{ getCompanyKey("city") }}</span>
                          </div>
                      </th>
                    <th v-if="setting.unit_ty">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("admin_report_unit_ty") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('unit_ty'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-unit_ty'))"
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
                    <th v-if="setting.rooms">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("admin_report_rooms") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('rooms'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-rooms'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.path">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("admin_report_path") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="items.sort(sortString('path'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="items.sort(sortString('-path'))"
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
                    <td>
                          <template>
                              {{
                                      $i18n.locale == "ar"
                                      ? data.name
                                      : data.name_e

                              }}
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
                    <td v-if="setting.owner">
                      <template v-if="data.owner">
                        {{
                          data.owner
                            ? $i18n.locale == "ar"
                              ? data.owner.name
                              : data.owner.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.wallet">
                      <template v-if="data.wallet">
                        {{
                          data.wallet
                            ? $i18n.locale == "ar"
                              ? data.wallet.name
                              : data.wallet.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.country">
                          <template v-if="data.country">
                              {{
                                  data.country
                                      ? $i18n.locale == "ar"
                                      ? data.country.name
                                      : data.country.name_e
                                      : "-"
                              }}
                          </template>
                      </td>
                    <td v-if="setting.governorate">
                          <template v-if="data.governorate">
                              {{
                                  data.governorate
                                      ? $i18n.locale == "ar"
                                      ? data.governorate.name
                                      : data.governorate.name_e
                                      : ""
                              }}
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
                    <td v-if="setting.unit_ty">{{ data.unit_ty }}</td>
                    <td v-if="setting.unit_area">{{ data.unit_area }}</td>
                    <td v-if="setting.rooms">{{ data.rooms }}</td>
                    <td v-if="setting.path">{{ data.path }}</td>
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
