<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import DocumentWithMoney from "../../../../components/document/withMoney/document-with-money";
import permissionGuard from "../../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Debit Note",
        meta: [{name: "Debit Note", content: 'Debit Note'}],
    },
    components: {
        Layout,
        PageHeader,
        DocumentWithMoney
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Debit Note RP", "all creditNote RP");
    });
    },
};
</script>

<template>
    <Layout>
        <PageHeader/>
        <DocumentWithMoney :document_id="7"/>
    </Layout>
</template>

<style>
</style>



