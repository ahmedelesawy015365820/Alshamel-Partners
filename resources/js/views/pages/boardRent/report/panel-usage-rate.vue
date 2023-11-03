<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import Calender from "../../../../components/document/withItem/report/calender";
import permissionGuard from "../../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
    page: {
        title: "Panel Usage Rate Report",
        meta: [{name: "Panel Usage Rate Report", content: 'Panel Usage Rate Report'}],
    },
    components: {
        Layout,
        PageHeader,
        Calender
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Panel Usage Rate", "all Store");
    });

    },
};
</script>

<template>
    <Layout>
        <PageHeader/>
        <Calender :document_detail_type="'board_rent'" />
    </Layout>
</template>




