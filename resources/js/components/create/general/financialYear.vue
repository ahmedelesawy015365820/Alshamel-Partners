<template>
    <!--  create   -->
    <b-modal
        :id="id"
        :title="type != 'edit'?getCompanyKey('financial_year_create_form'):getCompanyKey('financial_year_edit_form')"
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
                            {{ getCompanyKey("financial_year_name_ar") }}
                            <span v-if="isRequired('name')" class="text-danger"
                            >*</span
                            >
                        </label>
                        <div dir="rtl">
                            <input
                                type="text"
                                class="form-control arabicInput"
                                data-create="1"
                                @keyup="arabicValueName(create.name)"
                                v-model="$v.create.name.$model"
                                :class="{
                            'is-invalid': $v.create.name.$error || errors.name,
                            'is-valid':
                              !$v.create.name.$invalid && !errors.name,
                          }"
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
                <div class="col-md-12" v-if="isVisible('name_e')">
                    <div class="form-group">
                        <label for="field-2" class="control-label">
                            {{ getCompanyKey("financial_year_name_en") }}
                            <span v-if="isRequired('name_e')" class="text-danger"
                            >*</span
                            >
                        </label>
                        <div dir="ltr">
                            <input
                                type="text"
                                class="form-control englishInput"
                                data-create="2"
                                @keyup="englishValueName(create.name_e)"
                                v-model="$v.create.name_e.$model"
                                :class="{
                            'is-invalid':
                              $v.create.name_e.$error || errors.name_e,
                            'is-valid':
                              !$v.create.name_e.$invalid && !errors.name_e,
                          }"
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
                <div class="col-md-12" v-if="isVisible('year')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("financial_year_year") }}
                            <span v-if="isRequired('year')" class="text-danger">*</span>
                        </label>
                        <date-picker
                            type="year"
                            v-model="create.year"
                            format="YYYY"
                            valueType="format"
                            :confirm="false"
                            :class="{ 'is-invalid': $v.create.year.$error || errors.year,
                                       'is-valid':!$v.create.year.$invalid && !errors.year,
                            }"
                        ></date-picker>
                        <template v-if="errors.year">
                            <ErrorMessage v-for="(errorMessage,index) in errors.year"
                                          :key="index">
                                {{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('start_date')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("financial_year_start_date") }}
                            <span
                                v-if="isRequired('start_date')"
                                class="text-danger"
                            >*</span
                            >
                        </label>
                        <date-picker
                            v-model="create.start_date"
                            type="date"
                            format="YYYY-MM-DD"
                            valueType="format"
                        ></date-picker>
                        <div
                            v-if="!$v.create.start_date.required"
                            class="invalid-feedback"
                        >
                            {{ $t("general.fieldIsRequired") }}
                        </div>
                        <template v-if="errors.start_date">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.start_date"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('end_date')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("financial_year_end_date") }}
                            <span v-if="isRequired('end_date')" class="text-danger"
                            >*</span
                            >
                        </label>
                        <date-picker
                            v-model="create.end_date"
                            type="date"
                            format="YYYY-MM-DD"
                            valueType="format"
                        ></date-picker>
                        <div
                            v-if="!$v.create.end_date.required"
                            class="invalid-feedback"
                        >
                            {{ $t("general.fieldIsRequired") }}
                        </div>
                        <template v-if="errors.end_date">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.end_date"
                                :key="index"
                            >{{ errorMessage }}</ErrorMessage
                            >
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('is_active')">
                    <div class="form-group">
                        <label class="mr-2">
                            {{ getCompanyKey("financial_year_default") }}
                            <span v-if="isRequired('is_active')" class="text-danger">*</span>
                        </label>
                        <b-form-group
                            :class="{ 'is-invalid': $v.create.is_active.$error || errors.is_active,'is-valid': !$v.create.is_active.$invalid && !errors.is_active,}"
                        >
                            <b-form-radio class="d-inline-block"
                                          v-model="$v.create.is_active.$model"
                                          name="create_def_some-radios" value="1">
                                {{ $t("general.Yes") }}
                            </b-form-radio>
                            <b-form-radio class="d-inline-block m-1"
                                          v-model="$v.create.is_active.$model"
                                          name="create_def_some-radios" value="0">
                                {{ $t("general.No") }}
                            </b-form-radio>
                        </b-form-group>
                        <template v-if="errors.is_active">
                            <ErrorMessage v-for="(errorMessage, index) in errors.is_active"
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

<script>
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import DatePicker from "vue2-datepicker";
import {maxLength, minLength, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import {formatDateOnly} from "../../../helper/startDate";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

export default {
    mixins: [transMixinComp,successError],
    components: {
        ErrorMessage,
        loader,
        DatePicker,
    },
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/financial-years'}
    },
    data() {
        return {
            isCustom: false,
            fields: [],
            isLoader: false,
            create: {
                name: "",
                name_e: "",
                start_date: null,
                end_date: null,
                custom_date_start: this.formatDate(new Date()),
                custom_date_end: null,
                is_active: 0,
                year: '',
            },
            errors: {},
            is_disabled: false,
            company_id: null,
        };
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
            start_date: {
                required: requiredIf(function (model) {
                    return this.isRequired("start_date");
                }),
            },
            end_date: {
                required: requiredIf(function (model) {
                    return this.isRequired("name");
                }),
            },
            is_active: {
                required: requiredIf(function (model) {
                    return this.isRequired("is_active");
                })
            },
            year: {
                required: requiredIf(function (model) {
                    return this.isRequired("year");
                })
            },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        getCustomTableFields() {
            this.isCustom = true;
            adminApi
                .get(`/customTable/table-columns/general_financial_years`)
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
                name: "",
                name_e: "",
                start_date: null,
                end_date: null,
                custom_date_start: this.formatDate(new Date()),
                custom_date_end: null,
                is_active: 0,
                year: '',
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(`create`);
        },
        resetModal() {
            this.defaultData();
            setTimeout( () => {
                if(this.type != 'edit'){
                    if(!this.isPage)  this.getCustomTableFields();
                }else {
                    if(this.idObjEdit.dataObj){
                        let financialYear = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.name = financialYear.name;
                        this.create.name_e = financialYear.name_e;
                        this.create.custom_date_start = financialYear.start_date;
                        this.create.custom_date_end = financialYear.end_date;
                        this.create.start_date = financialYear.start_date;
                        this.create.end_date = financialYear.end_date;
                        this.create.is_active = financialYear.is_active;
                        this.create.year = financialYear.year;
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
                const { name, name_e, start_date, end_date,is_active,year } = this.create;
                if(this.type != 'edit') {
                    adminApi
                        .post(this.url, {
                            name, name_e,
                            start_date, end_date,
                            is_active,year,
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
                }else {
                    adminApi
                        .put(`${this.url}/${this.idObjEdit.idEdit}`, {
                            name, name_e,
                            start_date, end_date,
                            is_active,year
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
        formatDate(value) {
            return formatDateOnly(value);
        },
        arabicValueName(txt) {
            this.create.name = arabicValue(txt);
        },
        englishValueName(txt) {
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
