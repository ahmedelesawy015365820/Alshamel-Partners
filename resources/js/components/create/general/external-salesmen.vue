<template>
    <div>
        <Country
            :id="'country-create-external-salesman'"
            :companyKeys="companyKeys"
            :defaultsKeys="defaultsKeys"
            @created="getCategory"
        />
        <b-modal
            :id="id"
            :title="type != 'edit'?getCompanyKey('external_sale_man_create_form'):getCompanyKey('external_sale_man_create_form')"
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
                    <div class="col-md-6" v-if="isVisible('country_id')">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("external_saleman_country") }}
                                <span
                                    v-if="isRequired('country_id')"
                                    class="text-danger"
                                >*</span
                                >
                            </label>
                            <multiselect
                                @input="showCountryModal"
                                v-model="create.country_id"
                                :options="countries.map((type) => type.id)"
                                :custom-label="
                          (opt) => countries.find((x) => x.id == opt)?countries.find((x) => x.id == opt).name: null
                        "
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.country_id.$error || errors.country_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.country_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.country_id"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('phone')">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey("external_sale_man_phone") }}
                                <span v-if="isRequired('phone')" class="text-danger"
                                >*</span
                                >
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                v-model="$v.create.phone.$model"
                                :class="{
                          'is-invalid': $v.create.phone.$error || errors.phone,
                          'is-valid':
                            !$v.create.phone.$invalid && !errors.phone,
                        }"
                                id="field-1"
                            />
                            <div
                                v-if="!$v.create.phone.minLength"
                                class="invalid-feedback"
                            >
                                {{ $t("general.Itmustbeatleast") }}
                                {{ $v.create.phone.$params.minLength.min }}
                                {{ $t("general.letters") }}
                            </div>
                            <div
                                v-if="!$v.create.phone.maxLength"
                                class="invalid-feedback"
                            >
                                {{ $t("general.Itmustbeatmost") }}
                                {{ $v.create.phone.$params.maxLength.max }}
                                {{ $t("general.letters") }}
                            </div>
                            <template v-if="errors.phone">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.phone"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('email')">
                        <div class="form-group">
                            <label for="field-2" class="control-label">
                                {{ getCompanyKey("external_sale_man_email") }}
                                <span v-if="isRequired('email')" class="text-danger"
                                >*</span
                                >
                            </label>
                            <input
                                type="email"
                                class="form-control"
                                v-model="$v.create.email.$model"
                                :class="{
                          'is-invalid': $v.create.email.$error || errors.email,
                          'is-valid':
                            !$v.create.email.$invalid && !errors.email,
                        }"
                                id="field-2"
                            />
                            <div
                                v-if="!$v.create.email.maxLength"
                                class="invalid-feedback"
                            >
                                {{ $t("general.Itmustbeatmost") }}
                                {{ $v.create.email.$params.maxLength.max }}
                                {{ $t("general.letters") }}
                            </div>
                            <template v-if="errors.email">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.email"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('national_id')">
                        <div class="form-group">
                            <label for="field-4" class="control-label">
                                {{ getCompanyKey("external_sale_man_national_id") }}
                                <span
                                    v-if="isRequired('national_id')"
                                    class="text-danger"
                                >*</span
                                >
                            </label>
                            <input
                                type="text"
                                class="form-control"
                                v-model.trim="$v.create.national_id.$model"
                                :class="{
                          'is-invalid':
                            $v.create.national_id.$error || errors.national_id,
                          'is-valid':
                            !$v.create.national_id.$invalid &&
                            !errors.national_id,
                        }"
                                id="field-4"
                            />
                            <template v-if="errors.national_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.national_id"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('address')">
                        <div class="form-group">
                            <label for="field-7" class="control-label">
                                {{ getCompanyKey("external_sale_man_address") }}
                                <span v-if="isRequired('address')" class="text-danger"
                                >*</span
                                >
                            </label>
                            <input
                                type="email"
                                class="form-control"
                                v-model="$v.create.address.$model"
                                :class="{
                          'is-invalid':
                            $v.create.address.$error || errors.address,
                          'is-valid':
                            !$v.create.address.$invalid && !errors.address,
                        }"
                                id="field-7"
                            />
                            <div
                                v-if="!$v.create.address.minLength"
                                class="invalid-feedback"
                            >
                                {{ $t("general.Itmustbeatleast") }}
                                {{ $v.create.address.$params.minLength.min }}
                                {{ $t("general.letters") }}
                            </div>
                            <div
                                v-if="!$v.create.address.maxLength"
                                class="invalid-feedback"
                            >
                                {{ $t("general.Itmustbeatmost") }}
                                {{ $v.create.address.$params.maxLength.max }}
                                {{ $t("general.letters") }}
                            </div>
                            <template v-if="errors.address">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.address"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('rp_code')">
                        <div class="form-group">
                            <label for="field-8" class="control-label">
                                {{ getCompanyKey("external_sale_man_code") }}
                                <span v-if="isRequired('rp_code')" class="text-danger"
                                >*</span
                                >
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                v-model="$v.create.rp_code.$model"
                                :class="{
                          'is-invalid':
                            $v.create.rp_code.$error || errors.rp_code,
                          'is-valid':
                            !$v.create.rp_code.$invalid && !errors.rp_code,
                        }"
                                id="field-8"
                            />
                            <div
                                v-if="!$v.create.rp_code.maxLength"
                                class="invalid-feedback"
                            >
                                {{ $t("general.Itmustbeatmost") }}
                                {{ $v.create.rp_code.$params.maxLength.max }}
                                {{ $t("general.letters") }}
                            </div>
                            <template v-if="errors.rp_code">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.rp_code"
                                    :key="index"
                                >{{ errorMessage }}</ErrorMessage
                                >
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('is_active')">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("external_sale_man_status") }}
                                <span v-if="isRequired('is_active')" class="text-danger"
                                >*</span
                                >
                            </label>
                            <b-form-group
                                :class="{
                          'is-invalid':
                            $v.create.is_active.$error || errors.is_active,
                          'is-valid':
                            !$v.create.is_active.$invalid && !errors.is_active,
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
    </div>
