<template>
    <Layout>
        <PageHeader />
        <InvoiceCom />
    </Layout>
</template>

<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import InvoiceCom from "../../../../components/create/reservationCom";
import permissionGuard from "../../../../helper/permission";

export default {
    name: "Invoice",
    page: {
        title: "Invoice",
        meta: [{ name: "description", content: "Invoice" }],
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Invoice RP", "all creditNote RP");
    });

    },
    components: {
        Layout,
        PageHeader,
        InvoiceCom
    },
}
</script>

<style scoped>

</style>
