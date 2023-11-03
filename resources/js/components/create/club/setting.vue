<template>
    <b-modal
        :id="id"
        :title="type != 'edit'?getCompanyKey('settings_create_form'):getCompanyKey('settings_edit_form')"
        title-class="font-18"
        body-class="p-4 "
        :dialog-class="type != 'edit' ?'modal-full-width': ''"
        :size="type != 'edit' ?'':'lg'"
        :hide-footer="true"
        @show="resetModal"
        @hidden="resetModalHidden"
    >
        <form>
            <loader size="large" v-if="isCustom && !isPage" />
            <loader updateData="true" size="large" v-if="isLoader"/>
            <div class="mb-3 d-flex justify-content-end">
<!--                <b-button-->
<!--                    variant="success"-->
<!--                    @click.prevent="updateAllTransaction"-->
<!--                    type="button"-->
<!--                    class="mx-1 font-weight-bold px-3 mr-3 ml-3"-->
<!--                    v-if="!isLoader && type == 'edit'"-->
<!--                >-->
<!--                    {{ $t("general.updateData") }}-->
<!--                </b-button>-->
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
            <div v-if="type != 'edit'">
                <div class="row" v-if="isVisible('cm_members_type_id')">
                    <div class="col-md-4">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("member_type") }}
                                <span v-if="isRequired('cm_members_type_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                v-model="create.cm_members_type_id"
                                :options="typs.map((type) => type.id)"
                                :custom-label="
                              (opt) => typs.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar'
                                      ? typs.find((x) => x.id == opt).name
                                      : typs.find((x) => x.id == opt).name_e: null
                                "
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.cm_members_type_id.$error || errors.cm_members_type_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.cm_members_type_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.cm_members_type_id"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                </div>
                <template v-for="(item, index) in create.memberships_renewals">
                    <div class="row" :key="index">
                        <div class="col-md-2" v-if="isVisible('cm_permissions_id')">
                            <div class="form-group position-relative">
                                <label class="control-label">
                                    {{ getCompanyKey("member_permission") }}
                                    <span v-if="isRequired('cm_permissions_id')" class="text-danger">*</span>
                                </label>
                                <multiselect
                                    v-model="create.memberships_renewals[index].cm_permissions_id"
                                    :options="permissions.map((type) => type.id)"
                                    :custom-label="
                                      (opt) => permissions.find((x) => x.id == opt)?
                                        $i18n.locale == 'ar'
                                          ? permissions.find((x) => x.id == opt).name
                                          : permissions.find((x) => x.id == opt).name_e: null
                                    "
                                >
                                </multiselect>
                                <div
                                    v-if="$v.create.memberships_renewals.$each[index].cm_permissions_id.$error || errors.cm_permissions_id"
                                    class="text-danger"
                                >
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.cm_permissions_id">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.cm_permissions_id"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-2" v-if="isVisible('cm_financial_status_id')">
                            <div class="form-group position-relative">
                                <label class="control-label">
                                    {{ getCompanyKey("financial_status") }}
                                    <span v-if="isRequired('cm_financial_status_id')" class="text-danger">*</span>
                                </label>
                                <multiselect
                                    v-model="create.memberships_renewals[index].cm_financial_status_id"
                                    :options="status.map((type) => type.id)"
                                    :custom-label="
                                      (opt) => status.find((x) => x.id == opt)?
                                        $i18n.locale == 'ar'
                                          ? status.find((x) => x.id == opt).name
                                          : status.find((x) => x.id == opt).name_e: null
                                    "
                                >
                                </multiselect>
                                <div
                                    v-if="$v.create.memberships_renewals.$each[index].cm_financial_status_id.$error || errors.cm_financial_status_id"
                                    class="text-danger"
                                >
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.cm_financial_status_id">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.cm_financial_status_id"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-2" v-if="isVisible('membership_period')">
                            <div class="form-group">
                                <label class="control-label">
                                    {{ getCompanyKey("membership_period") }}
                                    <span v-if="isRequired('membership_period')" class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model="$v.create.memberships_renewals.$each[index].membership_period.$model"
                                    :class="{
                                                  'is-invalid': $v.create.memberships_renewals.$each[index].membership_period.$error || errors.membership_period,
                                                  'is-valid':
                                                    !$v.create.memberships_renewals.$each[index].membership_period.$invalid && !errors.membership_period,
                                                }"
                                />
                                <template v-if="errors.membership_period">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.membership_period"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-2" v-if="isVisible('allowed_subscription_date')">
                            <div class="form-group">
                                <label>
                                    {{ getCompanyKey("allowed_subscription_date") }}
                                    <span v-if="isRequired('allowed_subscription_date')" class="text-danger">*</span>
                                </label>
                                <div class="d-flex">
                                    <div class="form-group col-6">
                                        <label>{{ $t("general.day") }}</label>
                                        <select
                                            v-model="$v.create.memberships_renewals.$each[index].day.$model"
                                            class="custom-select"
                                            :class="{
                                                        'is-invalid': $v.create.memberships_renewals.$each[index].day.$error || errors.day,
                                                        'is-valid':
                                                          !$v.create.memberships_renewals.$each[index].day.$invalid && !errors.day,
                                                      }"
                                        >
                                            <option selected disabled value="">Choose...</option>
                                            <option :value="day" :key="day" v-for="day in getDay()">{{ day }}</option>
                                        </select>
                                        <template v-if="errors.day">
                                            <ErrorMessage v-for="(errorMessage, index) in errors.day" :key="index">{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{ $t("general.month") }}</label>
                                        <select
                                            v-model="$v.create.memberships_renewals.$each[index].month.$model"
                                            class="custom-select"
                                            :class="{
                                                        'is-invalid': $v.create.memberships_renewals.$each[index].month.$error || errors.month,
                                                        'is-valid': !$v.create.memberships_renewals.$each[index].month.$invalid && !errors.month,
                                                      }"
                                        >
                                            <option selected disabled value="">Choose...</option>
                                            <option :value="month" :key="month" v-for="month in getMonth()">{{ month }}</option>
                                        </select>
                                        <template v-if="errors.month">
                                            <ErrorMessage v-for="(errorMessage, index) in errors.month" :key="index">{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>

                                <template v-if="errors.allowed_subscription_date">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.allowed_subscription_date" :key="index">
                                        {{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div v-if="create.memberships_renewals[index].cm_permissions_id > 1 && isVisible('allowed_vote_date')" class="col-md-2">
                            <div class="form-group">
                                <label>
                                    {{ $t("general.allowed_vote_date") }}
                                    <span v-if="isRequired('allowed_vote_date')" class="text-danger">*</span>
                                </label>
                                <div class="d-flex">
                                    <div class="form-group col-6">
                                        <label>{{ $t("general.day") }}</label>
                                        <select
                                            v-model="$v.create.memberships_renewals.$each[index].vote_day.$model"
                                            class="custom-select"
                                            :class="{
                                                        'is-invalid': $v.create.memberships_renewals.$each[index].vote_day.$error || errors.vote_day,
                                                        'is-valid':
                                                          !$v.create.memberships_renewals.$each[index].vote_day.$invalid && !errors.vote_day,
                                                      }"
                                        >
                                            <option selected disabled value="">Choose...</option>
                                            <option :value="day" :key="day" v-for="day in getDay()">{{ day }}</option>
                                        </select>
                                        <template v-if="errors.vote_day">
                                            <ErrorMessage v-for="(errorMessage, index) in errors.vote_day" :key="index">{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                    <div class="form-group col-6">
                                        <label>{{ $t("general.month") }}</label>
                                        <select
                                            v-model="$v.create.memberships_renewals.$each[index].vote_month.$model"
                                            class="custom-select"
                                            :class="{
                                                        'is-invalid': $v.create.memberships_renewals.$each[index].vote_month.$error || errors.vote_month,
                                                        'is-valid':
                                                          !$v.create.memberships_renewals.$each[index].vote_month.$invalid && !errors.vote_month,
                                                      }"
                                        >
                                            <option selected disabled value="">Choose...</option>
                                            <option :value="month" :key="month" v-for="month in getMonth()">{{ month }}</option>
                                        </select>
                                        <template v-if="errors.vote_month">
                                            <ErrorMessage v-for="(errorMessage, index) in errors.vote_month" :key="index">{{ errorMessage }}
                                            </ErrorMessage>
                                        </template>
                                    </div>
                                </div>

                                <template v-if="errors.allowed_subscription_date">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.allowed_subscription_date"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-2 p-0 pt-3">
                            <button v-if="(create.memberships_renewals.length - 1) == index" type="button" @click.prevent="addNewField"
                                    class="custom-btn-dowonload">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button v-if="create.memberships_renewals.length > 1" type="button" @click.prevent="removeNewField(index)"
                                    class="custom-btn-dowonload">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
            <div class="row" v-else>
                <div class="col-md-6" v-if="isVisible('cm_members_type_id')">
                    <div class="form-group position-relative">
                        <label class="control-label">
                            {{ getCompanyKey("member_type") }}
                            <span v-if="isRequired('cm_members_type_id')" class="text-danger">*</span>
                        </label>
                        <multiselect
                            v-model="edit.cm_members_type_id"
                            :options="typs.map((type) => type.id)"
                            :custom-label="
                              (opt) => typs.find((x) => x.id == opt)?
                                $i18n.locale == 'ar'
                                  ? typs.find((x) => x.id == opt).name
                                  : typs.find((x) => x.id == opt).name_e: null
                            "
                        >
                        </multiselect>
                        <div
                            v-if="$v.edit.cm_members_type_id.$error || errors.cm_members_type_id"
                            class="text-danger"
                        >
                            {{ $t("general.fieldIsRequired") }}
                        </div>
                        <template v-if="errors.cm_members_type_id">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.cm_members_type_id"
                                :key="index"
                            >{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-6" v-if="isVisible('cm_permissions_id')">
                    <div class="form-group position-relative">
                        <label class="control-label">
                            {{ getCompanyKey("member_permission") }}
                            <span v-if="isRequired('cm_permissions_id')" class="text-danger">*</span>
                        </label>
                        <multiselect
                            v-model="edit.cm_permissions_id"
                            :options="permissions.map((type) => type.id)"
                            :custom-label="
                              (opt) => permissions.find((x) => x.id == opt)?
                                $i18n.locale == 'ar'
                                  ? permissions.find((x) => x.id == opt).name
                                  : permissions.find((x) => x.id == opt).name_e: null
                            "
                        >
                        </multiselect>
                        <div
                            v-if="$v.edit.cm_permissions_id.$error || errors.cm_permissions_id"
                            class="text-danger"
                        >
                            {{ $t("general.fieldIsRequired") }}
                        </div>
                        <template v-if="errors.cm_permissions_id">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.cm_permissions_id"
                                :key="index"
                            >{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-6" v-if="isVisible('cm_financial_status_id')">
                    <div class="form-group position-relative">
                        <label class="control-label">
                            {{ getCompanyKey("financial_status") }}
                            <span v-if="isRequired('cm_financial_status_id')" class="text-danger">*</span>
                        </label>
                        <multiselect
                            v-model="edit.cm_financial_status_id"
                            :options="status.map((type) => type.id)"
                            :custom-label="
                              (opt) => status.find((x) => x.id == opt)?
                                $i18n.locale == 'ar'
                                  ? status.find((x) => x.id == opt).name
                                  : status.find((x) => x.id == opt).name_e: null
                            "
                        >
                        </multiselect>
                        <div
                            v-if="$v.edit.cm_financial_status_id.$error || errors.cm_financial_status_id"
                            class="text-danger"
                        >
                            {{ $t("general.fieldIsRequired") }}
                        </div>
                        <template v-if="errors.cm_financial_status_id">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.cm_financial_status_id"
                                :key="index"
                            >{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-6" v-if="isVisible('membership_period')">
                    <div class="form-group">
                        <label class="control-label">
                            {{ getCompanyKey("membership_period") }}
                            <span v-if="isRequired('membership_period')" class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            v-model="$v.edit.membership_period.$model"
                            :custom-label="
                              (opt) => membership_period.find((x) => x.id == opt)?
                                $i18n.locale == 'ar'
                                  ? membership_period.find((x) => x.id == opt).name
                                  : membership_period.find((x) => x.id == opt).name_e: null
                            "
                        />
                        <template v-if="errors.membership_period">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.membership_period"
                                :key="index"
                            >{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div class="col-md-12" v-if="isVisible('allowed_subscription_date')">
                    <div class="form-group">
                        <label>
                            {{ getCompanyKey("allowed_subscription_date") }}
                            <span v-if="isRequired('allowed_subscription_date')" class="text-danger">*</span>
                        </label>
                        <div class="d-flex">
                            <div class="form-group col-6">
                                <label>{{ $t("general.day") }}</label>
                                <select
                                    v-model="$v.edit.day.$model"
                                    class="custom-select"
                                    :class="{
                                    'is-invalid': $v.edit.day.$error || errors.day,
                                    'is-valid':
                                      !$v.edit.day.$invalid && !errors.day,
                                  }"
                                >
                                    <option selected disabled value="">Choose...</option>
                                    <option :value="day" :key="day" v-for="day in getDay()">{{ day }}</option>
                                </select>
                                <template v-if="errors.day">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.day" :key="index">{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                            <div class="form-group col-6">
                                <label>{{ $t("general.month") }}</label>
                                <select
                                    v-model="$v.edit.month.$model"
                                    class="custom-select"
                                    :class="{
                                                                            'is-invalid': $v.edit.month.$error || errors.month,
                                                                            'is-valid':
                                                                              !$v.edit.month.$invalid && !errors.month,
                                                                          }"
                                >
                                    <option selected disabled value="">Choose...</option>
                                    <option :value="month" :key="month" v-for="month in getMonth()">{{ month }}</option>
                                </select>
                                <template v-if="errors.month">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.month" :key="index">{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>

                        <template v-if="errors.allowed_subscription_date">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.allowed_subscription_date"
                                :key="index"
                            >{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
                <div v-if="edit.cm_permissions_id > 1 && isVisible('allowed_vote_date')" class="col-md-12">
                    <div class="form-group">
                        <label>
                            {{ getCompanyKey("allowed_vote_date") }}
                            <span v-if="isRequired('allowed_vote_date')" class="text-danger">*</span>
                        </label>
                        <div class="d-flex">
                            <div class="form-group col-6">
                                <label>{{ $t("general.day") }}</label>
                                <select
                                    v-model="$v.edit.vote_day.$model"
                                    class="custom-select"
                                    :class="{
                                                                            'is-invalid': $v.edit.vote_day.$error || errors.vote_day,
                                                                            'is-valid':
                                                                              !$v.edit.vote_day.$invalid && !errors.vote_day,
                                                                          }"
                                >
                                    <option selected disabled value="">Choose...</option>
                                    <option :value="day" :key="day" v-for="day in getDay()">{{ day }}</option>
                                </select>
                                <template v-if="errors.vote_day">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.vote_day" :key="index">{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                            <div class="form-group col-6">
                                <label>{{ $t("general.month") }}</label>
                                <select
                                    v-model="$v.edit.vote_month.$model"
                                    class="custom-select"
                                    :class="{
                                                                            'is-invalid': $v.edit.vote_month.$error || errors.vote_month,
                                                                            'is-valid':
                                                                              !$v.edit.vote_month.$invalid && !errors.vote_month,
                                                                          }"
                                >
                                    <option selected disabled value="">Choose...</option>
                                    <option :value="month" :key="month" v-for="month in getMonth()">{{ month }}</option>
                                </select>
                                <template v-if="errors.vote_month">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.vote_month" :key="index">{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>

                        <template v-if="errors.allowed_subscription_date">
                            <ErrorMessage
                                v-for="(errorMessage, index) in errors.allowed_subscription_date"
                                :key="index"
                            >{{ errorMessage }}
                            </ErrorMessage>
                        </template>
                    </div>
                </div>
            </div>
        </form>
    </b-modal>
</template>

<script>
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import {required, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

export default {
    mixins: [transMixinComp,successError],
    components: {
        ErrorMessage,
        loader,
        Multiselect
    },
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/club-members/type-permission'}
    },
    data() {
        return {
            isCustom: false,
            fields: [],
            is_disabled: false,
            isLoader: false,
            create: {
                cm_members_type_id: null,
                memberships_renewals: [
                    {
                        day: null,
                        month: null,
                        vote_day: null,
                        vote_month: null,
                        cm_permissions_id: null,
                        cm_financial_status_id: null,
                        membership_period: "",
                        allowed_subscription_date: "",
                        allowed_vote_date: "",
                    }
                ]
            },
            company_id: null,
            edit: {
                day: null,
                month: null,
                vote_day: null,
                vote_month: null,
                cm_members_type_id: null,
                cm_permissions_id: null,
                cm_financial_status_id: null,
                membership_period: "",
                allowed_subscription_date: "",
                allowed_vote_date: "",
            },
            typs: [],
            permissions: [],
            status: [],
            errors: {},
        };
    },
    validations: {
        create: {
            cm_members_type_id: {required: requiredIf(function (model) {
                    return this.isRequired("cm_members_type_id");
                })},
            memberships_renewals: {
                $each: {
                    day: {required: requiredIf(function (model) {
                            return this.isRequired("allowed_subscription_date");
                        })},
                    month: {required: requiredIf(function (model) {
                            return this.isRequired("allowed_subscription_date");
                        })},
                    vote_day: {required: requiredIf(function (model) {
                            return this.isRequired("allowed_vote_date");
                        })},
                    vote_month: {required: requiredIf(function (model) {
                            return this.isRequired("allowed_vote_date");
                        })},
                    cm_permissions_id: {required: requiredIf(function (model) {
                            return this.isRequired("cm_permissions_id");
                        })},
                    cm_financial_status_id: {required: requiredIf(function (model) {
                            return this.isRequired("cm_financial_status_id");
                        })},
                    membership_period: {required: requiredIf(function (model) {
                            return this.isRequired("membership_period");
                        })},
                    allowed_subscription_date: {required: requiredIf(function (model) {
                            return this.isRequired("allowed_subscription_date");
                        })},
                    allowed_vote_date: {required: requiredIf(function (model) {
                            return this.isRequired("allowed_vote_date");
                        })},
                }
            }
        },
        edit: {
            cm_members_type_id: {required: requiredIf(function (model) {
                    return this.isRequired("cm_members_type_id");
                })},
            day: {required: requiredIf(function (model) {
                    return this.isRequired("allowed_subscription_date");
                })},
            month: {required: requiredIf(function (model) {
                    return this.isRequired("allowed_subscription_date");
                })},
            vote_day: {required: requiredIf(function (model) {
                    return this.isRequired("allowed_vote_date");
                })},
            vote_month: {required: requiredIf(function (model) {
                    return this.isRequired("allowed_vote_date");
                })},
            cm_permissions_id: {required: requiredIf(function (model) {
                    return this.isRequired("cm_permissions_id");
                })},
            cm_financial_status_id: {required: requiredIf(function (model) {
                    return this.isRequired("cm_financial_status_id");
                })},
            membership_period: {required: requiredIf(function (model) {
                    return this.isRequired("membership_period");
                })},
            allowed_subscription_date: {required: requiredIf(function (model) {
                    return this.isRequired("allowed_subscription_date");
                })},
            allowed_vote_date: {required: requiredIf(function (model) {
                    return this.isRequired("allowed_vote_date");
                })},
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/cm_type_permissions`)
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
        addNewField() {
            this.create.memberships_renewals.push({
                day: null,
                month: null,
                vote_day: null,
                vote_month: null,
                cm_permissions_id: null,
                cm_financial_status_id: null,
                membership_period: "",
                allowed_subscription_date: "",
                allowed_vote_date: "",
            });
        },
        removeNewField(index) {
            if (this.create.memberships_renewals.length > 1) {
                this.create.memberships_renewals.splice(index, 1);
            }
        },
        defaultData(edit = null){
            this.create = {
                memberships_renewals: [
                    {
                        day: null,
                        month: null,
                        vote_day: null,
                        vote_month: null,
                        cm_permissions_id: null,
                        cm_financial_status_id: null,
                        membership_period: "",
                        allowed_subscription_date: "",
                        allowed_vote_date: "",
                    }
                ]
            };
            this.edit = {
                day: null,
                month: null,
                vote_day: null,
                vote_month: null,
                cm_members_type_id: null,
                cm_permissions_id: null,
                cm_financial_status_id: null,
                membership_period: "",
                allowed_subscription_date: "",
                allowed_vote_date: "",
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.is_disabled = false;
        },
        resetForm() {
            this.defaultData();
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        async updateAllTransaction() {
            this.isLoader = true;
            await adminApi
                .get(`/club-members/members/updateCmMember`)
                .then((res) => {
                    setTimeout(() => {
                        this.successFun('DoneSuccessfully');
                    }, 500);
                })
                .catch((err) => {
                    this.errorFun('Error', 'Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        resetModal() {
            this.defaultData();
            setTimeout( async () => {
                if(this.type != 'edit'){
                    if(!this.isPage) await  this.getCustomTableFields();
                    if (this.isVisible("cm_members_type_id")) this.getType();
                    if (this.isVisible("cm_permissions_id")) this.getPermissions();
                    if (this.isVisible("cm_financial_status_id")) this.getStatus();
                }else {
                    if(this.idObjEdit.dataObj){
                        let setting = this.idObjEdit.dataObj;
                        if (this.isVisible("cm_members_type_id")) this.getType();
                        if (this.isVisible("cm_permissions_id")) this.getPermissions();
                        if (this.isVisible("cm_financial_status_id")) this.getStatus();
                        this.edit.day = setting.allowed_subscription_date.slice(3) ;
                        this.edit.month = setting.allowed_subscription_date.slice(0,2);
                        this.edit.cm_members_type_id = setting.type.id;
                        this.edit.cm_permissions_id = setting.cm_permissions_id;
                        this.edit.cm_financial_status_id = setting.financialStatus.id;
                        this.edit.membership_period = setting.membership_period;
                        this.edit.allowed_subscription_date = setting.allowed_subscription_date;
                        this.edit.vote_day = setting.allowed_vote_date.slice(3) ;
                        this.edit.vote_month = setting.allowed_vote_date.slice(0,2);
                        this.edit.allowed_vote_date = setting.allowed_vote_date;
                        this.errors = {};
                    }
                }
            },50);
        },
        AddSubmit() {
            let data = null;
            if(this.type != 'edit') {
                this.create.memberships_renewals.forEach((el,index) => {
                    this.create.memberships_renewals[index].allowed_subscription_date = `${el.month}-${el.day}`;
                    this.create.memberships_renewals[index].allowed_vote_date = `${el.vote_month}-${el.vote_day}`;
                    this.create.memberships_renewals[index].cm_members_type_id = this.create.cm_members_type_id;
                });
                this.$v.create.$touch();
                data = this.create;
            }else {
                this.edit.allowed_subscription_date = `${this.edit.month}-${this.edit.day}`;
                this.edit.allowed_vote_date = `${this.edit.vote_month}-${this.edit.vote_day}`;
                this.$v.edit.$touch();
                data = this.edit;
            }

            if (this.$v.create.$invalid && this.$v.edit.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                this.is_disabled = false;
                if(this.type != 'edit') {
                    adminApi
                        .post(this.url, { ...data, company_id: this.company_id })
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
                            ...data,
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
        getType() {
            this.isLoader = true;
             adminApi
                .get(`/club-members/members-types`)
                .then((res) => {
                    let l = res.data.data;
                    this.typs = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getPermissions() {
            this.isLoader = true;
            adminApi
                .get(`/club-members/members-permissions`)
                .then((res) => {
                    let l = res.data.data;
                    this.permissions = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getStatus() {
            this.isLoader = true;
            adminApi
                .get(`/club-members/financial-status`)
                .then((res) => {
                    let l = res.data.data;
                    this.status = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        updateAllData() {
            this.isLoader = true;
            adminApi
                .get(`/club-members/members/updateCmMember`)
                .then((res) => {
                    this.successFun('DoneSuccessfully');
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
        },
        getDay(){
            let days = [];
            for(let i = 1;i <= 31; ++i){
                i < 10? days.push(`0${i}`) : days.push(`${i}`);
            }
            return days;
        },
        getMonth(){
            let months = [];
            for(let i = 1;i <= 12; ++i){
                i < 10? months.push(`0${i}`) : months.push(`${i}`);
            }
            return months;
        },
    },
};
</script>

<style scoped>
form {
    position: relative;
}
</style>
