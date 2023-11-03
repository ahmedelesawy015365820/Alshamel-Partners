<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import loader from "../../../components/general/loader";
import translation from "../../../helper/mixin/translation-mixin";
import permissionGuard from "../../../helper/permission";
import customTable from "../../../helper/mixin/customTable";
import successError from "../../../helper/mixin/success&error";
import crudHelper from "../../../helper/mixin/crudHelper";
import Panel from "../../../components/create/boardRent/panel";
import searchPage from "../../../components/general/searchPage";
import actionSetting from "../../../components/general/actionSetting";
import tableCustom from "../../../components/general/tableCustom";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Panels",
        meta: [{ name: "description", content: "Panels" }],
    },
    mixins: [translation,customTable,successError,crudHelper],
    components: {
        Layout, PageHeader, loader, Panel,
        searchPage,actionSetting, tableCustom
    },
    data() {
        return {
            url: '/boards-rent/panels',
            searchMain: '',
            tableSetting: [
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_name_ar",isV: 'name',
                    type: 'string',sort: true,setting: {"name":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_name_en",isV: 'name_e',
                    type: 'string',sort: true,setting: {"name_e":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"boardRent_panel_description",isV: 'description',
                    type: 'string',sort: true,setting: {"description":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"boardRent_panel_description_e",isV: 'description_e',
                    type: 'string',sort: true,setting: {"description_e":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_code",isV: 'code',
                    type: 'string',sort: true,setting: {"code":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_new_code",isV: 'new_code',
                    type: 'string',sort: true,setting: {"new_code":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_size",isV: 'size',
                    type: 'string',sort: true,setting: {"size":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"boardRent_panel_note",isV: 'note',
                    type: 'string',sort: true,setting: {"note":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_face",isV: 'face',
                    type: 'string',sort: true,setting: {"face":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_unit_status", isV: 'unit_status_id',
                    type: 'relation',name: 'unitStatus',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"unit_status_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_category", isV: 'category_id',
                    type: 'relation',name: 'category',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"category_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_governorate", isV: 'governorate_id',
                    type: 'relation',name: 'governorate',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"governorate_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_city", isV: 'city_id',
                    type: 'relation',name: 'city',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"city_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_avenue", isV: 'avenue_id',
                    type: 'relation',name: 'avenue',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"avenue_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_street", isV: 'street_id',
                    type: 'relation',name: 'street',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"street_id":true},isSetting: true
                },
            ],
            sendSetting: {},
            searchField: [],
        };
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
        this.settingFun();
        this.getCustomTableFields('boards_rent_panels');
        this.getData(1,this.url,this.filterSearch(this.searchField));
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
          return permissionGuard(vm, "Board rent Panel", "all panel boardRent");
        });
    },
    methods: {
        filterSearch(fields){
            let _filterSetting = [...fields];
            let unit_status = _filterSetting.indexOf("unit_status_id");
            let category = _filterSetting.indexOf("category_id");
            let city = _filterSetting.indexOf("city_id");
            let governorate = _filterSetting.indexOf("governorate_id");
            let avenue = _filterSetting.indexOf("avenue_id");
            let street = _filterSetting.indexOf("street_id");
            if (unit_status > -1) {_filterSetting[unit_status] = this.$i18n.locale == "ar" ? "unitStatus.name" : "unitStatus.name_e";}
            if (category > -1) {_filterSetting[category] = this.$i18n.locale == "ar" ? "category.name" : "category.name_e";}
            if (city > -1) {_filterSetting[city] = this.$i18n.locale == "ar" ? "city.name" : "city.name_e";}
            if (avenue > -1) {_filterSetting[avenue] = this.$i18n.locale == "ar" ? "city.name" : "city.name_e";}
            if (street > -1) {_filterSetting[street] = this.$i18n.locale == "ar" ? "avenue.name" : "avenue.name_e";}
            if (governorate > -1) {_filterSetting[governorate] = this.$i18n.locale == "ar" ? "governorate.name" : "governorate.name_e";}
            let filter = '';

            for (let i = 0; i < _filterSetting.length; ++i) {
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
                            page="general.panelsTable"
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
                            :permissionCreate="isPermission('create panel boardRent')"
                            :permissionUpdate="isPermission('update panel boardRent')"
                            :permissionDelete="isPermission('delete panel boardRent')" :isExcl="true"
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
                        <Panel
                            :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                            :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired" :url="url"
                            :type="type" :idObjEdit="idEdit? {idEdit,dataObj: this.tables.find(el => el.id == idEdit)}:null"
                            @getDataTable="getData(1,url,filterSearch(searchField))" :isPermission="isPermission"
                        />
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
                                :tables="tables" :isEdit="true" :isDelete="true"
                                :permissionUpdate="isPermission('update panel boardRent')"
                                :permissionDelete="isPermission('delete panel boardRent')"
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
</style>
