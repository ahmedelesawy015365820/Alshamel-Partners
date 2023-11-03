<template>
    <b-modal
        id="pay-create"
        :title="$t('general.receipt')"
        title-class="font-18"
        body-class="p-4"
        :hide-footer="true"
        size="lg"
        @show="resetModal"
        @hidden="resetModalHidden"
    >
        <div class="row mx-0 modal-content-row" v-if="order">
            <!--Left payment details-->
            <div class="col-12 col-md-6 cart-border-right text-center">
                <div class="horizontal-scroll" id="printCustom">
                    <h5 class="text-center mb-4">{{ $t('general.returnOrder') + ' ' + $t('general.details') }}</h5>
<!--                    <div class="invoiceLogo text-center">-->
<!--                        <img v-if="order.orderType === 'sales'"-->
<!--                             :src="'https://www.nidec-avtronencoders.com/avtron-middleware2/public/assets/img/relnoimgnew.png'"-->
<!--                             width="100" class="img-fluid" alt=""-->
<!--                        >-->
<!--                    </div>-->
                    <div>
                        <div class="text-center header-line-height">
                            <small class='text-center font-weight-bold'>{{$i18n.locale == "ar" ? company.name : company.name_e }}</small>
                            <br>
                            <small class='text-center'>{{ formatDate(new Date()) }}</small>
                            <br v-if="serial">
                            <small v-if="serial" class='text-center'>{{ serial }}</small>
                            <br>
                            <small class='text-center'>
                                {{ $t('general.returnOrder') + ' ' + $t('general.receipt') }}
                            </small>
                            <br>
                            <small class="text-left">
                                {{ $t('general.SoldBy') }}: {{ getCreateBy() }}
                            </small>
                            <br>
                            <small v-if="order.orderType === 'sales'">
                            <span>
                                <span v-if="!order.customer">
                                    {{ $t('general.SoldTo') }}: {{ $t('general.walk_in_customer') }}
                                </span>
                                <span v-else>
                                    {{ $t('general.SoldTo')}}: {{ $i18n.locale == 'ar' ? order.customer.name : order.customer.name_e }}
                                </span>
                            </span>
                            </small>
