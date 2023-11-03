<template>
    <div>
        <bank :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'bank_create_form_general'" @created="$emit('created')" />
        <bankAccount :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'bank-account-create-general'" @created="$emit('created')" />
        <branch :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'branch-create-general'" @created="$emit('created')" />
        <employee :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="$emit('created')" />
        <customerGeneral :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="$emit('created')" />
        <country :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'country-create-general'" @created="$emit('created')" />
        <city :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'city-create-general'" @created="$emit('created')" />
        <propertyTree :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" @created="$emit('created')" />
        <avenue :companyKeys="companyKeys" :defaultsKeys="defaultsKeys" :id="'avenues-create-general'" @created="$emit('created')" />
    </div>
</template>

<script>
import employee from "./general/employee.vue";
import avenue from "./general/avenue";
import bank from "./general/bank";
import bankAccount from "./general/bankAccount";
import branch from "./general/branch";
import customerGeneral from "./general/customerGeneral";
import country from "./general/country";
import city from "./general/city";
import propertyTree from "./general/property-tree"
import Layout from "../../views/layouts/main";
import PageHeader from "../general/Page-header";
import Switches from "vue-switches";
import ErrorMessage from "../widgets/errorMessage";
import loader from "../general/loader";
import Multiselect from "vue-multiselect";

export default {
    name: "general",
    components: {
        Layout,
        PageHeader,
        Switches,
        ErrorMessage,
        loader,
        Multiselect,
        employee,
        customerGeneral,
        propertyTree,
        country,
        city,
        bank,
        bankAccount,
        branch,
        avenue
    },

    props:[
        "currentNode",
        "companyKeys",
        "defaultsKeys"
    ],
    methods:{

    }
}
</script>

<style scoped>

</style>
