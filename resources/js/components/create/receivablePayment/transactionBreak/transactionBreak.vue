<script>
import adminApi from "../../../../api/adminAxios";
import Switches from "vue-switches";
import {required, minLength, maxLength} from "vuelidate/lib/validators";
import Swal from "sweetalert2";
import ErrorMessage from "../../../widgets/errorMessage";
import loader from "../../../general/loader";
import transMixinComp from "../../../../helper/mixin/translation-comp-mixin";
import Multiselect from "vue-multiselect";
import InstallmentPaymentType from "../installmentPaymentType.vue";
import InstallmentStatus from "../installmentStatus";
import DatePicker from "vue2-datepicker";
import showBreak from "./showBreak";

/**
 * Advanced Table component
 */
export default {
    mixins: [transMixinComp],
    components: {
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
        DatePicker,
        InstallmentPaymentType,
        InstallmentStatus,
        showBreak
    },
    props: ["companyKeys", "defaultsKeys", "opening"],

    data() {
        return {
            isLoader: false,
            create: [],
            errors: {},
            dropDownSenders: [],
            customers: [],
            currencies: [],
            payment_types: [],
            installment_statuses: [],
            is_disabled: false,
            isCheckAll: false,
            checkAll: [],
            current_page: 1,
            enabled3: true,
            printLoading: true,
            isDebit: true,
            openingBreak:'',
            totalOpening: 0,
            totalDivision: 0,
            residual: 0,
            printObj: {
                id: "printCustom",
            },
        };
    },
    validations: {
        create: {
            $each: {
                break_id: {required},
                instalment_date: {required},
                rate: {required},
                instalment_type_id: {required},
                customer_id: {required},
                client_type_id: {},
                currency_id: {required},
                document_id: {},
                debit: {},
                credit: {},
                repate: {required},
                total: {},
                terms: {},
                installment_statu_id: {required},
                details: {},
                installment_amount: {},
            },
        },
    },
    watch: {
        opening(after, befour) {
            this.createOrUpdate();
        },
    },
    methods: {
        showInstallmentPaymentTypeModal(index) {
            if (this.create[index].instalment_type_id == 0) {
                this.$bvModal.show("installment_payment_type_create");
                this.create[index].instalment_type_id = null;
            }
        },
        showInstallmentStatusModal(index) {
            if (this.create[index].installment_statu_id == 0) {
                this.$bvModal.show("installment-create-status");
                this.create[index].installment_statu_id = null;
            }
        },
        /**
         *  get customer
         */
        async getCustomers() {
            this.isLoader = true;
            await adminApi
                .get(`/general-customer`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.unshift({id: 0, name: "اضافة زبون", name_e: "Add customer"});
                    this.customers = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        /**
         *  get currency
         */
        async getCurrencies() {
            this.isLoader = true;

            await adminApi
                .get(
                    `/currencies`
                )
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({id: 0, name: "اضف عمله جديده  ", name_e: "Add New Currency"});
                    this.currencies = l;
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

        async getInstallPaymentTypes() {
            this.isLoader = true;
            await adminApi
                .get(`/recievable-payable/rp_installment_payment_types`)
                .then((res) => {
                    let l = res.data.data;
                    l.unshift({
                        id: 0,
                        name: "اضف نوع دفع",
                        name_e: "Add installment payment type",
                    });
                    this.payment_types = l;
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

        async getInstallmentStatuses() {
            this.isLoader = true;
            await adminApi
                .get(`recievable-payable/rp_installment_status`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.unshift({
                        id: 0,
                        name: "اضف حالة الدفع",
                        name_e: "Add installment payment status",
                    });
                    this.installment_statuses = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },

        async getFirstBreak() {
            this.isLoader = true;
            await adminApi
                .get(`/recievable-payable/rp_break_down?break_id=${this.opening.id}&break_type=${this.opening.break_type}`)
                .then((res) => {
                    let l = res.data.data;
                    this.dataFirstBreakHandler(l);
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

        async dataFirstBreakHandler(data)
        {
            if (data.length > 0)
            {
                let opening = this.opening;
                if (opening.credit > 0) {
                    this.isDebit = false;
                    this.totalOpening = opening.credit;
                } else {
                    this.isDebit = true;
                    this.totalOpening = opening.debit;
                }
                this.totalDivision = 0 ;
                data.forEach((el)=>{
                    this.create.push({
                        instalment_date: el.instalment_date,
                        rate: el.rate,
                        currency_id: el.currency_id,
                        customer_id: el.customer_id,
                        client_type_id: el.client_type_id,
                        break_id: el.break_id,
                        break_type:el.break_type,
                        instalment_type_id: el.instalment_type_id,
                        document_id: el.document_id,
                        debit: el.debit,
                        credit: el.credit,
                        repate: el.repate,
                        total: el.total,
                        terms: el.terms,
                        installment_statu_id: el.installment_statu_id,
                        details: el.details,
                        installment_amount: el.total,
                        id:el.id

                    });
                    this.totalDivision += parseFloat(el.total);
                });
                this.residual = this.totalOpening - this.totalDivision;
            }else {
                this.calcTotalOpeningAndDivision();
                this.create.push({
                    instalment_date: data.date,
                    rate: data.rate,
                    currency_id: data.currency_id,
                    customer_id: data.customer_id,
                    client_type_id: data.client_type_id,
                    break_id: data.id,
                    instalment_type_id: 1,
                    break_type: data.break_type,
                    document_id: data.document_id,
                    debit: data.debit > 0 ? data.debit : 0,
                    credit: data.credit > 0 ? data.credit : 0 ,
                    repate: 1,
                    total: this.totalOpening,
                    terms: '',
                    installment_statu_id: 1,
                    details: '',
                    installment_amount: '',
                    id:null
                });
            }
        },

        addNewField() {
            let data = this.opening;
            this.create.push({
                instalment_date: new Date().toISOString().slice(0, 10),
                rate: data.rate,
                currency_id: data.currency_id,
                customer_id: data.customer_id,
                client_type_id: data.client_type_id,
                break_id: data.id,
                instalment_type_id: null,
                break_type: data.break_type,
                document_id: data.document_id,
                debit: 0,
                credit: 0,
                repate: 1,
                total: 0,
                terms: '',
                installment_statu_id: 1,
                details: '',
                installment_amount: '',
                id:null

            });
        },
        async removeNewField(index) {

            if (this.create.length > 1) {
                this.create.splice(index, 1);
            }
            this.totalDivision = 0;
            this.create.forEach((el) => {
                this.totalDivision += parseFloat(el.total);
            });
            this.residual = this.totalOpening - this.totalDivision;

            // if (this.create[index].id && this.opening.is_update)
            // {
            //     await this.deleteModule(this.create[index].id,index);
            // }else {
            //     if (this.create.length > 1) {
            //         this.create.splice(index, 1);
            //     }
            //     this.totalDivision = 0;
            //     this.create.forEach((el) => {
            //         this.totalDivision += parseFloat(el.total);
            //     });
            //     this.residual = this.totalOpening - this.totalDivision;
            // }

        },
        /**
         *  start delete module
         */
        async deleteModule(id,index) {
            await Swal.fire({
                title: `${this.$t("general.Areyousure")}`,
                text: `${this.$t("general.Youwontbeabletoreverthis")}`,
                type: "warning",
                showCancelButton: true,
                confirmButtonText: `${this.$t("general.Yesdeleteit")}`,
                cancelButtonText: `${this.$t("general.Nocancel")}`,
                confirmButtonClass: "btn btn-success mt-2",
                cancelButtonClass: "btn btn-danger ml-2 mt-2",
                buttonsStyling: false,
            }).then((result) => {
                if (result.value) {
                    this.isLoader = true;
                    adminApi.delete(`/recievable-payable/rp_break_down/${id}`)
                        .then((res) => {
                            if (this.create.length > 1) {
                                this.create.splice(index, 1);
                            }
                            this.totalDivision = 0;
                            this.create.forEach((el) => {
                                this.totalDivision += parseFloat(el.total);
                            });
                            this.residual = this.totalOpening - this.totalDivision;
                            Swal.fire({
                                icon: "success",
                                title: `${this.$t("general.Deleted")}`,
                                text: `${this.$t("general.Yourrowhasbeendeleted")}`,
                                showConfirmButton: false,
                                timer: 1500,
                            });
                        })
                        .catch((err) => {
                            if (err.response.status == 400) {
                                Swal.fire({
                                    icon: "error",
                                    title: `${this.$t("general.Error")}`,
                                    text: `${this.$t("general.CantDeleteRelation")}`,
                                });
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
            });
        },
        /**
         *  end delete module
         */
        async createOrUpdate ()
        {
            this.isLoader = true;
            await this.getCustomers();
            await this.getCurrencies();
            await this.getInstallPaymentTypes();
            await this.getInstallmentStatuses();
            if (this.opening.is_update)
            {
                await this.getFirstBreak();
            }else {
                await this.resetModal();
            }
        },
        /**
         *  hidden Modal (create)
         */
        resetModalHidden() {
            this.create = [];
            this.is_disabled = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
            this.$bvModal.hide(`opening-balance-break-create`);
        },
        calcTotalOpeningAndDivision ()
        {
            let data = this.opening;
            if (data.credit > 0) {
                this.isDebit = false;
                this.totalOpening = data.credit;
                this.totalDivision = data.credit;
            } else {
                this.isDebit = true;
                this.totalOpening = data.debit;
                this.totalDivision = data.debit;
            }
        },
        async resetModal() {
            this.isLoader = true;
            let data = this.opening;
            this.calcTotalOpeningAndDivision();
            this.create.push({
                instalment_date: data.date,
                rate: data.rate,
                currency_id: data.currency_id,
                customer_id: data.customer_id,
                customer_id: data.client_type_id,
                break_id: data.id,
                break_type: data.break_type,
                document_id: data.document_id,
                instalment_type_id: 1,
                debit: data.debit > 0 ? data.debit : 0,
                credit: data.credit > 0 ? data.credit : 0 ,
                repate: 1,
                total: this.totalOpening,
                terms: '',
                installment_statu_id: 1,
                details: '',
                installment_amount: this.totalOpening,
                id:null

            });
            this.isLoader = false;
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.errors = {};
        },
        /**
         *  create module
         */
        AddSubmit() {
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                let creatArr = [];
                this.create.forEach((el)=>{
                    if (!el.id || this.opening.is_update)
                    {
                        creatArr.push(el)
                    }
                });
                let break_downs={
                    break_downs:creatArr
                }
                adminApi
                    .post(`/recievable-payable/${this.opening.is_update ?'rp_break_down/update_break' :'rp_break_down'}`, break_downs)
                    .then((res) => {
                        this.is_disabled = true;
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
        calcBreak(index) {
            this.totalDivision = 0;
            if (!this.isDebit) {
                this.create[index].total = this.create[index].credit
                this.create[index].installment_amount = (this.create[index].credit / this.create[index].repate).toFixed(3) ;
                this.create.forEach((el) => {
                    this.totalDivision += parseFloat(el.total);
                });
                this.residual = this.totalOpening - this.totalDivision;
            } else {
                this.create[index].total = this.create[index].debit
                this.create[index].installment_amount = (this.create[index].debit / this.create[index].repate).toFixed(3) ;
                this.create.forEach((el) => {
                    this.totalDivision += parseFloat(el.total);
                });
                this.residual = this.totalOpening - this.totalDivision;
            }
        },
        calcTotal(index) {
            let repate = this.create[index].repate;
            if (this.create[index].repate) {
                if (!this.isDebit) {
                    this.create[index].total = this.create[index].credit * repate;
                } else {
                    this.create[index].total = this.create[index].debit * repate;
                }
            }
        },
        async showBreakDetails(index) {
            let row = this.create[index];
            if (row.id)
            {
                await this.BreakUpdate(index);
            }else {
                await this.BreakCreate(index);
            }
        },
        async BreakCreate(index) {
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                let break_downs = {
                    break_downs: [this.create[index]]
                }
                await adminApi.post(`/recievable-payable/rp_break_down`, break_downs)
                    .then((res) => {
                        this.is_disabled = true;
                        this.create[index].id = res.data[0].id;
                        this.showBreak(index);
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                text: `${this.$t('general.Addedsuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: `${this.$t('general.Error')}`,
                                text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                            });
                        }
                    }).finally(() => {
                        this.isLoader = false;
                        this.is_disabled = false;
                    });
            }

        },
        async BreakUpdate(index) {
            this.$v.create.$touch();

            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                let break_downs = {
                    break_downs: [this.create[index]]
                }
                await adminApi.post(`/recievable-payable/rp_break_down/update_break`, break_downs)
                    .then((res) => {
                        this.is_disabled = true;
                        this.create[index].id = res.data[0].id;
                        this.showBreak(index,true);
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                text: `${this.$t('general.Addedsuccessfully')}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }, 500);
                    })
                    .catch((err) => {
                        if (err.response.data) {
                            this.errors = err.response.data.errors;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: `${this.$t('general.Error')}`,
                                text: `${this.$t('general.Thereisanerrorinthesystem')}`,
                            });
                        }
                    }).finally(() => {
                        this.isLoader = false;
                        this.is_disabled = false;
                    });
            }

        },
        showBreak(index,is_update=false){
            if (this.create[index]) {
                this.openingBreak = this.create[index];
                this.openingBreak.is_update = is_update;
                this.$bvModal.show("opening-balance-break-create-show");
            }
        },
        isPermission(item) {
            if (this.$store.state.auth.type == "user") {
                return this.$store.state.auth.permissions.includes(item);
            }
            return true;
        },
    },
};
</script>

<template>
    <div>
        <InstallmentStatus :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'installment-create-status'"
                           :isPage="false"
                           type="create"
                           :isPermission="isPermission" @created="getInstallmentStatuses"/>
        <showBreak :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :opening="openingBreak"/>
        <InstallmentPaymentType :id="'installment_payment_type_create'"
                                :isPage="false"
                                type="create"
                                :isPermission="isPermission" :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="getInstallPaymentTypes"/>
        <!--  create   -->
        <b-modal
            id="opening-balance-break-create"
            :title="$t('general.Break')"
            title-class="font-18"
            body-class="p-4 "
            dialog-class="modal-full-width"
            scrollable
            :hide-footer="true"
            @hidden="resetModalHidden"
        >
            <form>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ $t('general.totalAmount') }}
                                    </label>
                                    <input
                                        :disabled="true"
                                        type="number"
                                        class="form-control"
                                        step="any"
                                        v-model="totalOpening"
                                    />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">
                                        {{ $t('general.totalDivision') }}
                                    </label>
                                    <input
                                        :disabled="true"
                                        type="number"
                                        class="form-control"
                                        step="any"
                                        v-model="totalDivision"
                                    />
                                </div>
                            </div>
<!--                            <div class="col-md-4">-->
<!--                                <div class="form-group">-->
<!--                                    <label class="control-label">-->
<!--                                        {{ $t('general.Residual') }}-->
<!--                                    </label>-->
<!--                                    <input-->
<!--                                        :disabled="true"-->
<!--                                        type="number"-->
<!--                                        class="form-control"-->
<!--                                        step="any"-->
<!--                                        v-model="residual"-->
<!--                                    />-->
<!--                                </div>-->
<!--                            </div>-->
                        </div>

                    </div>
                    <div class="col-md-6 mb-3 d-flex justify-content-end">
                        <template v-if="!is_disabled && residual == 0">
                            <b-button
                                variant="success"
                                type="button"
                                class="mx-1"
                                v-if="!isLoader"
                                @click.prevent="AddSubmit"
                            >
                                {{ $t("general.Save") }}
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
                </div>

                <template v-for="(item, index) in create">
                    <div class="row" v-if="index == 0">
                        <div class="col-md-1">
                            <div class="form-group position-relative">
                                <label v-if="index == 0" class="control-label">
                                    {{ getCompanyKey('installment_opening_balance_currency') }}
                                </label>
                                <multiselect
                                    :disabled="true"
                                    v-model="$v.create.$each[index].currency_id.$model"
                                    :options="currencies.map((type) => type.id)"
                                    :custom-label="(opt) => $i18n.locale == 'ar' ?currencies.find((x) => x.id == opt).name:currencies.find((x) => x.id == opt).name_e"
                                    :class="{
                                    'is-invalid': $v.create.$each[index].currency_id.$error || errors.currency_id,
                                    'is-valid': !$v.create.$each[index].currency_id.$invalid && !errors.currency_id,
                                  }"
                                >
                                </multiselect>
                                <template v-if="errors.currency_id">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.currency_id"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage
                                    >
                                </template>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label v-if="index == 0" class="control-label">
                                    {{ getCompanyKey('installment_opening_balance_rate') }}
                                </label>
                                <input
                                    :disabled="true"
                                    type="number"
                                    class="form-control"
                                    step="any"
                                    v-model="$v.create.$each[index].rate.$model"
                                    :class="{
                                    'is-invalid': $v.create.$each[index].rate.$error || errors.rate,
                                    'is-valid': !$v.create.$each[index].rate.$invalid && !errors.rate,
                                  }"
                                />
                                <template v-if="errors.rate">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.rate"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage
                                    >
                                </template>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group position-relative">
                                <label v-if="index == 0"
                                       class="control-label">{{ getCompanyKey("customer") }}
                                </label>
                                <multiselect
                                    :disabled="true"
                                    v-model="$v.create.$each[index].customer_id.$model"
                                    :options="customers.map((type) => type.id)"
                                    :custom-label="(opt) => $i18n.locale == 'ar' ?customers.find((x) => x.id == opt).name:customers.find((x) => x.id == opt).name_e"
                                    :class="{
                                        'is-invalid': $v.create.$each[index].customer_id.$error || errors.customer_id ,
                                        'is-valid': !$v.create.$each[index].customer_id.$invalid && !errors.customer_id,
                                      }"
                                >
                                </multiselect>

                                <template v-if="errors.customer_id">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.customer_id"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage
                                    >
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-1 p-0">
                            <div class="form-group">
                                <label v-if="index == 0" class="control-label" style="font-size: 12px">
                                    {{ $t('general.totalDebit') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    :disabled=" create[index].installment_statu_id != 1 ? true : !isDebit ? true :opening.is_update?false:create[index].id?true:false"
                                    type="number"
                                    step="any"
                                    class="form-control"
                                    style="padding: 0 0 0 5px"
                                    data-create="3"
                                    @input="calcBreak(index)"
                                    v-model="$v.create.$each[index].debit.$model"
                                    :class="{
                                        'is-invalid': $v.create.$each[index].debit.$error || errors.debit,
                                        'is-valid': !$v.create.$each[index].debit.$invalid && !errors.debit,
                                      }"
                                />
                                <template v-if="errors.debit">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.debit"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage
                                    >
                                </template>
                            </div>
                        </div>
                        <div class="col-md-1 p-0">
                            <div class="form-group">
                                <label v-if="index == 0" class="control-label" style="font-size: 12px">
                                    {{ $t('general.totalCredit') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    :disabled="create[index].installment_statu_id != 1 ? true : isDebit?true:opening.is_update?false:create[index].id?true:false"
                                    type="number"
                                    step="any"
                                    class="form-control"
                                    style="padding: 0 0 0 5px"
                                    @input="calcBreak(index)"
                                    data-create="3"
                                    v-model="$v.create.$each[index].credit.$model"
                                    :class="{
                                    'is-invalid': $v.create.$each[index].credit.$error || errors.credit,
                                    'is-valid': !$v.create.$each[index].credit.$invalid && !errors.credit,
                                  }"
                                />
                                <template v-if="errors.credit">
                                    <ErrorMessage
                                        v-for="(errorMessage, index) in errors.credit"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage
                                    >
                                </template>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label v-if="index == 0"
                                       class="control-label">{{ getCompanyKey("installment_payment_type_id") }}</label>
                                <multiselect
                                    :disabled="create[index].installment_statu_id != 1 ? true :false"
                                    @input="showInstallmentPaymentTypeModal(index)"
                                    v-model="create[index].instalment_type_id"
                                    :options="payment_types.map((type) => type.id)"
                                    :custom-label="
                                      (opt) =>
                                        $i18n.locale == 'ar'
                                          ? payment_types.find((x) => x.id == opt).name
                                          : payment_types.find((x) => x.id == opt).name_e
                                    "
                                >
                                </multiselect>
                                <div
                                    v-if="!$v.create.$each[index].instalment_type_id.required"
                                    class="invalid-feedback"
                                >
                                    {{ $t("general.fieldIsRequired") }}
                                </div>
                                <template v-if="errors.instalment_type_id">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.instalment_type_id"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label v-if="index == 0" class="control-label" style="font-size: 12px">
                                    {{ $t('general.rebates') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    :disabled="opening.is_update?true:create[index].installment_statu_id != 1 ? true : create[index].id?true:false"
                                    type="number"
                                    class="form-control"
                                    data-create="3"
                                    @input="calcBreak(index)"
                                    v-model="$v.create.$each[index].repate.$model"
                                    :class="{
                                    'is-invalid': $v.create.$each[index].repate.$error || errors.repate,
                                    'is-valid': !$v.create.$each[index].repate.$invalid && !errors.repate,
                                  }"
                                />
                                <template v-if="errors.repate">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.repate"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-1 p-0">
                            <div class="form-group">
                                <label v-if="index == 0" class="control-label">
                                    {{ getCompanyKey('installment_opening_balance_date') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <date-picker
                                    :disabled="create[index].installment_statu_id != 1 ? true : opening.is_update?false:create[index].id?true:false"
                                    type="date"
                                    v-model="$v.create.$each[index].instalment_date.$model"
                                    format="YYYY-MM-DD"
                                    valueType="format"
                                    :confirm="false"
                                    :class="{ 'is-invalid':
                                            $v.create.$each[index].instalment_date.$error ||
                                            errors.instalment_date,
                                        'is-valid':
                                            !$v.create.$each[index].instalment_date
                                                .$invalid &&
                                            !errors.instalment_date,
                                    }"
                                ></date-picker>
                                <template v-if="errors.instalment_date">
                                    <ErrorMessage v-for="(errorMessage,index) in errors.instalment_date"
                                                  :key="index">
                                        {{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-1 p-0" v-if="!opening.is_update">
                            <div class="form-group">
                                <label v-if="index == 0" class="control-label" style="font-size: 12px">
                                    {{ $t('general.installmentAmount') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    :disabled="true"
                                    type="number"
                                    step="any"
                                    class="form-control"
                                    style="padding: 0 0 0 5px"
                                    data-create="3"
                                    v-model="$v.create.$each[index].installment_amount.$model"
                                    :class="{
                                    'is-invalid': $v.create.$each[index].installment_amount.$error || errors.installment_amount,
                                    'is-valid': !$v.create.$each[index].installment_amount.$invalid && !errors.installment_amount,
                                  }"
                                />
                                <template v-if="errors.installment_amount">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.installment_amount" :key="index">
                                        {{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
<!--                        <div class="col-md-1 p-0">-->
<!--                            <div class="form-group">-->
<!--                                <label v-if="index == 0" class="control-label" style="font-size: 12px">-->
<!--                                    {{ $t('general.Total') }}-->
<!--                                    <span class="text-danger">*</span>-->
<!--                                </label>-->
<!--                                <input-->
<!--                                    :disabled="true"-->
<!--                                    type="number"-->
<!--                                    class="form-control"-->
<!--                                    style="padding: 0 0 0 5px"-->
<!--                                    data-create="3"-->
<!--                                    v-model="$v.create.$each[index].total.$model"-->
<!--                                    :class="{-->
<!--                                    'is-invalid': $v.create.$each[index].total.$error || errors.total,-->
<!--                                    'is-valid': !$v.create.$each[index].total.$invalid && !errors.total,-->
<!--                                  }"-->
<!--                                />-->
<!--                                <template v-if="errors.total">-->
<!--                                    <ErrorMessage-->
<!--                                        v-for="(errorMessage, index) in errors.total"-->
<!--                                        :key="index"-->
<!--                                    >{{ errorMessage }}-->
<!--                                    </ErrorMessage-->
<!--                                    >-->
<!--                                </template>-->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label v-if="index == 0" class="control-label" style="font-size: 12px">
                                    {{ $t('general.details') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    :disabled="create[index].installment_statu_id != 1 ? true : opening.is_update?false:create[index].id?true:false"
                                    type="text"
                                    class="form-control"
                                    v-model="$v.create.$each[index].details.$model"
                                    :class="{
                                    'is-invalid': $v.create.$each[index].details.$error || errors.details,
                                    'is-valid': !$v.create.$each[index].details.$invalid && !errors.details,
                                  }"
                                />
                                <template v-if="errors.details">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.details"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label v-if="index == 0">{{ $t('general.status') }}</label>
                                <multiselect
                                    :disabled="true"
                                    @input="showInstallmentStatusModal(index)"
                                    v-model="create[index].installment_statu_id"
                                    :options="installment_statuses.map((type) => type.id)"
                                    :custom-label="
                                          (opt) =>
                                            $i18n.locale == 'ar'
                                              ? installment_statuses.find((x) => x.id == opt).name
                                              : installment_statuses.find((x) => x.id == opt).name_e
                                        "
                                >
                                </multiselect>
                                <div
                                    v-if="!$v.create.$each[index].installment_statu_id.required"
                                    class="invalid-feedback"
                                >
                                    {{ $t("general.fieldIsRequired") }}
                                </div>

                                <template v-if="errors.installment_statu_id">
                                    <ErrorMessage v-for="(errorMessage, index) in errors.installment_statu_id"
                                        :key="index"
                                    >{{ errorMessage }}
                                    </ErrorMessage>
                                </template>
                            </div>
                        </div>

                        <div :class="[index == 0 ? ' pt-3':'' ,'col-md-1']">
<!--                            <b-button v-if="((create[index].repate > 1 && opening.is_update)||(create[index].repate > 1 && !create[index].id)) && parseInt(residual) == 0"-->
<!--                                      variant="primary"-->
<!--                                      class="btn btn-primary"-->
<!--                                      @click="showBreakDetails(index)"-->
<!--                            >-->
<!--                                {{ $t('general.Break') }}-->
<!--                            </b-button>-->

<!--                            <b-button v-else-->
<!--                                      variant="secondary"-->
<!--                                      class="btn btn-secondary"-->
<!--                            >-->
<!--                                {{ $t('general.Break') }}-->
<!--                            </b-button>-->
                        </div>

                        <div :class="[index == 0 ? ' pt-3':'' ,'col-md-1']">
                            <button v-if="(opening.is_update||(create.length > 1 && !create[index].id) ) && create[index].installment_statu_id == 1"
                                    type="button"
                                    @click.prevent="removeNewField(index)"
                                    class="custom-btn-dowonload"
                            >
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <b-button :disabled="residual > 0 ? false : true"
                                      variant="success"
                                      @click.prevent="addNewField"
                                      type="button" :class="['font-weight-bold px-2']"
                            >
                                <i class="fas fa-plus"></i>
                            </b-button>
                        </div>
                    </div>
                </template>
            </form>
        </b-modal>
        <!--  /create   -->
    </div>

</template>
<style>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    /* display: none; <- Crashes Chrome on hover */
    -webkit-appearance: none;
    margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
}

input[type=number] {
    -moz-appearance:textfield; /* Firefox */
}
</style>
