// state
export const state = {
    defaultsKeys: {},
    companyKeys: {},
    filterResult: {}
}

// getters
export const getters = {
    defaultsKeys: state => state.defaultsKeys,
    companyKeys: state => state.companyKeys,
    filterResult: state => state.filterResult
}

// mutations
export const mutations = {
    editDefaultsKeys(state,defaultsKeys){
        state.defaultsKeys = defaultsKeys;
    },
    editCompanyKeys(state,companyKeys){
        state.companyKeys = companyKeys;
    },
    editFilterResult(state,filterResult){
        state.filterResult = filterResult;
    },
};

// actions
export const actions = {};
