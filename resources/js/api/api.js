import axios from 'axios'

export default {
  setAuthToken: (token) => {
    axios.defaults.headers.common.Authorization = `Bearer ${token}`
  },

  removeAuthToken: () => {
    delete axios.defaults.headers.common.Authorization
  },

  get: (url, params) => {
    return axios.get(url, {
      responseType: 'json',
      params: params
    })
  },

  post: (url, data) => {
    return axios.post(url, data, {
      responseType: 'json'
    })
  },

  patch: (url, data = {}) => {
    data._method = 'PATCH'

    return axios.post(url, data, {
      responseType: 'json'
    })
  },

  delete: (url, data = {}) => {
    data._method = 'DELETE'

    return axios.post(url, data, {
      responseType: 'json'
    })
  }
}
