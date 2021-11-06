/* eslint-disable no-unused-vars */
import Vue from 'vue'
import vuetify from './plugins/vuetify'
import router from './plugins/router'
import store from './store'

require('./bootstrap')

// =============================================================================
// Main App Component
// =============================================================================
Vue.component('App', require('./components/layouts/App.vue').default)

// =============================================================================
// Turn off tips on production
// =============================================================================
Vue.config.productionTip = false

// =============================================================================
// Init Vue App
// =============================================================================
const app = new Vue({
  el: '#app',
  vuetify,
  router,
  store
})
