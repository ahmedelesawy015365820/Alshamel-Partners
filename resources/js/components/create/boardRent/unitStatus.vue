<template>
    <div>
        <Status
            id="status-create-unit-status" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
            @created="getStatus" :isPage="false" type="create" :isPermission="isPermission"
        />
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit'?getCompanyKey('boardRent_unitStatus_create_form'):getCompanyKey('boardRent_unitStatus_edit_form')"
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
                    <div class="col-md-12" v-if="isVisible('status_id')">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("boardRent_unitStatus_status") }}
                                <span v-if="isRequired('status_id')" class="text-danger">*</span>
                            </label>
                            <multiselect @input="showStatusModal" v-model="create.status_id"
                                         :options="generalStatuses.map((type) => type.id)"
                                         :custom-label="(opt) => generalStatuses.find((x) => x.id == opt).name?
                                            $i18n.locale == 'ar' ? generalStatuses.find((x) => x.id == opt).name:generalStatuses.find((x) => x.id == opt).name_e
                                            : null
                                         ">
                            </multiselect>
                            <div v-if="$v.create.status_id.$error || errors.status_id" class="text-danger">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.status_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.status_id" :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('name')">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey("boardRent_unitStatus_name_ar") }}
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
                    <div class="col-md-12" v-if="isVisible('name_e')">
                        <div class="form-group">
                            <label for="field-2" class="control-label">
                                {{ getCompanyKey("boardRent_unitStatus_name_en") }}
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
                </div>
            </form>
        </b-modal>
        <!--  /create   -->
    </div>
</template>

<script>
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import Status from "../general/status";
import {maxLength, minLength, required, requiredIf} from "vuelidate/lib/validators";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import adminApi from "../../../api/adminAxios";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

export default {
    name: "unitStatus",
    components: {
        ErrorMessage,
        loader,
        Multiselect,
        Status
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/boards-rent/unit_statuses'}
    },
    data() {
        return {
            fields: [],
            generalStatuses: [],
            isLoader: false,
            create: {
                name: "",
                name_e: "",
                status_id: null
            },
            company_id: null,
            errors: {},
            is_disabled: false,
            isCustom: false,
            module_type: 'bordRent'
        };
    },
    validations: {
        create: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(255) },
            name_e: { required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(2), maxLength: maxLength(255) },
            status_id: { required: requiredIf(function (model) {
                    return this.isRequired("status_id");
                }) }
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/boards_rent_unit_statuses`)
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
        defaultData(edit = null){
            this.create = {
                name: "",
                name_e: "",
                status_id: null
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
            setTimeout(  async () => {
                if(this.type != 'edit'){
                    if(!this.isPage)   await this.getCustomTableFields();
                    if (this.isVisible("status_id"))  this.getStatus();
                }else {
                    if(this.idObjEdit.dataObj){
                        let status = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.name = status.name;
                        this.create.name_e = status.name_e;
                        if (this.isVisible("status_id")) this.getStatus();
                        if (this.isVisible("status_id")) this.create.status_id = status.status_id;
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
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};

                if(this.type != 'edit'){
                    adminApi
                        .post(this.url, {
                            ...this.create, company_id: this.$store.getters["auth/company_id"],
                        })
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
                            company_id: this.$store.getters["auth/company_id"],
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
        showStatusModal() {
            if (this.create.status_id == 0) {
                this.$bvModal.show("status-create-unit-status");
                this.create.status_id = null;
            }
        },
        getStatus(){
            this.isLoader = true;

            adminApi
                .get(`/statuses?module_type=${this.module_type}`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Status')){
                        l.unshift({ id: 0, name: "اضف حاله", name_e: "Add Status" });
                    }
                    this.generalStatuses = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        }
    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
