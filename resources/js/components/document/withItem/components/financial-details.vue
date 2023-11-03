<template>
    <div>
        <!--  Financial Details   -->
        <b-modal
            dialog-class="modal-full-width"
            :id="`financial-details-${id}`"
            :title="$t('general.FinancialDetails')"
            title-class="font-18"
            body-class="p-4"
            :ref="`financial-${id}`"
            :hide-footer="true"
        >
            <div class="row">
                <div class="table-responsive mb-3 custom-table-theme position-relative">
                    <!--       start loader       -->
                    <loader size="large" v-if="isLoader" />
                    <!--       end loader       -->

                    <table class="table table-borderless table-hover table-centered m-0" >
                        <thead>
                        <tr>
                            <th>{{ $t("general.category") }}</th>
                            <th>{{ $t("general.governorate") }}</th>
                            <th>{{ $t("general.package") }}</th>
                            <th>{{ $t("general.from_date") }}</th>
                            <th>{{ $t("general.rent_days") }}</th>
                            <th>{{ $t("general.to_date") }}</th>
                            <th>{{ $t("general.Unit_type") }}</th>
                            <th>{{ $t("general.quantity") }}</th>
                            <th>{{ $t("general.PricePerUnit") }}</th>
                            <th>{{ $t("general.Total") }}</th>
                            <th>{{ $t("general.InvoiceDiscount") }}</th>
                            <th>{{ $t("general.NetInvoice") }}</th>
                            <th>{{ $t("general.sellMethodDiscount") }}</th>
                            <th v-if="document.attributes && document.attributes.unrealized_revenue != 0">{{ $t("general.unrelaized_revenue") }}</th>
                            <th v-if="document.attributes && document.attributes.commission != 0">{{ $t("general.externalCommission") }}</th>
                        </tr>
                        </thead>
                        <tbody v-if="data.header_details.length > 0">
                        <tr v-for="(item, index) in data.header_details"
                            :key="item.id"
                            class="body-tr-custom"
                        >
                            <td><h5 v-if="item.category_id && categories.length > 0" class="m-0 font-weight-normal">{{ $i18n.locale == 'ar' ? categories.find((x) => x.id == item.category_id).name: categories.find((x) => x.id == item.category_id).name_e }}</h5></td>
                            <td><h5 v-if="item.governorate_id && governorates.length > 0" class="m-0 font-weight-normal">{{ $i18n.locale == 'ar'? governorates.find((x) => x.id == item.governorate_id).name : governorates.find((x) => x.id == item.governorate_id).name_e }}</h5></td>
                            <td><h5 v-if="item.package_id && packages.length > 0" class="m-0 font-weight-normal">{{ $i18n.locale == 'ar' ? packages.find((x) => x.id == item.package_id).name: packages.find((x) => x.id == item.package_id).name_e}}</h5></td>
                            <td><h5 class="m-0 font-weight-normal">{{ item.date_from }}</h5></td>
                            <td><h5 class="m-0 font-weight-normal">{{ item.rent_days }}</h5></td>
                            <td><h5 class="m-0 font-weight-normal">{{ item.date_to }}</h5></td>
                            <td><h5 class="m-0 font-weight-normal">{{ item.unit_type }}</h5></td>
                            <td><h5 class="m-0 font-weight-normal">{{ item.quantity }}</h5></td>
                            <td><h5 class="m-0 font-weight-normal">{{ item.price_per_uint }}</h5></td>
                            <td><h5 class="m-0 font-weight-normal">{{ item.total }}</h5></td>
                            <td><h5 class="m-0 font-weight-normal">{{ item.invoice_discount }}</h5></td>
                            <td><h5 class="m-0 font-weight-normal">{{ item.net_invoice }}</h5></td>
                            <td><h5 class="m-0 font-weight-normal">{{ item.sell_method_discount }}</h5></td>
                            <td v-if="document.attributes && document.attributes.unrealized_revenue != 0"><h5 class="m-0 font-weight-normal">{{ item.unrealized_revenue }}</h5></td>
                            <td v-if="document.attributes && document.attributes.commission != 0"><h5 class="m-0 font-weight-normal">{{ item.external_commission }}</h5></td>
                        </tr>
                        </tbody>
                        <tbody v-else>
                        <tr>
                            <th class="text-center" colspan="15">
                                {{ $t("general.notDataFound") }}
                            </th>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </b-modal>
        <!--  /Financial Details   -->
    </div>
</template>

<script>
import loader from "../../../general/loader";

export default {
    name: "financial-details",
    components: {
        loader,
    },
    props: {
        id: {
            default:'create'
        },
        document: {
            default: null,
        },
        data: {
            default: null,
        },
        categories: {
            default: null,
        },
        governorates: {
            default: null,
        },
        packages: {
            default: null,
        },
    },
    data() {
        return {
            isLoader:false
        }
    }
}
</script>

<style scoped>
.custom-panel-quotation{
    background-color: #81afca !important;
    color: lemonchiffon;
    font-size: 16px;
    border: none;
}
.page-content {
    width: 100%;
}

.total {
    color: #343a40 !important;
    font-weight: bold;
}

.text-secondary-d1 {
    color: #728299 !important;
}

.page-header {
    margin: 0 0 1rem;
    padding-bottom: 1rem;
    padding-top: .5rem;
    border-bottom: 1px dotted #e2e2e2;
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

.face {
    display: inline-block;
    text-align: center;
    margin: 0 5px;
}

.face .face-name {
    background-color: #6dc6f5;
    padding: 0px 8px;
    font-size: 16px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 7px;
    display: block;
}
.row-danger {
    background-color:#f6a9a9 !important;
}
</style>
