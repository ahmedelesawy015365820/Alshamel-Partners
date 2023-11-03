<template>
    <div>
        <Employee
            :id="'employee-create-internal-sales'"
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getEmployees"
            :isPage="false" type="create" :isPermission="isPermission"
        />
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit'?getCompanyKey('internal_sale_man_create_form'):getCompanyKey('internal_sale_man_edit_form')"
            title-class="font-18"
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
                        :class="[
                      'font-weight-bold px-2',
                      is_disabled ? 'mx-2' : '',
                    ]"
                    >
                        {{ $t("general.AddNewRecord") }}
                    </b-button>
                    <template v-if="!is_disabled">
                        <b-button
                            variant="success"
                            type="button"
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
                    <!-- Emulate built in modal footer ok and cancel button actions -->

                    <b-button
                        variant="danger"
                        type="button"
                        @click.prevent="resetModalHidden"
                    >
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-12" v-if="isVisible('employee_id')" >
                        <div class="form-group">
                            <label class="my-1 mr-2">
                                {{ getCompanyKey("employee") }}
                                <span v-if="isRequired('employee_id')"  class="text-danger">*</span>
                            </label>
                            <multiselect
                                @input="showEmployeeModal"
                                v-model="create.employee_id"
                                :options="employees.map((type) => type.id)"
                                :custom-label="(opt) => employees.find((x) => x.id == opt)? employees.find((x) => x.id == opt).name: null"
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.employee_id.$error || errors.employee_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.employee_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.employee_id"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('is_active')" >
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("internal_sale_man_status") }}
                                <span v-if="isRequired('is_active')"  class="text-danger">*</span>
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
                                    value="active"
                                >{{ $t("general.Active") }}</b-form-radio
                                >
                                <b-form-radio
                                    class="d-inline-block m-1"
                                    v-model="$v.create.is_active.$model"
                                    name="some-radios"
                                    value="inactive"
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
import {requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Employee from "./employee";

export default {
    name: "internalSalesMen",
    mixins: [transMixinComp,successError],
    components: {
        ErrorMessage,
        loader,
        Employee
    },
    data() {
        return {
            fields: [],
            isCustom: false,
            isLoader: false,
            create: {
                is_active: "active",
                employee_id: null,
            },
            company_id: null,
            errors: {},
            employees: [],
            is_disabled: false,
        };
    },
    validations: {
        create: {
            employee_id: { required: requiredIf(function (model) {
                    return this.isRequired("employee_id");
                })  },
            is_active: { required: requiredIf(function (model) {
                    return this.isRequired("is_active");
                })  },
        },
    },
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/internal-salesmen'}
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/general_internal_salesman`)
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
        defaultData(edit = null){
            this.create = {
                is_active: "active",
                employee_id: null,
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
                    if(this.isVisible('employee_id'))  this.getEmployee();
                }else {
                    if(this.idObjEdit.dataObj){
                        let module = this.idObjEdit.dataObj;
                        this.errors = {};
                        if(this.isVisible('employee_id'))   this.getEmployee();
                        if(module.employee) this.create.employee_id = module.employee.id;
                        this.create.is_active = module.is_active;
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
                if(this.type != 'edit'){
                    adminApi
                        .post(this.url, { ...this.create, company_id: this.company_id })
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
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false;
                        });
                }else {
                    adminApi
                        .post(`${this.url}/${this.idObjEdit.idEdit}`, {
                            ...this.create
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
        getEmployee() {
            this.isLoader = true;

            adminApi
                .get(`/employees`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Employee')){
                        l.unshift({ id: 0, name: "اضف موظف", name_e: "Add Employee" });
                    }
                    this.employees = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        showEmployeeModal() {
            if (this.create.employee_id == 0) {
                this.$bvModal.show("employee-create-internal-sales");
                this.create.employee_id = null;
            }
        },
    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
