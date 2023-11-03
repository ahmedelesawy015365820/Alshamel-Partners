import Vue from "vue";

const routeModules = Vue.observable({
    value: localStorage.getItem("routeModules") ? JSON.parse(localStorage.getItem("routeModules")):[],
});


export default routeModules;
