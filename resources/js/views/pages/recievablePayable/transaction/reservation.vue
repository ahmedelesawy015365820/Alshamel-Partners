<template>
    <Layout>
        <PageHeader />
        <ReservationCom />
    </Layout>
</template>

<script>
import Layout from "../../../layouts/main";
import PageHeader from "../../../../components/general/Page-header";
import ReservationCom from "../../../../components/create/reservationCom";
import permissionGuard from "../../../../helper/permission";


export default {
    name: "reservation",
    page: {
        title: "Reservation",
        meta: [{ name: "description", content: "Reservation" }],
    },
    beforeRouteEnter(to, from, next) {
            next((vm) => {
      return permissionGuard(vm, "Reservation RP", "all reservation RP");
    });

    },
    components: {
        Layout,
        PageHeader,
        ReservationCom
    },
}
</script>

<style scoped>

</style>