<!--                            <small v-else>-->
<!--                                <span v-if="salesOrReceivingType === 'supplier'">-->
<!--                                    <span v-if="finalCart.customer.length === 0">-->
<!--                                        {{ trans('lang.received_from') }}: {{ trans('lang.walk_in_supplier') }}-->
<!--                                    </span>-->
<!--                                    <span v-else>-->
<!--                                        {{-->
<!--                                            trans('lang.received_from')-->
<!--                                        }} : {{ finalCart.customer.first_name + " " + finalCart.customer.last_name }}-->
<!--                                    </span>-->
<!--                                </span>-->
<!--                                <span v-else>-->
<!--                                    {{ trans('lang.received_from') }}: {{ finalCart.transferBranchName }}</span>-->
<!--                            </small>-->
<!--                            <small class="text-left invoice-show" style="display: none">-->
<!--                                {{ trans('lang.invoice_id') }}:{{ invoiceID }}-->
<!--                            </small>-->
                        </div>
                        <div class="invoice-table">
                            <table class="table product-card-font" style="font-weight:500">
                                <thead class="border-top-0">
                                <tr>
                                    <th class="cart-summary-table text-left">{{ $t('general.items') }}</th>
                                    <th class="cart-summary-table text-left">{{ $t('general.quantity') }}</th>
                                    <th class="cart-summary-table text-right">{{ $t('general.price') }}</th>
                                    <th class="cart-summary-table text-right">{{ $t('general.discount') }}</th>
                                    <th class="cart-summary-table text-right">{{ $t('general.total') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="cartItem in Variants">
                                    <template v-if="cartItem.product_variant_id && cartItem.product_id">
                                    <td class="cart-summary-table text-left">
                                        {{ selectProducts.find(el=>el.id == cartItem.product_id)? $i18n.locale == "ar" ?selectProducts.find(el=>el.id == cartItem.product_id).title:selectProducts.find(el=>el.id == cartItem.product_id).title_e:'---' }}
                                        <br>
                                        <span v-if="checkVariant(cartItem.product_id,cartItem.product_variant_id)">( {{ checkVariant(cartItem.product_id,cartItem.product_variant_id) }} )</span>
                                    </td>
                                    <td class="cart-summary-table"> {{ cartItem.qty }} </td>
                                    <td class="text-right cart-summary-table">{{ cartItem.price }} {{ order.currency ? $i18n.locale == 'ar'? order.currency.symbol:order.currency.symbol_e : 'KU' }}</td>
                                    <td class="text-right cart-summary-table" v-if="cartItem.discount >0">
                                        {{ cartItem.discount }}%
                                    </td>
                                    <td class="cart-summary-table" v-else></td>
                                    <td class="text-right cart-summary-table" v-if="cartItem.discount >0">
                                        {{ (cartItem.qty * cartItem.price) - ((cartItem.qty * cartItem.price * cartItem.discount) / 100)  }}
                                        {{ order.currency ? $i18n.locale == 'ar'? order.currency.symbol:order.currency.symbol_e : 'KU' }}
                                    </td>
                                    <td class="text-right cart-summary-table" v-else>
                                        {{ cartItem.qty * cartItem.price }}
                                        {{ order.currency ? $i18n.locale == 'ar'? order.currency.symbol:order.currency.symbol_e : 'KU' }}
                                    </td>
                                    </template>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr v-if="sub_discount > 0">
                                    <td class="cart-summary-table text-left">{{ $t('general.discount') }}</td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="text-right cart-summary-table ">{{ sub_discount }} {{ order.currency ? $i18n.locale == 'ar'? order.currency.symbol:order.currency.symbol_e : 'KU' }}</td>
                                </tr>
                                <tr>
                                    <td class="cart-summary-table font-weight-bold text-left">{{$t('general.sub_total')}}</td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="text-right cart-summary-table">
                                        {{ sub_total }}  {{ order.currency ? $i18n.locale == 'ar'? order.currency.symbol:order.currency.symbol_e : 'KU' }}
                                    </td>
                                </tr>
                                <tr v-if="taxProducts > 0">
                                    <td class="cart-summary-table text-left">{{ $t('general.tax') }}</td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="text-right cart-summary-table ">
                                        {{ taxProducts }}  {{ order.currency ? $i18n.locale == 'ar'? order.currency.symbol:order.currency.symbol_e : 'KU' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="cart-summary-table font-weight-bold text-left">{{$t('general.total')}}
                                    </td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="text-right cart-summary-table ">
                                        {{totalAmount}} {{ order.currency ? $i18n.locale == 'ar'? order.currency.symbol:order.currency.symbol_e : 'KU' }}
                                    </td>
                                </tr>
                                <tr v-for="(paymentTypes, index) in paymentListData"
                                    :key="`paymentListInvoiceData-${index}`">
                                    <td class="cart-summary-table text-left">{{ paymentTypes.paymentName }}</td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="text-right cart-summary-table">
                                        {{ paymentTypes.paid }}  {{ order.currency ? $i18n.locale == 'ar'? order.currency.symbol:order.currency.symbol_e : 'KU' }}
                                    </td>
                                </tr>
                                <tr v-if="exchangeValue>0">
                                    <td class="cart-summary-table text-left">{{ $t('general.exchange') }}</td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="text-right cart-summary-table ">
                                        {{ exchangeValue }}
                                        {{ order.currency ? $i18n.locale == 'ar'? order.currency.symbol:order.currency.symbol_e : 'KU' }}
                                    </td>
                                </tr>
                                <tr v-if="!status.printReceiptView">
                                    <td class="cart-summary-table font-weight-bold text-left" v-if="due >= 0">
                                        {{ $t('general.due') }}
                                    </td>
                                    <td class="cart-summary-table font-weight-bold text-left" v-else>
                                        {{$t('general.change')}}
                                    </td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="cart-summary-table"></td>
                                    <td class="text-right cart-summary-table">
                                        {{due}}  {{ order.currency ? $i18n.locale == 'ar'? order.currency.symbol:order.currency.symbol_e : 'KU' }}
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <a href="#" v-if="!status.isPaymentDone" v-print="'#printCustom'"
                   class="px-2 btn-before-receipt">
                    <i class="fe-printer"></i>
                    {{ $t('general.print_receipt') }}
                </a>
            </div>
            <div class="col-12 col-md-6">
                <div v-if="!status.isPaymentDone">
                    <div class="row mx-0 mb-4" id="js-payment-title">
                        <div class="col-6"><h5>{{ $t('general.total') }}</h5></div>
                        <div class="col-6 text-right payment-amount"><h5> {{ `-${totalAmount}` }} </h5></div>
                    </div>
                    <div>
                        <div class="payment-description" id="js-payment-description">
                            <div v-if="salesOrReceivingType !== 'internal-transfer'" class="row mx-0 mb-2"
                                 v-for="(paymentTypes, index) in paymentListData">
                                <div class="col-4 col-sm-6 col-md-5 col-lg-4 col-xl-4 mt-auto">
                                    <label>{{ paymentTypes.paymentName }}</label>
                                </div>
                                <div class="col-4 col-sm-5 col-md-6 col-lg-7 col-xl-7 pl-0">
                                    <label>{{ paymentTypes.paid }}</label>
                                </div>
                                <div class="col-1 mt-auto p-0 text-right">
                                    <a href="#"
                                       @click.prevent="clearPayment(index)">
                                        <i class="text-danger fa fa-trash"></i>
                                    </a>
                                </div>
                            </div>

                            <div v-if="salesOrReceivingType !== 'internal-transfer'" class="form-group row ml-0">
                                <label class="col-4 col-sm-6 col-md-5 col-lg-4 col-form-label">{{ paymentName }}</label>
                                <div class="col-4 col-sm-6 col-md-7 col-lg-8 col-xl-8 pl-0">
                                    <input type="number" step="any"  class="form-control" v-model="paymentValue" @input="getPaymentAmount">
                                </div>
                            </div>
                            <div  class="form-group row ml-0">
                                <label class="col-4 col-sm-6 col-md-5 col-lg-4 col-form-label">
                                    {{ $t('general.note') }}
                                </label>
                                <div class="col-4 col-sm-6 col-md-7 col-lg-8 col-xl-8 pl-0">
                                    <textarea class="form-control"
                                              v-model="salesNote">
                                    </textarea>
                                </div>
                            </div>
                        </div>

                        <div class="payment-action">
                            <div class="row mx-0 mt-2 no-gutters">
                                <div class="col-12">
                                    <hr class="custom-margin">
                                    <div class="payment-group" style="overflow: hidden;">
                                        <span v-for="(paymentTypes, index) in sellMethods">
                                           <button
                                               v-if="(salesOrReceivingType === 'customer' || salesOrReceivingType === 'supplier')"
                                               :id="paymentTypes.id"
                                               :class="[paymentTypes.id == paymentId?'activePayment':'','btn app-color mr-1 mb-1']"
                                               @click.prevent="setPayment(paymentTypes)">
                                                    {{ paymentTypes.name }}
                                            </button>
                                        </span>
                                    </div>
                                    <hr class="custom-margin">
                                    <span
                                        v-if="(salesOrReceivingType !== 'internal-transfer') && due == 0">
                                        <button v-if="!isLoader" class="btn btn-block app-color payment-button"
                                                @click.prevent="storeInvoice">
                                                {{ $t('general.done_payment') }}
                                        </button>
                                        <b-button
                                            class="btn btn-block app-color payment-button"
                                            disabled
                                            v-else
                                        >
                                            <b-spinner small></b-spinner>
                                            <span class="sr-only">{{ $t("login.Loading") }}...</span>
                                        </b-button>
                                    </span>
                                    <span v-else>
                                        <button class="btn btn-block app-color payment-button"
                                                @click.prevent="storeInvoiceCart()">
                                                {{ $t('general.add_payment') }}
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mx-0" v-else>
                    <div class="col-12 text-center">
                        <h4>{{ $t('general.payment_received') }}</h4>
                    </div>
                    <a href="#" v-print="'#printCustom'" class="printReceiptButton">
                        <i class="fe-printer"></i>
                        {{ $t('general.print_receipt') }}
                    </a>
                </div>


            </div>
        </div>
    </b-modal>
</template>

<script>
import {formatDateOnly} from "../../../../helper/startDate";
import adminApi from "../../../../api/adminAxios";
import Swal from "sweetalert2";
import loader from "../../../../components/general/loader";

export default {
    components: {
        loader
    },
    name: "PosOrderInvoiceReturn",
    props: [
        'order'
    ],
    data() {
        return {
            isLoader:false,
            status: {
                isPaymentDone: false,
                printReceiptView: false,
            },
            company_id:null,
            company:'',
            salesOrReceivingType:'customer',
            salesNote:'',
            paymentName: '',
            paymentId: '',
            paymentValue: 0,
            paidAmount: 0,
            exchangeValue:0,
            due:0,
            totalAmount:0,
            sellMethods: [],
            serial: '',
            paymentListData: [],
            payments: [],
            printObj: {
                id: "printCustom",
            },
        };
    },
    mounted() {
        this.company_id = this.$store.getters["auth/company_id"];
        this.getCompanyData();
        this.getPaymentMethod();
    },
    methods: {
        getCompanyData(){
            let company = this.$store.getters["auth/companies"]
            this.company = company.find((el) => el.id == this.company_id);
        },
        getCreateBy(){
            let created_by = this.$store.state.auth.type == 'admin' ?
                this.$store.state.auth.partner ? this.$store.state.auth.partner.name:this.$store.state.auth.partner.name_e:
                this.$store.state.auth.user ? this.$store.state.auth.user.name:this.$store.state.auth.user.name_e;
            return created_by;
        },
        formatDate(value) {
            return formatDateOnly(value);
        },
        checkVariant(product_id,variant_id) {
            let product = this.selectProducts.find(el => el.id == product_id);
            let title = product ? product.product_variant.find(elm => elm.id == variant_id).variant_title : '';
            return title ;
        },
        getPaymentMethod() {
            this.isLoader = true;
            adminApi.get(`/payment-methods`)
                .then((res) => {
                    let l = res.data.data;
                    this.sellMethods = l;
                    this.handelSellMethodData();
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
        handelSellMethodData(){
            if (this.sellMethods.length > 0)
            {
                let sellMethod = this.sellMethods.find(el => el.is_default == 1) ?this.sellMethods.find(el => el.is_default == 1): this.sellMethods[0];
                this.paymentName = this.$i18n.locale == 'ar' ? sellMethod.name:sellMethod.name_e;
                this.paymentId = sellMethod.id;
                this.paymentValue = (this.totalAmount - this.paidAmount) * -1;
            }

        },
        setPayment(paymentMethod) {
            this.paymentName = this.$i18n.locale == 'ar' ? paymentMethod.name:paymentMethod.name_e;
            this.paymentId = paymentMethod.id;
            this.paymentValue = (this.totalAmount - this.paidAmount) * -1;
            this.due = 0;
        },

        getPaymentAmount() {
            this.calculateBalance();
            this.due = this.totalAmount - (parseFloat(this.paidAmount) + parseFloat(this.paymentValue * -1));

        },
        calculateBalance() {
            let paidAmount = 0;
            this.paymentListData.forEach(function (payment) {
                if (payment.paid) {
                    paidAmount += parseFloat(payment.paid);
                }
            });
            this.paidAmount = paidAmount;
            if (this.paidAmount > this.totalAmount)
            {
                this.exchangeValue = this.paidAmount - this.totalAmount;
            }
        },
        storeInvoiceCart() {
            let paymentMethod = this.sellMethods.find(el => el.id == this.paymentId);
            this.paymentListData.push({
                paid: this.paymentValue,
                paymentID: this.paymentId,
                paymentName: this.paymentName,
                isActive:1
            });
            this.calculateBalance();
            this.setPayment(paymentMethod);
        },
        clearPayment(index) {
            this.paymentListData.splice(index, 1);
            this.calculateBalance();
            this.paymentValue = (this.totalAmount - this.paidAmount) * -1;
            this.due = this.totalAmount - (parseFloat(this.paidAmount) + parseFloat(this.paymentValue * -1));
        },

        /**
         *  reset Modal (create)
         */
        resetModalHidden() {
            if (this.status.isPaymentDone == true)
            {
                this.$emit('clear_order');
                this.serial = ''
            }
            this.status = {
                isPaymentDone: false,
                printReceiptView: false,
            };
            this.company_id = null;
            this.company = '';
            this.salesOrReceivingType = 'customer';
            this.salesNote = '';
            this.salesNote = '';
            this.paymentName = '';
            this.paymentId = '';
            this.paymentValue = 0;
            this.paidAmount = 0;
            this.totalAmount = 0;
            this.exchangeValue = 0;
            this.due = 0;
            this.paymentListData = [];
            this.payments = [];

            this.$bvModal.hide(`pay-create`);
        },
        /**
         *  hidden Modal (create)
         */
        resetModal() {
            this.serial = this.orderChoose.prefix;
            setTimeout(() => {
                this.totalAmount = this.order.tax_type == "excluded" ? this.total : parseFloat(this.total) + parseFloat(this.taxProducts);
                this.handelSellMethodData();
            }, 200);
        },
        storeInvoice() {
            this.isLoader = true;
            let payments = [];
            this.paymentListData.forEach((el) => {
                payments.push({
                    paid:el.paid,
                    payment_method_id:el.paymentID,
                    exchange:0,
                    is_active:1,
                });
            });
            payments.push({
                paid:this.paymentValue,
                payment_method_id:this.paymentId,
                exchange:this.exchangeValue,
                is_active:this.exchangeValue > 0 ? 0 : 1,
            });
            let allQty = 0;
            this.Variants.forEach(e => allQty += e.qty);
            this.$store.commit('orderReturn/plusQtyConvert');
            let dataCreate = {
                sales_note:this.salesNote,
                all_discount:this.order.allDiscount,
                customer_id:this.order.customer_id,
                branch_id:this.order.branch_id,
                created_by:this.getCreateBy(),
                document_id:4,
                products:this.order.details,
                payments,
                tax_type: this.order.tax_type,
                returned_invoice: this.orderChoose.prefix,
                order_return_type: this.allNumQty == allQty ? 'fully': 'partial',
                order_id: this.orderChoose.id
            }
            adminApi
                .post(`/point-of-sale/oreders/${this.order.type != 'purchase'?'return-order':'create-return-purchases-products'}`,dataCreate )
                .then((res) => {
                    this.is_disabled = true;
                    this.status.isPaymentDone = true;
                    this.status.printReceiptView = true;
                    setTimeout(() => {
                        Swal.fire({
                            icon: "success",
                            text: `${this.$t("general.Addedsuccessfully")}`,
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    }, 500);
                })
                .catch((err) => {})
                .finally(() => {
                    this.isLoader = false;
                });

        }
    },
    computed: {
        selectProducts(){
            return this.$store.getters['orderReturn/allSelectProducts'];
        },
        Variants(){
            return this.$store.getters['orderReturn/productDetails'];
        },
        sub_total(){
            return this.$store.getters['orderReturn/sub_total'];
        },
        all_discount(){
            return this.$store.getters['orderReturn/all_discount'];
        },
        sub_discount(){
            return this.$store.getters['orderReturn/sub_discount'];
        },
        total(){
            return this.$store.getters['orderReturn/total'];
        },
        taxProducts(){
            return this.$store.getters['orderReturn/tax'];
        },
        allNumQty(){
            return this.$store.getters['orderReturn/allNumQty'];
        },
        orderChoose(){
            return this.$store.getters['orderReturn/order'];
        },
    },
}
</script>

<style>
.invoice-table {
    display: block !important;
    width: 100% !important;
}
.invoice-table .table {
    border-collapse: collapse !important;
    width: 100% !important;
    padding: 5px !important;
    font-weight:500 !important;
}
.product-card-font {
    font-size: 0.75rem !important;
}
.table {
    width: 100% !important;
    margin-bottom: 1rem !important;
    color: #212529 !important;
}
.cart-border-right {
    border-right: 1px solid #dee2e6 !important;
}
.invoice-table .table tbody tr td:first-child {
    padding-left: 0 !important;
}
.invoice-table .table tbody tr td {
    border-top: 1px solid #dee2e6;
}
td.cart-summary-table {
    padding: 5px 5px !important;
}
.btn:not(:disabled):not(.disabled) {
    cursor: pointer;
}
.modal-content-row {
    min-height: 68vh;
}
.btn.app-color {
    color: #ffffff;
    background: rgba(59, 175, 218, 0.7490196078) ;
    border-color: rgba(59, 175, 218, 0.7490196078);
    -webkit-transition: all 0.2s ease-in-out;
    transition: all 0.2s ease-in-out;
}
.btn.app-color:hover {
    background: #0a8fc0 !important;
    border-color: #0a8fc0 !important;
}
.activePayment {
    background: #0a8fc0 !important;
    border-color: #0a8fc0 !important;
}
.cart-border-right .btn-before-receipt {
    padding: 8px 0;
    margin: 0 0 0 auto;
    border: 1px solid #0a8fc0;
    position: absolute;
    bottom: 0;
    right: 10px;
    left: 0;
    color: #0a8fc0;
    background-color: #dff0fe !important;
}
.cart-border-right .btn-before-receipt:hover {
    border: 1px solid #0a8fc0;
    color: #FFFFFF;
    background-color: #0a8fc0 !important;
}
.printReceiptButton {
    padding: 13px 0;
    margin: 37px 0;
    text-align: center !important;
    width: 100%;
    border: 1px solid #0a8fc0;
    background-color: #dff0fe !important;
    color: #0a8fc0;
}
.printReceiptButton:hover {
    border: 1px solid #0a8fc0;
    color: #FFFFFF;
    background-color: #0a8fc0 !important;
}
.horizontal-scroll {
    overflow-x: auto;
    height: 92%;
    max-height: 70vh;
    background-color: #dff0fe;
}
@media print {
    .do-not-print {
        display: none;
    }
    .arrow-sort {
        display: none;
    }
    th{
        color: black;
        text-align: center !important;
    }
    th.cart-summary-table{
        color: black;
        text-align: center !important;
    }
    td.cart-summary-table {
        padding: 5px 5px !important;
        text-align: center !important;
    }
    .data-header-print {
        width: 100%;
        display: inline-block;
    }
    body {
        background-color: #FFFFFF !important;
    }
    .horizontal-scroll {
        background-color: #FFFFFF !important;
    }
    .modal-content {
        background-color: #FFFFFF !important;
    }
}
</style>
