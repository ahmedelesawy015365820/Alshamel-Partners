<template>
    <div>
        <TimeTableAttend
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            :isPage="false"
            type="create"
            :isPermission="isPermission"
            :id="'timetables-header-create-time-table'"
            @created="getTimeHeder"
        />
        <!--  search   -->
        <b-modal
            id="search"
            :title="$t('general.Search')"
            title-class="font-18"
            body-class="p-4"
            :hide-footer="true"
        >
            <form>
                <div class="mb-3 d-flex justify-content-end">
                    <b-button
                        variant="success"
                        type="button" class="mx-1"
                        v-if="!isLoader"
                        @click.prevent="getEmployees(1)"
                    >
                        {{ $t('general.Search') }}
                    </b-button>

                    <b-button variant="success" class="mx-1" disabled v-else>
                        <b-spinner small></b-spinner>
                        <span class="sr-only">{{ $t('login.Loading') }}...</span>
                    </b-button>
                    <!-- Emulate built in modal footer ok and cancel button actions -->

                    <b-button variant="danger" type="button" @click.prevent="$bvModal.hide(`search`)">
                        {{ $t('general.Cancel') }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">
                                {{ $t('general.name') }}
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="location.name"
                            />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("employee_department") }}
                            </label>
                            <multiselect
                                v-model="location.department_id"
                                :options="departments.map((type) => type.id)"
                                :custom-label="(opt) => departments.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ? departments.find((x) => x.id == opt).name : departments.find((x) => x.id == opt).name_e
                                    : null
                                "
                            >
                            </multiselect>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("employee_branch") }}
                            </label>
                            <multiselect
                                v-model="location.branch_id"
                                :options="branches.map((type) => type.id)"
                                :custom-label="(opt) => branches.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ? branches.find((x) => x.id == opt).name : branches.find((x) => x.id == opt).name_e
                                    : null
                                "
                            >
                            </multiselect>
                        </div>
                    </div>
                </div>
            </form>
        </b-modal>
        <!--  /search   -->
        <b-modal
            :id="id"
            :title="type != 'edit'?getCompanyKey('time_employee_create_form'):getCompanyKey('time_employee_edit_form')"
            title-class="font-18"
            size="lg"
            :hide-footer="true"
            body-class="bankAccount"
            @show="resetModal"
            @hidden="resetModalHidden"
        >
            <form>
                <loader size="large" v-if="isCustom && !isPage" />
                <div class="card">
                    <div class="card-body">
                        <div class="mb-1 d-flex justify-content-end">
                            <b-button
                                v-if="type != 'edit'"
                                variant="success"
                                :disabled="!is_disabled"
                                @click.prevent="resetForm"
                                type="button"
                                :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']"
                            >
                                {{ $t("general.AddNewRecord") }}
                            </b-button>
                            <template v-if="!is_disabled">
                                <b-button
                                    variant="success"
                                    type="submit"
                                    class="mx-1"
                                    v-if="!isLoader"
                                    @click.prevent="AddSubmit"
                                >
                                    {{ type != "edit" ? $t("general.Add") : $t("general.edit") }}
                                </b-button>
                                <b-button variant="success" class="mx-1" disabled v-else>
                                    <b-spinner small></b-spinner>
                                    <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                </b-button>
                            </template>
                            <b-button
                                @click.prevent="resetModalHidden"
                                variant="danger"
                                type="button"
                            >
                                {{ $t("general.Cancel") }}
                            </b-button>
                        </div>
                    </div>
                    <b-tabs nav-class="nav-tabs nav-bordered">
                        <b-tab :title="$t('general.DataEntry')" active>
                            <div class="row justify-content-center">
                                <div class="col-md-8" v-if="isVisible('start_from')">
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("time_employee_start_from") }}
                                            <span v-if="isRequired('start_from')" class="text-danger">*</span>
                                        </label>
                                        <date-picker
                                            v-model="$v.create.start_from.$model"
                                            type="date"
                                            format="YYYY-MM-DD"
                                            valueType="format"
                                        ></date-picker>
                                        <div
                                            v-if="$v.create.start_from.$error"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.start_from">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.start_from"
                                                :key="index"
                                            >{{ errorMessage }}</ErrorMessage
                                            >
                                        </template>
                                    </div>
                                </div>
                                <div v-if="isVisible('timetables_header_id')" class="col-md-8" >
                                    <div class="form-group">
                                        <label class="my-1 mr-2">
                                            {{ getCompanyKey("time_employee_timetables_header") }}
                                            <span v-if="isRequired('timetables_header_id')" class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            @input="showTimeHeaderModal"
                                            v-model="$v.create.timetables_header_id.$model"
                                            :options="types.map((type) => type.id)"
                                            :custom-label=" (opt) => types.find((x) => x.id == opt)?
                                                $i18n.locale == 'ar' ? types.find((x) => x.id == opt).name : types.find((x) => x.id == opt).name_e
                                                : null
                                            "
                                            :class="{'is-invalid': $v.create.timetables_header_id.$error || errors.timetables_header_id,'is-valid': !$v.create.timetables_header_id.$invalid && !errors.timetables_header_id,}"
                                        >
                                        </multiselect>
                                        <div
                                            v-if="$v.create.timetables_header_id.$error"
                                            class="text-danger"
                                        >
                                            {{ $t("general.fieldIsRequired") }}
                                        </div>
                                        <template v-if="errors.timetables_header_id">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.timetable_types_id"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </b-tab>
                        <b-tab
                            :disabled="!header_id"
                            :title="$t('general.details')"
                        >
                            <div class="d-flex justify-content-end" v-if="type != 'edit'">
                                <div>
                                    <b-button
                                        v-if="!is_disabledDetail"
                                        @click.prevent="$bvModal.show(`search`)"
                                        variant="primary"
                                        class="mx-1 font-weight-bold"
                                    >
                                        {{ $t('general.Search') }}
                                        <i class="fe-search"></i>
                                    </b-button>
                                    <b-button
                                        variant="success"
                                        type="button"
                                        class="mx-1"
                                        v-if="!isLoaderDetail"
                                        @click.prevent="AddSubmitDateil"
                                    >
                                        {{ type != 'edit'? $t("general.Add"): $t("general.edit") }}
                                    </b-button>
                                    <b-button variant="success" class="mx-1" disabled v-else>
                                        <b-spinner small></b-spinner>
                                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                    </b-button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end" v-else>
                                <div>
                                    <b-button
                                        v-if="!is_disabledDetail"
                                        @click.prevent="$bvModal.show(`search`)"
                                        variant="primary"
                                        class="mx-1 font-weight-bold"
                                    >
                                        {{ $t('general.Search') }}
                                        <i class="fe-search"></i>
                                    </b-button>
                                </div>
                            </div>
                            <div class="row" style="overflow-y: scroll;height: 350px">
                                <!-- selet panals -->
                                <div class="col-md-6">
                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom position-relative">
                                        <div>
                                            <div class="d-inline-block" style="font-size: 13px">
                                                {{ employeePagination.from }}-{{ employeePagination.to }} /
                                                {{ employeePagination.total }}
                                            </div>
                                            <div class="d-inline-block">
                                                <a
                                                    href="javascript:void(0)"
                                                    :style="{
                                                          'pointer-events':
                                                            employeePagination.current_page > 1 ? '' : 'none',
                                                        }"
                                                    @click.prevent="getEmployees(employeePagination.current_page - 1)"
                                                >
                                                    <span>&lt;</span>
                                                </a>
                                                <input
                                                    type="text"
                                                    @keyup.enter="getEmployessPagination()"
                                                    v-model="current_page_emp"
                                                    disabled
                                                    class="pagination-current-page"
                                                />
                                                <a
                                                    href="javascript:void(0)"
                                                    :style="{
                                                          'pointer-events':
                                                            (employeePagination.last_page ==
                                                            employeePagination.current_page) || !employeePagination
                                                              ? 'none'
                                                              : '',
                                                        }"
                                                    @click.prevent="getEmployees(employeePagination.current_page + 1)"
                                                >
                                                    <span>&gt;</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div>
                                            <h4>{{ $t('general.SelectEmployee') }}</h4>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                    <table class="table table-borderless table-hover table-centered m-0">
                                        <thead>
                                        <tr>
                                            <th>
                                                <div class="d-flex justify-content-center">
                                                    <span>#</span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ $t('general.name') }}</span>
                                                </div>
                                            </th>
                                            <th >
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("employee_department") }}</span>
                                                </div>
                                            </th>
                                            <th >
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("employee_branch") }}</span>
                                                </div>
                                            </th>
                                            <th>
                                                {{ $t("general.Action") }}
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody v-if="selectEmployees.length > 0">
                                        <tr
                                            v-for="(data, index) in selectEmployees"
                                            :key="data.id"
                                            class="body-tr-custom"
                                        >
                                            <td scope="col">
                                                {{ data.id }}
                                            </td>
                                            <td scope="col">
                                                {{ $i18n.locale == 'ar' ? data.name : data.name_e }}
                                            </td>
                                            <td scope="col">
                                                {{  data.department ? $i18n.locale == 'ar' ? data.department.name : data.department.name_e : '-' }}
                                            </td>
                                            <td scope="col">
                                                {{ data.branch ? $i18n.locale == 'ar' ? data.branch.name : data.branch.name_e : '-' }}
                                            </td>
                                            <td scope="col" style="width: 0">
                                                <div class="form-check custom-control">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        :value="data.id"
                                                        @change="checkRowEmployee(data)"
                                                        v-model="CheckAllEmployees"
                                                        style="width: 17px; height: 17px"
                                                    />
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tbody v-else>
                                        <tr>
                                            <th class="text-center" colspan="11">
                                                {{ $t("general.notDataFound") }}
                                            </th>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- panals package -->
                                <div class="col-md-6">
                                    <!-- start Pagination -->
                                    <div class="d-inline-flex align-items-center pagination-custom position-relative">
                                        <div>
                                            <div class="d-inline-block" style="font-size: 13px">
                                                {{ employeePackagesPaginatation.from }}-{{ employeePackagesPaginatation.to }} /
                                                {{ employeePackagesPaginatation.total }}
                                            </div>
                                            <div class="d-inline-block">
                                                <a
                                                    href="javascript:void(0)"
                                                    :style="{
                                                          'pointer-events':
                                                            current_page_emp_pack > 1 ? '' : 'none',
                                                        }"
                                                    @click.prevent="paginate(current_page_emp_pack - 1)"
                                                >
                                                    <span>&lt;</span>
                                                </a>
                                                <input
                                                    type="text"
                                                    @keyup.enter="paginate(current_page_emp_pack)"
                                                    v-model="current_page_emp_pack"
                                                    class="pagination-current-page"
                                                />
                                                <a
                                                    href="javascript:void(0)"
                                                    :style="{
                                                          'pointer-events':
                                                            (employeePackagesPaginatation.last_page ==
                                                            current_page_emp_pack) || !employeePackagesPaginatation
                                                              ? 'none'
                                                              : '',
                                                        }"
                                                    @click.prevent="paginate(current_page_emp_pack + 1)"
                                                >
                                                    <span>&gt;</span>
                                                </a>
                                            </div>
                                        </div>
                                        <div>
                                            <h4>{{ $t('general.Selected') }}</h4>
                                        </div>
                                    </div>
                                    <!-- end Pagination -->
                                    <table class="table table-borderless table-hover table-centered">
                                        <thead>
                                        <tr>
                                            <th>
                                                <div class="d-flex justify-content-center">
                                                    <span>#</span>
                                                </div>
                                            </th>
                                            <th>
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ $t('general.name') }}</span>
                                                </div>
                                            </th>
                                            <th >
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("employee_department") }}</span>
                                                </div>
                                            </th>
                                            <th >
                                                <div class="d-flex justify-content-center">
                                                    <span>{{ getCompanyKey("employee_branch") }}</span>
                                                </div>
                                            </th>
                                            <th>
                                                {{ $t("general.Action") }}
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody v-if="employeePackages.length > 0">
                                        <tr
                                            v-for="(data, index) in employeePackages"
                                            :key="data.id"
                                            class="body-tr-custom"
                                        >
                                            <td scope="col">
                                                {{ data.id }}
                                            </td>
                                            <td scope="col">
                                                {{ $i18n.locale == 'ar' ? data.name : data.name_e }}
                                            </td>
                                            <td scope="col">
                                                {{  data.department ? $i18n.locale == 'ar' ? data.department.name : data.department.name_e : '-' }}
                                            </td>
                                            <td scope="col">
                                                {{ data.branch ? $i18n.locale == 'ar' ? data.branch.name : data.branch.name_e : '-' }}
                                            </td>
                                            <td scope="col" style="width: 0">
                                                <div class="form-check custom-control">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        :value="data.id"
                                                        @change="checkRowEmployee(data)"
                                                        v-model="CheckAllEmployees"
                                                        style="width: 17px; height: 17px"
                                                    />
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tbody v-else>
                                        <tr>
                                            <th class="text-center" colspan="11">
                                                {{ $t("general.notDataFound") }}
                                            </th>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </b-tab>
                    </b-tabs>
                </div>
            </form>
        </b-modal>
    </div>
