<template>
    <div>
        <Department
            :id="'department-create-periodic'" :isPage="false" type="create" :isPermission="isPermission"
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getDepartment"
        />
<!--        <Task-->
<!--            :id="'task-create-periodic'"-->
<!--            :companyKeys="companyKeys"-->
<!--            :defaultsKeys="defaultsKeys"-->
<!--            @created="getTask"-->
<!--        />-->
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit'?getCompanyKey('periodic_maintenance_create_form'):getCompanyKey('periodic_maintenance_edit_form')"
            title-class="font-18"
            size="lg"
            body-class="p-4 "
            :hide-footer="true"
            @show="resetModal"
            @hidden="resetModalHidden"
        >
            <form>
                <loader size="large" v-if="isCustom && !isPage" />
                <div class="mb-3 d-flex justify-content-end">
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
                    <!-- Emulate built in modal footer ok and cancel button actions -->
                    <template v-if="!is_disabled">
                        <b-button
                            variant="success"
                            type="submit"
                            class="mx-1"
                            v-if="!isLoader"
                            @click.prevent="AddSubmit"
                        >
                            {{ type != 'edit'? $t("general.Add"): $t("general.edit") }}
                        </b-button>

                        <b-button variant="success" class="mx-1" disabled v-else>
                            <b-spinner small></b-spinner>
                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                        </b-button>
                    </template>
                    <b-button
                        variant="danger"
                        type="button"
                        @click.prevent="resetModalHidden"
                    >
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-6" v-if="isVisible('department_id')">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("department") }}
                                <span v-if="isRequired('department_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                @input="showDepartmentModel"
                                v-model="$v.create.department_id.$model"
                                :options="departments.map((type) => type.id)"
                                :custom-label="(opt) => departments.find((x) => x.id == opt)?
                                   $i18n.locale == 'ar' ?departments.find((x) => x.id == opt).name:departments.find((x) => x.id == opt).name_e
                                    :null
                                "
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.department_id.$error || errors.department_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.department_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.department_id"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('task_id')">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("periodic_maintenance_task") }}
                                <span v-if="isRequired('task_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                @input="showTaskModel"
                                v-model="$v.create.task_id.$model"
                                :options="tasks.map((type) => type.id)"
                                :custom-label="(opt) => tasks.find((x) => x.id == opt)?
                                     tasks.find((x) => x.id == opt).task_title
                                    :null
                                "
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.task_id.$error || errors.task_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.task_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.country_id"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('name')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("priority_name_ar") }}
                                <span v-if="isRequired('name')" class="text-danger">*</span>
                            </label>
                            <div dir="rtl">
                                <input
                                    type="text"
                                    class="form-control"
                                    data-create="1"
                                    @keyup="arabicValueName(create.name)"
                                    v-model="$v.create.name.$model"
                                    :class="{
                            'is-invalid': $v.create.name.$error || errors.name,
                            'is-valid': !$v.create.name.$invalid && !errors.name,
                          }"
                                />
                            </div>
                            <template v-if="errors.name">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.name"
                                    :key="index"
                                >
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('name_e')">
                        <div class="form-group">
                            <label  class="control-label">
                                {{ getCompanyKey("priority_name_en") }}
                                <span v-if="isRequired('name_e')" class="text-danger">*</span>
                            </label>
                            <div dir="ltr">
                                <input
                                    type="text"
                                    class="form-control"
                                    data-create="2"
                                    @keyup="englishValueName(create.name_e)"
                                    v-model="$v.create.name_e.$model"
                                    :class="{
                            'is-invalid': $v.create.name_e.$error || errors.name_e,
                            'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                          }"
                                />
                            </div>
                            <template v-if="errors.name_e">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.name_e"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6"  v-if="isVisible('restart_period_id')">
                        <div class="form-group">
                            <label  class="control-label">
                                {{ getCompanyKey("periodic_maintenance_period") }}
                                <span  v-if="isRequired('restart_period_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                v-model="$v.create.restart_period_id.$model"
                                :options="restart_periods.map((type) => type.id)"
                                :custom-label="(opt) =>$i18n.locale == 'ar' ? restart_periods.find((x) => x.id == opt).name : restart_periods.find((x) => x.id == opt).name_e"
                            >
                            </multiselect>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('is_active')">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("periodic_maintenance_status") }}
                                <span v-if="isRequired('is_active')" class="text-danger">*</span>
                            </label>
                            <b-form-group
                                :class="{
                          'is-invalid': $v.create.is_active.$error || errors.is_active,
                          'is-valid': !$v.create.is_active.$invalid && !errors.is_active,
                        }"
                            >
                                <b-form-radio
                                    class="d-inline-block"
                                    v-model="$v.create.is_active.$model"
                                    name="some-radios"
                                    value="1"
                                >{{ $t("general.Active") }}</b-form-radio
                                >
                                <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.create.is_active.$model"
                                    name="some-radios"
                                    value="0"
                                >{{ $t("general.Inactive") }}</b-form-radio
                                >
                            </b-form-group>
                            <template v-if="errors.is_active">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.is_active"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                </div>
            </form>
        </b-modal>
        <!--  /create   -->
    </div>
