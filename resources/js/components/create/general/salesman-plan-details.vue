<template>
    <div>
        <SalemanPlan
            :id="'salesman-create-salesman-plan-details'" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys"
            :isPage="false" type="create" :isPermission="isPermission" @created="getPlans"
        />
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit'?getCompanyKey('salesmanplan_detail_create_form'):getCompanyKey('salesmanplan_detail_edit_form')"
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
                    <div class="col-md-12" v-if="isVisible('plan_id')">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("plan") }}
                                <span v-if="isRequired('plan_id')" class="text-danger"
                                >*</span
                                >
                            </label>
                            <multiselect
                                @input="showPlanModal"
                                v-model="create.plan_id"
                                :options="allPlans.map((type) => type.id)"
                                :custom-label=" (opt) => allPlans.find((x) => x.id == opt)?
                                    $i18n.locale == 'ar' ?allPlans.find((x) => x.id == opt).name:allPlans.find((x) => x.id == opt).name_e
                                    :null
                                "
                            >
                            </multiselect>
                            <div
                                v-if="$v.create.plan_id.$error || errors.plan_id"
                                class="text-danger"
                            >
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.salesmen_plans_source_id">
                                <ErrorMessage
                                    v-for="(errorMessage, index) in errors.branch_id"
                                    :key="index"
                                >{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('amount_from')">
                        <div class="form-group">
                            <label for="field-1" class="control-label">
                                {{ getCompanyKey("salesmanplan_detail_amount_from") }}
                                <span
                                    v-if="isRequired('amount_from')"
                                    class="text-danger"
                                >*</span
                                >
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                data-create="1"
                                v-model="$v.create.amount_from.$model"
                                :class="{
                          'is-invalid':
                            $v.create.amount_from.$error || errors.amount_from,
                          'is-valid':
                            !$v.create.amount_from.$invalid &&
                            !errors.amount_from,
                        }"
                                id="field-1"
                            />
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('amount_to')">
                        <div class="form-group">
                            <label for="field-2" class="control-label">
                                {{ getCompanyKey("salesmanplan_detail_amount_from") }}
                                <span v-if="isRequired('amount_to')" class="text-danger"
                                >*</span
                                >
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                data-create="1"
                                v-model="$v.create.amount_to.$model"
                                :class="{
                          'is-invalid':
                            $v.create.amount_to.$error || errors.amount_to,
                          'is-valid':
                            !$v.create.amount_to.$invalid && !errors.amount_to,
                        }"
                                id="field-2"
                            />
                        </div>
                    </div>
                    <div class="col-md-12" v-if="isVisible('commission_percent')">
                        <div class="form-group">
                            <label for="field-3" class="control-label">
                                {{
                                    getCompanyKey(
                                        "salesmanplan_detail_commission_percent"
                                    )
                                }}
                                <span
                                    v-if="isRequired('commission_percent')"
                                    class="text-danger"
                                >*</span
                                >
                            </label>
                            <input
                                type="number"
                                class="form-control"
                                data-create="1"
                                v-model="$v.create.commission_percent.$model"
                                :class="{
                          'is-invalid':
                            $v.create.commission_percent.$error ||
                            errors.commission_percent,
                          'is-valid':
                            !$v.create.commission_percent.$invalid &&
                            !errors.commission_percent,
                        }"
                                id="field-3"
                            />
                        </div>
                    </div>
                </div>
            </form>
        </b-modal>
        <!--  /create   -->
    </div>
</template>
<script>
import SalemanPlan from "./salesmanPlan";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import Multiselect from "vue-multiselect";
import {requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";

export default {
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/salesmen-plans-details'}
    },
    components: {
        SalemanPlan,
        ErrorMessage,
        loader,
        Multiselect,
    },
    mixins: [transMixinComp,successError],
    data() {
        return {
            fields: [],
            allPlans: [],
            isLoader: false,
            create: {
                plan_id: null,
                amount_from: 0,
                amount_to: 0,
                commission_percent: 0,
            },
            company_id: null,
            errors: {},
            is_disabled: false,
            isCustom: false
        };
    },
    validations: {
        create: {
            plan_id: {
                required: requiredIf(function (model) {
                    return this.isRequired("plan_id");
                }),
            },
            amount_from: {
                required: requiredIf(function (model) {
                    return this.isRequired("amount_from");
                }),
            },
            amount_to: {
                required: requiredIf(function (model) {
                    return this.isRequired("amount_to");
                }),
            },
            commission_percent: {
                required: requiredIf(function (model) {
                    return this.isRequired("commission_percent");
                }),
            },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        showPlanModal() {
            if (this.create.plan_id == 0) {
                this.$bvModal.show("salesman-create-salesman-plan-details");
                this.create.plan_id = null;
            }
        },
        async getCustomTableFields() {
            this.isCustom = true;
            await adminApi
                .get(`/customTable/table-columns/general_salesmen_plans_details`)
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
                plan_id: null,
                amount_from: 0,
                amount_to: 0,
                commission_percent: 0,
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
                    if (this.isVisible("plan_id"))  this.getPlans();
                }else {
                    if(this.idObjEdit.dataObj){
                        let branch = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.amount_from = branch.amount_from;
                        this.create.amount_to = branch.amount_to;
                        this.create.plan_id =  branch.plan? branch.plan.id: null;
                        this.create.commission_percent = branch.commission_percent;
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
                if(this.type != 'edit'){
                    adminApi
                        .post(this.url, { ...this.create, company_id: this.company_id })
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
        getPlans() {
            this.isLoader = true;

            adminApi
                .get(`/salesmen-plans/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    if(this.isPermission('create Plan')){
                        l.unshift({ id: 0, name: "اضف خطة", name_e: "Add plan" });
                    }
                    this.allPlans = l;
                })
                .catch((err) => {
                    this.errorFun('Error','Thereisanerrorinthesystem');
                })
                .finally(() => {
                    this.isLoader = false;
                });
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

<style>
form {
    position: relative;
}
</style>
