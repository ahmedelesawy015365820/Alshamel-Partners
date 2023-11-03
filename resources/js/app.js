require('./bootstrap');
window.$ = window.jQuery = require("jquery");
import Vue from 'vue';
import App from './App.vue';
import BootstrapVue from 'bootstrap-vue';
import Vuelidate from 'vuelidate';
import simplebar from "simplebar-vue";
import VueTour from 'vue-tour';
import vco from "v-click-outside";
import * as VueGoogleMaps from "vue2-google-maps";
import i18n from './lang/i18n';
import router from './router';
import store from './state/store';
import VueApexCharts from 'vue-apexcharts';
import VueStringFilter from 'vue-string-filter';
import Lightbox from 'vue-easy-lightbox';
import Print from 'vue-print-nb';

Vue.config.productionTip = false;

Vue.use(vco);
// As a plugin
import VueMask from 'v-mask';
import VueQuillEditor from 'vue-quill-editor';
import VueDraggable from "vue-draggable";
Vue.use(VueDraggable);
Vue.use(VueQuillEditor);
Vue.use(VueMask);

import VueSlideBar from 'vue-slide-bar';
import 'vue-phone-number-input/dist/vue-phone-number-input.css';

Vue.component('VuePhoneNumberInput', require('vue-phone-number-input'));
Vue.component('VueSlideBar', VueSlideBar);
Vue.component('pagination-laravel', require('laravel-vue-pagination'));
Vue.component('apexchart', VueApexCharts);
Vue.use(BootstrapVue);
Vue.use(Vuelidate);
Vue.use(require('vue-chartist'));
Vue.component('simplebar', simplebar);
Vue.use(VueStringFilter);
Vue.use(VueTour);
Vue.use(Lightbox);
Vue.use(Print);

Vue.use(VueGoogleMaps, {
    load: {
        key: "AIzaSyAbvyBxmMbFhrzP9Z8moyYr6dCr-pzjhBE",
        libraries: "places"
    },
    installComponents: true
});


// change lang
if(!localStorage.getItem('lang')) localStorage.setItem('lang','ar');
let style_dashboard = document.getElementById('style_dashboard');
if(localStorage.getItem('lang') == 'ar'){
    document.body.style.textAlign = 'right';
    document.body.classList.add('rtl');
    document.querySelector('html').style.direction = 'rtl';
    document.querySelector('html').setAttribute('lang','ar');
    style_dashboard.setAttribute('href',window.location.origin +`/css/custom.css`);
}
else{
    document.body.style.textAlign = 'left';
    document.body.classList.remove('rtl');
    document.querySelector('html').style.direction = 'ltr';
    document.querySelector('html').setAttribute('lang',localStorage.getItem('lang') || 'ar');
    style_dashboard.setAttribute('href','');
}
new Vue({
    router,
    store,
    i18n,
    directives: {
        print
    },
    render: h => h(App)
}).$mount('#app');
