<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import DocumentWithMoney from "../../../../components/document/withMoney/document-with-money";
import permissionGuard from "../../../../helper/permission";

export default {
    page: {
        title: "Credit Note",
        meta: [{name: "Credit Note", content: 'Credit Note'}],
    },
    components: {
        Layout,
        PageHeader,
        DocumentWithMoney
    },
    beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Credit Note RP", "all creditNote RP");
    });


    },
}
</script>

<template>
    <Layout>
        <PageHeader/>
        <DocumentWithMoney :document_id="6"/>
    </Layout>
</template>

<style lang="scss">
</style>
