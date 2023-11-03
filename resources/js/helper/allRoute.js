import Vue from "vue";

const allRoutes = Vue.observable({
    value: localStorage.getItem("allRoutes") ? JSON.parse(localStorage.getItem("allRoutes")):[],
});

export default allRoutes;
