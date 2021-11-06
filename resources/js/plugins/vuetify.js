import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'

// ==============================
// Make Vue uses Vuetify
// ==============================
Vue.use(Vuetify)

// ==============================
// Vuetify Settings
// ==============================
const opts = {
  theme: {
    dark: false,
    themes: {
      light: {
        primary: '#673AB7',
        secondary: '#2196F3',
        accent: '#9575CD'
      }
    }
  }
}

// ==============================
// Export Settings
// ==============================
export default new Vuetify(opts)
