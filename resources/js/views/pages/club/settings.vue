<script>
import loader from "../../../components/general/loader";
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import translation from "../../../helper/mixin/translation-mixin";
import permissionGuard from "../../../helper/permission";
import customTable from "../../../helper/mixin/customTable";
import crudHelper from "../../../helper/mixin/crudHelper";
import searchPage from "../../../components/general/searchPage";
import actionSetting from "../../../components/general/actionSetting";
import tableCustom from "../../../components/general/tableCustom";
import Setting from "../../../components/create/club/setting";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Settings",
        meta: [{name: "description", content: "Settings"}],
    },
    mixins: [translation,customTable,crudHelper],
    components: {
        Layout, PageHeader, loader, searchPage,
        actionSetting, tableCustom,Setting
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "clubsetting", "all setting club");
        });
    },
    data() {
        return {
            url: 'club-members/type-permission',
            searchMain: '',
            tableSetting: [
                {
                    isFilter: true, isSet: true, trans: "member_type",isV: "cm_members_type_id",
                    type: "relation", name: "type", sort: false, col1: "name", col2: "name_e",
                    setting: { 'cm_members_type_id': true }, isSetting: true,
                },
                {
                    isFilter: true, isSet: true, trans: "member_permission",isV: "cm_permissions_id",
                    type: "relation", name: "permission", sort: false, col1: "name", col2: "name_e",
                    setting: { 'cm_permissions_id': true }, isSetting: true,
                },
                {
                    isFilter: true, isSet: true, trans: "financial_status",isV: "cm_financial_status_id",
                    type: "relation", name: "financialStatus", sort: false, col1: "name", col2: "name_e",
                    setting: { 'cm_financial_status_id': true }, isSetting: true,
                },
                {
                    isFilter: true,isSet: true,trans:"membership_period",isV: 'membership_period',
                    type: 'string',sort: true,setting: {"membership_period":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"allowed_subscription_date",isV: 'allowed_subscription_date',
                    type: 'string',sort: true,setting: {"allowed_subscription_date":true},isSetting: true,
                    columnCustom: 'allowed_subscription_date'
                },
                {
                    isFilter: false,isSet: true,trans:"allowed_vote_date",isV: 'allowed_vote_date',
                    type: 'string',sort: true,setting: {"allowed_vote_date":true},isSetting: true
                },
            ],
            sendSetting: {},
            searchField: [],
        };
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
        this.settingFun();
        this.getCustomTableFields('cm_type_permissions');
        this.getData(1,this.url,this.filterSearch(this.searchField));
    },
    methods: {
        filterSearch(fields){
            let indexC = fields.indexOf("cm_members_type_id"),
                indexx = fields.indexOf("cm_permissions_id"),
                indexy = fields.indexOf("cm_financial_status_id"),
                filter = '';
            if (indexC > -1) {
                fields[indexC] = this.$i18n.locale == "ar" ? "type.name" : "type.name_e";
            }
            if (indexx > -1) {
                fields[indexx] = this.$i18n.locale == "ar" ? "permission.name" : "permission.name_e";
            }
            if (indexy > -1) {
                fields[indexy] = this.$i18n.locale == "ar" ? "financialStatus.name" : "financialStatus.name_e";
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
        <PageHeader/>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <!-- start search -->
                        <searchPage
                            page="general.SettingTable"
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
                            :isCreate="false" :isEdit="true" :isDelete="false"
                            :permissionCreate="isPermission('create Status')"
                            :permissionUpdate="isPermission('update Status')"
                            :permissionDelete="isPermission('delete Status')" :isExcl="true"
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
                        <Setting
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
                            <loader size="large" v-if="isLoader"/>
                            <!--       end loader       -->

                            <tableCustom
                                :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                                :tables="tables" :isEdit="true" :isDelete="false"
                                :permissionUpdate="isPermission('update Status')"
                                :permissionDelete="isPermission('delete Status')"
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
