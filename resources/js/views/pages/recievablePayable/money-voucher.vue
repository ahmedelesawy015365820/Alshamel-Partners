<script>
import permissionGuard from "../../../helper/permission";

import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import loader from "../../../components/general/loader";
import translation from "../../../helper/mixin/translation-mixin";
import customTable from "../../../helper/mixin/customTable";
import crudHelper from "../../../helper/mixin/crudHelper";
import searchPage from "../../../components/general/searchPage";
import actionSetting from "../../../components/general/actionSetting";
import tableCustom from "../../../components/general/tableCustom";
import MoneyVoucher from "../../../components/create/receivablePayment/money-voucher";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Money Voucher",
    meta: [{ name: "Money Voucher", content: "Money Voucher" }],
  },
    mixins: [translation,customTable,crudHelper],
    components: {
        Layout, PageHeader, loader, searchPage,
        actionSetting, tableCustom, MoneyVoucher
    },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Money Voucher", "all Money Voucher");
    });
  },
  data() {
    return {
        url: '/voucher-headers',
        searchMain: '',
        tableSetting: [
            {
                isFilter: true, isSet: true, trans: "money_voucher_branch",isV: "branch_id",
                type: "relation", name: "branch", sort: false, col1: "name", col2: "name_e",
                setting: { branch_id: true }, isSetting: true,
            },
            {
                isFilter: true,isSet: true,trans:"money_voucher_serial_number",isV: 'prefix',
                type: 'string',sort: true,setting: {"prefix":true},isSetting: true
            },
            {
                isFilter: true,isSet: true,trans:"money_voucher_date",isV: 'date',
                type: 'string',sort: true,setting: {"date":true},isSetting: true
            },
            {
                isFilter: true, isSet: true, trans: "money_voucher_customer",isV: "customer_id",
                type: "relation", name: "customer", sort: false, col1: "name", col2: "name_e",
                setting: { customer_id: true }, isSetting: true,
            },
            {
                isFilter: true,isSet: true,trans:"money_voucher_amount",isV: 'amount',
                type: 'string',sort: true,setting: {"amount":true},isSetting: true
            },
            {
                isFilter: true, isSet: true, trans: "money_voucher_payment_method",isV: "payment_method_id",
                type: "relation", name: "paymentMethod", sort: false, col1: "name", col2: "name_e",
                setting: { payment_method_id: true }, isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "money_voucher_salesmen",isV: "salesmen_id",
                type: "relation", name: "salesmen", sort: false, col1: "name", col2: "name_e",
                setting: { salesmen_id: true }, isSetting: true,
            },
            {
                isFilter: true, isSet: true, trans: "money_voucher_document",isV: "document_id",
                type: "relation", name: "document", sort: false, col1: "name", col2: "name_e",
                setting: { document_id: true }, isSetting: true,
            },
        ],
        sendSetting: {},
        searchField: [],
    };
  },
  mounted() {
      this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
      this.settingFun();
      this.getCustomTableFields('general_voucher_headers');
      this.getData(1,this.url,this.filterSearch(this.searchField));
  },
  methods: {
      filterSearch(fields){
          let  filter = '';
          let index = fields.indexOf("customer_id");
          if (index > -1) {
              fields[index] =  this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
          }
          index = fields.indexOf("salesmen_id");
          if (index > -1) {
              fields[index] = this.$i18n.locale == "ar" ? "salesmen.name" : "salesmen.name_e";
          }
          index = fields.indexOf("branch_id");
          if (index > -1) {
              fields[index] = this.$i18n.locale == "ar" ? "branch.name" : "branch.name_e";
          }
          index = fields.indexOf("payment_method_id");
          if (index > -1) {
              fields[index] = this.$i18n.locale == "ar" ? "paymentMethod.name" : "paymentMethod.name_e";
          }
          index = fields.indexOf("document_id");
          if (index > -1) {
              fields[index] = this.$i18n.locale == "ar" ? "document.name" : "document.name_e";
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
                  page="general.MoneyVoucher"
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
                  :isCreate="true" :isEdit="false" :isDelete="true"
                  :permissionCreate="isPermission('create Money Voucher')"
                  :permissionUpdate="isPermission('update Money Voucher')"
                  :permissionDelete="isPermission('delete Money Voucher')" :isExcl="true"
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
              <MoneyVoucher
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
                    :tables="tables" :isEdit="false" :isDelete="true"
                    :permissionUpdate="isPermission('update Money Voucher')"
                    :permissionDelete="isPermission('delete Money Voucher')"
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
