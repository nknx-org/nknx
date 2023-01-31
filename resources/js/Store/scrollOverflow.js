import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

export default {
    state: {
        scrollOverflow: false
    },
    mutations: {
        set(state) {
            state.scrollOverflow = !state.scrollOverflow;
        }
    },
    getters: {
        get(state) {
            return state.scrollOverflow;
        }
    },
    actions: {
        toggleScrollOverflow({ commit }) {
            commit("set");
        }
    },
    namespaced: true
};
