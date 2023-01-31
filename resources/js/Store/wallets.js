import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

export default {
    state: {
        activeWallet: {
            id: 0,
            address: "",
            label: "",
            balance: "",
            snapshots: []
        }
    },
    mutations: {
        setActiveWallet(state, obj) {
            state.activeWallet = obj;
        }
    },
    namespaced: true
};
