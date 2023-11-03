<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import DocumentWithMoney from "../../../../components/document/withMoney/document-with-money";

import permissionGuard from "../../../../helper/permission";

export default {
  page: {
    title: "Payment voucher",
    meta: [{ name: "Payment voucher", content: "Payment voucher" }],
  },
  components: {
    Layout,
    PageHeader,
    DocumentWithMoney,
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Payment Voucher RP", "all paymentVoucher RP");
    });
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <DocumentWithMoney :document_id="39" />
  </Layout>
</template>

<style lang="scss">
</style>
