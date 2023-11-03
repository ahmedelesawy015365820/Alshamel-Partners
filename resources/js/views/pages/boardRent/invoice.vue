<template>
  <Layout>
    <PageHeader />
    <DocumentWithItem :document_id="4" />
  </Layout>
</template>

<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import DocumentWithItem from "../../../components/document/withItem/documentWithItem";
import permissionGuard from "../../../helper/permission";

export default {
  page: {
    title: "Invoice",
    meta: [{ name: "description", content: "Invoice" }],
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(vm, "Invoice BoardRent", "all Store");
    });
  },
  components: {
    Layout,
    PageHeader,
    DocumentWithItem,
  },
};
</script>

<style scoped>
</style>
