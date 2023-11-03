<template>
    <div>
        <Employee
            :id="'employee-create-documentReason'"
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getEmployees"
            :isPage="false" type="create" :isPermission="isPermission"
        />
        <DocumentStatus
            :id="'document-status-create-documentReason'"
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
            @created="getDocumentStatus" :isPage="false" type="create"
            :isPermission="isPermission"
        />
        <!--  create   -->
        <b-modal
            :id="id" size="lg"
            :title="type != 'edit'?getCompanyKey('document_reason_create_form'):getCompanyKey('document_reason_edit_form')"
            title-class="font-18" body-class="p-4 "
            :hide-footer="true" @show="resetModal" @hidden="resetModalHidden"
        >
            <form>
                <loader size="large" v-if="isCustom && !isPage" />
                <div class="mb-3 d-flex justify-content-end">
                    <b-button v-if="type != 'edit'" variant="success" :disabled="!is_disabled" @click.prevent="resetForm" type="button"
                              :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                        {{ $t("general.AddNewRecord") }}
                    </b-button>
                    <template v-if="!is_disabled">
                        <b-button variant="success" type="button" class="mx-1" v-if="!isLoader" @click.prevent="AddSubmit">
                            {{ type != 'edit'? $t("general.Add"): $t("general.edit") }}
                        </b-button>

                        <b-button variant="success" class="mx-1" disabled v-else>
                            <b-spinner small></b-spinner>
                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                        </b-button>
                    </template>
                    <b-button variant="danger" type="button" @click.prevent="resetModalHidden">
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-6" v-if="isVisible('employee_id')">
                        <div class="form-group">
                            <label>{{ getCompanyKey('employee') }}<span class="text-danger" v-if="isRequired('employee_id')">*</span></label>
                            <multiselect
                                @input="showEmployeeModal"
                                v-model="create.employee_id"
                                :options="employees.map((type) => type.id)"
                                :custom-label=" (opt) =>
                                employees.find((x) => x.id == opt)?$i18n.locale == 'ar' ?
                                employees.find((x) => x.id == opt).name :
                                employees.find((x) => x.id == opt).name_e: null"
                                :class="{'is-invalid': $v.create.employee_id.$error || errors.employee_id,'is-valid': !$v.create.employee_id.$invalid && !errors.employee_id,}"
                            >
                            </multiselect>
                            <template v-if="errors.employee_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.employee_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('decision_id')">
                        <div class="form-group">
                            <label>{{ getCompanyKey('document_decision') }}<span class="text-danger" v-if="isRequired('decision_id')">*</span></label>
                            <multiselect
                                @input="showStatusModal"
                                v-model="create.decision_id"
                                :options="status.map((type) => type.id)"
                                :custom-label=" (opt) => status.find((x) => x.id == opt)? $i18n.locale == 'ar' ? status.find((x) => x.id == opt).name : status.find((x) => x.id == opt).name_e : null"
                                :class="{'is-invalid': $v.create.decision_id.$error || errors.decision_id,'is-valid': !$v.create.decision_id.$invalid && !errors.decision_id,}"
                            >
                            </multiselect>
                            <template v-if="errors.decision_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.decision_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('decision_date')">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("document_decision_date") }}
                                <span v-if="isRequired('decision_date')" class="text-danger">*</span>
                            </label>
                            <date-picker
                                type="date"
                                v-model="$v.create.decision_date.$model"
                                format="YYYY-MM-DD"
                                valueType="format"
                                :confirm="false"
                                :class="{
                                'is-invalid': $v.create.decision_date.$error || errors.decision_date,
                                'is-valid': !$v.create.decision_date.$invalid && !errors.decision_date,
                            }">
                            </date-picker>
                            <template v-if="errors.decision_date">
                                <ErrorMessage v-for="(errorMessage, index) in errors.decision_date" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('approval_time')">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("document_approval_time") }}
                                <span v-if="isRequired('approval_time')" class="text-danger">*</span>
                            </label>
                            <date-picker
                                type="time"
                                v-model="$v.create.approval_time.$model"
                                format="HH:mm:ss"
                                valueType="format"
                                :confirm="false"
                                :class="{
                                'is-invalid': $v.create.approval_time.$error || errors.approval_time,
                                'is-valid': !$v.create.approval_time.$invalid && !errors.approval_time,
                            }">
                            </date-picker>
                            <template v-if="errors.approval_time">
                                <ErrorMessage v-for="(errorMessage, index) in errors.approval_time" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('document_serial_number')">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey("document_serial_number") }}
                                <span v-if="isRequired('document_serial_number')" class="text-danger">*</span>
                            </label>
                            <div dir="rtl">
                                <input
                                    type="text"
                                    class="form-control"
                                    data-create="1"
                                    v-model="$v.create.document_serial_number.$model"
                                    :class="{
                              'is-invalid': $v.create.document_serial_number.$error || errors.document_serial_number,
                              'is-valid': !$v.create.document_serial_number.$invalid && !errors.document_serial_number,
                            }"
                                    id="field-1"
                                />
                            </div>
                            <template v-if="errors.document_serial_number">
                                <ErrorMessage v-for="(errorMessage, index) in errors.document_serial_number" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('notes')">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("document_reason_notes") }}
                                <span v-if="isRequired('notes')" class="text-danger">*</span>
                            </label>
                            <textarea
                                v-model="$v.create.notes.$model"
                                class="form-control"
                                :maxlength="1000"
                                rows="5"
                                :class="{
                                'is-invalid': $v.create.notes.$error || errors.notes,
                                'is-valid': !$v.create.notes.$invalid && !errors.notes,
                            }"
                            ></textarea>
                            <template v-if="errors.notes">
                                <ErrorMessage v-for="(errorMessage, index) in errors.notes" :key="index">{{ errorMessage }}
                                </ErrorMessage>
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
import {requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import {formatDateOnly} from "../../../helper/startDate";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Employee from "./employee";
import DocumentStatus from "./document-status";
import Multiselect from "vue-multiselect";
import DatePicker from "vue2-datepicker";

export default {
    name: "document-reason",
    mixins: [transMixinComp,successError],
    components: {
        ErrorMessage,
        loader,
        Employee,
        DocumentStatus,
        Multiselect,
        DatePicker
    },
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/document-approval-details'},
    },
    data() {
        return {
            isCustom:false,
            fields: [],
            employees: [],
            status: [],
            isLoader: false,
            create: {
                document_serial_number: "",
                notes: "",
                decision_date: this.formatDate(new Date()),
                approval_time: "",
                employee_id: null,
                decision_id: null,
            },
            company_id: null,
            is_disabled: false,
            errors: []
        };
    },
    validations: {
        create: {
            document_serial_number: { required: requiredIf(function (model) {
                    return this.isRequired("document_serial_number");
                }) },
            notes: { required: requiredIf(function (model) {
                    return this.isRequired("notes");
                }) },
            decision_date: { required: requiredIf(function (model) {
                    return this.isRequired("decision_date");
                }) },
            approval_time: { required: requiredIf(function (model) {
                    return this.isRequired("approval_time");
                }) },
            employee_id: { required: requiredIf(function (model) {
                    return this.isRequired("employee_id");
                }) },
            decision_id: { required: requiredIf(function (model) {
                    return this.isRequired("decision_id");
                }) },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/general_document_approval_details`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
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
        formatDate(value) {
            return formatDateOnly(value);
        },
        resetDataCreate() {
            this.create = {
                document_serial_number: "",
                notes: "",
                decision_date: this.formatDate(new Date()),
                approval_time: "",
                employee_id: null,
                decision_id: null,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
        },
        resetModalHidden() {
            this.resetDataCreate();
            this.$bvModal.hide(this.id);
        },
        resetModal() {
            this.resetDataCreate();
            setTimeout( async () => {
                if(this.type != 'edit'){
                    if(!this.isPage)  await this.getCustomTableFields();
                    if(this.isVisible('employee_id')) this.getEmployees();
                    if(this.isVisible('decision_id')) this.getDocumentStatus();
                }else {
                    if(this.idObjEdit.dataObj){
                        let unitStatus = this.idObjEdit.dataObj;
                        this.create.document_serial_number = unitStatus.document_serial_number;
                        this.create.notes = unitStatus.notes;
                        this.create.decision_date = unitStatus.decision_date;
                        this.create.approval_time = unitStatus.approval_time;
                        if(this.isVisible('employee_id')) this.getEmployees();
                        this.create.employee_id = unitStatus.employee_id;
                        if(this.isVisible('decision_id')) this.getDocumentStatus();
                        this.create.decision_id = unitStatus.decision_id;
                    }
                }
            },50);
        },
        resetForm() {
            this.resetDataCreate();
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
                        .post(this.url, {
                            company_id: this.$store.getters["auth/company_id"],
                            ...this.create
                        })
                        .then((res) => {
                            this.is_disabled = true;
                            if (!this.isPage)
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
                                this.errorFun('Error', 'Thereisanerrorinthesystem');
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
        getEmployees() {
            adminApi
                .get(`/employees`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضاف موظف", name_e: "Add Employee" });
                    this.employees = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                });
        },
        showEmployeeModal() {
            if (this.create.employee_id == 0) {
                this.$bvModal.show("employee-create-documentReason");
                this.create.employee_id = null;
            }
        },
        getDocumentStatus() {
            adminApi
                .get(`/document-statuses`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({ id: 0, name: "اضاف قرار", name_e: "Add Decision" });
                    this.status = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                });
        },
        showStatusModal() {
            if (this.create.decision_id == 0) {
                this.$bvModal.show("document-status-create-documentReason");
                this.create.decision_id = null;
            }
        },
    },
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
