<template>
    <div>
        <!--  create   -->
        <b-modal
            :id="id"
            :title="type != 'edit' ?getCompanyKey('money_voucher_create_form'):getCompanyKey('money_voucher_edit_form')"
            title-class="font-18"
            body-class="p-4 "
            dialog-class="modal-full-width"
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
                    <div class="col-md-3" v-if="isVisible('document_id')">
                        <div class="form-group position-relative">
                            <label class="control-label">
                                {{ getCompanyKey("money_voucher_document") }}
                                <span v-if="isRequired('document_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                v-model="create.document_id"
                                @select="getAnotherData"
                                :options="documents.map((type) => type.id)"
                                :custom-label="
                                  (opt) =>
                                    documents.find((x) => x.id == opt)
                                      ? $i18n.locale == 'ar'
                                        ? documents.find((x) => x.id == opt).name
                                        : documents.find((x) => x.id == opt).name_e
                                      : null
                                "
                            >
                            </multiselect>
                            <div  v-if="$v.create.document_id.$error || errors.document_id" class="text-danger">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.document_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.document_id" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="isVisible('branch_id')">
                        <div class="form-group">
                            <label class="control-label">{{ getCompanyKey('money_voucher_branch') }} <span v-if="isRequired('branch_id')" class="text-danger">*</span></label>
                            <multiselect
                                @input="getSerialNumber('create',$event)"
                                v-model="create.branch_id"
                                :options="branches.map((type) => type.id)"
                                :custom-label="(opt) => $i18n.locale == 'ar'? branches.find((x) => x.id == opt).name: branches.find((x) => x.id == opt).name_e"
                                :class="{'is-invalid': $v.create.branch_id.$error || errors.branch_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.branch_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>

                            <template v-if="errors.branch_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.branch_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="isVisible('date')">
                        <div class="form-group">
                            <label class="control-label">
                                {{getCompanyKey('money_voucher_date')}}
                                <span v-if="isRequired('date')" class="text-danger">*</span>
                            </label>
                            <date-picker
                                :disabled="!create.branch_id"
                                @input="changeDateDocument('create')"
                                type="date"
                                v-model="create.date"
                                format="YYYY-MM-DD"
                                valueType="format"
                                :confirm="false"
                                :class="{'is-invalid': !financial_years_validate}"

                            ></date-picker>
                            <div v-if="!financial_years_validate" class="invalid-feedback">
                                {{ $t("general.The date is outside the permitted fiscal year") }}
                            </div>
                            <template v-if="errors.date">
                                <ErrorMessage v-for="(errorMessage, index) in errors.date" :key="index">
                                    {{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">
                                {{getCompanyKey('money_voucher_serial_number')}}
                            </label>
                            <input
                                :disabled="true"
                                type="text"
                                class="form-control"
                                v-model="serial_number"
                            />
                        </div>
                    </div>
                    <div class="col-md-3" v-if="isVisible('salesmen_id')">
                        <div class="form-group">
                            <label class="control-label">
                                {{getCompanyKey('money_voucher_salesmen')}}
                                <span v-if="isRequired('salesmen_id')" class="text-danger">*</span>
                            </label>

                            <multiselect
                                :disabled="!create.branch_id"
                                @input="getCustomerSalesman"
                                v-model="create.salesmen_id"
                                :options="salesmen.map((type) => type.id)"
                                :custom-label=" (opt) => $i18n.locale == 'ar' ? salesmen.find((x) => x.id == opt).name: salesmen.find((x) => x.id == opt).name_e"
                                :class="{'is-invalid':$v.create.salesmen_id.$error || errors.salesmen_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.salesmen_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.salesmen_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.salesmen_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="isVisible('client_type_id')">
                        <div class="form-group">
                            <label class="control-label">
                                {{getCompanyKey('money_voucher_client_type')}}
                                <span v-if="isRequired('client_type_id')" class="text-danger">*</span>
                            </label>

                            <multiselect
                                :disabled="true"
                                v-model="create.client_type_id"
                                :options="clientTypes.map((type) => type.id)"
                                :custom-label=" (opt) => clientTypes.find((x) => x.id == opt) ? $i18n.locale == 'ar' ? clientTypes.find((x) => x.id == opt).name: clientTypes.find((x) => x.id == opt).name_e:''"
                                :class="{'is-invalid':$v.create.client_type_id.$error || errors.client_type_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.client_type_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.client_type_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.client_type_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="isVisible('customer_id')">
                        <div class="form-group">
                            <label class="control-label">
                                {{getCompanyKey('money_voucher_customer')}}
                                <span v-if="isRequired('customer_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                :disabled="!create.branch_id"
                                :internalSearch="false"
                                @search-change="searchCustomer"
                                @input="getBreakCustomer(create.customer_id)"
                                v-model="create.customer_id"
                                :options="customers.map((type) => type.id)"
                                :custom-label="(opt) =>$i18n.locale == 'ar' ? customers.find((x) => x.id == opt).name: customers.find((x) => x.id == opt).name_e"
                                :class="{'is-invalid':$v.create.customer_id.$error || errors.customer_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.customer_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>

                            <template v-if="errors.customer_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.customer_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="isVisible('payment_method_id')">
                        <div class="form-group">
                            <label class="control-label">
                                {{getCompanyKey('money_voucher_payment_method')}}
                                <span v-if="isRequired('payment_method_id')" class="text-danger">*</span>
                            </label>
                            <multiselect
                                :disabled="!create.branch_id"
                                v-model="create.payment_method_id"
                                :options="paymentMethods.map((type) => type.id)" :custom-label="
                                                (opt) => $i18n.locale == 'ar' ? paymentMethods.find((x) => x.id == opt).name : paymentMethods.find((x) => x.id == opt).name_e"
                                :class="{ 'is-invalid':$v.create.payment_method_id.$error || errors.payment_method_id,}"
                            >
                            </multiselect>
                            <div v-if="!$v.create.payment_method_id.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.payment_method_id">
                                <ErrorMessage v-for="(errorMessage, index) in errors.payment_method_id"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="isVisible('amount')">
                        <div class="form-group">
                            <label class="control-label">
                                {{getCompanyKey('money_voucher_amount')}}
                                <span v-if="isRequired('amount')" class="text-danger">*</span>
                            </label>
                            <input
                                :disabled="!create.branch_id"
                                type="number"
                                class="form-control"
                                step="any"
                                v-model="create.amount"
                                :class="{'is-invalid':$v.create.amount.$error || errors.amount,}"
                            />
                            <div v-if="!$v.create.amount.required" class="invalid-feedback">
                                {{ $t("general.fieldIsRequired") }}
                            </div>
                            <template v-if="errors.amount">
                                <ErrorMessage v-for="(errorMessage, index) in errors.amount"
                                              :key="index">{{ errorMessage }}
                                </ErrorMessage>
                            </template>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="document && parseInt(document.is_break) == 2">
                        <div class="form-check mt-3">
                            <input style="transform: scale(1.5);" type="checkbox" v-model="open_invoice_details" value="true" id="flexCheckDefault1">
                            <label style="padding:9px" class="form-check-label" for="flexCheckDefault1">
                                {{$t('general.showInvoiceDetails')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="create.customer_id && open_invoice_details">
                        <div class="form-check mt-3">
                            <input style="transform: scale(1.5);" @change="getBreakCustomer(create.customer_id)" type="checkbox" v-model="with_paid" value="true" id="flexCheckDefault">
                            <label style="padding:9px" class="form-check-label" for="flexCheckDefault">
                                {{$t('general.showPayment')}}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3" v-if="create.amount && create.customer_id && open_invoice_details">
                        <b-button
                            variant="primary"
                            class="mx-1 mt-3"
                            @click.prevent="autoSettlement"
                        >
                            {{ $t("general.AutoSettlement") }}
                        </b-button>
                    </div>

                    <div class="col-md-12" v-if="document && document.attributes && parseInt(document.attributes.customer) != 0 && parseInt(document.is_break) > 0 && open_invoice_details">
                        <div class="page-content">
                            <div class="px-0">
                                <div class="row mt-4">
                                    <div class="col-12 col-lg-12">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="text-center text-150">
                                                    <i style="font-size:20px" class="fa fa-book fa-2x text-success-m2 mr-1"></i>
                                                    <span class="text-default-d3">{{$t("general.invoice_details")}}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- .row -->
                                        <div class="mt-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card-header p-0">
                                                        <h3 class="card-title float-left">
                                                            {{$t('general.CustomerDebts')}}
                                                        </h3>
                                                    </div>
                                                    <div class="table-responsive mb-3 custom-table-theme position-relative">
                                                        <!--       start loader       -->
                                                        <loader size="large" v-if="isLoader" />
                                                        <!--       end loader       -->

                                                        <table class="table table-borderless table-hover table-centered m-0" >
                                                            <thead>
                                                            <tr>
                                                                <th>{{ $t("general.documentSerial") }}</th>
<!--                                                                <th>{{ $t("general.salesmen") }}</th>-->
                                                                <th>{{ $t("general.type") }}</th>
                                                                <th>{{ $t("general.Date") }}</th>
                                                                <th>{{ $t("general.amount") }}</th>
                                                                <th>{{ $t("general.prev_settlement") }}</th>
                                                                <th>{{ $t("general.settlement_amount") }}</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody v-if="customerDebits.length > 0">
                                                            <tr v-for="(data, index) in customerDebits"
                                                                :key="data.id"
                                                                class="body-tr-custom"
                                                            >
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">{{ data.serial_number }}</h5>
                                                                </td>
<!--                                                                <td>-->
<!--                                                                    <h5 class="m-0 font-weight-normal">{{ data.salesman }}</h5>-->
<!--                                                                </td>-->
                                                                <td>
                                                                    <h5 v-if="data.document" class="m-0 font-weight-normal">{{$i18n.locale == "ar" ? data.document.name : data.document.name_e}}</h5>
                                                                    <h5 v-else class="m-0 font-weight-normal">---</h5>
                                                                </td>
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">
                                                                        {{ data.instalment_date }}
                                                                    </h5>
                                                                </td>
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>
                                                                </td>
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">{{ data.prev_settlement }}</h5>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input
                                                                            @input="checkDebitSettlement(index)"
                                                                            type="number"
                                                                            step="any"
                                                                            class="form-control englishInput"
                                                                            v-model="customerDebits[index].settlement_amount"
                                                                        />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr v-if="customerDebits.length > 0" class="total-amount">
                                                                <td></td>
                                                                <td></td>
                                                                <td colspan="3"><h5 class="m-0 font-weight-normal">{{$t('general.totalAmountSettlement')}}</h5></td>
                                                                <td v-html="totalDebitSettlement" class="amount-red"></td>
                                                            </tr>
                                                            </tbody>
                                                            <tbody v-else>
                                                            <tr>
                                                                <th class="text-center" colspan="6">
                                                                    {{ $t("general.notDataFound") }}
                                                                </th>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card-header p-0">
                                                        <h3 class="card-title float-left">
                                                            {{$t('general.CustomerCredit')}}
                                                        </h3>
                                                    </div>
                                                    <div class="table-responsive mb-3 custom-table-theme position-relative">
                                                        <!--       start loader       -->
                                                        <loader size="large" v-if="isLoader" />
                                                        <!--       end loader       -->

                                                        <table class="table table-borderless table-hover table-centered m-0" >
                                                            <thead>
                                                            <tr>
                                                                <th>{{ $t("general.documentSerial") }}</th>
<!--                                                                <th>{{ $t("general.salesmen") }}</th>-->
                                                                <th>{{ $t("general.type") }}</th>
                                                                <th>{{ $t("general.Date") }}</th>
                                                                <th>{{ $t("general.amount") }}</th>
                                                                <th>{{ $t("general.prev_settlement") }}</th>
                                                                <th>{{ $t("general.settlement_amount") }}</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody v-if="customerCredit.length > 0">
                                                            <tr v-for="(data, index) in customerCredit"
                                                                :key="data.id"
                                                                class="body-tr-custom"
                                                            >
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">{{ data.serial_number }}</h5>
                                                                </td>
<!--                                                                <td>-->
<!--                                                                    <h5 class="m-0 font-weight-normal">{{ data.salesman }}</h5>-->
<!--                                                                </td>-->
                                                                <td>
                                                                    <h5 v-if="data.document" class="m-0 font-weight-normal">{{$i18n.locale == "ar" ? data.document.name : data.document.name_e}}</h5>
                                                                    <h5 v-else class="m-0 font-weight-normal">---</h5>
                                                                </td>
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">
                                                                        {{ data.instalment_date }}
                                                                    </h5>
                                                                </td>
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">{{ data.amount }}</h5>
                                                                </td>
                                                                <td>
                                                                    <h5 class="m-0 font-weight-normal">{{ data.prev_settlement }}</h5>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <input
                                                                            @input="checkCreditSettlement(index)"
                                                                            type="number"
                                                                            step="any"
                                                                            class="form-control englishInput"
                                                                            v-model="customerCredit[index].settlement_amount"
                                                                        />
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr v-if="customerCredit.length > 0" class="total-amount">
                                                                <td></td>
                                                                <td></td>
                                                                <td colspan="3"><h5 class="m-0 font-weight-normal">{{$t('general.totalAmountSettlement')}}</h5></td>
                                                                <td v-html="totalCreditSettlement" class="amount-red"></td>
                                                            </tr>

                                                            </tbody>
                                                            <tbody v-else>
                                                            <tr>
                                                                <th class="text-center" colspan="6">
                                                                    {{ $t("general.notDataFound") }}
                                                                </th>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
import {maxLength, minLength, required, requiredIf} from "vuelidate/lib/validators";
import adminApi from "../../../api/adminAxios";
import {arabicValue, englishValue} from "../../../helper/langTransform";
import transMixinComp from "../../../helper/mixin/translation-comp-mixin";
import successError from "../../../helper/mixin/success&error";
import Swal from "sweetalert2";
import Multiselect from "vue-multiselect";
import {formatDateOnly} from "../../../helper/startDate";
import DatePicker from "vue2-datepicker";

export default {
    name: "Money-Voucher",
    mixins: [transMixinComp,successError],
    components: {
        ErrorMessage,
        loader,
        Multiselect,
        DatePicker
    },
    props: {
        id: {default: "create",}, companyKeys: {default: [],}, defaultsKeys: {default: [],},
        isPage: {default: true},isVisiblePage: {default: null},isRequiredPage: {default: null},
        type: {default: 'create'}, idObjEdit: {default: null},isPermission: {},url: {default: '/voucher-headers'}
    },
    data() {
        return {
            fields: [],
            typs: [],
            isLoader: false,
            bgs: [
                "bg-warning text-white",
                "bg-info text-white",
                "bg-success text-white",
                "bg-dark text-white",
                "bg-secondary text-white",
                "bg-danger text-white",
                "bg-primary text-white",
            ],
            isCustom: false,
            create: {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesmen_id: null,
                payment_method_id: null,
                customer_id: null,
                amount:0,
                document_id:null,
                client_type_id:1,
            },
            company_id: null,
            document_id: null,
            errors: {},
            is_disabled: false,
            document:null,
            financial_years_validate : true,
            relatedDocuments: [],
            customers: [],
            salesmen: [],
            paymentMethods: [],
            customerDebits:[],
            customerCredit:[],
            totalDebitSettlement: 0,
            totalCreditSettlement: 0,
            openTransfer:false,
            amountPaid:0,
            transaction:[],
            branches: [],
            documents:[],
            clientTypes:[],
            serial_number:'',
            with_paid:false,
            open_invoice_details:false,
        };
    },
    validations: {
        create: {
            date: { required: requiredIf(function (model) {
                    return this.isRequired("date");
                }),
            },
            customer_id: {required: requiredIf(function (model) {
                    return this.isRequired("customer_id");
                }),
            },
            branch_id: {required: requiredIf(function (model) {
                    return this.isRequired("branch_id");
                }),
            },
            salesmen_id: {required: requiredIf(function (model) {
                    return this.isRequired("salesmen_id");
                }),
            },
            payment_method_id: {required: requiredIf(function (model) {
                    return this.isRequired("payment_method_id");
                }),
            },
            amount: {required: requiredIf(function (model) {
                    return this.isRequired("amount");
                }),
            },
            document_id: {required: requiredIf(function (model) {
                    return this.isRequired("document_id");
                }),
            },
            client_type_id: {required: requiredIf(function (model) {
                    return this.isRequired("client_type_id");
                }),
            },
        },
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
    },
    methods: {
        formatDate(value) {
            return formatDateOnly(value);
        },
        async getCustomTableFields() {
            this.isCustom = true;
             await adminApi
                .get(`/customTable/table-columns/general_voucher_headers`)
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
            this.serial_number = "";
            this.customerCredit=[];
            this.customerDebits=[];
            this.totalDebitSettlement= 0;
            this.totalCreditSettlement= 0;
            this.openTransfer=false;
            this.amountPaid=0;
            this.amount=0;
            this.open_invoice_details=false;
            this.financial_years_validate = true;
            this.document = null;
            this.document_id = null;
            this.with_paid = false;
            this.create = {
                branch_id: null,
                date: this.formatDate(new Date()),
                salesmen_id: null,
                payment_method_id: null,
                customer_id: null,
                amount:0,
                document_id:null,
                client_type_id:1,
            };
            this.$nextTick(() => {
                this.$v.$reset();
            });
            this.is_disabled = false;
            this.errors = {};
        },
        resetModalHidden() {
            this.defaultData();
            this.$bvModal.hide(this.id);
        },
        resetModal() {
            this.defaultData();
            setTimeout( async () => {
                if(this.type != 'edit'){
                    if(!this.isPage) await this.getCustomTableFields();
                    if (this.isVisible("document_id")) this.getDocument();
                    // if (this.isVisible("branch_id")) this.getBranches();
                    // if (this.isVisible("salesmen_id")) this.getSalesmen();
                    // if (this.isVisible("payment_method_id")) this.getPaymentMethod();
                }else {
                    if(this.idObjEdit.dataObj){
                        if (this.isVisible("document_id")) this.getDocument();
                        if (this.isVisible("branch_id")) this.getBranches();
                        if (this.isVisible("salesmen_id")) this.getSalesmen();
                        if (this.isVisible("payment_method_id")) this.getPaymentMethod();
                        let module = this.idObjEdit.dataObj;
                        this.errors = {};
                        this.create.date = module.date;
                        this.create.salesmen_id = module.salesmen_id;
                        this.create.payment_method_id = module.payment_method_id;
                        this.create.customer_id = module.customer_id;
                        this.create.amount = module.amount;
                        this.create.document_id = module.document_id;
                    }
                }
            },50);
        },
        resetForm() {
            this.defaultData();
        },
        AddSubmit() {
            this.$v.create.$touch();
            if (this.open_invoice_details)
            {
                if (!this.validationBeforeSubmit()){return;}
            }
            if (this.$v.create.$invalid) {
                return;
            } else {
                this.isLoader = true;
                this.errors = {};
                if(this.type != 'edit'){
                    let data_create = this.create;
                    let break_settlement = [];
                    this.customerDebits.forEach((el,index) => {
                        if (el.settlement_amount > 0) {
                            break_settlement.push({
                                'break_id': el.id,
                                'amount': el.settlement_amount,
                            })
                        }
                    });
                    this.customerCredit.forEach((el,index) => {
                        if (el.settlement_amount > 0) {
                            break_settlement.push({
                                'break_id': el.id,
                                'amount': el.settlement_amount,
                            })
                        }
                    });
                    data_create.break_settlement = break_settlement;
                    adminApi
                        .post(this.url, { ...data_create, company_id: this.company_id })
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
       async getAnotherData() {
            this.document_id = this.create.document_id;
            this.document = this.documents.find((row) => this.document_id == row.id);
            if (this.isVisible("branch_id")) await this.getBranches();
            if (this.isVisible("salesmen_id")) await this.getSalesmen();
            if (this.isVisible("payment_method_id")) await this.getPaymentMethod();
            if (this.isVisible("client_type_id")) await this.getClientType();
            if (parseInt(this.document.is_break) == 1)
            {
                this.open_invoice_details = true;
            }
           this.serial_number = "";
           this.customerCredit=[];
           this.customerDebits=[];
           this.totalDebitSettlement= 0;
           this.totalCreditSettlement= 0;
           this.openTransfer=false;
           this.amountPaid=0;
           this.amount=0;
           this.financial_years_validate = true;
           this.with_paid = false;
           this.create.branch_id = null;
           this.create.date = this.formatDate(new Date());
           this.create.salesmen_id = null;
           this.create.payment_method_id = null;
           this.create.customer_id = null;
           this.create.amount = 0;
           this.create.client_type_id = 1;
        },
       async getDocument() {
            this.isLoader = true;
           await adminApi
                .get(`/document?company_id=${this.company_id}&document=1&document_detail_type=document_money`)
                .then((res) => {
                    let l = res.data.data;
                    this.documents = l;
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
       async getClientType() {
            this.isLoader = true;
           await adminApi
                .get(`/client-types/get-drop-down`)
                .then((res) => {
                    let l = res.data.data;
                    this.clientTypes = l;
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
        async getBranches() {
            this.isLoader = true;
            await adminApi
                .get(`/branches?document_id=${this.document_id}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.branches = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getSalesmen() {
            this.isLoader = true;
            await adminApi
                .get(`/employees?is_salesman=1&customer_handel=1`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.salesmen = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getCustomerSalesman(e)
        {
            let employee_id = e;
            if (employee_id)
            {
                await this.getCustomers(employee_id);
            }
        },
        async getCustomers(employee_id=null,search='') {
            this.isLoader = true;
            await adminApi
                .get(`/general-customer?limet=10&company_id=${this.company_id}&employee_id=${employee_id}&search=${search}&columns[0]=name&columns[1]=name_e&columns[2]=id`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
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
        async getPaymentMethod() {
            this.isLoader = true;
            await adminApi
                .get(`/payment-methods`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    this.paymentMethods = l;
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        async getBreakCustomer(id) {
            this.isLoader = true;
            this.customerDebits = [];
            this.customerCredit = [];
            await adminApi
                .get(`/recievable-payable/document-with-money-details?customer_id=${id}&with_paid=${this.with_paid}`)
                .then((res) => {
                    this.isLoader = false;
                    let l = res.data.data;
                    l.forEach((e) => {
                        if (parseFloat(e.debit) > 0)
                        {
                            this.customerDebits.push({
                                id:e.id,
                                break_type:e.break_type,
                                document:e.document,
                                amount:parseFloat(e.debit),
                                instalment_date:e.instalment_date,
                                // salesman: this.handelSalesman(e),
                                serial_number: this.handelSerial(e),
                                amount_status:e.amount_status,
                                settlement_amount:0,
                                prev_settlement:e.prev_settlement??0,
                            });
                        }else {
                            this.customerCredit.push({
                                id:e.id,
                                break_type:e.break_type,
                                document:e.document,
                                amount:parseFloat(e.credit),
                                instalment_date:e.instalment_date,
                                // salesman: this.handelSalesman(e),
                                serial_number: this.handelSerial(e),
                                amount_status:e.amount_status,
                                settlement_amount:0,
                                prev_settlement:e.prev_settlement??0,
                            });
                        }

                    });
                    this.accountTotalAmount();
                })
                .catch((err) => {
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.Thereisanerrorinthesystem")}`,
                    });
                });
        },
        handelSalesman(e)
        {
            if (e.break_type == 'invoice')
            {
                if (e.invoice)
                {
                    if (e.invoice.salesman)
                    {
                        return this.$i18n.locale == "ar" ? e.invoice.salesman.name : e.invoice.salesman.name_e;
                    }else {
                        return '---';
                    }
                }else {
                    return '---';
                }
            }
            if (e.break_type == 'contract')
            {
                if (e.contract)
                {
                    if (e.contract.salesman)
                    {
                        return this.$i18n.locale == "ar" ? e.contract.salesman.name : e.contract.salesman.name_e;
                    }else {
                        return '---';
                    }
                }else {
                    return '---';
                }
            }
            if (e.break_type == 'documentHeader')
            {
                if (e.documentHeader)
                {
                    if (e.documentHeader.employee)
                    {
                        return this.$i18n.locale == "ar" ? e.documentHeader.employee.name : e.documentHeader.employee.name_e;
                    }else {
                        return '---';
                    }
                }else {
                    return '---';
                }
            }

            return '---';
        },
        handelSerial(e)
        {
            if (e.break_type == 'invoice')
            {
                if (e.invoice)
                {
                    return e.invoice.prefix;
                }else {
                    return '---';
                }
            }
            if (e.break_type == 'contract')
            {
                if (e.contract)
                {
                    return e.contract.prefix;
                }else {
                    return '---';
                }
            }
            if (e.break_type == 'documentHeader')
            {
                if (e.documentHeader)
                {
                    return e.documentHeader.prefix;
                }else {
                    return '---';
                }
            }

            return '---';
        },
        async getSerialNumber(type='create',e)
        {
            let date = this.create.date;
            let shortYear =new Date(date).getFullYear();
            let twoDigitYear = shortYear.toString().substr(-2);
            let branch = this.branches.find((row) => e == row.id);
            let serial = branch.serials.find((row) => this.document_id == row.document_id);
            this.serial_number = `${twoDigitYear}-${branch.id}-${this.document_id}-${serial.perfix}`;
        },
        async changeDateDocument(type='create')
        {
            let date = this.create.date;
            let branch_id = this.create.branch_id;
            await this.checkFinancialYears(date,branch_id);
        },
        async checkFinancialYears(date,branch_id) {
            this.isLoader = true;
            await adminApi
                .get(`/document-headers/check-date?date=${date}`)
                .then((res) => {
                    let l = res.data;
                    if(!l)
                    {
                        this.financial_years_validate= false;
                        Swal.fire({
                            icon: "error",
                            title: `${this.$t("general.Error")}`,
                            text: `${this.$t("general.The date is outside the permitted fiscal year")}`,
                        });
                        this.serial_number = '';
                    }else{
                        this.financial_years_validate= true;
                        let shortYear =new Date(date).getFullYear();
                        let twoDigitYear = shortYear.toString().substr(-2);
                        let branch = this.branches.find((row) => branch_id == row.id);
                        this.serial_number = `${twoDigitYear}-${branch.id}-${this.document_id}-${branch.serials[0].perfix}`;
                    }
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
        async searchCustomer(e) {
            let search = e??'';
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                this.getCustomers(this.create.employee_id,search);
            }, 500);

        },
        checkDebitSettlement(index)
        {
            let settlement_amount = this.customerDebits[index].settlement_amount;
            let prev_settlement = this.customerDebits[index].prev_settlement;
            let amount = this.customerDebits[index].amount;
            if(parseFloat(settlement_amount) < 0){
                this.customerDebits[index].settlement_amount = 0 ;
            }else if ((amount - prev_settlement) >= settlement_amount ){
                this.accountTotalAmount();
            }else if((amount - prev_settlement) < settlement_amount) {
                this.customerDebits[index].settlement_amount = amount - prev_settlement;
                this.accountTotalAmount();
            }

        },
        checkCreditSettlement(index)
        {
            let settlement_amount = this.customerCredit[index].settlement_amount;
            let prev_settlement = this.customerCredit[index].prev_settlement;
            let amount = this.customerCredit[index].amount;
            if(parseFloat(settlement_amount) < 0){
                this.customerCredit[index].settlement_amount = 0 ;
            }else if ((amount - prev_settlement) >= settlement_amount ){
                this.accountTotalAmount();
            }else if((amount - prev_settlement) < settlement_amount) {
                this.customerCredit[index].settlement_amount = amount - prev_settlement;
                this.accountTotalAmount();
            }

        },
        // calculate total amount in customer Break and Transactions
        accountTotalAmount(){
            //account total amount order
            this.totalDebitSettlement = 0;
            this.customerDebits.forEach((el,index)=>{
                this.totalDebitSettlement += parseFloat(el.settlement_amount);
            });

            //account total amount transfer order
            this.totalCreditSettlement = 0;
            this.customerCredit.forEach((el,index)=>{
                this.totalCreditSettlement += parseFloat(el.settlement_amount);
            });
        },
        validationBeforeSubmit()
        {
            let amount = this.create.amount;
            if (parseInt(this.document.attributes.customer) == -1){
                if ( amount !=(this.totalDebitSettlement - this.totalCreditSettlement)){
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.There_is_an_error_in_the_calculations")}`,
                    });
                    return false;
                }
            }else if(parseInt(this.document.attributes.customer) == 1){
                if ( amount !=(this.totalCreditSettlement - this.totalDebitSettlement)){
                    Swal.fire({
                        icon: "error",
                        title: `${this.$t("general.Error")}`,
                        text: `${this.$t("general.There_is_an_error_in_the_calculations")}`,
                    });
                    return false;
                }
            }
            return true;
        },
        resetDebitsAndCredit()
        {
            this.customerDebits.forEach((e) => {
                e.settlement_amount=0
            });
            this.customerCredit.forEach((e) => {
                e.settlement_amount=0
            });
            this.totalDebitSettlement = 0;
            this.totalCreditSettlement = 0;
        },
        autoSettlement()
        {
            this.resetDebitsAndCredit();
            let amount = this.create.amount;
            if (parseInt(this.document.attributes.customer) == -1){
                this.customerDebits.forEach((e,index) => {
                    if ((e.amount - e.prev_settlement) != 0 && amount > 0)
                    {
                        if (amount >=  e.amount - e.prev_settlement)
                        {
                            e.settlement_amount= e.amount - e.prev_settlement;
                            amount -= e.amount - e.prev_settlement;
                        }else{
                            e.settlement_amount= amount;
                            amount -= amount
                        }
                    }
                });
            }else if(parseInt(this.document.attributes.customer) == 1){
                this.customerCredit.forEach((e,index) => {
                    if ((e.amount - e.prev_settlement) != 0 && amount > 0)
                    {
                        if (amount >=  e.amount - e.prev_settlement)
                        {
                            e.settlement_amount= e.amount - e.prev_settlement;
                            amount -= e.amount - e.prev_settlement;
                        }else{
                            e.settlement_amount= amount;
                            amount -= amount
                        }

                    }
                });
            }
            this.accountTotalAmount();
            if (amount)
            {
                Swal.fire({
                    icon: "error",
                    title: `${this.$t("general.Error")}`,
                    text: `${this.$t("general.TheSettlementAmountIsGreaterThanTheInstallments")}`,
                });
            }

        }

    }
}
</script>

<style scoped>
form {
    position: relative;
}
</style>
