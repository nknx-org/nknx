export const state = () => ({
  newWalletModalVisible: false,
  deleteWalletModalVisible: false,
  newNodeModalVisible: false,
  deleteNodeModalVisible: false,
  editNodeModalVisible: false,
  deleteAllNodesModalVisible: false,
  deleteAllWalletsModalVisible: false,
  receiveWalletModalVisible: false,
  fastDeployModalVisible: false,
  deleteAccountModalVisible: false,
  deleteProviderModalVisible: false,
  editProviderModalVisible: false,
  deleteFastDeployConfigModalVisible: false,
  editFastDeployConfigModalVisible: false,
  fastDeployCustomModalVisible: false,
  developmentUpdateModalVisible: false
})

export const mutations = {
  setDevelopmentUpdateModalVisible(state, obj) {
    state.developmentUpdateModalVisible = obj
  },
  setNewWalletModalVisible(state, obj) {
    state.newWalletModalVisible = obj
  },
  setDeleteWalletModalVisible(state, obj) {
    state.deleteWalletModalVisible = obj
  },
  setNewNodeModalVisible(state, obj) {
    state.newNodeModalVisible = obj
  },
  setDeleteNodeModalVisible(state, obj) {
    state.deleteNodeModalVisible = obj
  },
  setEditNodeModalVisible(state, obj) {
    state.editNodeModalVisible = obj
  },
  setDeleteAllNodesModalVisible(state, obj) {
    state.deleteAllNodesModalVisible = obj
  },
  setDeleteAllWalletsModalVisible(state, obj) {
    state.deleteAllWalletsModalVisible = obj
  },
  setReceiveWalletModalVisible(state, obj) {
    state.receiveWalletModalVisible = obj
  },
  setFastDeployModalVisible(state, obj) {
    state.fastDeployModalVisible = obj
  },
  setDeleteAccountModalVisible(state, obj) {
    state.deleteAccountModalVisible = obj
  },
  setDeleteProviderModalVisible(state, obj) {
    state.deleteProviderModalVisible = obj
  },
  setEditProviderModalVisible(state, obj) {
    state.editProviderModalVisible = obj
  },
  setDeleteFastDeployConfigModalVisible(state, obj) {
    state.deleteFastDeployConfigModalVisible = obj
  },
  setEditFastDeployConfigModalVisible(state, obj) {
    state.editFastDeployConfigModalVisible = obj
  },
  setFastDeployCustomModalVisible(state, obj) {
    state.fastDeployCustomModalVisible = obj
  }
}

export const getters = {
  getDevelopmentUpdateModalVisible(state) {
    return state.developmentUpdateModalVisible
  },
  getNewWalletModalVisible(state) {
    return state.newWalletModalVisible
  },
  getDeleteWalletModalVisible(state) {
    return state.deleteWalletModalVisible
  },
  getNewNodeModalVisible(state) {
    return state.newNodeModalVisible
  },
  getDeleteNodeModalVisible(state) {
    return state.deleteNodeModalVisible
  },
  getEditNodeModalVisible(state) {
    return state.editNodeModalVisible
  },
  getDeleteAllNodesModalVisible(state) {
    return state.deleteAllNodesModalVisible
  },
  getDeleteAllWalletsModalVisible(state) {
    return state.deleteAllWalletsModalVisible
  },
  getReceiveWalletModalVisible(state) {
    return state.receiveWalletModalVisible
  },
  getFastDeployModalVisible(state) {
    return state.fastDeployModalVisible
  },
  getDeleteAccountModalVisible(state) {
    return state.deleteAccountModalVisible
  },
  getDeleteProviderModalVisible(state) {
    return state.deleteProviderModalVisible
  },
  getEditProviderModalVisible(state) {
    return state.editProviderModalVisible
  },
  getDeleteFastDeployConfigModalVisible(state) {
    return state.deleteFastDeployConfigModalVisible
  },
  getEditFastDeployConfigModalVisible(state) {
    return state.editFastDeployConfigModalVisible
  },
  getFastDeployCustomModalVisible(state) {
    return state.fastDeployCustomModalVisible
  }
}

export const actions = {
  updateDevelopmentUpdateModalVisible({ commit }, obj) {
    commit('setDevelopmentUpdateModalVisible', obj)
  },
  updateNewWalletModalVisible({ commit }, obj) {
    commit('setNewWalletModalVisible', obj)
  },
  updateDeleteWalletModalVisible({ commit }, obj) {
    commit('setDeleteWalletModalVisible', obj)
  },
  updateNewNodeModalVisible({ commit }, obj) {
    commit('setNewNodeModalVisible', obj)
  },
  updateDeleteNodeModalVisible({ commit }, obj) {
    commit('setDeleteNodeModalVisible', obj)
  },
  updateEditNodeModalVisible({ commit }, obj) {
    commit('setEditNodeModalVisible', obj)
  },
  updateDeleteAllNodesModalVisible({ commit }, obj) {
    commit('setDeleteAllNodesModalVisible', obj)
  },
  updateDeleteAllWalletsModalVisible({ commit }, obj) {
    commit('setDeleteAllWalletsModalVisible', obj)
  },
  updateReceiveWalletModalVisible({ commit }, obj) {
    commit('setReceiveWalletModalVisible', obj)
  },
  updateFastDeployModalVisible({ commit }, obj) {
    commit('setFastDeployModalVisible', obj)
  },
  updateDeleteAccountModalVisible({ commit }, obj) {
    commit('setDeleteAccountModalVisible', obj)
  },
  updateDeleteProviderModalVisible({ commit }, obj) {
    commit('setDeleteProviderModalVisible', obj)
  },
  updateEditProviderModalVisible({ commit }, obj) {
    commit('setEditProviderModalVisible', obj)
  },
  updateDeleteFastDeployConfigModalVisible({ commit }, obj) {
    commit('setDeleteFastDeployConfigModalVisible', obj)
  },
  updateEditFastDeployConfigModalVisible({ commit }, obj) {
    commit('setEditFastDeployConfigModalVisible', obj)
  },
  updateFastDeployCustomModalVisible({ commit }, obj) {
    commit('setFastDeployCustomModalVisible', obj)
  }
}
