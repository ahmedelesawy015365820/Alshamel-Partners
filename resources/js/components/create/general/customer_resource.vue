<template>
    <!--  create   -->
    <b-modal
        :id="id"
        :title="type != 'edit'?getCompanyKey('customer_resource_create_form'):getCompanyKey('customer_resource_edit_form')"
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
                <div v-if="isVisible('name')" class="col-md-12">
                    <div class="form-group">
                        <label for="field-1" class="control-label">
                            {{ getCompanyKey("customer_resource_name_ar") }}
                            <span v-if="isRequired('name')" class="text-danger"
                            >*</span
                            >
                        </label>
                        <div dir="rtl">
                            <input
                                type="text"
                                class="form-control arabicInput"
                                v-model="$v.create.name.$model"
                                :class="{
                            'is-invalid': $v.create.name.$error || errors.name,
                            'is-valid':
                              !$v.create.name.$invalid && !errors.name,
                          }"
                                @keyup="arabicValue(create.name)"
                                id="field-1"
                            />
                        </div>
                        <div
                            v-if="!$v.create.name.minLength"
                            class="invalid-feedback"
                        >
                            {{ $t("general.Itmustbeatleast") }}
                            {{ $v.create.name.$params.minLength.min }}
                            {{ $t("general.letters") }}
                        </div>
                        <div
                            v-if="!$v.create.name.maxLength"
                            class="invalid-feedback"
                        >
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
                <div v-if="isVisible('name_e')" class="col-md-12">
                    <div class="form-group">
                        <label for="field-2" class="control-label">
                            {{ getCompanyKey("customer_resource_name_en") }}
                            <span v-if="isRequired('name_e')" class="text-danger"
                            >*</span
                            >
                        </label>
                        <div dir="ltr">
                            <input
                                type="text"
                                class="form-control englishInput"
                                v-model="$v.create.name_e.$model"
                                :class="{
                            'is-invalid':
                              $v.create.name_e.$error || errors.name_e,
                            'is-valid':
                              !$v.create.name_e.$invalid && !errors.name_e,
                          }"
                                @keyup="englishValue(create.name_e)"
                                id="field-2"
                            />
                        </div>
                        <div
                            v-if="!$v.create.name_e.minLength"
                            class="invalid-feedback"
                        >
                            {{ $t("general.Itmustbeatleast") }}
                            {{ $v.create.name_e.$params.minLength.min }}
                            {{ $t("general.letters") }}
                        </div>
                        <div
                            v-if="!$v.create.name_e.maxLength"
                            class="invalid-feedback"
                        >
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
                <div
                    class="col-md-12 position-relative"
                    v-if="isVisible('parent_id')"
                >
                    <div class="form-group">
                        <label>
                            {{ getCompanyKey('customer_resource_parent') }}
                        </label>
                        <multiselect
                            v-model="create.parent_id"
                            :options="parent.map((type) => type.id)"
                            :custom-label="
                          (opt) => parent.find((x) => x.id == opt)?
                            $i18n.locale == 'ar'
                              ? parent.find((x) => x.id == opt).name
                              : parent.find((x) => x.id == opt).name_e : null
                        "
                            :class="{ 'is-invalid': errors.parent_id }"
                        >
                        </multiselect>

                        <template v-if="errors.parent_id">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.parent_id"
                                :key="index"
                            >{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
            </div>
        </form>
    </b-modal>
    <!--  /create   -->
</template>

<script>
import ErrorMessage from "../../widgets/errorMessage";
import Multiselect from "vue-multiselect";
import loader from "../../general/loader";
import {maxLength, minLength, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

export default {
    components: {
        ErrorMessage,
        Multiselect,
        loader,
    },
    data() {
        return {
            isCustom:false,
            parent: [],
            isLoader: false,
            fields: [],
            create: {
                name: "",
                name_e: "",
                parent_id: null,
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
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/customer-sources'}
    },
    validations: {
        create: {
            name: {
                required: requiredIf(function (model) {
                    return this.isRequired("name");
                }),
                minLength: minLength(2),
                maxLength: maxLength(100),
            },
            name_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }),
                minLength: minLength(2),
                maxLength: maxLength(100),
            },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        getParents(id = null) {
            this.isLoader = true;

             adminApi
                .get(`/customer-sources${id?'?dontId='+id:''}`)
                .then((res) => {
                    let l = res.data.data;
                    this.parent = l;
                })
                .catch((err) => {
                    this.errorFun('Error', 'Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/general_customer_sources`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    this.errorFun('Error', 'Thereisanerrorinthesystem');
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
                name: "",
                name_e: "",
                parent_id: null,
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
                    if(!this.isPage) await this.getCustomTableFields();
                    if (this.isVisible("parent_id")) this.getParents();
                }else {
                    if(this.idObjEdit.dataObj){
                        let category = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.name = category.name;
                        this.create.name_e = category.name_e;
                        if (this.isVisible("parent_id")) this.getParents(category.id);
                        this.create.parent_id = category.parent_id? category.parent_id: null;
                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
            if (this.isVisible("parent_id")) this.getParents();
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
                if(this.type != 'edit') {
                    adminApi
                        .post(this.url, {
                            ...this.create,
                            company_id: this.company_id,
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
                } else{
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
        arabicValue(txt) {
            this.create.name = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.name_e = englishValue(txt);
        },
    },
};
</script>

<style scoped>
form {
    position: relative;
}
</style>
