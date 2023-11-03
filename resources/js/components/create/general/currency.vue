<template>
    <!--  create   -->
    <b-modal
        :id="id"
        :title="type != 'edit'?getCompanyKey('currency_create_form'):getCompanyKey('currency_edit_form')"
        title-class="font-18"
        dialog-class="modal-full-width"
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
                <div class="col-md-3" v-if="isVisible('name')">
                    <div class="form-group">
                        <label for="field-1" class="control-label">
                            {{ getCompanyKey('currency_name_ar') }}
                            <span v-if="isRequired('name')" class="text-danger">*</span>
                        </label>
                        <div dir="rtl">
                            <input
                                type="text"
                                class="form-control arabicInput"
                                data-create="1"
                                @keyup="arabicValue(create.name)"
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
                <div class="col-md-3" v-if="isVisible('symbol')">
                    <div class="form-group">
                        <label for="field-45" class="control-label">
                            {{ getCompanyKey('currency_symbol_ar') }}
                            <span v-if="isRequired('symbol')" class="text-danger">*</span>
                        </label>
                        <div dir="rtl">
                            <input
                                type="text"
                                class="form-control arabicInput"
                                data-create="3"
                                @keyup="arabicValueSymbol(create.symbol)"
                                v-model="$v.create.symbol.$model"
                                :class="{
                            'is-invalid': $v.create.symbol.$error || errors.symbol,
                            'is-valid': !$v.create.symbol.$invalid && !errors.symbol,
                          }"
                                id="field-45"
                            />
                        </div>
                        <div v-if="!$v.create.symbol.minLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatleast") }}
                            {{ $v.create.symbol.$params.minLength.min }}
                            {{ $t("general.letters") }}
                        </div>
                        <div v-if="!$v.create.symbol.maxLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatmost") }}
                            {{ $v.create.symbol.$params.maxLength.max }}
                            {{ $t("general.letters") }}
                        </div>
                        <template v-if="errors.symbol">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.symbol"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('code')">
                    <div class="form-group">
                        <label for="field-3" class="control-label">
                            {{ getCompanyKey('currency_code_ar') }}
                            <span v-if="isRequired('code')" class="text-danger">*</span>
                        </label>
                        <div dir="rtl">
                            <input
                                type="text"
                                class="form-control arabicInput"
                                data-create="5"
                                @keyup="arabicValueCode(create.code)"
                                v-model="$v.create.code.$model"
                                :class="{
                            'is-invalid': $v.create.code.$error || errors.code,
                            'is-valid': !$v.create.code.$invalid && !errors.code,
                          }"
                                id="field-3"
                            />
                        </div>
                        <div v-if="!$v.create.code.minLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatleast") }}
                            {{ $v.create.code.$params.minLength.min }}
                            {{ $t("general.letters") }}
                        </div>
                        <div v-if="!$v.create.code.maxLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatmost") }}
                            {{ $v.create.code.$params.maxLength.max }}
                            {{ $t("general.letters") }}
                        </div>
                        <template v-if="errors.code">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.code"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('fraction')">
                    <div class="form-group">
                        <label for="field-5" class="control-label">
                            {{ getCompanyKey('currency_fraction_ar') }}
                            <span v-if="isRequired('fraction')" class="text-danger">*</span>
                        </label>
                        <div dir="rtl">
                            <input
                                type="text"
                                class="form-control arabicInput"
                                data-create="7"
                                @keyup="arabicValueFraction(create.fraction)"
                                v-model="$v.create.fraction.$model"
                                :class="{
                            'is-invalid': $v.create.fraction.$error || errors.fraction,
                            'is-valid': !$v.create.fraction.$invalid && !errors.fraction,
                          }"
                                id="field-5"
                            />
                        </div>
                        <div v-if="!$v.create.fraction.minLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatleast") }}
                            {{ $v.create.fraction.$params.minLength.min }}
                            {{ $t("general.letters") }}
                        </div>
                        <div v-if="!$v.create.fraction.maxLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatmost") }}
                            {{ $v.create.fraction.$params.maxLength.max }}
                            {{ $t("general.letters") }}
                        </div>
                        <template v-if="errors.fraction">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.fraction"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('name_e')">
                    <div class="form-group">
                        <label for="field-2" class="control-label">
                            {{ getCompanyKey('currency_name_en') }}
                            <span v-if="isRequired('name_e')" class="text-danger">*</span>
                        </label>
                        <div dir="ltr">
                            <input
                                type="text"
                                class="form-control englishInput"
                                data-create="2"
                                @keyup="englishValue(create.name_e)"
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
                <div class="col-md-3" v-if="isVisible('symbol_e')">
                    <div class="form-group">
                        <label for="field-33" class="control-label">
                            {{ getCompanyKey('currency_symbol_en') }}
                            <span v-if="isRequired('symbol_e')" class="text-danger">*</span>
                        </label>
                        <div dir="ltr">
                            <input
                                type="text"
                                class="form-control englishInput"
                                data-create="4"
                                @keyup="englishValueSymbol(create.symbol_e)"
                                v-model="$v.create.symbol_e.$model"
                                :class="{
                            'is-invalid': $v.create.symbol_e.$error || errors.symbol_e,
                            'is-valid': !$v.create.symbol_e.$invalid && !errors.symbol_e,
                          }"
                                id="field-33"
                            />
                        </div>
                        <div v-if="!$v.create.symbol_e.minLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatleast") }}
                            {{ $v.create.symbol_e.$params.minLength.min }}
                            {{ $t("general.letters") }}
                        </div>
                        <div v-if="!$v.create.symbol_e.maxLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatmost") }}
                            {{ $v.create.symbol_e.$params.maxLength.max }}
                            {{ $t("general.letters") }}
                        </div>
                        <template v-if="errors.symbol_e">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.symbol_e"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('code_e')">
                    <div class="form-group">
                        <label for="field-4" class="control-label">
                            {{ getCompanyKey('currency_code_en') }}
                            <span v-if="isRequired('code_e')" class="text-danger">*</span>
                        </label>
                        <div dir="ltr">
                            <input
                                type="text"
                                class="form-control englishInput"
                                data-create="6"
                                @keyup="englishValueCode(create.code_e)"
                                @keypress.enter="moveInput('input', 'create', 7)"
                                v-model="$v.create.code_e.$model"
                                :class="{
                            'is-invalid': $v.create.code_e.$error || errors.code_e,
                            'is-valid': !$v.create.code_e.$invalid && !errors.code_e,
                          }"
                                id="field-4"
                            />
                        </div>
                        <div v-if="!$v.create.code_e.minLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatleast") }}
                            {{ $v.create.code_e.$params.minLength.min }}
                            {{ $t("general.letters") }}
                        </div>
                        <div v-if="!$v.create.code_e.maxLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatmost") }}
                            {{ $v.create.code_e.$params.maxLength.max }}
                            {{ $t("general.letters") }}
                        </div>
                        <template v-if="errors.code_e">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.code_e"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('fraction_e')">
                    <div class="form-group">
                        <label for="field-6" class="control-label">
                            {{ getCompanyKey('currency_fraction_en') }}
                            <span v-if="isRequired('fraction_e')" class="text-danger">*</span>
                        </label>
                        <div dir="ltr">
                            <input
                                type="text"
                                class="form-control englishInput"
                                data-create="8"
                                @keyup="englishValueFraction(create.fraction_e)"
                                v-model="$v.create.fraction_e.$model"
                                :class="{
                            'is-invalid':
                              $v.create.fraction_e.$error || errors.fraction_e,
                            'is-valid':
                              !$v.create.fraction_e.$invalid && !errors.fraction_e,
                          }"
                                id="field-6"
                            />
                        </div>
                        <div
                            v-if="!$v.create.fraction_e.minLength"
                            class="invalid-feedback"
                        >
                            {{ $t("general.Itmustbeatleast") }}
                            {{ $v.create.fraction_e.$params.minLength.min }}
                            {{ $t("general.letters") }}
                        </div>
                        <div
                            v-if="!$v.create.fraction_e.maxLength"
                            class="invalid-feedback"
                        >
                            {{ $t("general.Itmustbeatmost") }}
                            {{ $v.create.fraction_e.$params.maxLength.max }}
                            {{ $t("general.letters") }}
                        </div>
                        <template v-if="errors.fraction_e">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.fraction_e"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('fraction_no')">
                    <div class="form-group">
                        <label for="field-7" class="control-label">
                            {{ getCompanyKey('currency_fraction_number') }}
                            <span v-if="isRequired('fraction_no')" class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            data-create="9"
                            step="0.1"
                            @keypress.enter="moveInput('select', 'create', 10)"
                            v-model="$v.create.fraction_no.$model"
                            :class="{
                          'is-invalid':
                            $v.create.fraction_no.$error || errors.fraction_no,
                          'is-valid':
                            !$v.create.fraction_no.$invalid && !errors.fraction_no,
                        }"
                            id="field-7"
                        />
                        <template v-if="errors.fraction_no">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.fraction_no"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('is_default')">
                    <div class="form-group">
                        <label class="mr-2" for="field-11">
                            {{ getCompanyKey('currency_default') }}
                            <span v-if="isRequired('is_default')" class="text-danger">*</span>
                        </label>
                        <select
                            class="custom-select mr-sm-2"
                            id="field-11"
                            data-create="10"
                            @keypress.enter.prevent="moveInput('select', 'create', 11)"
                            v-model="$v.create.is_default.$model"
                            :class="{
                          'is-invalid': $v.create.is_default.$error || errors.is_default,
                          'is-valid':
                            !$v.create.is_default.$invalid && !errors.is_default,
                        }"
                        >
                            <option value="" selected>{{ $t("general.Choose") }}...</option>
                            <option value="1">{{ $t("general.Yes") }}</option>
                            <option value="0">{{ $t("general.No") }}</option>
                        </select>
                        <template v-if="errors.is_default">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.is_default"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-3" v-if="isVisible('is_active')">
                    <div class="form-group">
                        <label class="mr-2">
                            {{ getCompanyKey('currency_status') }}
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
</template>

