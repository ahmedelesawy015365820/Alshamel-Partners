<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import loader from "../../../../components/general/loader";
import translation from "../../../../helper/mixin/translation-mixin";
import customTable from "../../../../helper/mixin/customTable";
import successError from "../../../../helper/mixin/success&error";
import crudHelper from "../../../../helper/mixin/crudHelper";
import DocumrntReason from "../../../../components/create/general/document-reason";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import permissionGuard from "../../../../helper/permission";
/**
 * Advanced Table component
 */
export default {
  page: {
    title: "document Reason",
    meta: [{ name: "description", content: "document Reason" }],
  },
  mixins: [translation,customTable,successError,crudHelper],
  components: {
        Layout, PageHeader, loader, DocumrntReason,
        searchPage,actionSetting, tableCustom
  },
  data() {
    return {
        url: '/document-approval-details',
        searchMain: '',
        tableSetting: [
            {
                isFilter: true,isSet: true,trans:"document_serial_number",isV: 'document_serial_number',
                type: 'string',sort: true,setting: {"document_serial_number":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"document_reason_notes",isV: 'notes',
                type: 'string',sort: true,setting: {"notes":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"employee", isV: 'employee_id',
                type: 'relation',name: 'employee',sort: false,col1: 'name',col2: 'name_e',
                setting: {"employee_id":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"document_decision", isV: 'decision_id',
                type: 'relation',name: 'documentStatuse',sort: false,col1: 'name',col2: 'name_e',
                setting: {"decision_id":true},isSetting: true
            },
            {
                isFilter: false,isSet: true,trans:"document_decision_date",isV: 'decision_date',
                type: 'string',setting: {"decision_date":true},isSetting: true
            },
            {
                isFilter: false,isSet: true,trans:"document_approval_time",isV: 'approval_time',
                type: 'string',setting: {"approval_time":true},isSetting: true
            }
        ],
        sendSetting: {},
        searchField: [],
    };
  },
  mounted() {
      this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
      this.settingFun();
      this.getCustomTableFields('general_document_approval_details');
      this.getData(1,this.url,this.filterSearch(this.searchField));
  },
  beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Document Reason", "all Document Reason");
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
                  page="general.documentReasonTable"
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
                  :permissionCreate="isPermission('create Document Reason')"
                  :permissionUpdate="isPermission('update Document Reason')"
                  :permissionDelete="isPermission('delete Document Reason')" :isExcl="true"
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
              <DocumrntReason
                  :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                  :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired" :url="url"
                  :type="type" :idObjEdit="idEdit? {idEdit,dataObj: this.tables.find(el => el.id == idEdit)}:null"
                  @getDataTable="getData(1,url,filterSearch(searchField))" :isPermission="isPermission"
              />
            <!--  /create   -->

            <!-- start .table-responsive-->
            <div class="table-responsive mb-3 custom-table-theme position-relative"
                 ref="exportable_table" id="printCustom"
            >
              <!--       start loader       -->
              <loader size="large" v-if="isLoader" />
              <!--       end loader       -->

                <tableCustom
                    :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                    :tables="tables" :isEdit="true" :isDelete="true"
                    :permissionUpdate="isPermission('update Document Reason')"
                    :permissionDelete="isPermission('delete Document Reason')"
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
