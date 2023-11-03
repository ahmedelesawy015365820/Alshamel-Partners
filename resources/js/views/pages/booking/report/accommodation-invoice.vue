<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import loader from "../../../../components/general/loader";
import permissionGuard from "../../../../helper/permission";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import translation from "../../../../helper/mixin/translation-mixin";
import customTable from "../../../../helper/mixin/customTable";
import successError from "../../../../helper/mixin/success&error";
import crudHelper from "../../../../helper/mixin/crudHelper";
import adminApi from "../../../../api/adminAxios";
import Swal from "sweetalert2";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";

/**
 * Advanced Table component
 */
export default {
  page: {title: "accommodation invoice report", meta: [{ name: "accommodation invoice report", content: "accommodation invoice report" }],},
  mixins: [translation,customTable,successError,crudHelper],
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "accommodation invoice report", "all accommodation invoice report");
    });
  },
  components: {
      Layout, PageHeader, loader,
      searchPage,actionSetting, DatePicker,
      Multiselect, tableCustom
  },
  data() {
    return {
        customers:[],
      url: `/document-header-details/get-booking-Reports`,
      searchMain: '',
        is_disabled:false,
      tableSetting: [
          {
              isFilter: false,isSet: true,trans:"accommodation_report_serial_number", isV: 'prefix',
              type: 'relation',name: 'document_header',sort: false,col1: 'prefix',col2: 'prefix',
              setting: {"prefix":true},isSetting: true
          },
          {
              isFilter: false,isSet: true,trans:"accommodation_report_customer", isV: 'customer_id'
              ,type: 'relation1', name: 'document_header', name1: 'customer',sort: false,col1: 'name',col2: 'name_e',
              setting: {"customer_id":true},isSetting: true
          },
          {
              isFilter: false,isSet: true,trans:"accommodation_report_date",isV: 'date_from',
              type: 'date',sort: true,setting: {"date_from":true},isSetting: true
          },
          {
              isFilter: false,isSet: true,trans:"accommodation_report_room",isV: 'unit_id',
              type: 'relation', name:'unit',sort: false,col1: 'name',col2: 'name_e',
              setting: {"unit_id":true},isSetting: true
          },
          {
              isFilter: false,isSet: true,trans:"accommodation_report_price_per_day",isV: 'price_per_uint',
              type: 'string',setting: {"price_per_uint":true},isSetting: true
          },
          {
              isFilter: false,isSet: true,trans:"accommodation_report_total",isV: 'total',
              type: 'string',setting: {"total":true},isSetting: true
          },
          {
              isFilter: false,isSet: true,trans:"accommodation_report_discount",isV: 'discount',
              type: 'string',setting: {"discount":true},isSetting: true
          },
      ],
      sendSetting: {},
        create: {
            start_date: this.formatDate(new Date()),
            end_date: this.formatDate(new Date()),
            customer_id: null,
        },
      searchField: []
    };
  },
  mounted() {
    this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
    this.settingFun();
    this.filterDataApi();
  },
  methods: {
    settingFun(setting = null){
        if(setting) this.tableSetting = setting;
        let l = {},names = [];
        names = this.tableSetting.filter(e => e.isSet ).map(el => el.setting);
        this.tableSetting.forEach((e,i) => {
            l[e.isV] = names[i][e.isV];
            e['isSetting'] = l[e.isV];
        });
        this.sendSetting = l;
    },
       getCustomers() {
          this.isLoader = true;
           adminApi.get(`/general-customer`)
              .then((res) => {
                  let l = res.data.data;
                  this.customers = l;
                  this.isLoader = false;
              })
              .catch((err) => {
                  Swal.fire({
                      icon: "error",
                      title: `${this.$t("general.Error")}`,
                      text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                  });
              });
      },
       resetModal() {
        this.getCustomers();
        this.is_disabled = false;
      },
      resetModalHidden() {
          this.is_disabled = false;
          this.$bvModal.hide(`filter`);
      },
      filterDataApi()
      {
          this.getData(1,this.url+`?document_id=35&date_from=${this.create.start_date}&date_to=${this.create.end_date}&customer_id=${this.create.customer_id ? this.create.customer_id:''}&`,'');
      }
  }
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
            <searchPage
                page="general.DailyAccommodationInvoiceReport"
                :isVisible="isVisible"
                :filterSetting="tableSetting"
                :companyKeys="companyKeys"
                :defaultsKeys="defaultsKeys"
                @dataSearch="() => getData(1,url,'')"
                @searchFun="(fields) => searchField = fields"
                @editSearch="(search) => searchMain = search"
                :isSearch="false"
            />
            <!-- end search -->

            <!-- start setting -->
            <actionSetting
                :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :current_page="current_page"
                :isCreate="false" :isEdit="false" :isDelete="false"
                :isExcl="true"
                :isPrint="true" :checkAll="checkAll" :sideAction="true" :sidePaginate="true"
                :isFilter="true" :group="true" :isGroup="true" :isVisible="isVisible"
                :isSetting="true" :isPaginate="true" :settings="tableSetting"
                @gen_xsl="ExportExcel('xlsx')"
                @settingFun="setting => settingFun(setting)"
                :objPagination="objPagination"
                @perviousOrNext="(current) => getData(current,url,'')"
                @currentPage="(page) => current_page = page"
                @DataCurrentPage="(page) => getDataCurrentPage(page)"
                @actionChange="({typeAction,id}) => changeType({typeAction,id})"
            />
            <!-- end setting -->

            <!--  create or edit   -->
              <b-modal
                  id="filter"
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
                                  type="button" class="mx-1"
                                  v-if="!isLoader"
                                  @click.prevent="filterDataApi"
                              >
                                  {{ $t('general.Search') }}
                              </b-button>

                              <b-button variant="success" class="mx-1" disabled v-else>
                                  <b-spinner small></b-spinner>
                                  <span class="sr-only">{{ $t('login.Loading') }}...</span>
                              </b-button>
                          </template>
                          <!-- Emulate built in modal footer ok and cancel button actions -->

                          <b-button variant="danger" type="button" @click.prevent="resetModalHidden">
                              {{ $t('general.Cancel') }}
                          </b-button>
                      </div>
                      <div class="row">
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label class="control-label">
                                      {{ $t('general.fromDate') }}
                                      <span class="text-danger">*</span>
                                  </label>
                                  <date-picker
                                      type="date"
                                      v-model="create.start_date"
                                      format="YYYY-MM-DD"
                                      valueType="format"
                                      :confirm="false"
                                  ></date-picker>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group">
                                  <label class="control-label">
                                      {{ $t('general.toDate') }}
                                      <span class="text-danger">*</span>
                                  </label>
                                  <date-picker
                                      type="date"
                                      v-model="create.end_date"
                                      format="YYYY-MM-DD"
                                      valueType="format"
                                      :confirm="false"
                                  ></date-picker>
                              </div>
                          </div>
                          <div class="col-md-6">
                              <div class="form-group position-relative">
                                  <label
                                      class="control-label">{{ getCompanyKey("customer") }}
                                  </label>
                                  <multiselect
                                      v-model="create.customer_id"
                                      :options="customers.map((type) => type.id)"
                                      :custom-label="(opt) => $i18n.locale == 'ar' ?customers.find((x) => x.id == opt).name:customers.find((x) => x.id == opt).name_e"
                                  >
                                  </multiselect>
                              </div>
                          </div>
                      </div>
                  </form>
              </b-modal>
            <!--  /create   -->

            <!-- start .table-responsive-->
            <div
              class="table-responsive mb-3 custom-table-theme position-relative"
              ref="exportable_table" id="printCustom"
            >
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

              <tableCustom
                  :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                  :tables="tables" :isEdit="false" :isDelete="false"
                  :isVisible="isVisible" :tableSetting="tableSetting"
                  :enabled3="enabled3" :Tooltip="Tooltip"
                  :isInputCheck="false" :isLog="false" :isAction="false"
              />

            </div>
            <!-- end .table-responsive-->
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
