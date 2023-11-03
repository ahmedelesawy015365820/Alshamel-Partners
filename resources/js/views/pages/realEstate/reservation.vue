<script>
import Layout from "../../layouts/main";
import PageHeader from "../../../components/general/Page-header";
import ReservationCom from "../../../components/create/reservationCom";
import permissionGuard from "../../../helper/permission";

/**
 * Advanced Table component
 */
export default {
  page: {
    title: "Reservation",
    meta: [{ name: "description", content: "Reservation" }],
  },
  components: {
    Layout,
    PageHeader,
    ReservationCom,
  },
  beforeRouteEnter(to, from, next) {
    next((vm) => {
      return permissionGuard(
        vm,
        "Reservation RealEstate",
        "all reservation RealState"
      );
    });
  },
};
</script>

<template>
  <Layout>
    <PageHeader />
    <ReservationCom />
  </Layout>
</template>
