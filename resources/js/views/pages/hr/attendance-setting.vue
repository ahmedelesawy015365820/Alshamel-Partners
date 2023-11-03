<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import loader from "../../../components/general/loader";
import permissionGuard from "../../../helper/permission";
import AttendanceSetting from "../../../components/create/hr/attendance-setting";
import searchPage from "../../../components/general/searchPage";
import actionSetting from "../../../components/general/actionSetting";
import tableCustom from "../../../components/general/tableCustom";
import translation from "../../../helper/mixin/translation-mixin";
import customTable from "../../../helper/mixin/customTable";
import successError from "../../../helper/mixin/success&error";
import crudHelper from "../../../helper/mixin/crudHelper";

/**
 * Advanced Table component
 */
export default {
    page: {title: "Attendance Setting", meta: [{ name: "description", content: "Attendance Setting" }],},
    mixins: [translation,customTable,successError,crudHelper],
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Attendance Setting", "all attendanceSetting hr");
        });
    },
    components: {
        Layout, PageHeader, loader,
        searchPage,actionSetting, tableCustom,
        AttendanceSetting
    },
    data() {
        return {
            url: '/hr/attendance_settings',
            searchMain: '',
            tableSetting: [
                {
                    isFilter: true,isSet: true,trans:"attendance_setting_pre_in",isV: 'pre_in',
                    type: 'string',sort: true,setting: {"pre_in":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_setting_post_in",isV: 'post_in',
                    type: 'string',setting: {"post_in":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"attendance_setting_absent_minutes",isV: 'absent_minutes',
                    type: 'string',sort: true,setting: {"absent_minutes":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_setting_pre_out",isV: 'pre_out',
                    type: 'string',setting: {"pre_out":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_setting_post_out",isV: 'post_out',
                    type: 'string',setting: {"post_out":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_setting_max_out",isV: 'max_out',
                    type: 'string',setting: {"max_out":true},isSetting: true
                }
            ],
            sendSetting: {},
            searchField: []
        };
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
        this.settingFun();
        this.getCustomTableFields('hr_attendance_settings');
        this.getData(1,this.url,this.filterSearch(this.searchField));
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
                            page="general.attendanceSetting"
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
                            :permissionCreate="isPermission('create attendance Hr')"
                            :permissionUpdate="isPermission('update attendance Hr')"
                            :permissionDelete="isPermission('delete attendance Hr')" :isExcl="true"
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
                        <AttendanceSetting
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
                                :tables="tables" :isEdit="true" :isDelete="false"
                                :permissionUpdate="isPermission('update attendance Hr')"
                                :permissionDelete="isPermission('delete attendance Hr')"
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


<style scoped>

</style>
