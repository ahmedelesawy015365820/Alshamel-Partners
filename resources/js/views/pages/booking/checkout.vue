<template>
    <Layout>
        <PageHeader />
        <DocumentWithItem :document_id="34" />
    </Layout>
</template>

<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import DocumentWithItem from "../../../components/document/withItem/documentWithItem";
import Swal from "sweetalert2";
import permissionGuard from "../../../helper/permission";

export default {
    page: {
        title: "Check Out",
        meta: [{ name: "Check Out", content: "Check Out" }],
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            return permissionGuard(vm, "CheckOut", "all Store");
        });

    },
    components: {
        Layout,
        PageHeader,
        DocumentWithItem
    },
}
</script>

<style scoped>

</style>