</template>

<script>
import {integer, maxLength, minLength, required, requiredIf,maxValue} from "vuelidate/lib/validators";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import DatePicker from "vue2-datepicker";
import {formatDateOnly} from "../../../helper/startDate";
import TimeTableAttend from "./time-table-attendance";
export default {
    name: "time-table-attendance",
    components: {
        ErrorMessage,
        loader,
        Multiselect,
        DatePicker,
        TimeTableAttend
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission:{},url: {default: '/hr/employees-time-tables-header'}
    },
    data() {
        return {
            fields: [],
            isLoader: false,
            departments: [],
            branches: [],
            create: {
                start_from: this.formatDate(new Date()),
                timetables_header_id: null
            },
            location: {name: '',department_id: null,branch_id: null},
            errors: {},
            employeePagination: {},
            selectEmployees: [],
            CheckAllEmployees: [],
            current_page_emp: 1,
            current_page_emp_pack: 1,
            employeePackagesPaginatation: {},
            allEmployeePackages: [],
            employeePackages: [],
            is_disabled: false,
            isCustom: false,
            isLoaderDetail:false,
            header_id: null,
            detail_id: null,
            is_disabledDetail: false,
            types: []
        }
    },
    validations: {
        create: {
            start_from: {
                required: requiredIf(function (model) {
                    return this.isRequired("start_from");
                }), minLength: minLength(2), maxLength: maxLength(100),
            },
            timetables_header_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("timetables_header_id");
                })
            },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        showTimeHeaderModal() {
            if (this.create.timetables_header_id == 0) {
                this.$bvModal.show("timetables-header-create-time-table");
                this.create.timetables_header_id = null;
            }
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        getDepartnent() {
            this.isLoader = true;

            adminApi
                .get(`/depertments/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    this.departments = l;
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
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getEmployeesEdit(id) {
            this.isLoader = true;

            adminApi
                .get(`/hr/employees-time-tables-detail/get-employees-time-tables-details?employees_timetables_header_id=${id}`)
                .then((res) => {
                    let l = res.data.data;
                    if(l && l.length > 0){
                        this.allEmployeePackages = l;
                        l.forEach((el,index) => {
                            this.CheckAllEmployees.push(el.id);
                        });
                        this.paginate();
                    }
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/hr_timetables_header`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })
                .finally(() => {
                    this.isCustom = false;
                });
        },
        getTimeHeder() {
            this.isLoader = true;
            adminApi
                .get(`/hr/time-tables-header/get-drop-down`)
                .then((res) => {
                    let l =res.data.data;
                    if (this.isPermission("create City")) {
                        l.unshift({
                            id: 0,
                            name: "اضف دوام جديد",
                            name_e: "Add Time Table",
                        });
                    }
                    this.types = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        isVisible(fieldName) {
            if(!this.isPage){
                let res = this.fields.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_visible == 1 ? true : false;
            }else {
                return this.isVisiblePage(fieldName);
            }
        },
        isRequired(fieldName) {
            if(!this.isPage) {
                let res = this.fields.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_required == 1 ? true : false;
            }else {
                return this.isRequiredPage(fieldName);
            }
        },
        defaultData(edit = null){
            this.time_details = [];
            this.create = {
                start_from: this.formatDate(new Date()),
                timetables_header_id: null
            };
            this.location = {name: '',department_id: null,branch_id: null};
            this.header_id = null;
            this.isLoaderDetail = false;
            this.detail_id = null;
            this.is_disabled = false;
            this.is_disabledDetail = false;
            this.errors = {};
            this.employeePagination = {};
            this.selectEmployees = [];
            this.CheckAllEmployees = [];
            this.current_page_emp = 1;
            this.current_page_emp_pack = 1
            this.employeePackagesPaginatation = {};
            this.allEmployeePackages = [];
            this.employeePackages = [];
            this.$nextTick(() => {
                this.$v.$reset();
            });
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        resetModal() {
            this.defaultData();
            setTimeout( async () => {
                if(this.type != 'edit'){
                    if(!this.isPage)  await this.getCustomTableFields();
                    if(this.isVisible('timetables_header_id'))  this.getTimeHeder();
                    this.getDepartnent();
                    this.getBranches();
                }else {
                    this.time_details = [];
                    if(this.idObjEdit.dataObj){
                        let bankAccount = this.idObjEdit.dataObj;
                        this.header_id = bankAccount.id;
                        this.create.start_from= bankAccount.start_from;
                        this.create.timetables_header_id= bankAccount.timetables_header_id;
                        if(this.isVisible('timetables_header_id'))  this.getTimeHeder();
                        this.getEmployeesEdit(bankAccount.id)
                        this.getDepartnent();
                        this.getBranches();
                        this.errors = {};
                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                if(this.type != 'edit'){
                    adminApi
                        .post(this.url, { ...this.create, company_id: this.company_id })
                        .then((res) => {
                            this.is_disabled = true;
                            this.header_id = res.data.data.id;
                            if(!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");
                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
                            }, 200);

                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }else {
                    adminApi
                        .put(`${this.url}/${this.header_id}`, {
                            ...this.create,
                            employee_time_details:this.CheckAllEmployees,
                            company_id: this.$store.getters["auth/company_id"],
                        })
                        .then((res) => {
                            this.$emit("getDataTable");
                            setTimeout(() => {
                                Swal.fire({
                                    icon: "success",
                                    text: `${this.$t("general.Editsuccessfully")}`,
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false
                        });
                }

            }
        },
        AddSubmitDateil() {
            this.$v.time_details.$touch();

            if (this.$v.time_details.$invalid) {
                return;
            } else {
                this.isLoaderDetail = true;
                this.errors = {};
                this.is_disabledDetail = false;
                if(this.type != 'edit'){

                    adminApi
                        .post('/hr/employees-time-tables-detail', {
                            employee_time_details:this.CheckAllEmployees,
                            employees_timetables_header_id:this.header_id,
                            company_id: this.company_id,
                        })
                        .then((res) => {
                            this.is_disabledDetail = true;
                            if(!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");
                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
                            }, 200);

                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoaderDetail = false;
                        });
                }else {
                    adminApi
                        .put(`/hr/employees-time-tables-detail/${this.detail_id}`, {
                            ...this.time_details,
                            company_id: this.$store.getters["auth/company_id"],
                            timetables_header_id: this.header_id
                        })
                        .then((res) => {
                            this.$emit("getDataTable");
                            setTimeout(() => {
                                Swal.fire({
                                    icon: "success",
                                    text: `${this.$t("general.Editsuccessfully")}`,
                                    showConfirmButton: false,
                                    timer: 1500,
                                });
                            }, 500);
                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoaderDetail = false;
                        });
                }

            }
        },
        checkRowEmployee(data) {
            let panel = this.allEmployeePackages.find((el) => el.id == data.id);
            if (!panel) {
                this.allEmployeePackages.push(data);
            } else {
                let index = this.allEmployeePackages.findIndex((el) => el.id == data.id);
                this.allEmployeePackages.splice(index, 1);
            }
            this.paginate(this.current_page_emp_pack ? this.current_page_emp_pack : 1);
        },
        getEmployees(page = null) {
            this.isLoader = true;
            let filter = '';
            filter += this.location.name ? `&name=${this.location.name}` : '';
            filter += this.location.branch_id ? `&branch_id=${this.location.branch_id}` : '';
            filter += this.location.department_id ? `&department_id=${this.location.department_id}` : '';

            adminApi
                .get(
                    `/hr/employees-time-tables-detail/get-employees?page=${page ?? this.current_page_emp}&per_page=${30}&packages=1${filter}`
                )
                .then((res) => {
                    let l = res.data;
                    this.selectEmployees = l.data;
                    this.employeePagination = l.pagination;
                    this.current_page_emp = l.pagination.current_page;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');

                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getEmployessPagination() {

            if (
                this.current_page_emp <= this.employeePagination.last_page &&
                this.current_page_emp != this.employeePagination.current_page &&
                this.current_page_emp
            ) {
                this.isLoader = true;
                let filter = '';
                filter += this.location.name ? `&name=${this.location.name}` : '';
                filter += this.location.branch_id ? `&branch_id=${this.location.branch_id}` : '';
                filter += this.location.department_id ? `&department_id=${this.location.department_id}` : '';


                adminApi
                    .get(
                        `/hr/employees-time-tables-detail/get-employees?page=${20}&per_page=${7}&packages=1${filter}`
                    )
                    .then((res) => {
                        let l = res.data;
                        this.selectEmployees = l.data;
                        this.employeePagination = l.pagination;
                        this.current_page_emp = l.pagination.current_page;
                    })
                    .catch((err) => {
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                        });
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
            }
        },
        // paginate
        paginate(p = 1){

            const page = p;
            this.current_page_emp_pack = page;
            const limit = 20;
            const skip = (page - 1) * limit;
            const endIndex = page * limit;
            const total = this.allEmployeePackages.length;

            // Pagination result
            this.employeePackagesPaginatation.total = total;
            this.employeePackagesPaginatation.limit = limit;
            this.employeePackagesPaginatation.last_page = Math.ceil(total / limit);
            this.employeePackagesPaginatation.to = skip? (skip + limit) : limit;
            this.employeePackagesPaginatation.from = skip ? skip : 1;
            this.employeePackages = [];
            this.employeePackages = this.allEmployeePackages.slice(skip,skip+limit);

        }
    },
};
</script>
<style scoped>
form {
    position: relative;
}

.nav-bordered {
    border: unset !important;
}

.nav {
    background-color: #dff0fe;
}

.tab-content {
    padding: 70px 0 40px;
    min-height: 300px;
    background-color: #f5f5f5;
    position: relative;
}

.nav-tabs .nav-link {
    border: 1px solid #b7b7b7 !important;
    background-color: #d7e5f2;
    border-bottom: 0 !important;
    margin-bottom: 1px;
}

.nav-tabs .nav-link.active,
.nav-tabs .nav-item.show .nav-link {
    color: #000;
    background-color: hsl(0deg 0% 96%);
    border-bottom: 0 !important;
}

.img-thumbnail {
    max-height: 400px !important;
}
form {
    position: relative;
}


.text-secondary-d1 {
    color: #728299 !important;
}

.brc-default-l1 {
    border-color: #dce9f0 !important;
}

.ml-n1,
.mx-n1 {
    margin-left: -.25rem !important;
}

.mr-n1,
.mx-n1 {
    margin-right: -.25rem !important;
}

.mb-4,
.my-4 {
    margin-bottom: 1.5rem !important;
}

hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
    border-top: 1px solid rgba(0, 0, 0, .1);
}

.text-grey-m2 {
    color: #888a8d !important;
}

.text-success-m2 {
    color: #86bd68 !important;
}

.font-bolder,
.text-600 {
    font-weight: 600 !important;
}

.text-110 {
    font-size: 110% !important;
}

.text-blue {
    color: #478fcc !important;
}

.pb-25,
.py-25 {
    padding-bottom: .75rem !important;
}

.pt-25,
.py-25 {
    padding-top: .75rem !important;
}

.bgc-default-tp1 {
    background-color: rgba(121, 169, 197, .92) !important;
}

.bgc-default-l4,
.bgc-h-default-l4:hover {
    background-color: #f3f8fa !important;
}

.page-header .page-tools {
    -ms-flex-item-align: end;
    align-self: flex-end;
}

.btn-light {
    color: #757984;
    background-color: #f5f6f9;
    border-color: #dddfe4;
}

.w-2 {
    width: 1rem;
}

.text-120 {
    font-size: 120% !important;
}

.text-primary-m1 {
    color: #4087d4 !important;
}

.text-danger-m1 {
    color: #dd4949 !important;
}

.text-blue-m2 {
    color: #68a3d5 !important;
}

.text-150 {
    font-size: 150% !important;
}

.text-60 {
    font-size: 60% !important;
}

.text-grey-m1 {
    color: #7b7d81 !important;
}

.align-bottom {
    vertical-align: bottom !important;
}

.row-danger {
    background-color:#f6a9a9 !important;
}
</style>

