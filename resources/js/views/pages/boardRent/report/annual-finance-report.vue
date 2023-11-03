<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import AnnualFinanceReport from "../../../../components/document/withItem/report/annual-finance-report";
import permissionGuard from "../../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Annual Financial Report",
    meta: [
      { name: "Annual Financial Report", content: "Annual Financial Report" },
    ],
  },
  components: {
    Layout,
    PageHeader,
    AnnualFinanceReport,
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(
        vm,
        "Annual Finance Report",
        "all Store"
      );
    });
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <AnnualFinanceReport :document_detail_type="'board_rent'" />
  </Layout>
</template>




