import router from "../router";

export default function guest({next,store}){
    if (store.getters["auth/token"] ) {
        if (store.getters["auth/type"] == "admin"){
            if(!store.getters["auth/company_id"]){
                return next({name: 'company'});
            }else {
                return next({name: 'home'});
            }
        }else {
            return next({name: 'home'});
        }
    } else {
        return next();
    }
}
