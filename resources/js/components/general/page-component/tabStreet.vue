<template>
<div>
    <!--  create   -->
    <b-modal id="tab-street-create" :title="getCompanyKey('street_create_form')" title-class="font-18" body-class="p-4 "
             :hide-footer="true" @show="resetModal" @hidden="resetModalHidden">
        <form>
            <div class="mb-3 d-flex justify-content-end">
                <b-button variant="success" :disabled="!is_disabled" @click.prevent="resetForm" type="button"
                          :class="['font-weight-bold px-2', is_disabled ? 'mx-2' : '']">
                    {{ $t("general.AddNewRecord") }}
                </b-button>
                <template v-if="!is_disabled">
                    <b-button variant="success" type="submit" class="mx-1" v-if="!isLoader" @click.prevent="AddSubmit">
                        {{ $t("general.Add") }}
                    </b-button>
                    <b-button variant="success" class="mx-1" disabled v-else>
                        <b-spinner small></b-spinner>
                        <span class="sr-only">{{ $t("login.Loading") }}...</span>
                    </b-button>

                </template>
                <b-button @click.prevent="resetModalHidden" variant="danger" type="button">
                    {{ $t("general.Cancel") }}
                </b-button>
            </div>
            <div class="row">
                <div class="col-md-12" v-if="isVisible('name')">
                    <div class="form-group">
                        <label for="field-1" class="control-label">
                            {{ getCompanyKey("street_name_ar") }}
                            <span v-if="isRequired('name')" class="text-danger">*</span>
                        </label>
                        <div dir="rtl">
                            <input type="text" class="form-control arabicInput" data-create="1"
                                   v-model="$v.create.name.$model" :class="{
                            'is-invalid': $v.create.name.$error || errors.name,
                            'is-valid': !$v.create.name.$invalid && !errors.name,
                          }" @keyup="arabicValue(create.name)" id="field-1" />
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
                            <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('name_e')">
                    <div class="form-group">
                        <label for="field-2" class="control-label">
                            {{ getCompanyKey("street_name_en") }}
                            <span v-if="isRequired('name_e')" class="text-danger">*</span>
                        </label>
                        <div dir="ltr">
                            <input type="text" class="form-control englishInput" data-create="2"
                                   v-model="$v.create.name_e.$model" :class="{
                            'is-invalid': $v.create.name_e.$error || errors.name_e,
                            'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                          }" @keyup="englishValue(create.name_e)" id="field-2" />
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
                            <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index">{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('is_active')">
                    <div class="form-group">
                        <label class="mr-2">
                            {{ getCompanyKey("street_status") }}
                            <span v-if="isRequired('is_active')" class="text-danger">*</span>
                        </label>
                        <b-form-group :class="{
                        'is-invalid': $v.create.is_active.$error || errors.is_active,
                        'is-valid': !$v.create.is_active.$invalid && !errors.is_active,
                      }">
                            <b-form-radio class="d-inline-block" v-model="$v.create.is_active.$model" name="some-radios"
                                          value="active">{{ $t("general.Active") }}</b-form-radio>
                            <b-form-radio class="d-inline-block m-1" v-model="$v.create.is_active.$model" name="some-radios"
                                          value="inactive">{{ $t("general.Inactive") }}</b-form-radio>
                        </b-form-group>
                        <template v-if="errors.is_active">
                            <ErrorMessage v-for="(errorMessage, index) in errors.is_active" :key="index">{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
            </div>
        </form>
    </b-modal>
    <!--  /create   -->
    <!--  edit   -->
    <b-modal :id="`tab-street-edit`" :title="getCompanyKey('street_edit_form')"
             title-class="font-18" body-class="p-4" :hide-footer="true"
              @hidden="resetModalHiddenEdit">
        <form>
            <div class="mb-3 d-flex justify-content-end">
                <!-- Emulate built in modal footer ok and cancel button actions -->
                <b-button variant="success" type="submit" class="mx-1" v-if="!isLoader"
                          @click.prevent="editSubmit(editStreet.id)">
                    {{ $t("general.Edit") }}
                </b-button>

                <b-button variant="success" class="mx-1" disabled v-else>
                    <b-spinner small></b-spinner>
                    <span class="sr-only">{{ $t("login.Loading") }}...</span>
                </b-button>

                <b-button variant="danger" type="button"
                          @click.prevent="resetModalHiddenEdit">
                    {{ $t("general.Cancel") }}
                </b-button>
            </div>
            <div class="row">
                <div class="col-md-12" v-if="isVisible('name')">
                    <div class="form-group">
                        <label for="edit-1" class="control-label">
                            {{ getCompanyKey("street_name_ar") }}
                            <span v-if="isRequired('name')" class="text-danger">*</span>
                        </label>
                        <div dir="rtl">
                            <input type="text" class="form-control arabicInput" data-edit="1"
                                   v-model="$v.edit.name.$model" :class="{
                                      'is-invalid': $v.edit.name.$error || errors.name,
                                      'is-valid': !$v.edit.name.$invalid && !errors.name,
                                    }" @keyup="arabicValue(edit.name)" id="edit-1" />
                        </div>
                        <div v-if="!$v.edit.name.minLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatleast") }}
                            {{ $v.edit.name.$params.minLength.min }}
                            {{ $t("general.letters") }}
                        </div>
                        <div v-if="!$v.edit.name.maxLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatmost") }}
                            {{ $v.edit.name.$params.maxLength.max }}
                            {{ $t("general.letters") }}
                        </div>
                        <template v-if="errors.name">
                            <ErrorMessage v-for="(errorMessage, index) in errors.name" :key="index">{{ errorMessage
                                }}</ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('name_e')">
                    <div class="form-group">
                        <label for="edit-2" class="control-label">
                            {{ getCompanyKey("street_name_en") }}
                            <span v-if="isRequired('name_e')" class="text-danger">*</span>
                        </label>
                        <div dir="ltr">
                            <input type="text" class="form-control englishInput" data-edit="2"
                                   v-model="$v.edit.name_e.$model"
                                   :class="{
                                      'is-invalid':
                                        $v.edit.name_e.$error || errors.name_e,
                                      'is-valid':
                                        !$v.edit.name_e.$invalid && !errors.name_e,
                                    }" @keyup="englishValue(edit.name_e)" id="edit-2" />
                        </div>
                        <div v-if="!$v.edit.name_e.minLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatleast") }}
                            {{ $v.edit.name_e.$params.minLength.min }}
                            {{ $t("general.letters") }}
                        </div>
                        <div v-if="!$v.edit.name_e.maxLength" class="invalid-feedback">
                            {{ $t("general.Itmustbeatmost") }}
                            {{ $v.edit.name_e.$params.maxLength.max }}
                            {{ $t("general.letters") }}
                        </div>
                        <template v-if="errors.name_e">
                            <ErrorMessage v-for="(errorMessage, index) in errors.name_e" :key="index">{{
                                    errorMessage }}</ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('is_active')">
                    <div class="form-group">
                        <label class="mr-2">
                            {{ getCompanyKey("street_status") }}
                            <span v-if="isRequired('is_active')" class="text-danger">*</span>
                        </label>
                        <b-form-group :class="{
                                  'is-invalid':
                                    $v.edit.is_active.$error || errors.is_active,
                                  'is-valid':
                                    !$v.edit.is_active.$invalid && !errors.is_active,
                                }">
                            <b-form-radio class="d-inline-block" v-model="$v.edit.is_active.$model"
                                          name="some-radios" value="active">{{ $t("general.Active") }}</b-form-radio>
                            <b-form-radio class="d-inline-block m-1" v-model="$v.edit.is_active.$model"
                                          name="some-radios" value="inactive">{{ $t("general.Inactive") }}</b-form-radio>
                        </b-form-group>
                        <template v-if="errors.is_active">
                            <ErrorMessage v-for="(errorMessage, index) in errors.is_active" :key="index">{{
                                    errorMessage }}</ErrorMessage>
                        </template>
                    </div>
                </div>
            </div>
        </form>
    </b-modal>
    <!--  /edit   -->