<script>
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import {decimal, integer, maxLength, minLength, required, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import Multiselect from "vue-multiselect";
import successError from "../../../helper/mixin/success&error";

export default {
    name: "currency",
    components: {
        Multiselect,
        ErrorMessage,
        loader,
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "currency-create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/currencies'}
    },
    data() {
        return {
            fields: [],
            isLoader: false,
            isCustom:false,
            create: {
                name: "",
                name_e: "",
                symbol: "",
                symbol_e: "",
                code: "",
                code_e: "",
                fraction: "",
                fraction_e: "",
                fraction_no: 0,
                is_default: 0,
                is_active: 1,
            },
            errors: {},
            is_disabled: false,
            company_id: null
        };
    },
    validations: {
        create: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }) , minLength: minLength(2), maxLength: maxLength(100) },
            name_e: { required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }) , minLength: minLength(2), maxLength: maxLength(100) },
            symbol: { required: requiredIf(function (model) {
                    return this.isRequired("symbol");
                }) , minLength: minLength(2), maxLength: maxLength(100) },
            symbol_e: { required: requiredIf(function (model) {
                    return this.isRequired("symbol_e");
                }) , minLength: minLength(2), maxLength: maxLength(100) },
            code: { required: requiredIf(function (model) {
                    return this.isRequired("code");
                }) , minLength: minLength(3), maxLength: maxLength(15) },
            code_e: { required: requiredIf(function (model) {
                    return this.isRequired("code_e");
                }) , minLength: minLength(3), maxLength: maxLength(15) },
            fraction: { required: requiredIf(function (model) {
                    return this.isRequired("fraction");
                }) , minLength: minLength(3), maxLength: maxLength(15) },
            fraction_e: { required: requiredIf(function (model) {
                    return this.isRequired("fraction_e");
                }) , minLength: minLength(3), maxLength: maxLength(100) },
            fraction_no: { required: requiredIf(function (model) {
                    return this.isRequired("fraction_no");
                }) , decimal },
            is_default: { required: requiredIf(function (model) {
                    return this.isRequired("is_default");
                }) , integer },
            is_active: { required: requiredIf(function (model) {
                    return this.isRequired("is_active");
                }) , integer },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        getCustomTableFields() {
            this.isCustom = true;
             adminApi
                .get(`/customTable/table-columns/general_currencies`)
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
                symbol: "",
                symbol_e: "",
                code: "",
                code_e: "",
                fraction: "",
                fraction_e: "",
                fraction_no: 0,
                is_default: 0,
                is_active: 1,
            };
            this.errors = {};
            this.is_disabled = false;
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
            setTimeout(async () => {
                if(this.type != 'edit'){
                    if(!this.isPage) await this.getCustomTableFields();
                }else {
                    if(this.idObjEdit.dataObj){
                        let currency = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.name = currency.name;
                        this.create.name_e = currency.name_e;
                        this.create.symbol = currency.symbol;
                        this.create.symbol_e = currency.symbol_e;
                        this.create.code = currency.code;
                        this.create.code_e = currency.code_e;
                        this.create.fraction = currency.fraction;
                        this.create.fraction_e = currency.fraction_e;
                        this.create.fraction_no = currency.fraction_no;
                        this.create.is_active = currency.is_active;
                        this.create.is_default = currency.is_default;
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
            if (!this.create.code) {
                this.create.code = this.create.code_e;
            }
            if (!this.create.code_e) {
                this.create.code_e = this.create.code;
            }
            if (!this.create.symbol) {
                this.create.symbol = this.create.symbol_e;
            }
            if (!this.create.symbol_e) {
                this.create.symbol_e = this.create.symbol;
            }
            if (!this.create.fraction) {
                this.create.fraction = this.create.fraction_e;
            }
            if (!this.create.fraction_e) {
                this.create.fraction_e = this.create.fraction;
            }
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
        arabicValue(txt){
            this.create.name = arabicValue(txt);
        },
        arabicValueSymbol(txt){
            this.create.symbol = arabicValue(txt);
        },
        arabicValueCode(txt){
            this.create.code = arabicValue(txt);
        },
        arabicValueFraction(txt){
            this.create.fraction = arabicValue(txt);
        },
        englishValue(txt){
            this.create.name_e = englishValue(txt);
        },
        englishValueSymbol(txt){
            this.create.symbol_e = englishValue(txt);
        },
        englishValueCode(txt){
            this.create.code_e = englishValue(txt);
        },
        englishValueFraction(txt){
            this.create.fraction_e = englishValue(txt);
        }
    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
