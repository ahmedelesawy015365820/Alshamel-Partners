<template>
    <div>
        <b-modal
            :id="id"
            :title="type != 'edit'?getCompanyKey('attendance_times_create_form'):getCompanyKey('attendance_times_edit_form')"
            title-class="font-18"
            size="lg"
            :hide-footer="true"
            body-class="bankAccount"
            @show="resetModal"
            @hidden="resetModalHidden"
        >
            <form>
                <loader size="large" v-if="isCustom && !isPage" />
                <div class="card">
                    <div class="card-body">
                        <div class="mb-1 d-flex justify-content-end">
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
                                    {{ type != "edit" ? $t("general.Add") : $t("general.edit") }}
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
                    </div>
                    <b-tabs nav-class="nav-tabs nav-bordered">
                        <b-tab :title="$t('general.DataEntry')" active>
                            <div class="row">
                                <div class="col-md-6 direction" dir="rtl" v-if="isVisible('name')">
                                    <div class="form-group">
                                        <label for="field-1" class="control-label">
                                            {{ getCompanyKey("attendance_times_name_ar") }}
                                            <span v-if="isRequired('name')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            @keyup="arabicValue(create.name)"
                                            type="text"
                                            class="form-control"
                                            v-model="$v.create.name.$model"
                                            :class="{
                  'is-invalid': $v.create.name.$error || errors.name,
                  'is-valid': !$v.create.name.$invalid && !errors.name,
                }"
                                            id="field-1"
                                        />
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
                                <div class="col-md-6 direction-ltr" dir="ltr" v-if="isVisible('name_e')">
                                    <div class="form-group">
                                        <label for="field-2" class="control-label">
                                            {{ getCompanyKey("attendance_times_name_en") }}
                                            <span v-if="isRequired('name_e')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            @keyup="englishValue(create.name_e)"
                                            type="text"
                                            class="form-control"
                                            v-model="$v.create.name_e.$model"
                                            :class="{
                  'is-invalid': $v.create.name_e.$error || errors.name_e,
                  'is-valid': !$v.create.name_e.$invalid && !errors.name_e,
                }"
                                            id="field-2"
                                        />
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
                                <div class="col-md-6" v-if="isVisible('tt_monthly_hours')">
                                    <div class="form-group">
                                        <label class="control-label">
                                            {{ getCompanyKey("attendance_times_tt_monthly_hours") }}
                                            <span v-if="isRequired('tt_monthly_hours')" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            v-model="$v.create.tt_monthly_hours.$model"
                                            :class="{
                                              'is-invalid': $v.create.tt_monthly_hours.$error || errors.tt_monthly_hours,
                                              'is-valid': !$v.create.tt_monthly_hours.$invalid && !errors.tt_monthly_hours,
                                            }"
                                        />
                                        <template v-if="errors.tt_monthly_hours">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.tt_monthly_hours"
                                                :key="index"
                                            >{{ errorMessage }}</ErrorMessage
                                            >
                                        </template>
                                    </div>
                                </div>
                                <div v-if="isVisible('timetable_types_id')" class="col-md-6" >
                                    <div class="form-group">
                                        <label class="my-1 mr-2">
                                            {{ getCompanyKey("attendance_times_tt_type") }}
                                            <span v-if="isRequired('timetable_types_id')" class="text-danger">*</span>
                                        </label>
                                        <multiselect
                                            v-model="$v.create.timetable_types_id.$model"
                                            :options="types.map((type) => type.id)"
                                            :custom-label=" (opt) => types.find((x) => x.id == opt)?
                                                                            $i18n.locale == 'ar' ? types.find((x) => x.id == opt).name : types.find((x) => x.id == opt).name_e
                                                                            : null
                                                                        "
                                            :class="{'is-invalid': $v.create.timetable_types_id.$error || errors.timetable_types_id,'is-valid': !$v.create.timetable_types_id.$invalid && !errors.timetable_types_id,}"
                                        >
                                        </multiselect>
                                        <template v-if="errors.timetable_types_id">
                                            <ErrorMessage
                                                v-for="(errorMessage, index) in errors.timetable_types_id"
                                                :key="index"
                                            >{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </b-tab>
                        <b-tab
                            :disabled="!header_id"
                            :title="$t('general.details')"
                        >
                            <div class="col-md-12 p-0 m-0">
                                <div class="page-content">
                                    <div class="px-0">
                                        <div class="row">
                                            <div class="col-12 col-lg-12">
                                                <!-- .row -->
                                                <hr class="row" />
                                                <div>
                                                    <div
                                                        class="row text-600 text-white bgc-default-tp1 py-25">
                                                        <div :class="create.timetable_types_id != 2?'col-3':'col-2'" v-if="isVisibleDetail('day_no')">
                                                            {{ getCompanyKey('attendance_detail_day_no') }}
                                                        </div>
                                                        <div :class="create.timetable_types_id != 2?'col-3':'col-2'" v-if="isVisibleDetail('shift1_from')">
                                                            {{ getCompanyKey('attendance_detail_shift1_from') }}
                                                        </div>
                                                        <div :class="create.timetable_types_id != 2?'col-3':'col-2'" v-if="isVisibleDetail('shift1_to')">
                                                            {{ getCompanyKey('attendance_detail_shift1_to') }}
                                                        </div>
                                                        <div :class="create.timetable_types_id != 2?'col-3':'col-2'" v-if="isVisibleDetail('shift2_from') && create.timetable_types_id == 2">
                                                            {{ getCompanyKey('attendance_detail_shift2_from') }}
                                                        </div>
                                                        <div :class="create.timetable_types_id != 2?'col-3':'col-2'" v-if="isVisibleDetail('shift2_to') && create.timetable_types_id == 2">
                                                            {{ getCompanyKey('attendance_detail_shift2_to') }}
                                                        </div>
                                                        <div :class="create.timetable_types_id != 2?'col-3':'col-2'" v-if="isVisibleDetail('is_off')">
                                                            {{ getCompanyKey('attendance_detail_is_off') }}
                                                        </div>
                                                    </div>
                                                    <div v-for="(item, gIndex) in time_details" class="text-95  text-secondary-d3">
                                                        <div class="row mb-2 mb-sm-0 py-25" :class="[gIndex % 2 == 0 ? 'bgc-default-l4' : '' ]" >
                                                            <div :class="create.timetable_types_id != 2?'col-3 p-0':'col-2 p-0'" v-if="isVisibleDetail('day_no')">
                                                                <div class="form-group">
                                                                    <multiselect
                                                                        :disabled="true"
                                                                        v-model="$v.time_details.$each[gIndex].day_no.$model"
                                                                        :options="week.map((type) => type.id)"
                                                                        :custom-label=" (opt) => week.find((x) => x.id == opt)?
                                                                            $i18n.locale == 'ar' ? week.find((x) => x.id == opt).name : week.find((x) => x.id == opt).name_e
                                                                            : null
                                                                        "
                                                                        :class="{'is-invalid': $v.time_details.$each[gIndex].day_no.$error || errors.day_no,'is-valid': !$v.time_details.$each[gIndex].day_no.$invalid && !errors.day_no,}"
                                                                    >
                                                                    </multiselect>
                                                                    <div v-if="!$v.time_details.$each[gIndex].day_no.required" class="invalid-feedback">
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>

                                                                    <template v-if="errors[`time_details.${gIndex}.day_no`]">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors[`time_details.${gIndex}.day_no`]"
                                                                                      :key="index">{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div :class="create.timetable_types_id != 2?'col-3 p-0':'col-2 p-0'" v-if="isVisibleDetail('shift1_from')">
                                                                <div class="form-group">
                                                                    <date-picker
                                                                        type="time"
                                                                        v-model="$v.time_details.$each[gIndex].shift1_from.$model"
                                                                        format="HH:mm:ss"
                                                                        valueType="format"
                                                                        :confirm="false"
                                                                    >
                                                                    </date-picker>
                                                                    <div v-if="!$v.time_details.$each[gIndex].shift1_from.required" class="invalid-feedback">
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>
                                                                    <template v-if="errors[`time_details.${gIndex}.shift1_from`]">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors[`time_details.${gIndex}.shift1_from`]" :key="index">
                                                                            {{
                                                                                errorMessage
                                                                            }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div :class="create.timetable_types_id != 2?'col-3 p-0':'col-2 p-0'" v-if="isVisibleDetail('shift1_to')">
                                                                <div class="form-group">
                                                                    <date-picker
                                                                        type="time"
                                                                        v-model="$v.time_details.$each[gIndex].shift1_to.$model"
                                                                        format="HH:mm:ss"
                                                                        valueType="format"
                                                                        :confirm="false"
                                                                    >
                                                                    </date-picker>
                                                                    <div v-if="!$v.time_details.$each[gIndex].shift1_to.required" class="invalid-feedback">
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>
                                                                    <template v-if="errors[`time_details.${gIndex}.shift1_to`]">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors[`time_details.${gIndex}.shift1_to`]" :key="index">
                                                                            {{
                                                                                errorMessage
                                                                            }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div :class="create.timetable_types_id != 2?'col-3 p-0':'col-2 p-0'" v-if="isVisibleDetail('shift2_from') && create.timetable_types_id == 2">
                                                                <div class="form-group">
                                                                    <date-picker
                                                                        type="time"
                                                                        v-model="$v.time_details.$each[gIndex].shift2_from.$model"
                                                                        format="HH:mm:ss"
                                                                        valueType="format"
                                                                        :confirm="false"
                                                                    >
                                                                    </date-picker>
                                                                    <div v-if="!$v.time_details.$each[gIndex].shift2_from.required" class="invalid-feedback">
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>
                                                                    <template v-if="errors[`time_details.${gIndex}.shift2_from`]">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors[`time_details.${gIndex}.shift2_from`]" :key="index">
                                                                            {{
                                                                                errorMessage
                                                                            }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div :class="create.timetable_types_id != 2?'col-3 p-0':'col-2 p-0'" v-if="isVisibleDetail('shift2_to') && create.timetable_types_id == 2">
                                                                <div class="form-group">
                                                                    <date-picker
                                                                        type="time"
                                                                        v-model="$v.time_details.$each[gIndex].shift2_to.$model"
                                                                        format="HH:mm:ss"
                                                                        valueType="format"
                                                                        :confirm="false"
                                                                    >
                                                                    </date-picker>
                                                                    <div v-if="!$v.time_details.$each[gIndex].shift2_to.required" class="invalid-feedback">
                                                                        {{ $t("general.fieldIsRequired") }}
                                                                    </div>
                                                                    <template v-if="errors[`time_details.${gIndex}.shift2_to`]">
                                                                        <ErrorMessage v-for="(errorMessage, index) in errors[`time_details.${gIndex}.shift2_to`]" :key="index">
                                                                            {{
                                                                                errorMessage
                                                                            }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                            <div :class="create.timetable_types_id != 2?'col-3 p-0':'col-2 p-0'" v-if="isVisibleDetail('is_off')">
                                                                <div class="form-group">
                                                                    <b-form-group>
                                                                        <b-form-radio
                                                                            class="d-inline-block"
                                                                            v-model="$v.time_details.$each[gIndex].is_off.$model"
                                                                            :name="`some-radios-create-${gIndex}`"
                                                                            value="1"
                                                                        >{{ $t("general.Yes") }}</b-form-radio>
                                                                        <b-form-radio
                                                                            class="d-inline-block m-1"
                                                                            v-model="$v.time_details.$each[gIndex].is_off.$model"
                                                                            :name="`some-radios-create-${gIndex}`"
                                                                            value="0"
                                                                        >{{ $t("general.No") }}</b-form-radio>
                                                                    </b-form-group>
                                                                    <template v-if="errors[`time_details.${gIndex}.is_off`]">
                                                                        <ErrorMessage
                                                                            v-for="(errorMessage, index) in errors[`time_details.${gIndex}.is_off`]"
                                                                            :key="index"
                                                                        >{{ errorMessage }}
                                                                        </ErrorMessage>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row border-b-2 brc-default-l2"></div>
                                                    <div class="row mt-3"></div>
                                                    <hr v-if='type != "edit"' />
                                                    <div v-if='type != "edit"'>
                                                        <span class="text-secondary-d1 text-105">{{$t("general.Thank_you") }}</span>
                                                        <div class="px-4 float-right mt-3 mt-lg-0">
                                                            <b-button
                                                                      variant="primary"
                                                                      :disabled="is_disabledDetail"
                                                                      v-if="!isLoaderDetail"
                                                                      class="btn btn-primary btn-bold px-4 float-right mt-3 mx-2 mt-lg-0"
                                                                      @click="AddSubmitDateil"
                                                            >
                                                                {{ type != "edit" ? $t("general.Add") : $t("general.edit") }}
                                                            </b-button>
                                                            <b-button variant="secondary" class="mx-1" disabled v-else>
                                                                <b-spinner small></b-spinner>
                                                                <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                                            </b-button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </b-tab>
                    </b-tabs>
                </div>
            </form>
        </b-modal>
    </div>
</template>

<script>
import {integer, maxLength, minLength, required, requiredIf,maxValue} from "vuelidate/lib/validators";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import adminApi from "../../../api/adminAxios";
import Swal from "sweetalert2";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import { arabicValue, englishValue } from "../../../helper/langTransform";
import DatePicker from "vue2-datepicker";
export default {
    name: "time-table-attendance",
    components: {
        ErrorMessage,
        loader,
        Multiselect,
        DatePicker
    },
    mixins: [transMixinComp,successError],
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission:{},url: {default: '/hr/time-tables-header'}
    },
    data() {
        return {
            fields: [],
            fieldsDetail: [],
            isLoader: false,
            create: {
                name: '',
                name_e: '',
                timetable_types_id: 1,
                tt_monthly_hours: 0
            },
            time_details: [],
            week: [
                {id:6,name:'السبت',name_e:'Saturday'},
                {id:7,name:'الاحد',name_e:'Sundday'},
                {id:1,name:'الاثنين',name_e:'Monday'},
                {id:2,name:'الثلاثاء',name_e:'Tuesday'},
                {id:3,name:'الاربعاء',name_e:'Wednesday'},
                {id:4,name:'الخميس',name_e:'Thursday'},
                {id:5,name:'الجمعه',name_e:'Friday'},
            ],
            errors: {},
            is_disabled: false,
            isCustom: false,
            isLoaderDetail:false,
            header_id: null,
            detail_id: null,
            is_disabledDetail: false,
            types: []
        }
    },
    validations: {
        create: {
            name: {
                required: requiredIf(function (model) {
                    return this.isRequired("name");
                }),minLength: minLength(2), maxLength: maxLength(100),},
            name_e: { required: requiredIf(function (model) {
                    return this.isRequired("name_e");
                }), minLength: minLength(2), maxLength: maxLength(100) },
            timetable_types_id: { required: requiredIf(function (model) {
                    return this.isRequired("timetable_types_id");
                })},
            tt_monthly_hours: { required: requiredIf(function (model) {
                    return this.isRequired("tt_monthly_hours");
                }),integer, maxValue: maxValue(999) },
        },
        time_details: {
            required,
            $each: {
                is_off: {
                    required: requiredIf(function (model) {
                        return this.isRequiredDetail("is_off");
                    }),integer},
                shift1_from: { required: requiredIf(function (model) {
                        return this.isRequiredDetail("shift1_from");
                    }), },
                shift1_to: { required: requiredIf(function (model) {
                        return this.isRequiredDetail("shift1_to");
                    }), },
                shift2_from: { required: requiredIf(function (model) {
                        return this.isRequiredDetail("shift2_from") && this.create.timetable_types_id == 2;
                    }), },
                shift2_to: { required: requiredIf(function (model) {
                        return this.isRequiredDetail("shift2_to") && this.create.timetable_types_id == 2;
                    }), },
                day_no: { required: requiredIf(function (model) {
                        return this.isRequiredDetail("day_no");
                    }), integer,maxValue: maxValue(7) },
            }
        }
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/hr_timetables_header`)
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
                    this.isCustom = false;
                });
        },
        getType() {
            this.isLoader = true;
            adminApi
                .get(`/hr/time-tables-types`)
                .then((res) => {
                    this.types = res.data.data;
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
        getCustomTableFieldsDetail() {
            this.isCustom = true;
            adminApi
                .get(`/customTable/table-columns/hr_timetables_detail`)
                .then((res) => {
                    this.fieldsDetail = res.data;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                })
                .finally(() => {
                    this.isCustom = false;
                });
        },
        isVisibleDetail(fieldName) {
                let res = this.fieldsDetail.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_visible == 1 ? true : false;
        },
        isRequiredDetail(fieldName) {
                let res = this.fieldsDetail.filter((field) => {
                    return field.column_name == fieldName;
                });
                return res.length > 0 && res[0].is_required == 1 ? true : false;
        },
        defaultData(edit = null){
            this.time_details = [];
            this.create = {
                name: '',
                name_e: '',
                timetable_types_id: 1,
                tt_monthly_hours: 0
            };
            this.week.forEach(e => {
                this.time_details.push({
                    day_no: e.id,
                    shift1_from: '00:00:00',
                    shift1_to: '00:00:00',
                    shift2_from: '00:00:00',
                    shift2_to: '00:00:00',
                    is_off: 0
                });
            });
            this.header_id = null;
            this.isLoaderDetail = false;
            this.detail_id = null;
            this.is_disabled = false;
            this.is_disabledDetail = false;
            this.errors = {};
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
            setTimeout( async () => {
                if(this.type != 'edit'){
                    if(!this.isPage)  await this.getCustomTableFields();
                    if(this.isVisible('timetable_types_id'))  this.getType()
                }else {
                    this.time_details = [];
                    if(this.idObjEdit.dataObj){
                        this.getCustomTableFieldsDetail();
                        let bankAccount = this.idObjEdit.dataObj;
                        this.header_id = bankAccount.id;
                        this.create.name= bankAccount.name;
                        this.create.name_e= bankAccount.name_e;
                        this.create.timetable_types_id= bankAccount.timetable_types_id;
                        this.create.tt_monthly_hours= bankAccount.tt_monthly_hours;
                        if(this.isVisible('timetable_types_id'))  this.getType()
                        if(bankAccount.timetablesDetails.length > 0){
                            bankAccount.timetablesDetails.forEach(e => {
                                this.time_details.push({
                                    timetables_header_id: e.timetables_header_id,
                                    day_no: e.day_no,
                                    shift1_from: e.shift1_from?e.shift1_from:'00:00:00',
                                    shift1_to: e.shift1_to?e.shift1_to:'00:00:00',
                                    shift2_from: e.shift2_from?e.shift2_from:'00:00:00',
                                    shift2_to: e.shift2_to?e.shift2_to:'00:00:00',
                                    is_off: e.is_off,
                                });
                            });
                        }else {
                            this.week.forEach(e => {
                                this.time_details.push({
                                    day_no: e.id,
                                    shift1_from: '00:00:00',
                                    shift1_to: '00:00:00',
                                    shift2_from: '00:00:00',
                                    shift2_to: '00:00:00',
                                    is_off: 0
                                });
                            });
                        }
                        this.errors = {};
                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            if (this.create.name || this.create.name_e) {
                this.create.name = this.create.name
                    ? this.create.name
                    : this.create.name_e;
                this.create.name_e = this.create.name_e
                    ? this.create.name_e
                    : this.create.name;
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
                        .post(this.url, { ...this.create, company_id: this.company_id })
                        .then((res) => {
                            this.is_disabled = true;
                            this.header_id = res.data.data.id;
                            this.getCustomTableFieldsDetail();
                            if(!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");
                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
                            }, 200);

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
                    this.time_details.forEach(e => {
                        e.timetables_header_id = this.header_id;
                        if(this.create.timetable_types_id == 1){
                            e.shift2_from = '';
                            e.shift2_to = '';
                        }
                    });
                    adminApi
                        .put(`${this.url}/${this.header_id}`, {
                            ...this.create,
                            time_details:this.time_details,
                            company_id: this.$store.getters["auth/company_id"],
                        })
                        .then((res) => {
                            this.$emit("getDataTable");
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
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoader = false
                            this.time_details.forEach(e => {
                                if(this.create.timetable_types_id == 1){
                                    e.shift2_from = '00:00:00';
                                    e.shift2_to = '00:00:00';
                                }
                            });
                        });
                }

            }
        },
        AddSubmitDateil() {
            this.$v.time_details.$touch();

            if (this.$v.time_details.$invalid) {
                return;
            } else {
                this.isLoaderDetail = true;
                this.errors = {};
                this.is_disabledDetail = false;
                if(this.type != 'edit'){
                    this.time_details.forEach(e => {
                        e.timetables_header_id = this.header_id;
                        if(this.create.timetable_types_id == 1){
                            e.shift2_from = '';
                            e.shift2_to = '';
                        }
                    });

                    adminApi
                        .post('/hr/time-tables-detail', {
                            time_details:this.time_details,
                            company_id: this.company_id,
                        })
                        .then((res) => {
                            this.is_disabledDetail = true;
                            if(!this.isPage)
                                this.$emit("created");
                            else
                                this.$emit("getDataTable");
                            setTimeout(() => {
                                this.successFun('Addedsuccessfully');
                            }, 200);

                        })
                        .catch((err) => {
                            if (err.response.data) {
                                this.errors = err.response.data.errors;
                            } else {
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoaderDetail = false;
                        });
                }else {
                    adminApi
                        .put(`/hr/time-tables-detail/${this.detail_id}`, {
                            ...this.time_details,
                            company_id: this.$store.getters["auth/company_id"],
                            timetables_header_id: this.header_id
                        })
                        .then((res) => {
                            this.$emit("getDataTable");
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
                                this.errorFun('Error','Thereisanerrorinthesystem');
                            }
                        })
                        .finally(() => {
                            this.isLoaderDetail = false;
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

.nav-bordered {
    border: unset !important;
}

.nav {
    background-color: #dff0fe;
}

.tab-content {
    padding: 70px 60px 40px;
    min-height: 300px;
    background-color: #f5f5f5;
    position: relative;
}

.nav-tabs .nav-link {
    border: 1px solid #b7b7b7 !important;
    background-color: #d7e5f2;
    border-bottom: 0 !important;
    margin-bottom: 1px;
}

.nav-tabs .nav-link.active,
.nav-tabs .nav-item.show .nav-link {
    color: #000;
    background-color: hsl(0deg 0% 96%);
    border-bottom: 0 !important;
}

.img-thumbnail {
    max-height: 400px !important;
}
form {
    position: relative;
}


.text-secondary-d1 {
    color: #728299 !important;
}

.brc-default-l1 {
    border-color: #dce9f0 !important;
}

.ml-n1,
.mx-n1 {
    margin-left: -.25rem !important;
}

.mr-n1,
.mx-n1 {
    margin-right: -.25rem !important;
}

.mb-4,
.my-4 {
    margin-bottom: 1.5rem !important;
}

hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
    border-top: 1px solid rgba(0, 0, 0, .1);
}

.text-grey-m2 {
    color: #888a8d !important;
}

.text-success-m2 {
    color: #86bd68 !important;
}

.font-bolder,
.text-600 {
    font-weight: 600 !important;
}

.text-110 {
    font-size: 110% !important;
}

.text-blue {
    color: #478fcc !important;
}

.pb-25,
.py-25 {
    padding-bottom: .75rem !important;
}

.pt-25,
.py-25 {
    padding-top: .75rem !important;
}

.bgc-default-tp1 {
    background-color: rgba(121, 169, 197, .92) !important;
}

.bgc-default-l4,
.bgc-h-default-l4:hover {
    background-color: #f3f8fa !important;
}

.page-header .page-tools {
    -ms-flex-item-align: end;
    align-self: flex-end;
}

.btn-light {
    color: #757984;
    background-color: #f5f6f9;
    border-color: #dddfe4;
}

.w-2 {
    width: 1rem;
}

.text-120 {
    font-size: 120% !important;
}

.text-primary-m1 {
    color: #4087d4 !important;
}

.text-danger-m1 {
    color: #dd4949 !important;
}

.text-blue-m2 {
    color: #68a3d5 !important;
}

.text-150 {
    font-size: 150% !important;
}

.text-60 {
    font-size: 60% !important;
}

.text-grey-m1 {
    color: #7b7d81 !important;
}

.align-bottom {
    vertical-align: bottom !important;
}

.row-danger {
    background-color:#f6a9a9 !important;
}
</style>

