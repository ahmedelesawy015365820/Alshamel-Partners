<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import loader from "../../../components/general/loader";
import permissionGuard from "../../../helper/permission";
import searchPage from "../../../components/general/searchPage";
import actionSetting from "../../../components/general/actionSetting";
import tableCustom from "../../../components/general/tableCustom";
import translation from "../../../helper/mixin/translation-mixin";
import customTable from "../../../helper/mixin/customTable";
import successError from "../../../helper/mixin/success&error";
import crudHelper from "../../../helper/mixin/crudHelper";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import {required} from "vuelidate/lib/validators";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";

/**
 * Advanced Table component
 */
export default {
    page: {title: "Attendance And Departure", meta: [{ name: "description", content: "Attendance And Departure" }],},
    mixins: [translation,customTable,successError,crudHelper],
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "Attendance And Departure Report", "all attendanceAndDeparture hr");
        });
    },
    components: {
        Layout, PageHeader, loader,
        searchPage,actionSetting, tableCustom,
        DatePicker,Multiselect
    },
    data() {
        return {
            url: '/hr/reports/attendance-departure',
            searchMain: '',
            tableSetting: [
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_employee_id",isV: 'employee_id',
                    type: 'string',sort: true,setting: {"employee_id":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_employee_name",isV: 'name',
                    type: 'string',sort: true,setting: {"name":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_day",isV: 'day',
                    type: 'string',sort: true,setting: {"day":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_attendance",isV: 'attend_1',
                    type: 'string',sort: true,setting: {"attend_1":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_departure",isV: 'depart_1',
                    type: 'string',sort: true,setting: {"depart_1":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_attendance_shift2",isV: 'attend_2',
                    type: 'string',sort: true,setting: {"attend_2":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_departure_shift2",isV: 'depart_2',
                    type: 'string',sort: true,setting: {"depart_2":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_delay",isV: 'late',
                    type: 'string',sort: true,setting: {"late":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_additional",isV: 'overtime',
                    type: 'string',sort: true,setting: {"overtime":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_Actualnumberofhours",isV: 'exact_hours',
                    type: 'string',sort: true,setting: {"exact_hours":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_absence",isV: 'absence',
                    type: 'string',sort: true,setting: {"absence":true},isSetting: true
                },
                {
                    isFilter: false,isSet: true,trans:"attendance_and_departure_note",isV: 'notes',
                    type: 'string',sort: true,setting: {"notes":true},isSetting: true
                },
                // {
                //     isFilter: false,isSet: true,trans:"time_employee_timetables_header",isV: 'timetables_header_id',
                //     type: 'relation',setting: {"timetables_header_id":true},isSetting: true,name: 'timetablesHeader',
                //     col1: 'name',col2: 'name_e'
                // }
            ],
            sendSetting: {},
            searchField: [],
            isLoader: false,
            create: {
                month: '',
                year: '',
                department_id: null,
                branch_id: null,
                employeesIds: []
            },
            departments: [],
            branches: [],
            employees: []
        };
    },
    validations: {
        create: {
            month: {required},
            year: {required},
            member_status_id: {},
            brnach_id: {},
            employeesIds: {required},
        },
    },
    mounted() {
        this.searchField = this.tableSetting.filter(e => e.isFilter ).map(el => el.isV);
        this.settingFun();
        this.getBranches();
        this.getDepartnent();
        this.getEmployees();
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
        getDataAttend(page = 1) {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;

                let filter = '';
                for (let i = 0; i < this.create.employeesIds.length; ++i) {
                    filter += `&employeesIds[${i}]=${this.create.employeesIds[i]}`;
                }

                adminApi.get(`${this.url}?month=${this.create.month}&year=${this.create.year}&department_id=${this.create.department_id}&branch_id=${this.create.branch_id}&page=${page}&per_page=50${filter}`)
                    .then((res) => {
                        let l = res.data;
                        this.tables = l.data;
                        this.objPagination = l.pagination;
                        this.current_page = parseInt(l.pagination.current_page);
                    })
                    .catch((err) => {
                        Swal.fire({
                            icon: 'error',
                            title: `${this.$t('general.Error')}`,
                            text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                        });
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
            }
        },
        getDataCurrentPageAttend(page = 1) {
            this.$v.create.$touch();
            if (this.$v.create.$invalid) {
                return;
            } else {
                    this.isLoader = true;

                    let filter = '';
                    for (let i = 0; i < this.create.employeesIds.length; ++i) {
                        filter += `&employeesIds[${i}]=${this.create.employeesIds[i]}`;
                    }

                    adminApi.get(`${this.url}?month=${this.create.month}&year=${this.create.year}&department_id=${this.create.department_id}&branch_id=${this.create.branch_id}&page=${page}&per_page=50${filter}`)
                        .then((res) => {
                            let l = res.data;
                            this.tables = l.data;
                            this.objPagination = l.pagination;
                            this.current_page = parseInt(l.pagination.current_page);
                        })
                        .catch((err) => {
                            Swal.fire({
                                icon: 'error',
                                title: `${this.$t('general.Error')}`,
                                text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                            });
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
            }
        },
        getDepartnent() {
            this.isLoader = true;

            adminApi
                .get(`/depertments/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    this.departments = l;
                    this.create.employeesIds = [];
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getBranches() {
            this.isLoader = true;

            adminApi
                .get(`/branches/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    this.branches = l;
                    this.create.employeesIds = [];
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getEmployees() {
            this.isLoader = true;

            let department = this.create.department_id ? `&department_id=${this.create.department_id}` : '';
            let branch = this.create.branch_id ? `&branch_id=${this.create.branch_id}` : '';

            adminApi
                .get(`/employees/get-drop-down?limet=30${department}${branch}`)
                .then((res) => {
                    let l = res.data.data;
                    this.employees = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
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
                            page="general.AttendanceAndDepartureTables"
                            :isVisible="isVisible"
                            :filterSetting="tableSetting"
                            :companyKeys="companyKeys"
                            :defaultsKeys="defaultsKeys"
                            @dataSearch="() => getDataAttend"
                            @searchFun="(fields) => searchField = fields"
                            @editSearch="(search) => searchMain = search"
                            :isSearch="false"
                        />
                        <!-- end search -->

                        <!-- start setting -->
                        <actionSetting
                            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :current_page="current_page"
                            :isCreate="false" :isEdit="false" :isDelete="false"
                            :permissionCreate="false"
                            :permissionUpdate="false"
                            :permissionDelete="false" :isExcl="true"
                            :isPrint="true" :checkAll="checkAll" :sideAction="true" :sidePaginate="true"
                            :isFilter="true" :group="true" :isGroup="true" :isVisible="isVisible"
                            :isSetting="true" :isPaginate="true" :settings="tableSetting"
                            @delete="ids => deleteRow(ids,url)"
                            @gen_xsl="ExportExcel('xlsx')"
                            @settingFun="setting => settingFun(setting)"
                            :objPagination="objPagination"
                            @perviousOrNext="(current) => getDataAttend(current)"
                            @currentPage="(page) => current_page = page"
                            @DataCurrentPage="(page) => getDataCurrentPageAttend(page)"
                            @actionChange="({typeAction,id}) => changeType({typeAction,id})"
                        />
                        <!-- end setting -->

                        <!--  create   -->
                        <b-modal
                            id="filter"
                            :title="$t('general.Search')"
                            title-class="font-18"
                            body-class="p-4"
                            size="lg"
                            :hide-footer="true"
                            @hidden="$bvModal.hide('filter')"
                        >
                            <form>
                                <div class="mb-3 d-flex justify-content-end">
                                    <template>
                                        <b-button
                                            variant="success"
                                            type="button" class="mx-1"
                                            v-if="!isLoader"
                                            @click.prevent="getDataAttend(1)"
                                        >
                                            {{ $t('general.Search') }}
                                        </b-button>

                                        <b-button variant="success" class="mx-1" disabled v-else>
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t('login.Loading') }}...</span>
                                        </b-button>
                                    </template>
                                    <!-- Emulate built in modal footer ok and cancel button actions -->

                                    <b-button variant="danger" type="button" @click.prevent="$bvModal.hide('filter')">
                                        {{ $t('general.Cancel') }}
                                    </b-button>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ $t('general.month') }}
                                            </label>
                                            <date-picker
                                                type="month"
                                                v-model="$v.create.month.$model"
                                                format="MM"
                                                valueType="format"
                                                :confirm="false"
                                                >
                                            </date-picker>
                                            <div
                                                v-if="
                                                  $v.create.month.$error
                                                "
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ $t('general.Year') }}
                                            </label>
                                            <date-picker
                                                type="year"
                                                v-model="$v.create.year.$model"
                                                format="YYYY"
                                                valueType="format"
                                                :confirm="false"
                                            >
                                            </date-picker>
                                            <div
                                                v-if="
                                                  $v.create.year.$error
                                                "
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey("employee_branch") }}
                                            </label>
                                            <multiselect
                                                v-model="create.branch_id"
                                                :options="branches.map((type) => type.id)"
                                                :custom-label="
                                                  (opt) => branches.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'
                                                      ? branches.find((x) => x.id == opt).name
                                                      : branches.find((x) => x.id == opt).name_e: null
                                                "
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey("employee_department") }}
                                            </label>
                                            <multiselect
                                                v-model="create.department_id"
                                                :options="departments.map((type) => type.id)"
                                                :custom-label="
                                                  (opt) => departments.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'
                                                      ? departments.find((x) => x.id == opt).name
                                                      : departments.find((x) => x.id == opt).name_e: null
                                                "
                                            >
                                            </multiselect>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group position-relative">
                                            <label class="control-label">
                                                {{ getCompanyKey("attendance_and_departure_employees") }}
                                            </label>
                                            <multiselect
                                                :multiple="true"
                                                v-model="$v.create.employeesIds.$model"
                                                :options="employees.map((type) => type.id)"
                                                :custom-label="
                                                  (opt) => employees.find((x) => x.id == opt)?
                                                    $i18n.locale == 'ar'
                                                      ? employees.find((x) => x.id == opt).name
                                                      : employees.find((x) => x.id == opt).name_e: null
                                                "
                                            >
                                            </multiselect>
                                            <div
                                                v-if="
                                                  $v.create.employeesIds.$error
                                                "
                                                class="text-danger"
                                            >
                                                {{ $t("general.fieldIsRequired") }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </b-modal>
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
                                :tables="tables" :isEdit="false" :isDelete="false"
                                :permissionUpdate="false"
                                :permissionDelete="false"
                                :isVisible="isVisible" :tableSetting="tableSetting"
                                :enabled3="enabled3" :Tooltip="Tooltip" @logFire="(id) => log(id,url)"
                                @delete="ids => deleteRow(ids,url)" @editRow="id => dbClickRow(id)"
                                @checkRows="(cR) => checkAll = cR" @checkRowTable="id => checkRow(id)"
                                :isInputCheck="false" :isLog="false" :isAction="false"
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
