import Vue from "vue";

const _companies = Vue.observable({
    value: localStorage.getItem("companies") ? JSON.parse(localStorage.getItem("companies")) : [],
});

const routeClicked = Vue.observable({
    value: false
});

const selectedParents = Vue.observable({
    value: localStorage.getItem("selectedParents") ? JSON.parse(localStorage.getItem("selectedParents")) : []
});
export {_companies,routeClicked,selectedParents};


