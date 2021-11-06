<template>
  <router-view />
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  data () {
    return {

    }
  },
  computed: {
    ...mapGetters({
      user: 'auth/USER',
      token: 'auth/TOKEN'
    }),
    userID () {
      return this.user?.id
    }
  },
  beforeRouteEnter (to, from, next) {
    next(vm => {
      vm.getChats()
    })
  },
  watch: {
    user (val) {
      if (val) {
        this.connectToChannels()
      }
    }
  },
  methods: {
    ...mapActions({
      _getChats: 'chat/GET'
    }),
    getChats () {
      this._getChats()
    },
    connectToChannels () {
      window.Echo.private(`users.${this.userID}`)
        .listen('.new-message', (e) => {
          if (e) {
            this._getChats()
          }
        })
    }
  }
}
</script>
