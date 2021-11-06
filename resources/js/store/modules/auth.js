import api from '../../api/api'

const state = {
  user: null
}

const getters = {
  TOKEN: () => localStorage.getItem('chat-app-token'),
  USER: state => state.user
}

const mutations = {
  SET_USER: (state, payload) => { state.user = payload }
}

const actions = {
  AUTHENTICATE: ({ commit }, token) => {
    api.setAuthToken(token)
  },

  LOGOUT: ({ commit }) => {
    commit('SET_USER', null)
    localStorage.removeItem('chat-app-token')
    api.removeAuthToken()
  },

  ATTEMPT_LOGIN: async ({ commit }, credentials) => {
    return await new Promise((resolve, reject) => {
      api.post('/api/auth/login', credentials)
        .then((response) => {
          resolve(response.data)
        })
        .catch((e) => {
          reject(e.response.data)
        })
    })
  },

  REGISTER: async ({ commit }, data) => {
    return await new Promise((resolve, reject) => {
      api.post('/api/auth/register', data)
        .then((response) => {
          resolve(response.data)
        })
        .catch((e) => {
          reject(e.response.data)
        })
    })
  },

  GET_USER_DATA: async ({ commit }) => {
    return await new Promise((resolve, reject) => {
      api.get('/api/auth/user')
        .then((response) => {
          commit('SET_USER', response.data?.data)
          resolve(true)
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
