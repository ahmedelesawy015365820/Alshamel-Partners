<script>
import adminApi from "../../../api/adminAxios";
import { minLength, maxLength, requiredIf} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

/**
 * Advanced Table component
 */
export default {
    components: {
        Multiselect,
        ErrorMessage,
        loader,
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "currency-create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/customer-groups'}
    },
    data() {
        return {
            fields: [],
            isCustom:false,
            isLoader: false,
            create: {
                title: "",
                title_e: "",
                discount: 0,
                is_default: 0,
            },
            company_id: null,
            errors: {},
            is_disabled: false,
        };
    },
    validations: {
        create: {
            title: {
                required: requiredIf(function (model) {
                    return this.isRequired("title");
                }), minLength: minLength(2), maxLength: maxLength(100)
            },
            title_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("title_e");
                }), minLength: minLength(2), maxLength: maxLength(100)
            },
            discount: {
                required: requiredIf(function (model) {
                    return this.isRequired("discount");
                })
            },
            is_default: {
                required: requiredIf(function (model) {
                    return this.isRequired("is_default");
                })
            },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/general_customer_groups`)
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
                title: "",
                title_e: "",
                discount: 0,
                is_default: 0,
            };
            this.isLoader = false;
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        arabicValue(txt) {
            this.create.title = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.title_e = englishValue(txt);
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        resetModal() {
            this.defaultData();
            setTimeout(async () => {
                if(this.type != 'edit'){
                    if(!this.isPage) await this.getCustomTableFields();
                }else {
                    if(this.idObjEdit.dataObj){
                        let group = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.title = group.title;
                        this.create.title_e = group.title_e;
                        this.create.discount = group.discount;
                        this.create.is_default = group.is_default;
                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            if (!this.create.title) {
                this.create.title = this.create.title_e;
            }
            if (!this.create.title_e) {
                this.create.title_e = this.create.title;
            }
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;

                if(this.type != 'edit') {
                    adminApi
                        .post(this.url, {
                            ...this.create,
                            company_id: this.$store.getters["auth/company_id"],
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
    },
};
</script>

<template>
    <!--  create   -->
    <b-modal
        :id="id"
        :title="type != 'edit'?getCompanyKey('customer_groups_create_form'):getCompanyKey('customer_groups_edit_form')"
        title-class="font-18"
        body-class="p-4 "
        :hide-footer="true"
        @show="resetModal"
        @hidden="resetModalHidden"
    >
        <form>
            <loader size="large" v-if="isCustom && !isPage" />
            <div class="mb-3 d-flex justify-content-end">
                <b-button variant="success"  v-if="type != 'edit'" :disabled="!is_disabled" @click.prevent="resetForm"
                          type="button"
                          :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                    {{ $t("general.AddNewRecord") }}
                </b-button>
                <template v-if="!is_disabled">
                    <b-button variant="success" type="button" class="mx-1" v-if="!isLoader"
                              @click.prevent="AddSubmit">
                        {{ type != 'edit'? $t("general.Add"): $t("general.edit") }}
                    </b-button>

                    <b-button variant="success" class="mx-1" disabled v-else>
                        <b-spinner small></b-spinner>
                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                    </b-button>
                </template>
                <!-- Emulate built in modal footer ok and cancel button actions -->

                <b-button variant="danger" type="button" @click.prevent="resetModalHidden">
                    {{ $t("general.Cancel") }}
                </b-button>
            </div>
            <div class="row">
                <div class="col-md-12" v-if="isVisible('title')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("customer_groups_title_ar") }}
                            <span v-if="isRequired('title')" class="text-danger">*</span>
                        </label>
                        <div dir="rtl">
                            <input
                                @keyup="arabicValue(create.title)"
                                type="text"
                                class="form-control"
                                data-create="1"
                                @keypress.enter="moveInput('input', 'create', 2)"
                                v-model="$v.create.title.$model"
                                :class="{
                                                       'is-invalid': $v.create.title.$error || errors.title,
                                                       'is-valid': !$v.create.title.$invalid && !errors.title,
                                                    }"
                            />
                        </div>
                        <div v-if="!$v.create.title.minLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatleast") }}
                            {{ $v.create.title.$params.minLength.min }}
                            {{ $t("general.letters") }}
                        </div>
                        <div v-if="!$v.create.title.maxLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatmost") }}
                            {{ $v.create.title.$params.maxLength.max }}
                            {{ $t("general.letters") }}
                        </div>
                        <template v-if="errors.title">
                            <ErrorMessage v-for="(errorMessage, index) in errors.title" :key="index">
                                {{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('title_e')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("customer_groups_title_en") }}
                            <span v-if="isRequired('title_e')" class="text-danger">*</span>
                        </label>
                        <div dir="ltr">
                            <input
                                @keyup="englishValue(create.title_e)" type="text"
                                class="form-control englishInput"
                                data-create="2"
                                @keypress.enter="moveInput('select', 'create', 3)"
                                v-model="$v.create.title_e.$model"
                                :class="{
                                                      'is-invalid': $v.create.title_e.$error || errors.title_e,
                                                      'is-valid': !$v.create.title_e.$invalid && !errors.title_e,
                                                    }"
                            />
                        </div>
                        <div v-if="!$v.create.title_e.minLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatleast") }}
                            {{ $v.create.title_e.$params.minLength.min }}
                            {{ $t("general.letters") }}
                        </div>
                        <div v-if="!$v.create.title_e.maxLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatmost") }}
                            {{ $v.create.title_e.$params.maxLength.max }}
                            {{ $t("general.letters") }}
                        </div>
                        <template v-if="errors.title_e">
                            <ErrorMessage v-for="(errorMessage, index) in errors.title_e" :key="index">
                                {{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('discount')">
                    <div class="form-group">
                        <label class="mr-2">
                            {{ getCompanyKey("customer_groups_discount") }}
                            <span v-if="isRequired('discount')" class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            step="any"
                            class="form-control"
                            v-model="$v.create.discount.$model"
                            :class="{
                                                    'is-invalid': $v.create.discount.$error || errors.discount,
                                                    'is-valid': !$v.create.discount.$invalid && !errors.discount,
                                                }"
                        />
                        <template v-if="errors.discount">
                            <ErrorMessage v-for="(errorMessage, index) in errors.discount" :key="index">
                                {{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-6" v-if="isVisible('is_default')">
                    <div class="form-group">
                        <label class="mr-2">
                            {{ getCompanyKey("customer_groups_default") }}
                            <span v-if="isRequired('is_default')" class="text-danger">*</span>
                        </label>
                        <b-form-group
                            :class="{
                                                    'is-invalid': $v.create.is_default.$error || errors.is_default,
                                                    'is-valid': !$v.create.is_default.$invalid && !errors.is_default,
                                                }"
                        >
                            <b-form-radio class="d-inline-block"
                                          v-model="$v.create.is_default.$model"
                                          name="create_def_some-radios" value="1">
                                {{ $t("general.Yes") }}
                            </b-form-radio>
                            <b-form-radio class="d-inline-block m-1"
                                          v-model="$v.create.is_default.$model"
                                          name="create_def_some-radios" value="0">
                                {{ $t("general.No") }}
                            </b-form-radio>
                        </b-form-group>
                        <template v-if="errors.is_default">
                            <ErrorMessage v-for="(errorMessage, index) in errors.is_default"
                                          :key="index">{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
            </div>
        </form>
    </b-modal>
    <!--  /create   -->

</template>

<style>
form {
    position: relative;
}
</style>
