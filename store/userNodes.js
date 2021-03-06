export const state = () => ({
  userNodes: false,
  userNodesStats: false,
  userConfig: {
    filter: '',
    sort: 'relayMessageCount',
    order: '',
    search: '',
    page: 1,
    prevPage: null,
    nextPage: null,
    lading: false
  }
})

export const mutations = {
  setUserNodes(state, userNodesObj) {
    state.userNodes = userNodesObj
  },
  setUserConfig(state, userConfigObj) {
    state.userConfig = userConfigObj
  },
  setUserNodesStats(state, userNodesStatsObj) {
    state.userNodesStats = userNodesStatsObj
  }
}

export const getters = {
  getUserNodes(state) {
    return state.userNodes
  },
  getUserConfig(state) {
    return state.userConfig
  },
  getUserNodesStats(state) {
    return state.userNodesStats
  }
}

export const actions = {
  async updateUserNodes({ commit }) {
    const {
      page,
      filter,
      sort,
      order,
      search
    } = this.state.userNodes.userConfig
    const data = await this.$axios.$get(
      `nodes?page=${page}&syncState=${filter}&orderBy=${sort}&order=${order}&search=${search}`
    )
    commit('setUserNodes', data)
  },
  updateUserConfig({ commit }, config) {
    commit('setUserConfig', config)
  },
  async updateUserNodesStats({ commit }) {
    const data = await this.$axios.$get(`/nodes/summary?aggregate=day`)
    commit('setUserNodesStats', data)
  }
}
