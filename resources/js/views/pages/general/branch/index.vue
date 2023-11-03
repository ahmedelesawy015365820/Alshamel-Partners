<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import loader from "../../../../components/general/loader";
import translation from "../../../../helper/mixin/translation-mixin";
import Branch from "../../../../components/create/general/branch";
import permissionGuard from "../../../../helper/permission";
import customTable from "../../../../helper/mixin/customTable";
import crudHelper from "../../../../helper/mixin/crudHelper";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Branch",
    meta: [{ name: "description", content: "Branch" }],
  },
  mixins: [translation,customTable,crudHelper],
  components: {
    Layout, PageHeader, loader, searchPage,
    actionSetting, tableCustom, Branch
  },
  data() {
    return {
        url: '/branches',
        searchMain: '',
        tableSetting: [
            {
                isFilter: false,isSet: true,trans:"branch_parent", isV: 'parent_id'
                ,type: 'relation', name: 'parent',sort: false,col1: 'name',col2: 'name_e',
                setting: {"parent_id":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"branch_name_ar",isV: 'name',
                type: 'string',sort: true,setting: {"name":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"branch_name_en",isV: 'name_e',
                type: 'string',sort: true,setting: {"name_e":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"branch_address",isV: 'address',
                type: 'string',sort: true,setting: {"address":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"branch_fax",isV: 'fax',
                type: 'string',sort: true,setting: {"fax":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"branch_p_o_pox",isV: 'p_o_pox',
                type: 'string',sort: true,setting: {"p_o_pox":true},isSetting: true
            },
            {
                isFilter: false,isSet: true,trans:"branch_status",isV: 'is_active',
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
      this.getCustomTableFields('general_branches');
      this.getData(1,this.url,this.filterSearch(this.searchField));
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Branch", "all Branch");
    });
  },
  methods: {
      filterSearch(fields){
          let filter = '';
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
                  page="branch.branchesTable"
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
                  :permissionCreate="isPermission('create Branch')"
                  :permissionUpdate="isPermission('update Branch')"
                  :permissionDelete="isPermission('delete Branch')" :isExcl="true"
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
            <Branch
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
                    :permissionUpdate="isPermission('update Branch')"
                    :permissionDelete="isPermission('delete Branch')"
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
