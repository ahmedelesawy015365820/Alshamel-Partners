<script>
import DatePicker from "vue2-datepicker";
import Status from "../../../components/create/general/status.vue";
import Sponsor from "../../../components/create/club/sponsor.vue";
import FinancialStatus from "../../../components/create/club/financialStatus.vue";
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
import permissionGuard from "../../../helper/permission";

import ErrorMessage from "../../../components/widgets/errorMessage";
import loader from "../../../components/general/loader";
import alphaArabic from "../../../helper/alphaArabic";
import alphaEnglish from "../../../helper/alphaEnglish";
import {
  dynamicSortString,
  dynamicSortNumber,
} from "../../../helper/tableSort";
import translation from "../../../helper/mixin/translation-mixin";
import Multiselect from "vue-multiselect";
import Branch from "../../../components/create/general/branch.vue";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Members",
    meta: [{ name: "description", content: "Members" }],
  },
  mixins: [translation],
  components: {
    FinancialStatus,
    Sponsor,
    Status,
    DatePicker,
    Layout,
    PageHeader,
    Switches,
    ErrorMessage,
    loader,
    Multiselect,
    Branch,
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Member", "all members club");
    });
  },
  data() {
    return {
      transactions: [],
      fields: [],
      per_page: 50,
      search: "",
      debounce: {},
      enabled3: true,
      membersPagination: {},
      members: [],
      isLoader: false,
      Tooltip: "",
      mouseEnter: "",
      statuses: [],
      sponsors: [],
      session_date: "",
      membership_date: "",
      birth_date: new Date(),
      applying_date: "",
      financialStatuses: [],
      memberTypes: [],
      edit: {
        gender: 1,
        phone_code: "",
        applying_date: this.formatDate(new Date()),
        applying_number: "",
        first_name: "",
        second_name: "",
        third_name: "",
        last_name: "",
        family_name: "",
          member_status_id: null,
        birth_date: this.formatDate(new Date()),
        national_id: "",
        nationality_number: "",
        home_phone: "",
        work_phone: "",
        home_address: "",
        work_address: "",
        membership_date: this.formatDate(new Date()),
        membership_number: "",
        job: "",
        degree: "",
        acceptance: "0",
        session_date: this.formatDate(new Date()),
        session_number: "",
        sponsor: "active",
        sponsor_id: null,
        member_type: "",
          member_kind_id: null,
        financial_status_id: null,
      },
      company_id: null,
      errors: {},
      isCheckAll: false,
      checkAll: [],
      branches: [],
      current_page: 1,
      codeCountry: "",
      setting: {
        gender: true,
        name: true,
          member_status_id: true,
        auto_status_sun: true,
        birth_date: true,
        national_id: true,
        nationality_number: true,
        home_phone: true,
        work_phone: true,
        home_address: true,
        work_address: true,
        job: true,
        degree: true,
        sponsor: true,
        sponsor_id: true,
        membership_number: true,
        applying_number: true,
        financial_status_id: true,
          PaymentDate: true,
          document_no: true,
          ForAYear: true,
      },
      is_disabled: false,
      fullName: '',
      filterSetting: [
        "full_name",
        "national_id",
        "membership_number",
      ],
      printLoading: true,
      printObj: {
        id: "printData",
      },
    };
  },
  validations: {
    edit: {
      first_name: {  },
      second_name: {  },
      gender: {  },
      third_name: {  },
      last_name: {  },
      family_name: {  },
        member_status_id: {  },
      birth_date: {  },
      membership_number: {  },
      national_id: {  },
      nationality_number: {  },
      home_phone: {  },
      work_phone: {  },
      home_address: {  },
      work_address: {  },
      job: {  },
      degree: {  },
      sponsor: {  },
      acceptance: {  },
      session_number: {  },
      session_date: {  },
      sponsor_id: {},
      member_type: {},
        member_kind_id: {  },
      financial_status_id: {  },
      applying_date: {  },
      applying_number: {  },
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
        this.members.forEach((el) => {
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
    resetModalTransation(id){
        let editTable = this.edit;
        this.fullName = `${editTable.first_name} ${editTable.second_name ? editTable.second_name : ''} ${editTable.third_name? editTable.third_name: ''} ${editTable.last_name? editTable.last_name: ''} ${editTable.family_name ? editTable.family_name : ''}`;
        this.isLoader = true;
        this.$bvModal.show(`modal-transaction`)
        adminApi
            .get(
                `/club-members/transactions/member-transaction/${id}`
            )
            .then((res) => {
                let l = res.data;
                console.log(l.data)
                this.transactions = l.data;
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
    resetModalHiddenTransation(){
        this.transactions = [];
        this.fullName = '';
        this.$bvModal.hide(`modal-transaction`);
    },
    v_dateCreate(e, name) {
      if (e) {
        this.create[name] = formatDateOnly(e);
      } else {
        this.create[name] = null;
      }
    },
    v_dateEdit(e, name) {
      if (e) {
        this.edit[name] = formatDateOnly(e);
      } else {
        this.edit[name] = null;
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
          .get(`/club-members/members/logs/${id}`)
          .then((res) => {
            let l = res.data.data;
            l.forEach((e) => {
              this.Tooltip += `Created By: ${e.causer_type}; Event: ${
                e.event
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
      let _filterSetting = [...this.filterSetting];
      let index = this.filterSetting.indexOf("sponsor_id");
      if (index > -1) {
        _filterSetting[index] =
          this.$i18n.locale == "ar" ? "sponsors.name" : "sponsors.name_e";
      }
      index = this.filterSetting.indexOf("member_status_id");
      if (index > -1) {
        _filterSetting[index] =
          this.$i18n.locale == "ar" ? "status.name" : "status.name_e";
      }
      let filter = "";
      for (let i = 0; i < _filterSetting.length; ++i) {
        filter += `columns[${i}]=${_filterSetting[i]}&`;
      }
      adminApi
        .get(
          `/club-members/members?page=${page}&per_page=${this.per_page}&company_id=${this.company_id}&search=${this.search}&${filter}`
        )
        .then((res) => {
          let l = res.data;
          this.members = l.data;
          this.membersPagination = l.pagination;
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
        this.current_page <= this.membersPagination.last_page &&
        this.current_page != this.membersPagination.current_page &&
        this.current_page
      ) {
        this.isLoader = true;
        let _filterSetting = [...this.filterSetting];
        let index = this.filterSetting.indexOf("sponsor_id");
        if (index > -1) {
          _filterSetting[index] =
            this.$i18n.locale == "ar" ? "sponsors.name" : "sponsors.name_e";
        }
        index = this.filterSetting.indexOf("member_status_id");
        if (index > -1) {
          _filterSetting[index] =
            this.$i18n.locale == "ar" ? "status.name" : "status.name_e";
        }
        let filter = "";
        for (let i = 0; i < _filterSetting.length; ++i) {
          filter += `columns[${i}]=${_filterSetting[i]}&`;
        }

        adminApi
          .get(
            `/club-members/members?page=${this.current_page}&per_page=${this.per_page}&search=${this.search}&${filter}&company_id=${this.company_id}`
          )
          .then((res) => {
            let l = res.data;
            this.members = l.data;
            this.membersPagination = l.pagination;
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
     *  edit countrie
     */
    /**
     *   show Modal (edit)
     */
     resetModalEdit(id) {
      let member = this.members.find((e) => id == e.id);
       this.getStatus();
       this.getSponsors();
       this.getFinancialStatus();
       this.getMemberTypes();
      this.birth_date = new Date(member.birth_date);
      this.session_date = member.session_date
        ? new Date(member.session_date)
        : "";
      this.membership_date = member.membership_date
        ? new Date(member.membership_date)
        : "";
      this.applying_date = member.applying_date
        ? new Date(member.applying_date)
        : "";
      this.edit.applying_number = member.applying_number;
      this.edit.membership_number = member.membership_number;
      this.edit.session_number = member.session_number;
      this.edit.first_name = member.first_name;
      this.edit.phone_code = member.phone_code;
      this.edit.second_name = member.second_name;
      this.edit.member_kind_id = member.member_kind_id ?? null;
      this.edit.financial_status_id = member.financial_status_id ?? null;
      this.edit.member_type = member.member_type;
      this.edit.third_name = member.third_name;
      this.edit.last_name = member.last_name;
      this.edit.family_name = member.family_name;
      this.edit.member_status_id = member.member_status_id ?? null;
      this.edit.birth_date = member.birth_date;
      this.edit.national_id = member.national_id;
      this.edit.nationality_number = member.nationality_number;
      this.edit.home_phone = member.home_phone;
      this.edit.work_phone = member.work_phone;
      this.edit.home_address = member.home_address;
      this.edit.work_address = member.work_address;
      this.edit.job = member.job;
      this.edit.degree = member.degree;
      this.edit.sponsor = member.sponsors;
      this.edit.sponsor_id = member.sponsor_id ?? null;
      this.edit.gender = member.gender;
      this.errors = {};
    },
    /**
     *  hidden Modal (edit)
     */
    resetModalHiddenEdit(id) {
      this.birth_date = "";
      this.session_date = "";
      this.applying_date = "";
      this.membership_date = "";
      this.errors = {};
      this.edit = {
        gender: 1,
        applying_date: this.formatDate(new Date()),
        applying_number: "",
        first_name: "",
        second_name: "",
        third_name: "",
        last_name: "",
        family_name: "",
          member_status_id: null,
        birth_date: this.formatDate(new Date()),
        national_id: "",
        nationality_number: "",
        home_phone: "",
        work_phone: "",
        home_address: "",
        work_address: "",
        job: "",
        degree: "",
        sponsor: "active",
        sponsor_id: null,
        member_type: "",
          member_kind_id: null,
        financial_status_id: null,
      };
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

     getStatus() {
      this.isLoader = true;

       adminApi
        .get(`/club-members/cm-status`)
        .then((res) => {
          let l = res.data.data;
          this.statuses = l;
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
     getSponsors() {
      this.isLoader = true;
       adminApi
        .get(`/club-members/sponsers`)
        .then((res) => {
          let l = res.data.data;
          this.sponsors = l;
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
    getMemberTypes() {
      this.isLoader = true;
      adminApi
        .get(`/club-members/members-types`)
        .then((res) => {
          this.memberTypes = res.data.data;
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
     getFinancialStatus() {
      this.isLoader = true;
       adminApi
        .get(`/club-members/financial-status`)
        .then((res) => {
          let l = res.data.data;
          this.financialStatuses = l;
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
            fn || ("Stores" + "." || "SheetJSTableExport.") + (type || "xlsx")
          );
        }
        this.enabled3 = true;
      }, 100);
    },
    arabicValue(txt) {
      this.create.name = arabicValue(txt);
      this.edit.name = arabicValue(txt);
    },
    englishValue(txt) {
      this.create.name_e = englishValue(txt);
      this.edit.name_e = englishValue(txt);
    },
    updatePhoneEdit(e) {
      this.edit.phone_code = e.countryCallingCode;
    },
      /**
       *  edit
       */
      editSubmit(id) {
          this.$v.edit.$touch();

          if (this.$v.edit.$invalid) {
              return;
          } else {
              this.isLoader = true;
              this.errors = {};
              adminApi
                  .put(`/club-members/members/${id}`, this.edit)
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
              <h4 class="header-title">{{ $t("general.membersTable") }}</h4>
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
                          value="membership_number"
                          class="mb-1"
                      >{{ getCompanyKey("member_membership_number") }}
                      </b-form-checkbox>
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="national_id"
                      class="mb-1"
                      >{{ getCompanyKey("member_national_id") }}
                    </b-form-checkbox>
                    <b-form-checkbox
                      v-model="filterSetting"
                      value="full_name"
                      class="mb-1"
                      >{{ $t("general.name") }}
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
              <div class="col-md-3 d-flex align-items-center mb-1 mb-xl-0">
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
                      :html="`${$t('general.setting')} <i class='fe-'></i>`"
                      ref="dropdown"
                      class="dropdown-custom-ali"
                    >
                      <b-form-checkbox v-model="setting.name" class="mb-1"
                        >{{ $t("name") }}
                      </b-form-checkbox>

                      <b-form-checkbox
                        v-model="setting.membership_number"
                        class="mb-1"
                        >{{ getCompanyKey("member_membership_number") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.financial_status_id"
                        class="mb-1"
                        >{{ getCompanyKey("financial_status") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.applying_number"
                        class="mb-1"
                        >{{ getCompanyKey("member_applying_number") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.member_status_id" class="mb-1"
                        >{{ getCompanyKey("status") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.birth_date" class="mb-1"
                        >{{ getCompanyKey("member_birth_date") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.gender" class="mb-1"
                        >{{ getCompanyKey("member_type") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.national_id"
                        class="mb-1"
                        >{{ getCompanyKey("member_national_id") }}
                      </b-form-checkbox>
                        <b-form-checkbox v-model="setting.PaymentDate" class="mb-1">{{ $t("general.PaymentDate") }}</b-form-checkbox>
                        <b-form-checkbox v-model="setting.document_no" class="mb-1">{{ $t("general.ReceiptNumber") }}</b-form-checkbox>
                        <b-form-checkbox v-model="setting.ForAYear" class="mb-1">{{ $t("general.ForAYear") }}</b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.nationality_number"
                        class="mb-1"
                        >{{ getCompanyKey("member_nationality_number") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.home_phone" class="mb-1"
                        >{{ getCompanyKey("member_home_phone") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.work_phone" class="mb-1"
                        >{{ getCompanyKey("member_work_phone") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.home_address"
                        class="mb-1"
                        >{{ getCompanyKey("member_home_address") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.work_address"
                        class="mb-1"
                        >{{ getCompanyKey("member_work_address") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.job" class="mb-1"
                        >{{ getCompanyKey("member_job") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.degree" class="mb-1"
                        >{{ getCompanyKey("member_degree") }}
                      </b-form-checkbox>
                      <b-form-checkbox v-model="setting.sponsor_id" class="mb-1"
                        >{{ getCompanyKey("sponsor") }}
                      </b-form-checkbox>
                      <b-form-checkbox
                        v-model="setting.auto_status_sun"
                        class="mb-1"
                        >{{ getCompanyKey("auto_status_sun") }}
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
                      {{ membersPagination.from }}-{{ membersPagination.to }} /
                      {{ membersPagination.total }}
                    </div>
                    <div class="d-inline-block">
                      <a
                        href="javascript:void(0)"
                        :style="{
                          'pointer-events':
                            membersPagination.current_page == 1 ? 'none' : '',
                        }"
                        @click.prevent="
                          getData(membersPagination.current_page - 1)
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
                            membersPagination.last_page ==
                            membersPagination.current_page
                              ? 'none'
                              : '',
                        }"
                        @click.prevent="
                          getData(membersPagination.current_page + 1)
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

                <!--  /start show transaction   -->
                <b-modal
                    dialog-class="modal-full-width"
                    :id="`modal-transaction`"
                    :title="$t('general.ReceiptVoucherMember') + fullName"
                    title-class="font-18"
                    body-class="p-4"
                    :hide-footer="true"
                    @hidden="resetModalHiddenTransation"
                >
                    <form>
                        <div class="mb-3 d-flex justify-content-end">
                            <b-button
                                variant="danger"
                                type="button"
                                @click.prevent="resetModalHiddenTransation"
                            >
                                {{ $t("general.Cancel") }}
                            </b-button>
                        </div>

                        <!--       start loader       -->
                        <loader size="large" v-if="isLoader" />
                        <!--       end loader       -->

                        <div class="table-responsive mb-3 custom-table-theme position-relative">
                            <table
                                class="table table-borderless table-hover table-centered m-0"
                            >
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>#</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{ $t('general.date') }}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{ $t('general.type') }}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{ $t('general.ForAYear') }}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{ $t('general.year_from') }}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{ $t('general.year_to') }}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{ $t('general.amount') }}</span>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="d-flex justify-content-center">
                                                <span>{{ $t('general.ReceiptNumber') }}</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody v-if="members.length > 0">
                                <tr
                                    v-for="(data, index) in transactions"
                                    :key="data.id"
                                    class="body-tr-custom"
                                >
                                    <td>
                                        {{ index + 1 }}
                                    </td>
                                    <td>
                                        {{ formatDate(data.date) }}
                                    </td>
                                    <td>
                                        {{ data.type  ? data.type == 'subscribe' ? $t('general.subscribe'):$t('general.renew'): '-' }}
                                    </td>
                                    <td>
                                        {{ data.year }}
                                    </td>
                                    <td>
                                        {{ data.date_from ? data.date_from: data.year_from }}
                                    </td>
                                    <td>
                                        {{ data.date_to ? data.date_to: data.year_to }}
                                    </td>
                                    <td>
                                        {{ data.amount }}
                                    </td>
                                    <td>
                                        {{ data.document_no }}
                                    </td>
<!--                                    <td v-if="data.prefix == 'old'">-->
<!--                                        {{ `${data.year_from}-${data.branch_id}-${data.document_id}-${data.prefix}-${data.serial_number}` }}-->
<!--                                    </td>-->
<!--                                    <td v-else>-->
<!--                                        {{data.prefix}}-->
<!--                                    </td>-->
                                </tr>

                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <th class="text-center" colspan="30">
                                        {{ $t("general.notDataFound") }}
                                    </th>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </b-modal>
                <!--  /edit show transaction   -->

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
                    <th v-if="setting.membership_number">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("member_membership_number")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('membership_number'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-membership_number'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.name">
                      <div class="d-flex justify-content-center">
                        <span>{{ $t("general.name") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.financial_status_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("financial_status") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.applying_number">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("member_applying_number")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.member_status_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("status") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-name_e'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <!-- <th v-if="setting.auto_status_parent">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("auto_status_parent") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-name_e'))"
                          ></i>
                        </div>
                      </div>
                    </th> -->
                    <th v-if="setting.auto_status_sun">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("auto_status_sun") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('name_e'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-name_e'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.national_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("member_national_id") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('national_id'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-national_id'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                      <th v-if="setting.PaymentDate">
                          <div class="d-flex justify-content-center">
                              <span>{{ $t("general.PaymentDate") }}</span>
                          </div>
                      </th>
                      <th v-if="setting.document_no">
                          <div class="d-flex justify-content-center">
                              <span>{{ $t("general.ReceiptNumber") }}</span>
                          </div>
                      </th>
                      <th v-if="setting.ForAYear">
                          <div class="d-flex justify-content-center">
                              <span>{{ $t("general.ForAYear") }}</span>
                          </div>
                      </th>
                    <th v-if="setting.birth_date">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("member_birth_date") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('birth_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-birth_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.gender">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("member_gender") }}</span>
                      </div>
                    </th>
                    <th v-if="setting.nationality_number">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("member_nationality_number")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="
                              members.sort(sortString('nationality_number'))
                            "
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              members.sort(sortString('-nationality_number'))
                            "
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.home_phone">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("member_home_phone") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('home_phone'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-home_phone'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.work_phone">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("member_work_phone") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('work_phone'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-work_phone'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.home_address">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("member_home_address") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('home_address'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-home_address'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.work_address">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("member_work_address") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('work_address'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-work_address'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.membership_date">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("member_membership_date")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('membership_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="
                              members.sort(sortString('-membership_date'))
                            "
                          ></i>
                        </div>
                      </div>
                    </th>

                    <th v-if="setting.job">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("member_job") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('job'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-job'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.degree">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("member_degree") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('degree'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-degree'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.acceptance">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("member_acceptance") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('acceptance'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-acceptance'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.session_date">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("member_session_date") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('session_date'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-session_date'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.session_number">
                      <div class="d-flex justify-content-center">
                        <span>{{
                          getCompanyKey("member_session_number")
                        }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('session_number'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-session_number'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="setting.sponsor_id">
                      <div class="d-flex justify-content-center">
                        <span>{{ getCompanyKey("sponsor") }}</span>
                        <div class="arrow-sort">
                          <i
                            class="fas fa-arrow-up"
                            @click="members.sort(sortString('name'))"
                          ></i>
                          <i
                            class="fas fa-arrow-down"
                            @click="members.sort(sortString('-name'))"
                          ></i>
                        </div>
                      </div>
                    </th>
                    <th v-if="enabled3" class="do-not-print">
                      <i class="fas fa-ellipsis-v"></i>
                    </th>
                  </tr>
                </thead>
                <tbody v-if="members.length > 0">
                  <tr
                    @click.capture="checkRow(data.id)"
                    @dblclick.prevent="$bvModal.show(`modal-edit-${data.id}`)"
                    v-for="(data, index) in members"
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
                    <td v-if="setting.membership_number">
                      {{ data.membership_number }}
                    </td>
                    <td v-if="setting.name">
                      {{
                        `${data.first_name} ${data.second_name} ${data.third_name} ${data.last_name} ${data.family_name}`
                      }}
                    </td>
                    <td v-if="setting.financial_status_id">
                      <template v-if="data.financial_status">
                        {{
                          data.financial_status
                            ? $i18n.locale == "ar"
                              ? data.financial_status.name
                              : data.financial_status.name_e
                            : ""
                        }}
                      </template>
                    </td>
                    <td v-if="setting.applying_number">
                      {{ data.applying_number }}
                    </td>
                    <td v-if="setting.member_status_id">
                      {{
                        data.status
                          ? $i18n.locale == "ar"
                            ? data.status.name
                            : data.status.name_e
                          : "-"
                      }}
                    </td>
                    <td v-if="setting.auto_status_sun">
                      {{
                        data.membersType
                          ? $i18n.locale == "ar"
                            ? data.membersType.name
                            : data.membersType.name_e
                          : "-"
                      }}
                    </td>
                    <td v-if="setting.national_id">
                      {{ data.national_id }}
                    </td>
                      <td v-if="setting.PaymentDate">
                          {{data.transaction ? formatDate(data.transaction.date) : '---' }}
                      </td>
                      <td v-if="setting.document_no">
                          {{ data.transaction ? data.transaction.document_no : '---' }}
                      </td>
                      <td v-if="setting.ForAYear">
                          {{ data.transaction ? data.transaction.year : '---' }}
                      </td>
                    <td v-if="setting.birth_date">
                      {{ data.birth_date }}
                    </td>
                    <td v-if="setting.gender">
                      {{
                        data.gender  ?data.gender == 1
                          ? $t("general.male")
                          : $t("general.female"): null
                      }}
                    </td>
                    <td v-if="setting.nationality_number">
                      {{ data.nationality_number }}
                    </td>
                    <td v-if="setting.home_phone">
                      {{ data.home_phone }}
                    </td>
                    <td v-if="setting.work_phone">
                      {{ data.work_phone }}
                    </td>
                    <td v-if="setting.home_address">
                      {{ data.home_address }}
                    </td>
                    <td v-if="setting.work_address">
                      {{ data.work_address }}
                    </td>
                    <td v-if="setting.membership_date">
                      {{ data.membership_date }}
                    </td>
                    <td v-if="setting.job">
                      {{ data.job }}
                    </td>
                    <td v-if="setting.degree">
                      {{ data.degree }}
                    </td>
                    <td v-if="setting.acceptance">
                      <span class="text-success">
                        <template v-if="data.acceptance == '0'">
                          {{ $t("general.pending") }}
                        </template>
                        <template v-else-if="data.acceptance == '1'">
                          {{ $t("general.accepted") }}
                        </template>
                        <template v-else="data.acceptance == '2'">
                          {{ $t("general.declined") }}
                        </template>
                      </span>
                    </td>
                    <td v-if="setting.session_date">
                      {{ data.session_date }}
                    </td>
                    <td v-if="setting.session_number">
                      {{ data.session_number }}
                    </td>
                    <td v-if="setting.sponsor_id">
                      {{
                        data.sponsors
                          ? $i18n.locale == "ar"
                            ? data.sponsors.name
                            : data.sponsors.name_e
                          : "-"
                      }}
                    </td>
                    <b-modal
                      dialog-class="modal-full-width"
                      :id="`modal-edit-${data.id}`"
                      :title="getCompanyKey('member_edit_form')"
                      title-class="font-18"
                      body-class="p-4"
                      :ref="`edit-${data.id}`"
                      :hide-footer="true"
                      @show="resetModalEdit(data.id)"
                      @hidden="resetModalHiddenEdit(data.id)"
                    >
                      <form>
                        <div class="mb-3 d-flex justify-content-end">
                          <b-button
                                variant="info"
                                type="button"
                                @click.prevent="resetModalTransation(data.id)"
                            >
                                {{ $t("general.ReceiptVoucher") }}
                            </b-button>
                            <b-button
                                variant="success"
                                type="submit"
                                class="mx-1"
                                v-if="!isLoader"
                                @click.prevent="editSubmit(data.id)"
                            >
                                {{ $t("general.Edit") }}
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
                            class="mx-1 d-flex justify-content-end"
                            variant="danger"
                            type="button"
                            @click.prevent="
                              $bvModal.hide(`modal-edit-${data.id}`)
                            "
                          >
                            {{ $t("general.Cancel") }}
                          </b-button>
                        </div>
                        <div class="row">
                          <div class="col-md-3 position-relative">
                            <div class="form-group">
                              <label class="control-label">
                                {{ getCompanyKey("apply_membership_date") }}
                              </label>
                              <date-picker
                                disabled=""
                                type="date"
                                v-model="applying_date"
                                defaultValue
                                confirm
                              ></date-picker>
                              <template v-if="errors.applying_date">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.applying_date"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("apply_membership_number")
                              }}</label>
                              <input
                                disabled
                                v-model="$v.edit.applying_number.$model"
                                class="form-control"
                                type="text"
                              />
                              <template v-if="errors.applying_number">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.applying_number"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                        </div>
                        <hr
                          style="
                            margin: 10px 0 !important;
                            border-top: 1px solid rgb(141 163 159 / 42%);
                          "
                        />
                        <div class="row">
                          <div class="col-md-2">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("member_first_name")
                              }}</label>
                              <input
                                v-model="$v.edit.first_name.$model"
                                disabled
                                class="form-control"
                                type="text"
                              />
                              <template v-if="errors.first_name">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.first_name"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("member_second_name")
                              }}</label>
                              <input
                                v-model="$v.edit.second_name.$model"
                                disabled
                                class="form-control"
                                type="text"
                              />
                              <template v-if="errors.second_name">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.second_name"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("member_third_name")
                              }}</label>
                              <input
                                v-model="$v.edit.third_name.$model"
                                disabled
                                class="form-control"
                                type="text"
                              />
                              <template v-if="errors.third_name">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.third_name"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("member_last_name")
                              }}</label>
                              <input
                                v-model="$v.edit.last_name.$model"
                                disabled
                                class="form-control"
                                type="text"
                              />
                              <template v-if="errors.last_name">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.last_name"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("member_family_name")
                              }}</label>
                              <input
                                v-model="$v.edit.family_name.$model"
                                disabled
                                class="form-control"
                                type="text"
                              />
                              <template v-if="errors.family_name">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.family_name"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                        </div>
                        <hr
                          style="
                            margin: 10px 0 !important;
                            border-top: 1px solid rgb(141 163 159 / 42%);
                          "
                        />
                        <div class="row">
                          <div class="col-md-3 position-relative">
                            <div class="form-group">
                              <label class="control-label">
                                {{ getCompanyKey("member_birth_date") }}
                              </label>
                              <date-picker
                                disabled
                                type="date"
                                v-model="birth_date"
                                defaultValue
                                @change="v_dateEdit($event, 'birth_date')"
                                confirm
                              ></date-picker>
                              <template v-if="errors.date">
                                <ErrorMessage
                                  v-for="(errorMessage, index) in errors.date"
                                  :key="index"
                                >
                                  {{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label class="mr-2">
                                {{ getCompanyKey("member_gender") }}
                              </label>
                              <b-form-group>
                                <b-form-radio
                                  class="d-inline-block"
                                  disabled
                                  v-model="$v.edit.gender.$model"
                                  name="create_gender"
                                  value="1"
                                  >{{ $t("general.male") }}
                                </b-form-radio>
                                <b-form-radio
                                  class="d-inline-block m-1"
                                  disabled
                                  v-model="$v.edit.gender.$model"
                                  name="create_gender"
                                  value="0"
                                  >{{ $t("general.female") }}
                                </b-form-radio>
                              </b-form-group>
                              <template v-if="errors.gender">
                                <ErrorMessage
                                  v-for="(errorMessage, index) in errors.gender"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("member_national_id")
                              }}</label>
                              <input
                                v-model="$v.edit.national_id.$model"
                                disabled
                                class="form-control"
                                type="text"
                              />
                              <template v-if="errors.national_id">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.national_id"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("member_nationality_number")
                              }}</label>
                              <input
                                v-model="$v.edit.nationality_number.$model"
                                disabled
                                class="form-control"
                                type="text"
                              />
                              <template v-if="errors.nationality_number">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.nationality_number"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("member_home_phone")
                              }}</label>
                              <VuePhoneNumberInput
                                v-model="$v.edit.home_phone.$model"
                                disabled
                                :default-country-code="edit.phone_code"
                                valid-color="#28a745"
                                error-color="#dc3545"
                                :preferred-countries="['FR', 'EG', 'DE']"
                              />
                              <template v-if="errors.home_phone">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.home_phone"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("member_work_phone")
                              }}</label>
                              <VuePhoneNumberInput
                                v-model="$v.edit.work_phone.$model"
                                disabled
                                :default-country-code="edit.phone_code"
                                valid-color="#28a745"
                                error-color="#dc3545"
                                :preferred-countries="['FR', 'EG', 'DE']"
                              />
                              <template v-if="errors.work_phone">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.work_phone"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("member_home_address")
                              }}</label>
                              <input
                                v-model="$v.edit.home_address.$model"
                                disabled
                                class="form-control"
                                type="text"
                              />
                              <template v-if="errors.home_address">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.home_address"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("member_work_address")
                              }}</label>
                              <input
                                v-model="$v.edit.work_address.$model"
                                disabled
                                class="form-control"
                                type="text"
                              />
                              <template v-if="errors.work_address">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.work_address"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>{{ getCompanyKey("member_job") }}</label>
                              <input
                                v-model="$v.edit.job.$model"
                                class="form-control"
                                disabled
                                type="text"
                              />
                              <template v-if="errors.job">
                                <ErrorMessage
                                  v-for="(errorMessage, index) in errors.job"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("member_degree")
                              }}</label>
                              <input
                                v-model="$v.edit.degree.$model"
                                class="form-control"
                                disabled
                                type="text"
                              />
                              <template v-if="errors.degree">
                                <ErrorMessage
                                  v-for="(errorMessage, index) in errors.degree"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                        </div>
                        <hr
                          style="
                            margin: 10px 0 !important;
                            border-top: 1px solid rgb(141 163 159 / 42%);
                          "
                        />
                        <div class="row">
                          <div class="col-md-3">
                            <div class="form-group position-relative">
                              <label class="control-label">
                                {{ getCompanyKey("status") }}
                              </label>
                              <multiselect
                                v-model="edit.member_status_id"
                                :options="statuses.map((type) => type.id)"
                                :custom-label="
                                  (opt) => statuses.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar'
                                      ? statuses.find((x) => x.id == opt).name
                                      : statuses.find((x) => x.id == opt).name_e: null
                                "
                              >
                              </multiselect>
                              <div
                                v-if="
                                  $v.edit.member_status_id.$error || errors.member_status_id
                                "
                                class="text-danger"
                              >
                                {{ $t("general.fieldIsRequired") }}
                              </div>
                              <template v-if="errors.member_status_id">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.member_status_id"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
<!--                          <div class="col-md-3">-->
<!--                            <div class="form-group">-->
<!--                              <label>{{ getCompanyKey("member_type") }}</label>-->
<!--                              <input-->
<!--                                v-model="$v.edit.member_type.$model"-->
<!--                                class="form-control"-->
<!--                                type="text"-->
<!--                              />-->
<!--                              <template v-if="errors.member_type">-->
<!--                                <ErrorMessage-->
<!--                                  v-for="(-->
<!--                                    errorMessage, index-->
<!--                                  ) in errors.member_type"-->
<!--                                  :key="index"-->
<!--                                  >{{ errorMessage }}-->
<!--                                </ErrorMessage>-->
<!--                              </template>-->
<!--                            </div>-->
<!--                          </div>-->
                          <div class="col-md-3">
                            <div class="form-group position-relative">
                              <label class="control-label">
                                {{ getCompanyKey("member_type_id") }}
                              </label>
                              <multiselect
                                v-model="edit.member_kind_id"
                                :options="memberTypes.map((type) => type.id)"
                                :custom-label="
                                  (opt) => memberTypes.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar'
                                      ? memberTypes.find((x) => x.id == opt)
                                          .name
                                      : memberTypes.find((x) => x.id == opt)
                                          .name_e: null
                                "
                              >
                              </multiselect>
                              <div
                                v-if="
                                  $v.edit.member_kind_id.$error ||
                                  errors.member_kind_id
                                "
                                class="text-danger"
                              >
                                {{ $t("general.fieldIsRequired") }}
                              </div>
                              <template v-if="errors.member_kind_id">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.member_kind_id"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group position-relative">
                              <label class="control-label">
                                {{ getCompanyKey("financial_status") }}
                              </label>
                              <multiselect
                                  :disabled="true"
                                v-model="edit.financial_status_id"
                                :options="
                                  financialStatuses.map((type) => type.id)
                                "
                                :custom-label="
                                  (opt) => financialStatuses.find(
                                          (x) => x.id == opt
                                        )?
                                    $i18n.locale == 'ar'
                                      ? financialStatuses.find(
                                          (x) => x.id == opt
                                        ).name
                                      : financialStatuses.find(
                                          (x) => x.id == opt
                                        ).name_e: null
                                "
                              >
                              </multiselect>
                              <div
                                v-if="
                                  $v.edit.financial_status_id.$error ||
                                  errors.financial_status_id
                                "
                                class="text-danger"
                              >
                                {{ $t("general.fieldIsRequired") }}
                              </div>
                              <template v-if="errors.financial_status_id">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.financial_status_id"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3 position-relative">
                            <div class="form-group">
                              <label class="control-label">
                                {{ getCompanyKey("member_membership_date") }}
                              </label>
                              <date-picker
                                type="date"
                                v-model="membership_date"
                                disabled
                                defaultValue
                                @change="v_dateEdit($event, 'membership_date')"
                                confirm
                              ></date-picker>
                              <template v-if="errors.date">
                                <ErrorMessage
                                  v-for="(errorMessage, index) in errors.date"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("member_membership_number")
                              }}</label>
                              <input
                                v-model="$v.edit.membership_number.$model"
                                disabled
                                class="form-control"
                                type="text"
                              />
                              <template v-if="errors.membership_number">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.membership_number"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
<!--                          <div class="col-md-3">-->
<!--                            <div class="form-group">-->
<!--                              <label class="mr-2">-->
<!--                                {{ getCompanyKey("member_acceptance") }}-->
<!--                              </label>-->
<!--                              <b-form-group>-->
<!--                                <b-form-radio-->
<!--                                    disabled-->
<!--                                  class="d-inline-block"-->
<!--                                  v-model="$v.edit.acceptance.$model"-->
<!--                                  name="edit_acceptance"-->
<!--                                  value="0"-->
<!--                                  >-->
<!--                                    {{ $t("general.pending") }}-->
<!--                                </b-form-radio>-->
<!--                                <b-form-radio-->
<!--                                    disabled-->
<!--                                  class="d-inline-block m-1"-->
<!--                                  v-model="$v.edit.acceptance.$model"-->
<!--                                  name="edit_acceptance"-->
<!--                                  value="1"-->
<!--                                  >-->
<!--                                    {{ $t("general.accepted") }}-->
<!--                                </b-form-radio>-->
<!--                                <b-form-radio-->
<!--                                    disabled-->
<!--                                  class="d-inline-block m-1"-->
<!--                                  v-model="$v.edit.acceptance.$model"-->
<!--                                  name="edit_acceptance"-->
<!--                                  value="2"-->
<!--                                  >-->
<!--                                    {{ $t("general.declined") }}-->
<!--                                </b-form-radio>-->
<!--                              </b-form-group>-->
<!--                              <template v-if="errors.acceptance">-->
<!--                                <ErrorMessage-->
<!--                                  v-for="(-->
<!--                                    errorMessage, index-->
<!--                                  ) in errors.acceptance"-->
<!--                                  :key="index"-->
<!--                                  >{{ errorMessage }}-->
<!--                                </ErrorMessage>-->
<!--                              </template>-->
<!--                            </div>-->
<!--                          </div>-->
                          <div class="col-md-3 position-relative">
                            <div class="form-group">
                              <label class="control-label">
                                {{ getCompanyKey("member_session_date") }}
                              </label>
                              <date-picker
                                disabled
                                type="date"
                                v-model="session_date"
                                defaultValue
                                @change="v_dateEdit($event, 'session_date')"
                                confirm
                              ></date-picker>
                              <template v-if="errors.date">
                                <ErrorMessage
                                  v-for="(errorMessage, index) in errors.date"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <label>{{
                                getCompanyKey("member_session_number")
                              }}</label>
                              <input
                                disabled
                                v-model="$v.edit.session_number.$model"
                                class="form-control"
                                type="text"
                              />
                              <template v-if="errors.session_number">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.session_number"
                                  :key="index"
                                  >{{ errorMessage }}
                                </ErrorMessage>
                              </template>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group position-relative">
                              <label class="control-label">
                                {{ getCompanyKey("sponsor") }}
                              </label>
                              <multiselect
                                v-model="edit.sponsor_id"
                                :options="sponsors.map((type) => type.id)"
                                :custom-label=" (opt) => sponsors.find((x) => x.id == opt)?
                                  $i18n.locale == 'ar'
                                    ? sponsors.find((x) => x.id == opt).name
                                    : sponsors.find((x) => x.id == opt).name_e : null
                                "
                              >
                              </multiselect>
                              <div
                                v-if="
                                  $v.edit.sponsor_id.$error || errors.sponsor_id
                                "
                                class="text-danger"
                              >
                                {{ $t("general.fieldIsRequired") }}
                              </div>
                              <template v-if="errors.sponsor_id">
                                <ErrorMessage
                                  v-for="(
                                    errorMessage, index
                                  ) in errors.sponsor_id"
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
                    <td v-if="enabled3" class="do-not-print">
                      <button
                        @mouseover="log(data.id)"
                        @mousemove="log(data.id)"
                        type="button"
                        class="btn"
                        data-toggle="tooltip"
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
                    <th class="text-center" colspan="30">
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
@media print {
  .do-not-print {
    display: none;
  }

  .arrow-sort {
    display: none;
  }

  .text-success {
    background-color: unset;
    color: #6c757d !important;
    border: unset;
  }

  .text-danger {
    background-color: unset;
    color: #6c757d !important;
    border: unset;
  }
}
thead th {
  white-space: nowrap !important;
}
table td {
  white-space: nowrap !important;
}
.custom-radio
  .custom-control-input:disabled:checked
  ~ .custom-control-label::before {
  background-color: #2494be;
}
</style>
