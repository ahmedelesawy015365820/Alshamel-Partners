<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import FinanceReport from "../../../../components/document/withItem/report/finance-report";
import permissionGuard from "../../../../helper/permission";


/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Finance Report",
        meta: [{name: "Finance Report", content: 'Finance Report'}],
    },
    components: {
        Layout,
        PageHeader,
        FinanceReport
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Finance Report BoardRent", "all Store");
    });

    },
};
</script>

<template>
    <Layout>
        <PageHeader/>
        <FinanceReport :document_detail_type="'board_rent'" />
    </Layout>
</template>




