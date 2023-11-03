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
import DocumentWithStatus from "../../../../components/create/general/document-with-status";
/**
 * Advanced Table component
 */
export default {
  page: {
    title: "link documents with status",
    meta: [{ name: "description", content: "link documents with status" }],
  },
  mixins: [translation,customTable,crudHelper],
  components: {
        Layout, PageHeader, loader, searchPage,
        actionSetting, tableCustom, DocumentWithStatus
  },
  data() {
    return {
        url: '/document-company-module-status',
        searchMain: '',
        tableSetting: [
            {
                isFilter: true,isSet: true,trans:"link_documents_with_status_document", isV: 'document_id',
                type: 'relation',name: 'document',sort: false,col1: 'name',col2: 'name_e',
                setting: {"document_id":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"link_documents_with_status_document_module_type", isV: 'document_module_type_id'
                ,type: 'relation', name: 'document_module_type',sort: false,col1: 'name',col2: 'name_e',
                setting: {"document_module_type_id":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"link_documents_with_status_status", isV: 'status_id'
                ,type: 'relation', name: 'status',sort: false,col1: 'name',col2: 'name_e',
                setting: {"status_id":true},isSetting: true
            },
        ],
        sendSetting: {},
        searchField: [],
    };
  },
  beforeRouteEnter(to, from, next) {
          next((vm) => {
      return permissionGuard(vm, "link documents with status", "all documents with status");
    });

    },
  mounted() {
      this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
      this.settingFun();
      this.getCustomTableFields('document_company_module_status');
      this.getData(1,this.url,this.filterSearch(this.searchField));
  },
  methods: {
     filterSearch(fields){
          let indexC = fields.indexOf("document_id"),
              indexG = fields.indexOf("document_module_type_id"),
              indexX = fields.indexOf("status_id"),
              filter = '';
          if (indexC > -1) {
              fields[indexC] = this.$i18n.locale == "ar" ? "document.name" : "document.name_e";
          }
          if (indexG > -1) {
              fields[indexG] = this.$i18n.locale == "ar" ? "documentModuleType.name" : "documentModuleType.name_e";
          }
          if (indexX > -1) {
              fields[indexX] = this.$i18n.locale == "ar" ? "status.name" : "status.name_e";
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
                  page="general.linkDocumentsWithStatusTable"
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
                  :permissionCreate="isPermission('create documents with status')"
                  :permissionUpdate="isPermission('update documents with status')"
                  :permissionDelete="isPermission('delete documents with status')" :isExcl="true"
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

              <!--  create or edit   -->
              <DocumentWithStatus
                  :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :url="url"
                  :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired"
                  :type="type" :idObjEdit="idEdit? {idEdit,dataObj: this.tables.find(el => el.id == idEdit)}:null"
                  @getDataTable="getData(1,url,filterSearch(searchField))" :isPermission="isPermission"
              />
              <!--  /create   -->

            <!-- start .table-responsive-->
            <div class="table-responsive mb-3 custom-table-theme position-relative" ref="exportable_table"
              id="printCustom">
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

              <tableCustom
                    :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                    :tables="tables" :isEdit="true" :isDelete="true"
                    :permissionUpdate="isPermission('update documents with status')"
                    :permissionDelete="isPermission('delete documents with status')"
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
