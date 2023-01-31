import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

export default {
  state: {
    topbarExpanded: false
  },
  mutations: {
    setTopbar(state) {
        state.topbarExpanded = !state.topbarExpanded
    }
  },
  getters: {
    getTopbar(state) {
        return state.topbarExpanded
    }
  },
  actions: {
    toggleTopbar({ commit }) {
        commit('setTopbar')
    }
  },
  namespaced: true,
};
