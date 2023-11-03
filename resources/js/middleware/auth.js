import router from "../router/index";

export default function auth({ next, store }){
    if (!store.getters["auth/token"]) {
        return router.push({name: 'login'});
    } else {
        if (store.getters["auth/type"] == "admin"){
            if(!store.getters["auth/company_id"]){
                return router.push({name: 'company'});
            }else {
                return next();
            }
        }else {
            return next();
        }

    }
}
