import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

export default {
    state: {
        activeFdConfig: {
            id: 0
        },
        activeProfiles: [],
        activeProvider: "",
        ssh: false
    },
    mutations: {
        setActiveFdConfig(state, fastDeployConfigsObj) {
            state.activeFdConfig = fastDeployConfigsObj;
        },
        setActiveProfiles(state, vpsProfiles) {
            state.activeProfiles = vpsProfiles;
        },
        setActiveProvider(state, provider) {
            state.activeProvider = provider;
        },
        setSsh(state, bool) {
            state.ssh = bool;
        }
    },
    namespaced: true
};
