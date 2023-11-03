<template>
    <Layout>
        <PageHeader />
        <ContractCom />
    </Layout>
</template>

<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import ContractCom from "../../../../components/create/contractCom";
import permissionGuard from "../../../../helper/permission";

export default {
    name: "contract",page: {
        title: "Contract",
        meta: [{ name: "description", content: "Contract" }],
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Contract RP", "all contract RP");
    });


    },
    components: {
        Layout,
        PageHeader,
        ContractCom
    },

}
</script>

<style scoped>

</style>
