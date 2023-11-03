<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import StatementOfAccount from "../../../../components/document/withItem/report/customer-statement-of-account";
import permissionGuard from "../../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Customer Statement Of Account Report",
        meta: [{name: "Customer Statement Of Account Report", content: 'Customer Statement Of Account Report'}],
    },
    components: {
        Layout,
        PageHeader,
        StatementOfAccount
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Customer Statement Of Account", "all Store");
    });


    },
};
</script>

<template>
    <Layout>
        <PageHeader/>
        <StatementOfAccount :document_detail_type="'board_rent'" />
    </Layout>
</template>




