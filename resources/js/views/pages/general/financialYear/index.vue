<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import loader from "../../../../components/general/loader";
import translation from "../../../../helper/mixin/translation-mixin";
import permissionGuard from "../../../../helper/permission";
import customTable from "../../../../helper/mixin/customTable";
import crudHelper from "../../../../helper/mixin/crudHelper";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import FinancialYear from "../../../../components/create/general/financialYear";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Financial Year",
    meta: [{ name: "description", content: "Financial Year" }],
  },
  mixins: [translation,customTable,crudHelper],
  components: {
        Layout, PageHeader, loader, searchPage,
        actionSetting, tableCustom, FinancialYear
  },
  data() {
    return {
        url: '/financial-years',
        searchMain: '',
        tableSetting: [
            {
                isFilter: true,isSet: true,trans:"financial_year_name_ar",isV: 'name',
                type: 'string',sort: true,setting: {"name":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"financial_year_name_en",isV: 'name_e',
                type: 'string',sort: true,setting: {"name_e":true},isSetting: true
            },
            {
                isFilter: false,isSet: true,trans:"financial_year_start_date",isV: 'start_date',
                type: 'date',sort: true,setting: {"start_date":true},isSetting: true
            },
            {
                isFilter: false,isSet: true,trans:"financial_year_end_date",isV: 'end_date',
                type: 'date',sort: true,setting: {"end_date":true},isSetting: true
            },
            {
                isFilter: false,isSet: true,trans:"financial_year_default",isV: 'is_active',
                type: 'boolean',setting: {"is_active":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"financial_year_year",isV: 'year',
                type: 'string',sort: true,setting: {"year":true},isSetting: true
            },
        ],
        sendSetting: {},
        searchField: [],
    };
  },
  mounted() {
      this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
      this.settingFun();
      this.getCustomTableFields('general_financial_years');
      this.getData(1,this.url,this.filterSearch(this.searchField));
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Financial Year", "all Financial Year");
    });
  },
  methods: {
      filterSearch(fields){
          let indexC = fields.indexOf("country_id"),
              filter = '';
          if (indexC > -1) {
              fields[indexC] = this.$i18n.locale == "ar" ? "country.name" : "country.name_e";
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
                  page="financialYear.financialYearsTable"
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
                  :permissionCreate="isPermission('create Financial Year')"
                  :permissionUpdate="isPermission('update Financial Year')"
                  :permissionDelete="isPermission('delete Financial Year')" :isExcl="true"
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
              <FinancialYear
                  :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :url="url"
                  :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired"
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
                    :permissionUpdate="isPermission('update Financial Year')"
                    :permissionDelete="isPermission('delete Financial Year')"
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

