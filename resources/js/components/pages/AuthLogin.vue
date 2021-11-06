<template>
  <v-container
    class="fill-height"
    fluid
  >
    <v-row
      justify="center"
    >
      <v-col
        cols="12"
        md="7"
        lg="5"
        xl="3"
      >
        <v-form
          ref="loginForm"
          v-model="loginForm"
          @submit.prevent="validateLogin"
        >
          <v-card outlined>
            <v-progress-linear
              absolute
              :active="authenticating"
              color="primary"
              indeterminate
              top
            />

            <v-overlay
              :value="authenticating"
              absolute
              opacity="0.5"
              color="white"
            />

            <v-card-title>
              Login
            </v-card-title>

            <v-card-text>
              <v-text-field
                label="Email"
                outlined
                v-model="email"
                :rules="rules.email"
                type="email"
                :error="!!loginError"
                :error-messages="loginError"
              />

              <v-text-field
                label="Password"
                outlined
                :type="showPassword ? 'text' : 'password'"
                v-model="password"
                :rules="rules.password"
              />

              <v-checkbox
                label="Show Password"
                class="mt-0"
                v-model="showPassword"
              />

              <v-btn
                color="primary"
                block
                x-large
                type="submit"
                depressed
                class="my-3"
              >
                Login
              </v-btn>

              <router-link
                class="text-decoration-none"
                :to="{ name: 'auth.register' }"
              >
                Create an account
              </router-link>
            </v-card-text>
          </v-card>
        </v-form>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
  data () {
    return {
      authenticating: false,
      loginForm: null,
      loginError: null,

      email: null,
      password: null,
      showPassword: false,

      rules: {
        email: [
          v => !!v || 'Please enter your email.'
        ],

        password: [
          v => !!v || 'Please enter your password.'
        ]
      }
    }
  },
  computed: {

  },
  methods: {
    ...mapActions({
      _authenticate: 'auth/AUTHENTICATE',
      _attemptLogin: 'auth/ATTEMPT_LOGIN',
      _getUserData: 'auth/GET_USER_DATA'
    }),
    validateLogin () {
      if (this.$refs.loginForm.validate()) {
        this.authenticating = true
        this.loginError = null

        this._attemptLogin({
          email: this.email,
          password: this.password
        })
          .then((response) => {
            const token = response?.token

            if (token) {
              localStorage.setItem('chat-app-token', token)
              this._authenticate(token)

              this._getUserData()
                .then((response) => {
                  if (response) {
                    this.$router.replace({
                      name: 'chat.home'
                    })
                  }
                })
            }
          })
          .catch((e) => {
            this.loginError = e.message
            this.authenticating = false
          })
      }
    }
  }
}
</script>
