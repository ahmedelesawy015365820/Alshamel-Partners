<script>
import {formatDateOnly} from "../../../../helper/startDate";
/**
 * Advanced Table component
 */
export default {
    name: "print-multi-renewal",
    props: {
        data_row: {
            default: [],
        },
    },
    data(){
        return {
            printObj: {
                id: "printInv",
            }
        }
    },
    methods: {
        formatDate(value) {
            return formatDateOnly(value);
        },
    }
};
</script>

<template>
    <div v-if="data_row.length > 0" id="printInv">
        <div class="invoice" v-for="(data, index) in data_row"
             :key="data.id">
            <div class="image-header">
                <img src="/images/sulib.png"/>
                <h1>{{$t('general.SulaibikhatClub')}}</h1>
            </div>
            <h1 v-if="data.serial" style="text-align: center;">{{$t('general.docu')}} {{data.serial?($i18n.locale == 'ar' ? data.serial.name :
                data.serial.name_e) : ""}}</h1>
            <h1 v-else style="text-align: center;">{{$t('general.ReceiptVoucher')}}</h1>
            <hr>
            <h4 style="text-align: right;">{{$t('general.Date')}} : {{formatDate(data.date)}} </h4>
            <h4 style="text-align: right;">{{$t('general.DocumentNumber')}} : {{data.document_no}}</h4>
            <hr>
            <h2 style="text-align: center;">{{$t('general.TheMember')}}</h2>
            <h4 style="text-align: right;">{{$t('general.memberName')}} : {{data.member.full_name}}</h4>
            <h4 style="text-align: right;">{{$t('general.MembershipNo')}}: {{data.member.membership_number}}</h4>
            <hr>
            <h2 style="text-align: center;">{{$t('general.theDetails')}}</h2>
            <h4 style="text-align: right;">{{$t('general.forYear')}}: {{data.year}}</h4>
            <h4 style="text-align: right;">{{$t('general.totalAmount')}}: {{data.amount}}</h4>
        </div>
    </div>
</template>

<style scoped>
h1, h2, h3, p {
    margin: 5px 0;
    color: #333;
}

hr {
    border: none;
    border-top: 1px solid #ccc;
    margin: 10px 0;
}

.invoice {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    margin-bottom: 220px;
}

button {
    margin-top: 20px;
}

.invoice .center{
    text-align: center;
}
.invoice .right{
    text-align: right;
}

.image-header{
    width: 100%;
}
.image-header img{
    width: 100px;
    display: inline-block;
    margin-bottom: -42px;
}
.image-header h1{
    width: 80%;
    display: inline-block;
    text-align: right;
}
</style>