</div>
</template>

<script>
import adminApi from "../../../api/adminAxios";
import {
    required,
    minLength,
    maxLength,
    integer,
    alpha,
    requiredIf,
} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import Switches from "vue-switches";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../loader";
import Multiselect from "vue-multiselect";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import { dynamicSortNumber, dynamicSortString } from "../../../helper/tableSort";
import { formatDateOnly } from "../../../helper/startDate";
import { arabicValue, englishValue } from "../../../helper/langTransform";

export default {
    name: "tabStreet",
    components: {
        Switches,
        Multiselect,
        ErrorMessage,
        loader,
    },
    mixins: [transMixinComp],
    data() {
        return {
            fields: [],
            isLoader: false,
            create: {
                name: "",
                name_e: "",
                is_active: "active",
            },
            edit: {
                name: "",
                name_e: "",
                is_active: "active",
            },
            company_id: null,
            errors: {},
            isCheckAll: false,
            checkAll: [],
            is_disabled: false,
        };
    },
    validations: {
        create: {
            name: {
                required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(100),
            },
            name_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(2), maxLength: maxLength(100),
            },
            is_active: {
                required: requiredIf(function (model) {
                    return this.isRequired("is_active");
                })
            },
        },
        edit: {

            name: {
                required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(100)
            },
            name_e: {
                required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(2), maxLength: maxLength(100),
            },
            is_active: {
                required: requiredIf(function (model) {
                    return this.isRequired("is_active");
                })
            },
        },
    },
    mounted() {
        this.getCustomTableFields();
        this.company_id = this.$store.getters["auth/company_id"];
    },
    watch: {
        editStreet(after,befour){
            this.edit.name = after.name;
            this.edit.name_e = after.name_e;
            this.edit.is_active = after.is_active;
        }
    },
    props: [
        "companyKeys",
        "defaultsKeys",
        "avenue_id",
        "editStreet",
    ],
    methods: {
        getCustomTableFields() {
            adminApi
                .get(`/customTable/table-columns/general_streets`)
                .then((res) => {
                    this.fields = res.data;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        isVisible(fieldName) {
            let res = this.fields.filter((field) => {
                return field.column_name == fieldName;
            });
            return res.length > 0 && res[0].is_visible == 1 ? true : false;
        },
        isRequired(fieldName) {
            let res = this.fields.filter((field) => {
                return field.column_name == fieldName;
            });
            return res.length > 0 && res[0].is_required == 1 ? true : false;
        },
        arabicValue(txt) {
            this.create.name = arabicValue(txt);
            this.edit.name = arabicValue(txt);
        },
        englishValue(txt) {
            this.create.name_e = englishValue(txt);
            this.edit.name_e = englishValue(txt);
        },
        resetModalHiddenEdit() {
            this.errors = {};
            this.edit = {
                name: "",
                name_e: "",
                is_active: "active",
            };
            this.$bvModal.hide(`tab-street-edit`);
        },
        resetModalHidden() {
            this.create = {
                name: "",
                name_e: "",
                is_active: "active",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
            this.$bvModal.hide(`tab-street-create`);
        },
        /**
         *  hidden Modal (create)
         */
        resetModal() {
            this.create = {
                name: "",
                name_e: "",
                is_active: "active",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  create countrie
         */
        resetForm() {
            this.create = {
                name: "",
                name_e: "",
                is_active: "active",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
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
                let data = {
                    name: this.create.name,
                    name_e: this.create.name_e,
                    avenue_id: this.avenue_id,
                    is_active: this.create.is_active,
                    company_id: this.company_id,
                };
                adminApi
                    .post(`/streets`, data)
                    .then((res) => {
                        this.is_disabled = true;
                        this.$emit("created");
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Addedsuccessfully")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: `${this.$t("general.Error")}`,
                                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                            });
                        }
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
            }
        },
        /**
         *  edit countrie
         */
        editSubmit(id) {
            if (!this.edit.name) {
                this.edit.name = this.edit.name_e;
            }
            if (!this.edit.name_e) {
                this.edit.name_e = this.edit.name;
            }
            this.$v.edit.$touch();

            if (this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                let data = {
                    name: this.edit.name,
                    name_e: this.edit.name_e,
                    avenue_id: this.avenue_id,
                    is_active: this.edit.is_active,
                    company_id: this.company_id,
                };
                adminApi
                    .put(`/streets/${id}`, data)
                    .then((res) => {
                        this.$bvModal.hide(`tab-street-edit`);
                        this.$emit("created");
                        setTimeout(() => {
                            Swal.fire({
                                icon: "success",
                                text: `${this.$t("general.Editsuccessfully")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: `${this.$t("general.Error")}`,
                                text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                            });
                        }
                    })
                    .finally(() => {
                        this.isLoader = false;
                    });
            }
        },
    }
}
</script>

<style scoped>

</style>
