import api from '../../api/api'

const state = {

}

const getters = {

}

const mutations = {

}

const actions = {
  SEARCH: async ({ commit }, payload) => {
    return await new Promise((resolve, reject) => {
      api.get('/api/users/search', payload)
        .then((response) => {
          resolve(response.data)
        })
        .catch((e) => {
          reject(e.response.data)
        })
    })
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
