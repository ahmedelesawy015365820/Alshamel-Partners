<template>
    <!--  create   -->
    <b-modal
        :id="id"
        :title="type != 'edit'?getCompanyKey('payment_type_create_form'):getCompanyKey('payment_type_edit_form')"
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
                <div class="col-md-12" v-if="isVisible('name')">
                    <div class="form-group">
                        <label for="field-1" class="control-label">
                            {{ getCompanyKey("payment_type_name_ar") }}
                            <span v-if="isRequired('name')" class="text-danger"
                            >*</span
                            >
                        </label>
                        <div dir="rtl">
                            <input
                                type="text"
                                class="form-control"
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
                            >
                                {{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('name_e')">
                    <div class="form-group">
                        <label for="field-2" class="control-label">
                            {{ getCompanyKey("payment_type_name_en") }}
                            <span v-if="isRequired('name_e')" class="text-danger"
                            >*</span
                            >
                        </label>
                        <div dir="ltr">
                            <input
                                type="text"
                                class="form-control"
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
                            >{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('is_default')">
                    <div class="form-group">
                        <label class="mr-2">
                            {{ getCompanyKey("payment_type_default") }}
                            <span
                                v-if="isRequired('is_default')"
                                class="text-danger"
                            >*</span
                            >
                        </label>
                        <b-form-group
                            :class="{
                          'is-invalid':
                            $v.create.is_default.$error || errors.is_default,
                          'is-valid':
                            !$v.create.is_default.$invalid &&
                            !errors.is_default,
                        }"
                        >
                            <b-form-radio
                                class="d-inline-block"
                                v-model="$v.create.is_default.$model"
                                name="some-radios"
                                value="1"
                            >{{ $t("general.Yes") }}</b-form-radio
                            >
                            <b-form-radio
                                class="d-inline-block m-1"
                                v-model="$v.create.is_default.$model"
                                name="some-radios"
                                value="0"
                            >{{ $t("general.No") }}</b-form-radio
                            >
                        </b-form-group>
                        <template v-if="errors.is_default">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.is_default"
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
</template>

<script>
import {required, minLength, maxLength, integer, requiredIf,} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import {arabicValue,englishValue} from "../../../helper/langTransform";

export default {
    name: "payment-type",
    mixins: [transMixinComp,successError],
    components: {
        ErrorMessage,
        loader,
    },
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/payment-types'}
    },
    data() {
        return {
            fields: [],
            isLoader: false,
            isCustom: false,
            create: {
                name: "",
                name_e: "",
                is_default: 1,
            },
            company_id: null,
            errors: {},
            is_disabled: false,
        };
    },
    validations: {
        create: {
            name: {
                required: requiredIf(function (model) {
                    return this.isRequired("name");
                }),
                minLength: minLength(3),
                maxLength: maxLength(100),
            },
            name_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }),
                minLength: minLength(3),
                maxLength: maxLength(100),
            },
            is_default: {
                required: requiredIf(function (model) {
                    return this.isRequired("is_default");
                }),
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
                .get(`/customTable/table-columns/general_payment_types`)
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
            this.create = { name: "", name_e: "", is_default: 0 };
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
            setTimeout( () => {
                if(this.type != 'edit'){
                    if(!this.isPage)  this.getCustomTableFields();
                }else {
                    if(this.idObjEdit.dataObj){
                        let color = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.name = color.name;
                        this.create.name_e = color.name_e;
                        this.create.is_default = color.is_default;
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
                if(this.type != 'edit') {
                    adminApi
                        .post(this.url, {
                            ...this.create,
                            company_id: this.company_id,
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
        arabicValue(txt) {
            this.create.name = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.name_e = englishValue(txt);
        },
    },
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
