import axios from "axios";
import Cookies from "js-cookie";
import store from "../state/store";
import router from "../router/index";


const adminApi = axios.create({
    baseURL: `${process.env.MIX_APP_URL}api/`
});

adminApi.interceptors.request.use(
    function (config) {
        config.headers['lang'] = localStorage.getItem("lang") || 'ar';
        config.headers['Authorization'] = "Bearer " + (Cookies.get("token") || '');
        config.headers['company-id'] = JSON.parse(localStorage.getItem("company_id"));
        if (JSON.parse(localStorage.getItem("type"))){
            config.headers['admin_id'] =  JSON.parse(localStorage.getItem("type")) == "admin" ? JSON.parse(localStorage.getItem("partner")).id : null;
            config.headers['admin_name'] =  JSON.parse(localStorage.getItem("type")) == "admin" ? JSON.parse(localStorage.getItem("partner")).name_e : null;
            config.headers['user_id'] = JSON.parse(localStorage.getItem("type")) == "admin" ? null : JSON.parse(localStorage.getItem("user")).id;
            config.headers['type'] = JSON.parse(localStorage.getItem("type"));
        }
        return config;
    },
    function (error) {
        return Promise.reject(error);
    }
);
adminApi.defaults.headers.common['secretApi'] = 'Snr92EUKCmrE06PiJ';
adminApi.defaults.headers.common['Accept'] = 'application/json';

adminApi.interceptors.response.use(function (response) {
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
// end axios
export default adminApi;
