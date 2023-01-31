import Vue from "vue";
import Vuex from "vuex";

Vue.use(Vuex);

export default {
    state: {
        nodesConfig: {
            search: "",
            per_page: 10,
            page: 1,
            syncState: "ALL",
            sort: "relayMessageCount",
            order: "desc"
        }
    },
    mutations: {
        setSearch(state, payload) {
            state.nodesConfig.search = payload;
        },
        setSyncState(state, payload) {
            state.nodesConfig.syncState = payload;
        },
        setPage(state, payload) {
            state.nodesConfig.page = payload;
        },
        setPerPage(state, payload) {
            state.nodesConfig.per_page = payload;
        },
        setSort(state, sort) {
            if (state.nodesConfig.sort === sort) {
                state.nodesConfig.order =
                    state.nodesConfig.order === "desc" ? "asc" : "desc";
            } else {
                state.nodesConfig.sort = sort;
                state.nodesConfig.order = "desc";
            }
        }
    },
    namespaced: true
};
