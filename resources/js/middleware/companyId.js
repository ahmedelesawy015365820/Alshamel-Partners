import router from "../router/index";

export default function companyId({ next, store }){
    if (!store.getters["auth/token"]) {
        return router.push({name: 'login'});
    } else {
        return next();
    }
}
