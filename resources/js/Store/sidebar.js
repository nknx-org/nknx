import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

export default {
  state: {
    sidebarExpanded: false
  },
  mutations: {
    set(state) {
        state.sidebarExpanded = !state.sidebarExpanded
    }
  },
  getters: {
    get(state) {
        return state.sidebarExpanded
    }
  },
  actions: {
    toggleSidebar({ commit }) {
        commit('set')
    }
  },
  namespaced: true,
};