</template>

<script>
import Multiselect from "vue-multiselect";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Country from "./country";
import {email, integer, maxLength, minLength, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import {formatDateOnly} from "../../../helper/startDate";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

export default {
    mixins: [transMixinComp,successError],
    components: {
        Multiselect,
        ErrorMessage,
        loader,
        Country,
    },
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/external-salesmen'}
    },
    data() {
        return {
            isCustom: false,
            fields: [],
            isLoader: false,
            create: {
                phone: "",
                address: "",
                rp_code: "",
                email: "",
                country_id: null,
                national_id: null,
                is_active: "active",
            },
            errors: {},
            company_id: null,
            is_disabled: false,
            countries: [],
        };
    },
    validations: {
        create: {
            // name: { required: requiredIf(function (model) {
            //         return this.isRequired("name");
            //     }) , minLength: minLength(2), maxLength: maxLength(100) },
            // name_e: { required: requiredIf(function (model) {
            //         return this.isRequired("name_e");
            //     }) , minLength: minLength(2), maxLength: maxLength(100) },
            phone: {
                required: requiredIf(function (model) {
                    return this.isRequired("phone");
                }),
                minLength: minLength(8),
                maxLength: maxLength(20),
                integer,
            },
            address: {
                required: requiredIf(function (model) {
                    return this.isRequired("address");
                }),
                minLength: minLength(5),
                maxLength: maxLength(255),
            },
            email: {
                required: requiredIf(function (model) {
                    return this.isRequired("email");
                }),
                email,
                maxLength: maxLength(100),
            },
            rp_code: {
                required: requiredIf(function (model) {
                    return this.isRequired("rp_code");
                }),
                maxLength: maxLength(20),
            },
            country_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("country_id");
                }),
            },
            national_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("national_id");
                }),
            },
            is_active: {
                required: requiredIf(function (model) {
                    return this.isRequired("is_active");
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
                .get(`/customTable/table-columns/general_external_salesmen`)
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
        defaultData(edit = null){
            this.create = {
                phone: "",
                address: "",
                rp_code: "",
                email: "",
                country_id: null,
                national_id: null,
                is_active: "active",
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
                    if (this.isVisible("country_id")) this.getCategory();
                }else {
                    if(this.idObjEdit.dataObj){
                        let externalSalesmen = this.idObjEdit.dataObj;
                        if (this.isVisible("country_id")) this.getCategory();
                        this.create.country_id = externalSalesmen.country.id ?? null;
                        this.create.national_id = externalSalesmen.national_id;
                        this.create.phone = externalSalesmen.phone;
                        this.create.email = externalSalesmen.email;
                        this.create.rp_code = externalSalesmen.rp_code;
                        this.create.address = externalSalesmen.address;
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
        getCategory() {
            this.isLoader = true;
            this.countries = [];
            adminApi
                .get(`/countries/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Country')){
                        l.unshift({ id: 0, name: "اضف دولة", name_e: "Add Country" });
                    }
                    this.countries = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        showCountryModal() {
            if (this.create.country_id == 0) {
                this.$bvModal.show("country-create-external-salesman");
                this.create.country_id = null;
            }
        },
        arabicValue(txt){
            this.create.name = arabicValue(txt);
        },
        englishValue(txt){
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
