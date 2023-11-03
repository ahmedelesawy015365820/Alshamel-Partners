<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import InvoiceCom from "../../../components/create/invoiceCom";
import permissionGuard from "../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Invoice",
        meta: [{ name: "description", content: "Invoice" }],
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Invoice Realestate", "all invoice RealState");
    });

    },
    components: {
        Layout,
        PageHeader,
        InvoiceCom
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <InvoiceCom />
    </Layout>
</template>
