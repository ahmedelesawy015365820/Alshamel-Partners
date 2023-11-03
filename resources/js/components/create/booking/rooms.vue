<template>
    <div>
        <Floor
            :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
            :isPage="false" type="create" :isPermission="isPermission"
            :id="'floor-create'" @created="getFloors"
        />
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit'?getCompanyKey('room_create_form'):getCompanyKey('room_edit_form')"
            title-class="font-18"
            body-class="p-4"
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
                    <div v-if="isVisible('booking_floor_id')" class="col-md-6">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("room_floor") }}
                                <span v-if="isRequired('booking_floor_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                @input="showFloorModal"
                                v-model="create.booking_floor_id"
                                :options="floors.map((type) => type.id)"
                                :custom-label="(opt) => $i18n.locale == 'ar'? floors.find((x) => x.id == opt).name: floors.find((x) => x.id == opt).name_e"
                            >
                            </multiselect>
                            <div  v-if="$v.create.booking_floor_id.$error || errors.booking_floor_id" class="text-danger">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.booking_floor_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.booking_floor_id" :key="index">{{ errorMessage }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('unit_status_id')" class="col-md-6">
                        <div class="form-group">
                            <label>
                                {{ getCompanyKey('room_unit_status') }}
                                <span v-if="isRequired('unit_status_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                v-model="create.unit_status_id"
                                :options="statuses.map((type) => type.id)"
                                :custom-label=" (opt) => statuses.find((x) => x.id == opt)?
                                            $i18n.locale == 'ar' ? statuses.find((x) => x.id == opt).name : statuses.find((x) => x.id == opt).name_e
                                            : null
                                        "
                                :class="{'is-invalid': $v.create.unit_status_id.$error || errors.unit_status_id,'is-valid': !$v.create.unit_status_id.$invalid && !errors.unit_status_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.unit_status_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>

                            <template v-if="errors.unit_status_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.unit_status_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('code')" class="col-md-6">
                        <div class="form-group">
                            <label for="field-4353" class="control-label">
                                {{ getCompanyKey("room_code") }}
                                <span v-if="isRequired('code')" class="text-danger">*</span>
                            </label>
                            <div dir="ltr">
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="$v.create.code.$model"
                                    :class="{
                                        'is-invalid': $v.create.code.$error || errors.code,
                                        'is-valid':!$v.create.code.$invalid && !errors.code,
                                    }"
                                    id="field-4353"
                                />
                            </div>
                            <div v-if="!$v.create.code.maxLength" class="invalid-feedback">
                                {{ $t("general.Itmustbeatmost") }}
                                {{ $v.create.code.$params.maxLength.max }}
                                {{ $t("general.letters") }}
                            </div>
                            <template v-if="errors.code">
                                <ErrorMessage v-for="(errorMessage, index) in errors.code" :key="index">{{ errorMessage }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('price')" class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("room_price") }}
                                <span v-if="isRequired('price')" class="text-danger">*</span>
                            </label>
                            <div dir="ltr">
                                <input
                                    type="number"
                                    step="any"
                                    class="form-control"
                                    v-model="$v.create.price.$model"
                                    :class="{
                                                        'is-invalid':
                                                          $v.create.price.$error || errors.price,
                                                        'is-valid':
                                                          !$v.create.price.$invalid && !errors.price,
                                                      }"
                                />
                            </div>
                            <template v-if="errors.price">
                                <ErrorMessage v-for="(errorMessage, index) in errors.price" :key="index">{{ errorMessage }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('name')">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey('room_name_ar') }}
                                <span v-if="isRequired('name')" class="text-danger">*</span>
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
                    <div class="col-md-6" v-if="isVisible('name_e')">
                        <div class="form-group">
                            <label for="field-2" class="control-label">
                                {{ getCompanyKey('room_name_en') }}
                                <span v-if="isRequired('name_e')" class="text-danger">*</span>
                            </label>
                            <div dir="ltr">
                                <input
                                    type="text"
                                    class="form-control englishInput"
                                    data-create="2"
                                    @keyup="englishValueName(create.name_e)"
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
                    <div v-if="isVisible('number_of_individuals')" class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("room_number_of_individuals") }}
                                <span v-if="isRequired('number_of_individuals')" class="text-danger">*</span>
                            </label>
                            <div dir="ltr">
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model="$v.create.number_of_individuals.$model"
                                    :class="{
                                                        'is-invalid':
                                                          $v.create.number_of_individuals.$error || errors.number_of_individuals,
                                                        'is-valid':
                                                          !$v.create.number_of_individuals.$invalid && !errors.number_of_individuals,
                                                      }"
                                />
                            </div>
                            <template v-if="errors.number_of_individuals">
                                <ErrorMessage v-for="(errorMessage, index) in errors.number_of_individuals" :key="index">{{ errorMessage }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div v-if="isVisible('extra_guest_price')" class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">
                                {{ getCompanyKey("room_extra_guest_price") }}
                                <span v-if="isRequired('extra_guest_price')" class="text-danger">*</span>
                            </label>
                            <div dir="ltr">
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model="$v.create.extra_guest_price.$model"
                                    :class="{
                                                        'is-invalid':
                                                          $v.create.extra_guest_price.$error || errors.extra_guest_price,
                                                        'is-valid':
                                                          !$v.create.extra_guest_price.$invalid && !errors.extra_guest_price,
                                                      }"
                                />
                            </div>
                            <template v-if="errors.extra_guest_price">
                                <ErrorMessage v-for="(errorMessage, index) in errors.extra_guest_price" :key="index">{{ errorMessage }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('description')">
                        <div class="form-group">
                            <label class="mr-2">
                                {{getCompanyKey("room_description_ar")}}
                                <span v-if="isVisible('description')" class="text-danger">*</span>
                            </label>
                            <textarea
                                @input="arabicValueDescription(create.description)"
                                v-model="$v.create.description.$model"
                                class="form-control"
                                :maxlength="1000"
                                rows="5"
                            ></textarea>
                            <template v-if="errors.description">
                                <ErrorMessage v-for="(errorMessage, index) in errors.description" :key="index">{{ errorMessage }}</ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-6" v-if="isVisible('description_e')">
                        <div class="form-group">
                            <label class="mr-2">
                                {{ getCompanyKey("room_description_en")}}
                                <span v-if="isRequired('description_e')" class="text-danger">*</span>
                            </label>
                            <textarea
                                @input="englishValueDescription(create.description_e)"
                                v-model="$v.create.description_e.$model"
                                class="form-control"
                                :maxlength="1000"
                                rows="5"
                            ></textarea>
                            <template v-if="errors.description_e">
                                <ErrorMessage v-for="(errorMessage, index) in errors.description_e" :key="index">{{ errorMessage }}</ErrorMessage>
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
import {maxLength, minLength, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import Swal from "sweetalert2";
import Floor from "./floor.vue";
import Multiselect from "vue-multiselect";

export default {
    name: "Services",
    components: {
        ErrorMessage,
        loader,
        Floor,
        Multiselect
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/booking/units'}
    },
    data() {
        return {
            fields: [],
            statuses: [],
            floors: [],
            isLoader: false,
            isCustom: false,
            company_id:null,
            create: {
                name: "",
                name_e: "",
                code: "",
                price: "",
                description: "",
                description_e: "",
                unit_status_id: 12,
                number_of_individuals:1,
                extra_guest_price:0,
                booking_floor_id:null,
            },
            errors: {},
            is_disabled: false,
        };
    },
    validations: {
        create: {
            name: { required: requiredIf(function (model) {
                    return this.isRequired("name");
                }), minLength: minLength(2), maxLength: maxLength(150) },
            name_e: { required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(2), maxLength: maxLength(150) },
            code: { required: requiredIf(function (model) {
                    return this.isRequired("code");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            price: { required: requiredIf(function (model) {
                    return this.isRequired("price");
                }),
            },
            description: { required: requiredIf(function (model) {
                    return this.isRequired("description");
                }), maxLength: maxLength(1000),
            },
            description_e: { required: requiredIf(function (model) {
                    return this.isRequired("description_e");
                }), maxLength: maxLength(1000),
            },
            unit_status_id: { required: requiredIf(function (model) {
                    return this.isRequired("unit_status_id");
                }),
            },
            number_of_individuals: { required: requiredIf(function (model) {
                    return this.isRequired("number_of_individuals");
                }),
            },
            extra_guest_price: { required: requiredIf(function (model) {
                    return this.isRequired("extra_guest_price");
                }),
            },
            booking_floor_id: { required: requiredIf(function (model) {
                    return this.isRequired("extra_guest_price");
                }),
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
                .get(`/customTable/table-columns/booking_units`)
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
                code: "",
                price: "",
                description: "",
                description_e: "",
                unit_status_id: 12,
                number_of_individuals:1,
                extra_guest_price:0,
                booking_floor_id:null,
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
                    if (this.isVisible("booking_floor_id"))  this.getFloors();
                    if (this.isVisible("unit_status_id"))  this.getStatus();
                }else {
                    if(this.idObjEdit.dataObj){
                        let brand = this.idObjEdit.dataObj;
                        if (this.isVisible("booking_floor_id"))  this.getFloors();
                        if (this.isVisible("unit_status_id"))  this.getStatus();
                        this.errors = {};
                        this.create.name = brand.name;
                        this.create.name_e = brand.name_e;
                        this.create.description = brand.description;
                        this.create.description_e = brand.description_e;
                        this.create.code = brand.code;
                        this.create.price = brand.price;
                        this.create.unit_status_id =  brand.unit_status_id;
                        this.create.number_of_individuals = brand.number_of_individuals;
                        this.create.extra_guest_price = brand.extra_guest_price;
                        this.create.booking_floor_id = brand.booking_floor_id;
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
            if (!this.create.description) {
                this.create.description = this.create.description_e;
            }
            if (!this.create.description_e) {
                this.create.description_e = this.create.description;
            }
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                if(this.type != 'edit'){
                    adminApi
                        .post(this.url, {...this.create,company_id:this.company_id})
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
        getFloors() {
            this.isLoader = true;
             adminApi.get(`/booking/floors`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    if (this.isPermission("create floors")) {
                        l.unshift({ id: 0, name: "اضاف طابق", name_e: "Add Floor" });
                    }
                    this.floors = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        getStatus(){
            this.isLoader = true;
            adminApi.get(`/statuses/get-drop-down?module_type=booking`)
                .then((res) => {
                    let l = res.data.data;
                    this.statuses = l;
                })
                .catch((err) => {
                    this.errorFun("Error", "Thereisanerrorinthesystem");
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        arabicValueName(txt){
            this.create.name = arabicValue(txt);
        },
        englishValueName(txt){
            this.create.name_e = englishValue(txt);
        },
        arabicValueDescription(txt) {
            this.create.description = arabicValue(txt);
        },
        englishValueDescription(txt) {
            this.create.description_e = englishValue(txt);
        },
        showFloorModal() {
            if (this.create.booking_floor_id == 0) {
                this.$bvModal.show("floor-create");
                this.create.booking_floor_id = null;
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
