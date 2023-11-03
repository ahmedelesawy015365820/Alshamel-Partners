<template>
    <div>
        <template v-if="document && document.document_detail_type == 'board_rent'">
            <CreateOrUpdatePanel :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :other_data="other_data" :id="'create'" @created="getData()" />
        </template>
        <template v-if="document && document.document_detail_type == 'booking' && document_id == 34">
            <CreateOrUpdateCheckout :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :other_data="other_data" :id="'create'" @created="getData()" />
        </template>
        <template v-if="document && document.document_detail_type == 'booking' && document_id == 40">
            <CreateOrUpdateMaintenance :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :other_data="other_data" :id="'create'" @created="getData()" />
        </template>
        <template v-if="document && document.document_detail_type == 'booking' && document_id != 34 && document_id != 40">
            <CreateOrUpdateBooking :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :other_data="other_data" :id="'create'" @created="getData()" />
        </template>
        <template v-if="document && document.document_detail_type == 'normal'">
            <CreateOrUpdateItems :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :document="document" :document_id="document_id" :other_data="other_data" :id="'create'" @created="getData()" />
        </template>
    </div>
</template>

<script>
import CreateOrUpdatePanel from "./components/create-or-update-panel";
import CreateOrUpdateBooking from "./components/create-or-update-booking";
import CreateOrUpdateItems from "./components/create-or-update-items";
import CreateOrUpdateCheckout from "./components/create-or-update-checkout";
import CreateOrUpdateMaintenance from "./components/create-or-update-maintenance";
import ErrorMessage from "../../widgets/errorMessage";
import loader from "../../general/loader";
import DatePicker from "vue2-datepicker";
import Multiselect from "vue-multiselect";
import translation from "../../../helper/mixin/translation-mixin";

export default {
    name: "documentHeaderDetail",
    props: {
        document_id: {
            default: null,
        },
        document: {
            default: null,
        },
        other_data: {
            default: null,
        },
    },
    mixins: [translation],
    components: {
        CreateOrUpdatePanel,
        CreateOrUpdateBooking,
        CreateOrUpdateItems,
        CreateOrUpdateCheckout,
        CreateOrUpdateMaintenance
    },
    methods: {
        getData(){
            this.$emit("created");
        }
    }
}
</script>
