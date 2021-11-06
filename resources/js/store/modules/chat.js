import api from '../../api/api'

const state = {
  chats: []
}

const getters = {
  LIST: state => state.chats
}

const mutations = {
  SET_CHATS: (state, payload) => { state.chats = payload }
}

const actions = {
  GET: async ({ commit }) => {
    return await new Promise((resolve, reject) => {
      api.get('/api/chats')
        .then((response) => {
          commit('SET_CHATS', response?.data?.data)
          resolve(true)
        })
        .catch((e) => {
          reject(e.response.data)
        })
    })
  },

  SHOW: async ({ commit }, payload) => {
    return await new Promise((resolve, reject) => {
      api.get(`/api/chats/${payload.code}`)
        .then((response) => {
          resolve(response.data)
        })
        .catch((e) => {
          reject(e.response.data)
        })
    })
  },

  GET_MESSAGES: async ({ commit }, payload) => {
    return await new Promise((resolve, reject) => {
      api.get(`/api/chats/${payload.code}/messages`)
        .then((response) => {
          resolve(response.data)
        })
        .catch((e) => {
          reject(e.response.data)
        })
    })
  },

  SEND_MESSAGE: async ({ commit }, data) => {
    return await new Promise((resolve, reject) => {
      api.post(`/api/chats/${data.code}/message`, data)
        .then((response) => {
          resolve(response.data)
        })
        .catch((e) => {
          reject(e.response.data)
        })
    })
  },

  SEND_NEW_MESSAGE: async ({ commit }, data) => {
    return await new Promise((resolve, reject) => {
      api.post('/api/chats/new', data)
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
