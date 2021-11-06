<template>
  <v-app>
    <v-overlay
      :value="userLoading"
      opacity="1"
      absolute
      color="white"
    >
      <v-progress-circular
        indeterminate
        color="primary"
      />
    </v-overlay>

    <router-view name="navigation" />

    <v-main class="no-transition overflow-hidden">
      <router-view />
    </v-main>
  </v-app>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
  data () {
    return {
      userLoading: false
    }
  },
  computed: {
    ...mapGetters({
      user: 'auth/USER',
      token: 'auth/TOKEN'
    })
  },
  created () {
    const token = this.token

    if (token) {
      this.authenticate(token)
    }
  },
  methods: {
    ...mapActions({
      _authenticate: 'auth/AUTHENTICATE',
      _getUserData: 'auth/GET_USER_DATA'
    }),

    authenticate (token) {
      this._authenticate(token)
      this.getUserData()
    },

    getUserData () {
      this.userLoading = true
      this._getUserData()
        .then((response) => {
          if (response) {
            this.userLoading = false
          }
        })
    },
  }
}
</script>

<style lang="scss">
  html {
    overflow: hidden !important;
    scrollbar-width: none;
    -ms-overflow-style: none;
  }

  .no-transition {
    transition: none !important;
  }
</style>
