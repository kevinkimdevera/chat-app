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
          ref="registerForm"
          v-model="registerForm"
          @submit.prevent="validateForm"
        >
          <v-card outlined>
            <v-progress-linear
              absolute
              :active="loading"
              color="primary"
              indeterminate
              top
            />

            <v-overlay
              :value="loading"
              absolute
              opacity="0.5"
              color="white"
            />

            <v-card-title>
              Create an account
            </v-card-title>

            <v-card-text>
              <v-text-field
                label="Name"
                outlined
                v-model="name"
                :rules="rules.name"
                type="email"
              />

              <v-text-field
                label="Email"
                outlined
                v-model="email"
                :rules="rules.email"
                type="email"
                :error="!!emailError"
                :error-messages="emailError"
              />

              <v-text-field
                label="Password"
                outlined
                :type="showPassword ? 'text' : 'password'"
                v-model="password"
                :rules="rules.password"
              />

              <v-text-field
                label="Re-type Password"
                outlined
                :type="showPassword ? 'text' : 'password'"
                v-model="confirmPassword"
                :rules="rules.confirm_password"
              />

              <v-checkbox
                label="Show Passwords"
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
                Register
              </v-btn>

              <router-link
                class="text-decoration-none"
                :to="{ name: 'auth.login' }"
              >
                Already have an account?
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
      loading: false,
      registerForm: null,
      registrationErrors: null,

      name: null,
      email: null,
      password: null,
      confirmPassword: null,
      showPassword: false,

      rules: {
        name: [
          v => !!v || 'Please enter your name.'
        ],

        email: [
          v => !!v || 'Please enter your email.',
          v => /.+@.+\..+/.test(v) || 'Please enter a valid email address.'
        ],

        password: [
          v => !!v || 'Please enter your password.',
          v => (v && v.length >= 8) || 'Password must be atleast 8 characters.'
        ],

        confirm_password: [
          v => !!v || 'Please enter re-type your password.',
          v => v === this.password || "Passwords didn't match."
        ]
      }
    }
  },
  computed: {
    emailError () {
      return this.registrationErrors?.email
    }
  },
  methods: {
    ...mapActions({
      registerAccount: 'auth/REGISTER',
      authenticate: 'auth/AUTHENTICATE',
      getUserData: 'auth/GET_USER_DATA'
    }),
    validateForm () {
      if (this.$refs.registerForm.validate()) {
        this.loading = true
        this.registrationErrors = null

        this.registerAccount({
          name: this.name,
          email: this.email,
          password: this.password,
          password_confirmation: this.confirmPassword
        })
          .catch((e) => {
            this.registrationErrors = e.errors
            this.loading = false
          })
          .then((response) => {
            if (response.created) {
              const token = response?.token

              localStorage.setItem('chat-app-token', token)
              this.authenticate(token)

              this.getUserData()
                .then((response) => {
                  if (response) {
                    this.$router.replace({
                      name: 'chat.home'
                    })
                  }
                })
            }
          })
      }
    }
  }
}
</script>
