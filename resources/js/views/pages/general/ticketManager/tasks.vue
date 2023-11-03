<script>
import permissionGuard from "../../../../helper/permission";
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import loader from "../../../../components/general/loader";
import translation from "../../../../helper/mixin/translation-mixin";
import Task from "../../../../components/create/general/task";
import searchPage from "../../../../components/general/searchPage";
import actionSetting from "../../../../components/general/actionSetting";
import tableCustom from "../../../../components/general/tableCustom";
import customTable from "../../../../helper/mixin/customTable";
import successError from "../../../../helper/mixin/success&error";
import crudHelper from "../../../../helper/mixin/crudHelper";

export default {
    page: {
        title: "Tasks",
        meta: [{ name: "description", content: 'Tasks' }],
    },
    mixins: [translation,customTable,successError,crudHelper],
    components: {
        Layout, PageHeader, loader, Task,
        searchPage,actionSetting, tableCustom
    },
    beforeRouteEnter(to, from, next) {
         next((vm) => {
              return permissionGuard(vm, "Tasks", "all Task");
         });
    },
    data() {
        return {
            url: '/tasks',
            searchMain: '',
            tableSetting: [
                {
                    isFilter: true,isSet: true,trans:"task_type",isV: 'type',
                    type: 'string',sort: true,setting: {"type":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"employee", isV: 'employee_id',
                    type: 'relation',name: 'employee',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"employee_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_task_department", isV: 'department_id'
                    ,type: 'relation', name: 'department',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"department_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"customer",isV: 'customer_id',
                    type: 'relation', name:'customer',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"customer_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"city",isV: 'department_task_id',
                    type: 'relation', name:'department_task',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"department_task_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"task_status",isV: 'status_id',
                    type: 'relation', name:'status',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"status_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_task_equipment",isV: 'equipment_id',
                    type: 'relation', name:'equipment',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"equipment_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_task_location",isV: 'location_id',
                    type: 'relation', name:'location',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"location_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"task_priority",isV: 'priority_id',
                    type: 'relation', name:'priority',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"priority_id":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"task_owners",isV: 'owners',
                    type: 'relation', name:'owners',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"owners":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"task_supervisors",isV: 'supervisors',
                    type: 'relation', name:'supervisors',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"supervisors":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"task_notifications",isV: 'notifications',
                    type: 'relation', name:'notifications',sort: false,col1: 'name',col2: 'name_e',
                    setting: {"notifications":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"general_customer_contact_person",isV: 'contact_person',
                    type: 'string',sort: true,setting: {"contact_person":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"general_customer_contact_phones",isV: 'contact_phone',
                    type: 'string',sort: true,setting: {"contact_phone":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"task_title",isV: 'task_title',
                    type: 'string',sort: true,setting: {"task_title":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"execution_date",isV: 'execution_date',
                    type: 'string',sort: true,setting: {"execution_date":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"task_start_time",isV: 'start_time',
                    type: 'string',sort: true,setting: {"start_time":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"task_end_time",isV: 'end_time',
                    type: 'string',sort: true,setting: {"end_time":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"execution_duration",isV: 'execution_duration',
                    type: 'string',sort: true,setting: {"execution_duration":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"notification_date",isV: 'notification_date',
                    type: 'string',sort: true,setting: {"notification_date":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"execution_end_date",isV: 'execution_end_date',
                    type: 'string',sort: true,setting: {"execution_end_date":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_note",isV: 'note',
                    type: 'string',sort: true,setting: {"note":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_panel_admin_note",isV: 'admin_note',
                    type: 'string',sort: true,setting: {"admin_note":true},isSetting: true
                },
                {
                    isFilter: true,isSet: true,trans:"boardRent_task_task_requirement",isV: 'task_requirement',
                    type: 'string',sort: true,setting: {"task_requirement":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"boardRent_task_is_closed",isV: 'is_closed',
                    type: 'boolean',setting: {"is_closed":true},isSetting: true
                }
            ],
            sendSetting: {},
            searchField: [],
        }
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
        this.settingFun();
        this.$store.dispatch('locationIp/getIp');
        this.getCustomTableFields('general_tasks');
        this.getData(1,this.url,this.filterSearch(this.searchField));
    },
    methods: {
        filterSearch(fields){
            let indexemployee = fields.indexOf("employee_id"),
                indexG = fields.indexOf("department_id"),
                indexCty = fields.indexOf("customer_id"),
                indexdepartmentTask = fields.indexOf("department_task_id"),
                indexstatus = fields.indexOf("status_id"),
                filter = '';
            if (indexemployee > -1) {
                fields[indexemployee] = this.$i18n.locale == "ar" ? "employee.name" : "employee.name_e";
            }
            if (indexG > -1) {
                fields[indexG] = this.$i18n.locale == "ar" ? "department.name" : "department.name_e";
            }
            if (indexCty > -1) {
                fields[indexCty] = this.$i18n.locale == "ar" ? "customer.name" : "customer.name_e";
            }
            if (indexdepartmentTask > -1) {
                fields[indexdepartmentTask] = this.$i18n.locale == "ar" ? "departmentTask.name" : "departmentTask.name_e";
            }
            if (indexstatus > -1) {
                fields[indexstatus] = this.$i18n.locale == "ar" ? "departmentTask.name" : "departmentTask.name_e";
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
                            page="general.tasksTable"
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
                            :permissionCreate="isPermission('create Task')"
                            :permissionUpdate="isPermission('update Task')"
                            :permissionDelete="isPermission('delete Task')" :isExcl="true"
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
                        <Task
                            :id="'create'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
                            :isPage="true" :isVisiblePage="isVisible" :isRequiredPage="isRequired" :url="url"
                            :type="type" :idObjEdit="idEdit? {idEdit,dataObj: this.tables.find(el => el.id == idEdit)}:null"
                            @getDataTable="getData(1,url,filterSearch(searchField))" :isPermission="isPermission"
                        />
                        <!--  /create   -->

                        <!--  logs   -->
                        <b-modal
                            id="logs"
                            :title="getCompanyKey('boardRent_task_create_form')"
                            title-class="font-18"
                            dialog-class="modal-full-width"
                            :hide-footer="true"
                            @hidden="logs = [];log= {}"
                        >
                            <div class="mb-3">
                                <h3 class="text-center">{{ $t('general.Initial_Value') }}</h3>
                                <table class="table table-borderless table-hover table-centered m-0">
                                    <thead>
                                    <tr>
                                        <th v-if="sendSetting.employee_id">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('boardRent_task_type') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.department_id">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("boardRent_task_department") }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.type">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("customer") }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.customer_id">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("customer") }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.contact_person">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('general_customer_contact_person') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.contact_phone">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('general_customer_contact_phones') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.equipment_id">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("equipment") }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.location_id">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("boardRent_task_location") }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.priority_id">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("task_priority") }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.task_requirement">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("boardRent_task_task_requirement") }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.is_closed">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey("boardRent_task_is_closed") }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.task_title">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('task_title') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.department_task_id">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('task_type') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.owners">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('task_owners') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.supervisors">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('task_supervisors') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.notifications">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('task_notifications') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.status_id">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('task_status') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.execution_date">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('execution_date') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.start_time">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('task_start_time') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.end_time">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('task_end_time') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.execution_duration">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('execution_duration') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.notification_date">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('notification_date') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.execution_end_date">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('execution_end_date') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.note">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('boardRent_panel_note') }}</span>
                                            </div>
                                        </th>
                                        <th v-if="sendSetting.admin_note">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ getCompanyKey('boardRent_panel_admin_note') }}</span>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="log.employee_id">
                                    <tr>
                                        <td v-if="sendSetting.employee_id">
                                            <h5 v-if="log.employee" class="m-0 font-weight-normal">
                                                {{ log.employee? $i18n.locale == "ar" ? log.employee.name : log.employee.name_e : ' - '}}
                                            </h5>
                                        </td>
                                        <td v-if="sendSetting.department_id">
                                            <h5 v-if="log.department" class="m-0 font-weight-normal">
                                                {{$i18n.locale == "ar" ? log.department.name : log.department.name_e }}
                                            </h5>
                                        </td>
                                        <th v-if="sendSetting.type">
                                            <div class="d-flex justify-content-center">
                                                <span>{{ log.type }}</span>
                                            </div>
                                        </th>
                                        <td v-if="sendSetting.customer_id">
                                            <h5 v-if="log.customer" class="m-0 font-weight-normal">
                                                {{ $i18n.locale == "ar" ? log.customer.name : log.customer.name_e}}
                                            </h5>
                                        </td>
                                        <td v-if="sendSetting.contact_person"><h5 class="m-0 font-weight-normal">{{ log.contact_person }}</h5></td>
                                        <td v-if="sendSetting.contact_phone"><h5 class="m-0 font-weight-normal">{{ log.contact_phone }}</h5></td>
                                        <th v-if="sendSetting.equipment_id">
                                            <h5 v-if="log.equipment" class="m-0 font-weight-normal">
                                                {{ $i18n.locale == "ar" ? log.equipment.name : log.equipment.name_e}}
                                            </h5>
                                        </th>
                                        <th v-if="sendSetting.location_id">
                                            <h5 v-if="log.location" class="m-0 font-weight-normal">
                                                {{ $i18n.locale == "ar" ? log.location.name : log.location.name_e}}
                                            </h5>
                                        </th>
                                        <th v-if="sendSetting.priority_id">
                                            <h5 v-if="log.priority" class="m-0 font-weight-normal">
                                                {{ $i18n.locale == "ar" ? log.priority.name : log.priority.name_e}}
                                            </h5>
                                        </th>
                                        <th v-if="sendSetting.task_requirement">
                                            {{ log.task_requirement}}
                                        </th>
                                        <th v-if="sendSetting.is_closed">
                                        <span
                                            :class="[
                                              log.is_closed == 1 ? 'text-success' : 'text-danger',
                                              'badge',
                                            ]"
                                        >
                                            {{
                                                  log.is_closed == 1
                                                      ? `${$t("general.Yes")}`
                                                : `${$t("general.No")}`
                                            }}
                                          </span>
                                        </th>
                                        <td v-if="sendSetting.task_title"><h5 class="m-0 font-weight-normal">{{ log.task_title }}</h5></td>
                                        <td v-if="sendSetting.department_task_id">
                                            <h5 v-if="log.department_task" class="m-0 font-weight-normal">
                                                {{ $i18n.locale == "ar" ? log.department_task.name : log.department_task.name_e}}
                                            </h5>
                                        </td>
                                        <td v-if="sendSetting.owners">
                                            <h5 v-if="log.owners.length > 0 && log.owners" class="m-0 font-weight-normal">
                                            <span v-for="(owner,index) in log.owners"
                                                  :key="owner.id">
                                            {{ $i18n.locale == "ar" ? owner.name : owner.name_e}}
                                                <span> - </span>
                                            </span>
                                            </h5>
                                        </td>
                                        <td v-if="sendSetting.supervisors">
                                            <h5 v-if="log.supervisors.length > 0 &&  log.supervisors" class="m-0 font-weight-normal">
                                            <span v-for="(supervisor,index) in log.supervisors"
                                                  :key="supervisor.id">
                                            {{ $i18n.locale == "ar" ? supervisor.name : supervisor.name_e}}
                                                 <span> - </span>
                                            </span>
                                            </h5>
                                        </td>
                                        <td v-if="sendSetting.notifications">
                                            <h5 v-if="log.notifications.length > 0 && log.notifications" class="m-0 font-weight-normal">
                                            <span v-for="(notification,index) in log.notifications"
                                                  :key="notification.id">
                                            {{ $i18n.locale == "ar" ? notification.name : notification.name_e}}
                                                 <span> - </span>
                                            </span>
                                            </h5>
                                        </td>
                                        <td v-if="sendSetting.status_id">
                                            <h5 v-if="log.status" class="m-0 font-weight-normal">
                                                {{ $i18n.locale == "ar" ? log.status.name : log.status.name_e}}
                                            </h5>
                                        </td>
                                        <td v-if="sendSetting.execution_date"><h5 class="m-0 font-weight-normal">{{ log.execution_date }}</h5></td>
                                        <td v-if="sendSetting.start_time"><h5 class="m-0 font-weight-normal">{{ log.start_time }}</h5></td>
                                        <td v-if="sendSetting.end_time"><h5 class="m-0 font-weight-normal">{{ log.end_time }}</h5></td>
                                        <td v-if="sendSetting.execution_duration"><h5 class="m-0 font-weight-normal">{{ log.execution_duration }}</h5></td>
                                        <td v-if="sendSetting.notification_date"><h5 class="m-0 font-weight-normal">{{ log.notification_date }}</h5></td>
                                        <td v-if="sendSetting.execution_end_date"><h5 class="m-0 font-weight-normal">{{ log.execution_end_date }}</h5></td>
                                        <td v-if="sendSetting.note"><h5 class="m-0 font-weight-normal">{{ log.note }}</h5></td>
                                        <th v-if="sendSetting.admin_note">
                                            {{ log.admin_note }}
                                        </th>
                                    </tr>
                                    </tbody>
                                    <tbody v-else>
                                    <tr>
                                        <th class="text-center" colspan="21">{{ $t('general.notDataFound') }}</th>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </b-modal>
                        <!--  /logs   -->

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
                                :permissionUpdate="isPermission('update Task')"
                                :permissionDelete="isPermission('delete Task')"
                                :isVisible="isVisible" :tableSetting="tableSetting"
                                :enabled3="enabled3" :Tooltip="Tooltip" @logFire="(id) => getLog(data.id,url)"
                                @delete="ids => deleteRow(ids,url)" @editRow="id => dbClickRow(id)"
                                @checkRows="(cR) => checkAll = cR" @checkRowTable="id => checkRow(id)"
                                :isInputCheck="true" :isLog="true" :isAction="true"
                                :isLogClick="true"
                            />

                        </div>
                        <!-- end .table-responsive-->

                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>
