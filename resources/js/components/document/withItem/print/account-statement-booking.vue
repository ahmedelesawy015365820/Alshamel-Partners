
<template>
    <div>

        <b-modal
            :id="id"
            :title="$t('general.accountStatement')"
            title-class="font-18"
            dialog-class="modal-full-width"
            body-class="p-4 "
            :hide-footer="true"
            @show="resetModal"
            @hidden="resetModalHidden"
        >
            <div class="page-content container">
                <loader size="large" v-if="isLoader" />
                <div class="page-header text-blue-d2">
                    <h1 class="page-title text-secondary-d1">
                        {{$t('general.InvoiceNO')}} : {{dataPrint.prefix}}
                    </h1>

                    <div class="page-tools">
                        <div class="action-buttons">
                            <a v-print="'#printSettlement'" class="btn bg-white btn-light mx-1px text-95" href="#" data-title="Print">
                                <i class="mr-1 fa fa-print text-primary-m1 text-120 w-2"></i>
                                {{$t('general.print')}}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="container px-0" id="printSettlement">
                    <div class="row mt-4">
                        <div class="col-12 col-lg-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center text-150">
                                        <span class="text-default-d3 fw-bold text-uppercase">{{$store.getters["auth/partner"].name_e}}</span>
                                        <img src="/images/statement.png" alt="Company logo" style="width: 7%;" />
                                        <span class="text-default-d3 fw-bold">{{$store.getters["auth/partner"].name}}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="text-center">
                                        <h3>{{$t('general.accountStatement')}}</h3>
                                    </div>
                                </div>
                            </div>
                            <!-- .row -->

    <!--                        <hr class="row brc-default-l1 mx-n1 mb-4" />-->

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="my-2"><i class="fa fa-circle text-blue-m2 text-xs mr-1"></i> <span class="text-600 text-90">{{$t('general.InvoiceNO')}}:</span> {{dataPrint.prefix}}</div>

                                    <div class="my-2">
                                        <i class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                        <span class="text-600 text-90">{{$t('general.CompanyName')}}:</span>
                                        {{dataPrint.customer_type == 0? dataPrint.company ? $i18n.locale == "ar" ? dataPrint.company.name : dataPrint.company.name_e : '' :''}}
                                    </div>

                                    <div class="my-2">
                                        <i class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                        <span class="text-600 text-90">{{$t('general.GuestName')}}:</span>
                                        {{ dataPrint.customer ? $i18n.locale == "ar" ? dataPrint.customer.name : dataPrint.customer.name_e : ''}}
                                    </div>
                                    <div class="my-2">
                                        <i class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                        <span class="text-600 text-90">{{$t('general.Address')}}:</span>
                                        {{dataPrint.customer ? dataPrint.customer.address : ''}}
                                    </div>
                                </div>
                                <!-- /.col -->

                                <div class="text-95 col-sm-6 align-self-start d-sm-flex justify-content-end">
                                    <hr class="d-sm-none" />
                                    <div class="text-grey-m2">
                                        <div class="my-2">
                                            <i class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                            <span class="text-600 text-90">{{$t('general.LevelBooking')}}:</span>
                                            {{level ? $i18n.locale == "ar" ? level.name : level.name_e : ''}}
                                        </div>

                                        <div class="my-2">
                                            <i class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                            <span class="text-600 text-90">{{$t('general.NoOfPersons')}}:</span>
                                            {{dataPrint.attendans_num}}
                                        </div>

                                        <div class="my-2">
                                            <i class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                            <span class="text-600 text-90">{{$t('general.Arival')}}:</span>
                                            {{dataPrint.document_number ? dataPrint.document_number.date : ''}}
                                        </div>
                                        <div class="my-2">
                                            <i class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                            <span class="text-600 text-90">{{$t('general.Departure')}}:</span>
                                            {{dataPrint.date}}
                                        </div>
                                        <div class="my-2">
                                            <i class="fa fa-circle text-blue-m2 text-xs mr-1"></i>
                                            <span class="text-600 text-90">{{$t('general.TotalNights')}}:</span>
                                            {{total_nights}}
                                        </div>
                                    </div>
                                </div>
                                <!-- /.col -->
                            </div>

                            <div class="mt-4">
                                <div class="row text-600 text-white bgc-default-tp1 py-25">
                                    <div class="d-none d-sm-block col-2">{{$t('general.Date')}}</div>
                                    <div class="d-none d-sm-block col-1">{{$t('general.time')}}</div>
                                    <div class="col-8 col-sm-3">{{$t('general.DescriptionBooking')}}</div>
                                    <div class="d-none d-sm-block col-4 col-sm-2">{{$t('general.Receipt')}}</div>
                                    <div class="d-none d-sm-block col-sm-2">{{$t('general.charges')}}</div>
                                    <div class="col-2">{{$t('general.payments')}}</div>
                                </div>

                                <div class="text-95 text-secondary-d3">
                                    <div class="row mb-2 mb-sm-0 py-25" v-for="(data, index) in details">
                                        <div class="d-none d-sm-block col-2">{{data.date}}</div>
                                        <div class="d-none d-sm-block col-1">{{data.time}}</div>
                                        <div class="col-8 col-sm-3">{{data.description}}</div>
                                        <div class="d-none d-sm-block col-2 text-95">{{data.receipt}}</div>
                                        <div class="d-none d-sm-block col-2 text-95">{{data.charges}} KD</div>
                                        <div class="col-2 text-secondary-d2">{{data.payments}} KD</div>
                                    </div>
                                </div>

                                <div class="row border-b-2 brc-default-l2"></div>

                                <div class="row mt-3">
                                    <div class="col-12 col-sm-7 text-grey-d2 text-95 mt-2 mt-lg-0">
                                        {{$t('general.Extra_note')}}
                                    </div>

                                    <div class="col-12 col-sm-5 text-grey text-90 order-first order-sm-last">
                                        <div class="row my-2">
                                            <div class="col-7 text-right">
                                                {{$t('general.Total')}}
                                            </div>
                                            <div class="col-5">
                                                <span class="text-150 text-success-d3 opacity-2">{{total}} KD</span>
                                            </div>
                                        </div>

                                        <div class="row my-2">
                                            <div class="col-7 text-right">
                                                {{$t('general.paid')}}
                                            </div>
                                            <div class="col-5">
                                                <span class="text-150 text-success-d3 opacity-2">{{paid}} KD</span>
                                            </div>
                                        </div>

                                        <div class="row my-2">
                                            <div class="col-7 text-right">
                                                {{$t('general.discount')}}
                                            </div>
                                            <div class="col-5">
                                                <span class="text-150 text-success-d3 opacity-2">{{dataPrint.invoice_discount}} KD</span>
                                            </div>
                                        </div>

                                        <div class="row my-2">
                                            <div class="col-7 text-right">
                                                {{$t('general.remaining')}}
                                            </div>
                                            <div class="col-5">
                                                <span class="text-150 text-success-d3 opacity-2">{{parseFloat(total - paid - dataPrint.invoice_discount).toFixed(3)}} KD</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr />

                                <div class="row">
                                    <div class="col-4 mb-5 text-center">
                                        <span class="text-secondary-d1 text-105">{{$t('general.GuestSignature')}}</span>
                                    </div>
                                    <div class="col-4 mb-5 text-center">
                                        <span class="text-secondary-d1 text-105">{{$t('general.ReceptionistSignature')}}</span>
                                    </div>
                                    <div class="col-4 mb-5 text-center">
                                        <span class="text-secondary-d1 text-105">{{$t('general.ManagerSignature')}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
import {formatDateOnly} from "../../../../helper/startDate";
import Swal from "sweetalert2";
import loader from "../../../general/loader";
import ErrorMessage from "../../../widgets/errorMessage";
import adminApi from "../../../../api/adminAxios";

export default {
    name: "account-statement-booking",
    props: {
        id: {default: "create",},
        document_row_id: { default: '',},
    },
    components: {
        ErrorMessage,
        loader,
    },
    data(){
        return {
            isLoader:false,
            dataPrint:'',
            level:'',
            total_nights:0,
            total:0,
            paid:0,
            details:[],
            printObj: {
                id: "printSettlement",
            }
        }
    },
    methods: {
        formatDate(value) {
            return formatDateOnly(value);
        },
        async resetModal() {
            this.dataPrint='';
            this.level='';
            this.total_nights=0;
            this.total=0;
            this.paid=0;
            this.details=[];
            await this.getDataPrint();
            this.level = this.dataPrint.document_header_details.find((row) => null != row.unit_id)?this.dataPrint.document_header_details.find((row) => null != row.unit_id).unit:'';
            this.dataPrint.document_header_details.forEach((e,index) => {
                if (e.unit_id != null )
                {
                    this.total_nights += 1;
                }
                this.details.push({
                    date:e.date_from,
                    time:e.check_in_time,
                    description: parseInt(e.is_stripe) == 0 ? this.$t('general.accommodation') : this.$i18n.locale == "ar" ? e.item.name:e.item.name_e,
                    receipt:e.prefix_related,
                    charges:parseFloat(e.total).toFixed(3),
                    payments:0,
                });
            });

            if(this.dataPrint.break_settlement && this.dataPrint.break_settlement.break_voucher_headers.length > 0)
            {
                this.dataPrint.break_settlement.break_voucher_headers.forEach((e,index) => {
                    this.details.push({
                        date:e.date,
                        time: this.currentTime(e.created_at),
                        description: this.$i18n.locale == "ar" ? e.document.name : e.document.name_e,
                        receipt:e.prefix,
                        charges:parseInt(e.document.attributes.customer) == 1 ? parseFloat(e.amount).toFixed(3) : 0,
                        payments:parseInt(e.document.attributes.customer) == -1 ? parseFloat(e.amount).toFixed(3) : 0,
                    });
                });
            };
            this.details.sort((a, b) => {
                const dateA = new Date(a.date);
                const dateB = new Date(b.date);
                return dateA - dateB;
            });
            let total = 0;
            let paid = 0;
            this.details.forEach((e)=>{
                total += parseFloat(e.charges);
                paid += parseFloat(e.payments);
            });
            this.total = total.toFixed(3);
            this.paid = paid.toFixed(3);
        },
        resetModalHidden() {
            this.dataPrint='';
            this.level='';
            this.total_nights=0;
            this.total=0;
            this.paid=0;
            this.details=[];
            this.$bvModal.hide(this.id);
        },
        currentTime(date)
        {
            const currentTime = new Date(date);
            const hours = currentTime.getHours();
            const minutes = currentTime.getMinutes();

            const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
            return formattedTime;
        },

       async getDataPrint()
        {
            this.isLoader = true;
            await adminApi
                .get(`/document-headers/check-out-print/${this.document_row_id}`)
                .then((res) => {
                    let l = res.data;
                    this.dataPrint = l;
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
        }

    }
}
</script>

<style scoped>
.text-secondary-d1 {
    color: #728299!important;
}
.page-header {
    margin: 0 0 1rem;
    padding-bottom: 1rem;
    padding-top: .5rem;
    border-bottom: 3px dotted #000000;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-pack: justify;
    justify-content: space-between;
    -ms-flex-align: center;
    align-items: center;
}
.page-title {
    padding: 0;
    margin: 0;
    font-size: 1.75rem;
    font-weight: 300;
}
.brc-default-l1 {
    border-color: #dce9f0!important;
}

.ml-n1, .mx-n1 {
    margin-left: -.25rem!important;
}
.mr-n1, .mx-n1 {
    margin-right: -.25rem!important;
}
.mb-4, .my-4 {
    margin-bottom: 1.5rem!important;
}

hr {
    margin-top: 1rem;
    margin-bottom: 1rem;
    border: 0;
    border-top: 1px solid rgba(0,0,0,.1);
}

.text-grey-m2 {
    color: #888a8d!important;
}

.text-success-m2 {
    color: #86bd68!important;
}

.font-bolder, .text-600 {
    font-weight: 600!important;
}

.text-110 {
    font-size: 110%!important;
}
.text-blue {
    color: #478fcc!important;
}
.pb-25, .py-25 {
    padding-bottom: .75rem!important;
}

.pt-25, .py-25 {
    padding-top: .75rem!important;
}
.bgc-default-tp1 {
    background-color: rgba(121,169,197,.92)!important;
}
.bgc-default-l4, .bgc-h-default-l4:hover {
    background-color: #f3f8fa!important;
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
    font-size: 120%!important;
}
.text-primary-m1 {
    color: #4087d4!important;
}

.text-danger-m1 {
    color: #dd4949!important;
}
.text-blue-m2 {
    color: #68a3d5!important;
}
.text-150 {
    font-size: 150%!important;
}
.text-60 {
    font-size: 60%!important;
}
.text-grey-m1 {
    color: #7b7d81!important;
}
.align-bottom {
    vertical-align: bottom!important;
}
.fw-bold {
    font-weight: 700!important;
}
small{
    font-size: 80% !important;
}


</style>