</template>

<script>
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import {maxLength, minLength, requiredIf} from "vuelidate/lib/validators";
import Multiselect from "vue-multiselect";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import successError from "../../../helper/mixin/success&error";
import Department from "./departmentTask";

export default {
    name: "periodic_maintenance",
    mixins: [transMixinComp,successError],
    components: {
        Multiselect,
        ErrorMessage,
        loader,
        Department,
    },
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/periodic-maintenances'}

    },
    validations: {
        create: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }) , minLength: minLength(3), maxLength: maxLength(255) },
            name_e: { required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }) , minLength: minLength(3), maxLength: maxLength(255) },
            period: { required: requiredIf(function (model) {
                    return this.isRequired("period");
                }) },
            task_id: { required: requiredIf(function (model) {
                    return this.isRequired("task_id");
                }) , maxLength: maxLength(20) },
            department_id: { required: requiredIf(function (model) {
                    return this.isRequired("department_id");
                })  },
            restart_period_id : { required: requiredIf(function (model) {
                    return this.isRequired("restart_period_id");
                })  },
            is_active: { required: requiredIf(function (model) {
                    return this.isRequired("is_active");
                })  },
        },
    },
    data(){
        return {
            fields: [],
            tasks: [],
            restart_periods: [],
            departments: [],
            create: {
                name: '',
                name_e: '',
                task_id: null,
                department_id: null,
                restart_period_id: null,
                is_active: 1,
            },
            errors: {},
            is_disabled: false,
            company_id: null,
            isLoader: false,
            isCustom: false
        }
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
             await adminApi
                .get(`/customTable/table-columns/general_periodic_maintenances`)
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
        arabicValueName(txt){
            this.create.name = arabicValue(txt);
        },
        englishValueName(txt){
            this.create.name_e = englishValue(txt);
        },
        defaultData(edit = null){
            this.create = {
                name: '',
                name_e: '',
                task_id: null,
                department_id: null,
                restart_period_id: null,
                is_active: 1,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
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
                     if(this.isVisible('task_id'))  this.getTask();
                     if(this.isVisible('department_id'))  this.getDepartment();
                     if(this.isVisible('restart_period_id'))  this.getRestartPeriod();
                 }else {
                     if(this.idObjEdit.dataObj){
                         let externalSalesmen = this.idObjEdit.dataObj;
                         this.errors = {};
                         this.create.is_active = externalSalesmen.is_active;
                         this.create.name = externalSalesmen.name;
                         this.create.name_e = externalSalesmen.name_e;
                         if (this.isVisible("task_id"))  this.getTask();
                         this.create.task_id = externalSalesmen.task_id;
                         if (this.isVisible("department_id"))  this.getDepartment();
                         this.create.department_id = externalSalesmen.department_id;
                         if (this.isVisible("restart_period_id"))  this.getRestartPeriod();
                         this.create.restart_period_id = externalSalesmen.restart_period_id;
                     }
                 }
             },50);
         },
        resetForm() {
            this.create = {
                name: '',
                name_e: '',
                restart_period_id: null,
                task_id: null,
                department_id: null,
                is_active: 1,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
        },
        AddSubmit() {
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                if(this.type != 'edit') {
                    adminApi
                        .post(this.url, {...this.create, company_id: this.company_id})
                        .then((res) => {
                            this.is_disabled = true;
                            if(!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");
                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
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
                            this.isLoader = false;
                        });
                }else {
                    adminApi
                        .put(`${this.url}/${this.idObjEdit.idEdit}`, {
                            ...this.create,
                        })
                        .then((res) => {
                            this.$bvModal.hide(this.id);
                            this.$emit("getDataTable");
                            setTimeout(() => {
                                this.successFun('Editsuccessfully');
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
                            this.isLoader = false;
                        });
                }
            }
        },
        getTask() {
            this.isLoader = true;
             adminApi.get(`/tasks/get-drop-down`)
                .then((res) => {
                    let l = res.data;
                    this.tasks = l.data;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getDepartment() {
            this.isLoader = true;
             adminApi
                .get(`/depertments/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    this.departments = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getRestartPeriod() {
            this.isLoader = true;
             adminApi
                .get(`/restart-period/get-Restart-period-in-serial`)
                .then((res) => {
                    let l = res.data.message;
                    this.restart_periods = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        showDepartmentModel(){
            if (this.create.department_id == 0) {
                this.$bvModal.show("department-create-periodic");
                this.create.department_id = null;
            }
        },
        showTaskModel(){
            if (this.create.task_id == 0) {
                this.$bvModal.show("department-create-periodic");
                this.create.task_id = null;
            }
        }
    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
