<script>
import adminApi from "../../../api/adminAxios";
import { required, minLength, maxLength, decimal, minValue } from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import Multiselect from "vue-multiselect";
import successError from "../../../helper/mixin/success&error";
/**
 * Advanced Table component
 */
export default {
    components: {
        ErrorMessage,
        loader,
        Multiselect,
    },
    data() {
        return {
            isCustom: false,
            fields: [],
            departments: [],
            isLoader: false,
            create: {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                department_id: null
            },
            company_id: null,
            errors: {},
            is_disabled: false,
        };
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/department-tasks'},
        department_id: {default: null}
    },
    validations: {
        create: {
            name: { required, minLength: minLength(2), maxLength: maxLength(255) },
            name_e: { required, minLength: minLength(2), maxLength: maxLength(255) },
            description: {required},
            description_e: {required},
            department_id: {required}
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/general_depertment_tasks`)
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
        arabicValue(txt) {
            this.create.name = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.name_e = englishValue(txt);
        },
        arabicValueDescription(txt){
            this.create.description = arabicValue(txt);
        },
        englishValueDescription(txt){
            this.create.description_e = englishValue(txt);
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        defaultData(edit = null){
            this.create = {
                name: "",
                name_e: "",
                description: "",
                description_e: "",
                department_id: null
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
        },
        resetModal() {
            this.defaultData();
            setTimeout( async () => {
                if(this.type != 'edit'){
                    if(!this.isPage) await this.getCustomTableFields();
                    if (this.isVisible("department_id") && !this.department_id) this.this.getDepartnent();
                }else {
                    if(this.idObjEdit.dataObj){
                        let task = this.idObjEdit.dataObj;
                        this.errors = {};
                        if (this.isVisible("department_id")) this.getDepartnent();
                        this.create.name = task.name;
                        this.create.name_e = task.name_e;
                        this.create.description = task.description;
                        this.create.description_e = task.description_e;
                        this.create.department_id = task.department_id;
                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            if (!this.create.name) {
                this.create.name = this.create.name_e;
            }
            if (!this.create.name_e) {
                this.create.name_e = this.create.name;
            }
            if(!this.create.description){ this.create.description = this.create.description_e}
            if(!this.create.description_e){ this.create.description_e = this.create.description}
            if( this.isVisible('department_id') && this.department_id ) this.create.department_id = this.department_id;

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
        getDepartnent() {
            this.isLoader = true;

             adminApi
                .get(
                    `/depertments`
                )
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
    },
};
</script>

<template>
    <div>
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit'?getCompanyKey('boardRent_task_create_form'):getCompanyKey('boardRent_task_edit_form')"
            title-class="font-18"
            body-class="p-4 "
            size="lg"
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
                        @click.prevent="resetModalHidden"
                        variant="danger"
                        type="button"
                    >
                        {{ $t("general.Cancel") }}
                    </b-button>
                </div>
                <div class="row">
                    <div class="col-md-6" v-if="!department_id && isVisible('department_id')">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("boardRent_task_department") }}
                                <span v-if="isRequired('department_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                v-model="create.department_id"
                                :options="departments.map((type) => type.id)"
                                :custom-label=" (opt) => departments.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ? departments.find((x) => x.id == opt).name : departments.find((x) => x.id == opt).name_e
                                    : null
                                "
                            >
                            </multiselect>
                            <div v-if="$v.create.department_id.$error || errors.department_id" class="text-danger">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.department_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.department_id" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="!department_id && isVisible('department_id')" class="col-md-6"></div>
                    <div v-if="isVisible('name')" class="col-md-6">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey("boardRent_task_name_ar") }}
                                <span v-if="isRequired('name')" class="text-danger">*</span>
                            </label>
                            <div dir="rtl">
                                <input
                                    @keyup="arabicValue(create.name)"
                                    type="text"
                                    class="form-control"
                                    data-create="1"
                                    v-model="$v.create.name.$model"
                                    :class="{
                            'is-invalid': $v.create.name.$error || errors.name,
                            'is-valid': !$v.create.name.$invalid && !errors.name,
                          }"
                                    id="field-1"
                                />
                            </div>
                            <div v-if="!$v.create.name.minLength" class="invalid-feedback">
                                {{ $t("general.Itmustbeatleast") }}
                                {{ $v.create.name.$params.minLength.min }}
                                {{ $t("general.letters") }}
                            </div>
                            <div v-if="!$v.create.name.maxLength" class="invalid-feedback">
                                {{ $t("general.Itmustbeatmost") }}
                                {{ $v.create.name.$params.maxLength.max }}
                                {{ $t("general.letters") }}
                            </div>
                            <template v-if="errors.name">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.name"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('name_e')" class="col-md-6">
                        <div class="form-group">
                            <label for="field-2" class="control-label">
                                {{ getCompanyKey("boardRent_task_name_en") }}
                                <span v-if="isRequired('name_e')" class="text-danger">*</span>
                            </label>
                            <div dir="ltr">
                                <input
                                    @keyup="englishValue(create.name_e)"
                                    type="text"
                                    class="form-control englishInput"
                                    data-create="2"
                                    v-model="$v.create.name_e.$model"
                                    :class="{
                            'is-invalid': $v.create.name_e.$error || errors.name_e,
                            'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                          }"
                                    id="field-2"
                                />
                            </div>
                            <div v-if="!$v.create.name_e.minLength" class="invalid-feedback">
                                {{ $t("general.Itmustbeatleast") }}
                                {{ $v.create.name_e.$params.minLength.min }}
                                {{ $t("general.letters") }}
                            </div>
                            <div v-if="!$v.create.name_e.maxLength" class="invalid-feedback">
                                {{ $t("general.Itmustbeatmost") }}
                                {{ $v.create.name_e.$params.maxLength.max }}
                                {{ $t("general.letters") }}
                            </div>
                            <template v-if="errors.name_e">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.name_e"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('description')" class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey('boardRent_task_description_ar') }}
                                <span v-if="isRequired('description')" class="text-danger">*</span>
                            </label>
                            <textarea @input="arabicValueDescription(create.description)" v-model="$v.create.description.$model" class="form-control" :maxlength="1000" rows="5"></textarea>
                            <template v-if="errors.description">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.description"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('description_e')" class="col-md-6">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey('boardRent_task_description_en') }}
                                <span v-if="isRequired('description_e')" class="text-danger">*</span>
                            </label>
                            <textarea @input="englishValueDescription(create.description_e)" v-model="$v.create.description_e.$model" class="form-control" :maxlength="1000" rows="5"></textarea>
                            <template v-if="errors.description_e">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.description_e"
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
