<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import ContractCom from "../../../components/create/contractCom";
import permissionGuard from "../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Contract",
        meta: [{ name: "description", content: "Contract" }],
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Contract", "all contract RealState");
    });

     },
    components: {
        Layout,
        PageHeader,
        ContractCom
    },
};
</script>

<template>
    <Layout>
        <PageHeader />
        <ContractCom />
    </Layout>
</template>
