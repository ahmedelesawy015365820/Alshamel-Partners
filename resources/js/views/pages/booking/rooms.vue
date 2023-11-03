<script>
import permissionGuard from "../../../helper/permission";
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import loader from "../../../components/general/loader";
import translation from "../../../helper/mixin/translation-mixin";
import customTable from "../../../helper/mixin/customTable";
import crudHelper from "../../../helper/mixin/crudHelper";
import Room from "../../../components/create/booking/rooms";
import searchPage from "../../../components/general/searchPage";
import actionSetting from "../../../components/general/actionSetting";
import tableCustom from "../../../components/general/tableCustom";
/**
 * Advanced Table component
 */
export default {
    page: {
        title: "booking rooms",
        meta: [{ name: "booking rooms", content: "booking rooms" }],
    },
    mixins: [translation,customTable,crudHelper],
    components: {
        Layout, PageHeader, loader, searchPage,
        actionSetting, tableCustom, Room
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Booking Rooms", "all Booking Rooms");
        });
    },
    data() {
        return {
            url: '/booking/units',
            searchMain: '',
            tableSetting: [
                {
                    isFilter: true,isSet: true,trans:"room_code",isV: 'code',
                    type: 'string',sort: true,setting: {"code":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"room_price",isV: 'price',
                    type: 'string',sort: true,setting: {"price":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"room_name_ar",isV: 'name',
                    type: 'string',sort: true,setting: {"name":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"room_name_en",isV: 'name_e',
                    type: 'string',sort: true,setting: {"name_e":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"room_unit_status",isV: 'unit_status_id',
                    type: 'relation', name:'unit_status',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"unit_status_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"room_number_of_individuals",isV: 'number_of_individuals',
                    type: 'string',sort: true,setting: {"number_of_individuals":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"room_extra_guest_price",isV: 'extra_guest_price',
                    type: 'string',sort: true,setting: {"extra_guest_price":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"room_floor",isV: 'booking_floor_id',
                    type: 'relation', name:'floor',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"booking_floor_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"room_description_ar",isV: 'description',
                    type: 'string',sort: true,setting: {"description":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"room_description_en",isV: 'description_e',
                    type: 'string',sort: true,setting: {"description_e":true},isSetting: true
                },
            ],
            sendSetting: {},
            searchField: [],
        };
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
        this.settingFun();
        this.getCustomTableFields('booking_units');
        this.getData(1,this.url,this.filterSearch(this.searchField));
    },
    methods: {
        filterSearch(fields){
            let indexStatus = fields.indexOf("unit_status_id"),
                indexFloor = fields.indexOf("booking_floor_id"),
                filter = '';
            if (indexStatus > -1) {
                fields[indexStatus] = this.$i18n.locale == "ar" ? "unit_status.name" : "unit_status.name_e";
            }
            if (indexFloor > -1) {
                fields[indexFloor] = this.$i18n.locale == "ar" ? "room_floor.name" : "room_floor.name_e";
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
                            page="general.roomTable"
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
                            :permissionCreate="isPermission('create items')"
                            :permissionUpdate="isPermission('update items')"
                            :permissionDelete="isPermission('delete items')" :isExcl="true"
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
                        <Room
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
                                :permissionUpdate="isPermission('update items')"
                                :permissionDelete="isPermission('delete items')"
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
