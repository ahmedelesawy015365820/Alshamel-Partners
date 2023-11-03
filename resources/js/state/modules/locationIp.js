import axios from 'axios';

// state
export const state = {
    countryCode: ''
}

// getters
export const getters = {
    countryCode: state => state.countryCode,
}

// mutations
export const mutations = {
    editCountryCode(state,countryCode){
        state.countryCode = countryCode;
    },
};

// actions
export const actions = {
    async getIp({ commit }){
        $.getJSON('https://ipapi.co/json/', function(data) {
            commit('editCountryCode',data.country_code);
        });
    }
};
