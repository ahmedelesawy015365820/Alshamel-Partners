<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import permissionGuard from "../../../../helper/permission";
import loader from "../../../../components/general/loader";
import translation from "../../../../helper/mixin/translation-mixin";
import customTable from "../../../../helper/mixin/customTable";
import crudHelper from "../../../../helper/mixin/crudHelper";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import ExternalSalesmen from "../../../../components/create/general/external-salesmen";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "External Salesmen",
    meta: [{ name: "description", content: "External Salesmen" }],
  },
    mixins: [translation,customTable,crudHelper],
    components: {
        Layout, PageHeader, loader, searchPage,
        actionSetting, tableCustom, ExternalSalesmen
    },
  beforeRouteEnter(to, from, next) {
        next((vm) => {
      return permissionGuard(vm, "External Sales man", "all External Sales Men");
    });

  },
  data() {
    return {
        url: '/external-salesmen',
        searchMain: '',
        tableSetting: [
            {
                isFilter: true,isSet: true,trans:"external_sale_man_phone",isV: 'phone',
                type: 'string',sort: true,setting: {"phone":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"external_sale_man_address",isV: 'address',
                type: 'string',sort: true,setting: {"address":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"external_saleman_country", isV: 'country_id',
                type: 'relation',name: 'country',sort: false,col1: 'name',col2: 'name_e',
                setting: {"country_id":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"external_sale_man_email",isV: 'email',
                type: 'string',setting: {"email":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"external_sale_man_code",isV: 'rp_code',
                type: 'string',setting: {"rp_code":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"external_sale_man_national_id",isV: 'national_id',
                type: 'string',setting: {"national_id":true},isSetting: true
            },
            {
                isFilter: false,isSet: true,trans:"external_sale_man_status",isV: 'is_active',
                type: 'boolean',setting: {"is_active":true},isSetting: true
            }
        ],
        sendSetting: {},
        searchField: [],
    };
  },
  mounted() {
      this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
      this.settingFun();
      this.getCustomTableFields('general_external_salesmen');
      this.getData(1,this.url,this.filterSearch(this.searchField));
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
                  page="externalSalesmen.externalSalesmensTable"
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
                  :permissionCreate="isPermission('create External Sales Men')"
                  :permissionUpdate="isPermission('update External Sales Men')"
                  :permissionDelete="isPermission('delete External Sales Men')" :isExcl="true"
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
              <ExternalSalesmen
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
                    :permissionUpdate="isPermission('update External Sales Men')"
                    :permissionDelete="isPermission('delete External Sales Men')"
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

