<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import loader from "../../../../components/general/loader";
import permissionGuard from "../../../../helper/permission";
import translation from "../../../../helper/mixin/translation-mixin";
import customTable from "../../../../helper/mixin/customTable";
import successError from "../../../../helper/mixin/success&error";
import crudHelper from "../../../../helper/mixin/crudHelper";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import CustomerGeneral from "../../../../components/create/general/customerGeneral.vue";
/**
 * Advanced Table component
 */

export default {
  page: {
    title: "General Customers",
    meta: [{ name: "description", content: "Customers" }],
  },
  mixins: [translation,customTable,successError,crudHelper],
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Customer", "all Customer");
    });
  },
  components: {
       Layout, PageHeader, CustomerGeneral,loader,
       searchPage,actionSetting, tableCustom
  },
  data() {
    return {
        url: '/general-customer',
        searchMain: '',
        tableSetting: [
            {
                isFilter: true,isSet: true,trans:"general_customer_name_ar",isV: 'name',
                type: 'string',sort: true,setting: {"name":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"general_customer_name_en",isV: 'name_e',
                type: 'string',sort: true,setting: {"name_e":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"general_customer_phone",isV: 'phone',
                type: 'string',sort: true,setting: {"phone":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"general_customer_email",isV: 'email',
                type: 'string',sort: true,setting: {"email":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"general_customer_code",isV: 'rp_code',
                type: 'string',sort: true,setting: {"rp_code":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"general_customer_contact_person",isV: 'contact_person',
                type: 'string',sort: true,setting: {"contact_person":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"general_customer_contact_phone",isV: 'contact_phone',
                type: 'string',sort: true,setting: {"contact_phone":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"general_customer_nationality",isV: 'nationality',
                type: 'string',sort: true,setting: {"nationality":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"general_customer_national_id",isV: 'national_id',
                type: 'string',sort: true,setting: {"national_id":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"general_customer_country", isV: 'country_id',
                type: 'relation',name: 'country',sort: false,col1: 'name',col2: 'name_e',
                setting: {"country_id":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"general_customer_city",isV: 'city_id',
                type: 'relation', name:'city',sort: false,col1: 'name',col2: 'name_e',
                setting: {"city_id":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"general_customer_city",isV: 'bank_account_id',
                type: 'relation', name:'bankAccount',sort: false,col1: 'name',col2: 'name_e',
                setting: {"bank_account_id":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"customer_employee",isV: 'employee_id',
                type: 'relation', name:'employee',sort: false,col1: 'name',col2: 'name_e',
                setting: {"employee_id":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"customer_main_category",isV: 'customer_main_category_id',
                type: 'relation', name:'customer_main_category',sort: false,col1: 'name',col2: 'name_e',
                setting: {"customer_main_category_id":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"customer_sub_category",isV: 'customer_sub_category_id',
                type: 'relation', name:'customer_sub_category',sort: false,col1: 'name',col2: 'name_e',
                setting: {"customer_sub_category_id":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"customer_sector",isV: 'sector_id',
                type: 'relation', name:'sector',sort: false,col1: 'name',col2: 'name_e',
                setting: {"sector_id":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"customer_source",isV: 'customer_source_id',
                type: 'relation', name:'customerSource',sort: false,col1: 'name',col2: 'name_e',
                setting: {"customer_source_id":true},isSetting: true
            },
        ],
        sendSetting: {},
        searchField: [],
    };
  },
  mounted() {
      this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
      this.settingFun();
      this.getCustomTableFields('general_customers');
      this.getData(1,this.url,this.filterSearch(this.searchField));
  },
  methods: {
      filterSearch(fields){
          let indexC = fields.indexOf("country_id"),
              indexCty = fields.indexOf("city_id"),
              indexBank= fields.indexOf("bank_account_id"),
              indexEmp = fields.indexOf("employee_id"),
              indexMain = fields.indexOf("customer_main_category_id"),
              indexSub = fields.indexOf("customer_sub_category_id"),
              indexSector = fields.indexOf("sector_id"),
              indexSource = fields.indexOf("customer_source_id"),
              filter = '';
          if (indexC > -1) {
              fields[indexC] = this.$i18n.locale == "ar" ? "country.name" : "country.name_e";
          }
          if (indexCty > -1) {
              fields[indexCty] = this.$i18n.locale == "ar" ? "city.name" : "city.name_e";
          }
          if (indexBank > -1) {
              fields[indexBank] = this.$i18n.locale == "ar" ? "bankAccount.account_number" : "bankAccount.account_number";
          }
          if (indexEmp > -1) {
              fields[indexEmp] = this.$i18n.locale == "ar" ? "employee.name" : "employee.name_e";
          }
          if (indexMain > -1) {
              fields[indexMain] = this.$i18n.locale == "ar" ? "customer_main_category.name" : "customer_main_category.name_e";
          }
          if (indexSub > -1) {
              fields[indexSub] = this.$i18n.locale == "ar" ? "customer_sub_category.name" : "customer_sub_category.name_e";
          }
          if (indexSector > -1) {
              fields[indexSector] = this.$i18n.locale == "ar" ? "sector.name" : "sector.name_e";
          }
          if (indexSource > -1) {
              fields[indexSource] = this.$i18n.locale == "ar" ? "customerSource.name" : "customerSource.name_e";
          }
          for (let i = 0; i < fields.length; ++i) {
              filter += `columns[${i}]=${fields[i]}&`;
          }
          return filter;
      },
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
              <searchPage
                  page="general.customersTable"
                  :isVisible="isVisible"
                  :filterSetting="tableSetting"
                  :companyKeys="companyKeys"
                  :defaultsKeys="defaultsKeys"
                  @dataSearch="() => getData(1,url,filterSearch(searchField))"
                  @searchFun="(fields) => searchField = fields"
                  @editSearch="(search) => searchMain = search"
                  :isSearch="true"
              />
            <!-- end search -->

            <!-- start setting -->
              <actionSetting
                  :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :current_page="current_page"
                  :isCreate="true" :isEdit="true" :isDelete="true"
                  :permissionCreate="isPermission('create Customer')"
                  :permissionUpdate="isPermission('update Customer')"
                  :permissionDelete="isPermission('delete Customer')" :isExcl="true"
                  :isPrint="true" :checkAll="checkAll" :sideAction="true" :sidePaginate="true"
                  :isFilter="true" :group="true" :isGroup="true" :isVisible="isVisible"
                  :isSetting="true" :isPaginate="true" :settings="tableSetting"
                  @delete="ids => deleteRow(ids,url)"
                  @gen_xsl="ExportExcel('xlsx')"
                  @settingFun="setting => settingFun(setting)"
                  :objPagination="objPagination"
                  @perviousOrNext="(current) => getData(current,url,filterSearch(searchField))"
                  @currentPage="(page) => current_page = page"
                  @DataCurrentPage="(page) => getDataCurrentPage(page)"
                  @actionChange="({typeAction,id}) => changeType({typeAction,id})"
              />
            <!-- end setting -->

            <!--  create   -->
              <CustomerGeneral
                  :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                  :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired" :url="url"
                  :type="type" :idObjEdit="idEdit? {idEdit,dataObj: this.tables.find(el => el.id == idEdit)}:null"
                  @getDataTable="getData(1,url,filterSearch(searchField))" :isPermission="isPermission"
              />
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

                <tableCustom
                    :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                    :tables="tables" :isEdit="true" :isDelete="true"
                    :permissionUpdate="isPermission('update Customer')"
                    :permissionDelete="isPermission('delete Customer')"
                    :isVisible="isVisible" :tableSetting="tableSetting"
                    :enabled3="enabled3" :Tooltip="Tooltip" @logFire="(id) => log(id,url)"
                    @delete="ids => deleteRow(ids,url)" @editRow="id => dbClickRow(id)"
                    @checkRows="(cR) => checkAll = cR" @checkRowTable="id => checkRow(id)"
                    :isInputCheck="true" :isLog="true" :isAction="true"
                />

            </div>
            <!-- end .table-responsive-->
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
<style>
.dropdown-menu-custom-company.dropdown .dropdown-menu {
  padding: 5px 10px !important;
  overflow-y: scroll;
  height: 300px;
}
.modal-body .card .tabs .tab-content {
  padding: 20px 60px 40px !important;
}
</style>
