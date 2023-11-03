import Cookies from "js-cookie";
import adminApi from '../../api/adminAxios';
import router from "../../router/index";
import { selectedParents } from "../../helper/global";
import routeModules from "../../helper/Rule"
import allRoute from "../../helper/allRoute"
// state
export const state = {
    token: Cookies.get("token") || null,
    permissions: localStorage.getItem("permissions")? JSON.parse(localStorage.getItem("permissions")): [] || [],
    partner: localStorage.getItem("partner") ? JSON.parse(localStorage.getItem("partner")): []  || {},
    companies: localStorage.getItem("companies") ? JSON.parse(localStorage.getItem("companies")): []  || [],
    company_id: localStorage.getItem("company_id") ? JSON.parse(localStorage.getItem("company_id")): []  || null,
    work_flow_trees: localStorage.getItem("work_flow_trees") ? JSON.parse(localStorage.getItem("work_flow_trees")): []  || [],
    allWorkFlow: localStorage.getItem("allWorkFlow") ? JSON.parse(localStorage.getItem("allWorkFlow")): []  || [],
    user: localStorage.getItem("user") ? JSON.parse(localStorage.getItem("user")): []  || {},
    type: localStorage.getItem("type") ? JSON.parse(localStorage.getItem("type")): []  || "",
    parentModule: localStorage.getItem("parentModule") ? JSON.parse(localStorage.getItem("parentModule")):[],
    notification: true,
    is_web: 1,
    customModules: localStorage.getItem("customModules") ? JSON.parse(localStorage.getItem("customModules")):[]
}

// getters
export const getters = {
    token: state => state.token,
    permissions: state => state.permissions,
    loading: state => state.loading,
    partner: state => state.partner,
    companies: state => state.companies,
    errors: state => state.errors,
    company_id: state => state.company_id,
    work_flow_trees: state => state.work_flow_trees,
    user: state => state.user,
    type: state => state.type,
    parentModule: state => state.parentModule,
    is_web: state => state.is_web,
    customModules: state => state.customModules,
}

// mutations
export const mutations = {
    editToken(state,token){
        state.token = token;
        Cookies.set('token',token,{ expires: 7 });
    },
    editPermission(state,permissions){
        let name = [];
        permissions.forEach(el => {
            name.push(el.name);
        });
        state.permissions = name;
        localStorage.setItem('permissions',JSON.stringify(name));
    },
    editPartner(state,partner){
        state.partner = partner;
        localStorage.setItem('partner',JSON.stringify(partner));
    },
    editCompanies(state,companies){
        state.companies = companies;
        localStorage.setItem('companies',JSON.stringify(companies));
    },
    editCompanyId(state,company_id){
        state.company_id = company_id;
        localStorage.setItem('company_id',JSON.stringify(company_id));
    },
    editWorkFlowTrees(state,work_flow_trees){
        state.work_flow_trees = work_flow_trees;
        localStorage.setItem('work_flow_trees',JSON.stringify(work_flow_trees));
    },
    allWorkFlow(state,allWorkFlow){
        state.allWorkFlow = allWorkFlow;
        localStorage.setItem('allWorkFlow',JSON.stringify(allWorkFlow));
    },
    logoutToken(state){
        // state.roles = null;
        state.token = null;
        state.partner = {};
        state.user = {};
        state.companies = [];
        state.work_flow_trees = [];
        state.permissions = [];
        state.allWorkFlow = [];
        state.company_id = null;
        state.type = '';
        state.parentModule = [];
        selectedParents.value=[];
        allRoute.value=[];
        routeModules.value=[];
            // state.permission = null;
        Cookies.remove('token');
        localStorage.removeItem('companies');
        localStorage.removeItem('partner');
        localStorage.removeItem('company_id');
        localStorage.removeItem('work_flow_trees');
        localStorage.removeItem('allWorkFlow');
        localStorage.removeItem('user');
        localStorage.removeItem('permissions');
        localStorage.removeItem('type');
        localStorage.removeItem('parentModule');
        localStorage.removeItem('selectedParents');
        localStorage.removeItem('routeModules');
        localStorage.removeItem('allRoutes');
    },
    editErrors(state,errors){
        state.errors = errors;
    },
    editType(state,type){
        state.type = type;
        localStorage.setItem('type',JSON.stringify(type));
    },
    editUser(state,user){
        state.user = user;
        localStorage.setItem('user',JSON.stringify(user));
    },
    editNotification(state,notification){
        state.notification = notification;
        localStorage.setItem('notification',JSON.stringify(notification));
    },
    editParentModule(state,parentModule){
        state.parentModule = parentModule;
        localStorage.setItem('parentModule',JSON.stringify(parentModule));
    },
    editIsWeb(state,is_web){
        state.is_web = is_web;
    },
    editCustomModules(state,modules){
        state.customModules = modules;
        localStorage.setItem('customModules',JSON.stringify(modules));
    }
};

// actions
export const actions = {};
