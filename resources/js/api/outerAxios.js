import axios from "axios";
import Cookies from "js-cookie";
import store from "../state/store";
import router from "../router";

const outerAxios = axios.create({
    baseURL: `${process.env.MIX_APP_URL_OUTSIDE}api/`
});

outerAxios.interceptors.request.use(
    function (config) {
        config.headers['lang'] = localStorage.getItem("lang") || 'ar';
        config.headers['Authorization'] = "Bearer " + (Cookies.get("token") || '');
        return config;
    },
    function (error) {
        return Promise.reject(error);
    }
);
outerAxios.defaults.headers.common['secretApi'] = 'Snr92EUKCmrE06PiJ';
outerAxios.defaults.headers.common['Accept'] = 'application/json';

outerAxios.interceptors.response.use(function (response) {
    return response;
}, function (error) {
    if (401 === error.response.status) {
        // handle error: inform user, go to login, etc
        store.commit('auth/logoutToken');
        return router.push({name: 'login'});
    } else {
        return Promise.reject(error);
    }
});

export default outerAxios;
