<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import DocumentWithMoney from "../../../../components/document/withMoney/document-with-money";

import permissionGuard from "../../../../helper/permission";

export default {
  page: {
    title: "receipt voucher",
    meta: [{ name: "receipt voucher", content: "receipt voucher" }],
  },
  components: {
    Layout,
    PageHeader,
    DocumentWithMoney,
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Receipt Voucher RP", "all receiptVoucher RP");
    });
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <DocumentWithMoney :document_id="5" />
  </Layout>
</template>

<style lang="scss">
</style>
