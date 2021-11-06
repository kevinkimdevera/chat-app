<template>
  <header>
    <v-app-bar
      app
      clipped-left
      clipped-right
      color="primary"
      dark
    >
      <v-app-bar-nav-icon @click="toggleNav" />
      <v-toolbar-title>Chat App</v-toolbar-title>

      <v-spacer />

      <template v-if="authenticated">
        <v-menu
          offset-y
          transition="slide-y-transition"
        >
          <template #activator="{ on }">
            <v-btn
              icon
              v-on="on"
            >
              <v-avatar
                color="primary lighten-1"
                size="40"
              >
                <template v-if="userAvatar.type === 'placeholder'">
                  {{ userAvatar.avatar }}
                </template>
              </v-avatar>
            </v-btn>
          </template>

          <v-card width="360">
            <v-list nav>
              <v-list-item two-line>
                <v-list-item-avatar
                  size="48"
                  color="primary"
                  class="white--text"
                >
                  <v-avatar>
                    <template v-if="userAvatar.type === 'placeholder'">
                      {{ userAvatar.avatar }}
                    </template>
                  </v-avatar>
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title class="font-weight-medium">
                    {{ userName }}
                  </v-list-item-title>
                  <v-list-item-subtitle>{{ userEmail }}</v-list-item-subtitle>
                </v-list-item-content>
              </v-list-item>

              <v-divider class="my-2" />

              <v-list-item @click="logout">
                <v-list-item-icon>
                  <v-icon>mdi-logout-variant</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>Sign Out</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list>
          </v-card>
        </v-menu>
      </template>
    </v-app-bar>

    <v-navigation-drawer
      app
      clipped
      v-model="nav"
      width="320"
      :permanent="$vuetify.breakpoint.smAndUp"
    >
      <v-toolbar flat>
        <v-toolbar-title class="text-subtitle-1">
          Chats
        </v-toolbar-title>

        <v-spacer />

        <v-btn
          icon
          @click="showNewChatDialog"
        >
          <v-icon color="primary">
            mdi-message-plus-outline
          </v-icon>
        </v-btn>
      </v-toolbar>
      <v-list dense>
        <template v-for="(chat, c) in chats">
          <v-list-item
            link
            active-class=" primary--text"
            :key="`chat-list-item-${c}`"
            :to="{ name: 'chat.view', params: { code: chat.code } }"
          >
            <v-list-item-avatar
              color="accent"
              size="36"
            >
              {{ chat.interlocutor.avatar }}
            </v-list-item-avatar>

            <v-list-item-content>
              <v-list-item-title
                :class="{
                  'font-weight-black': chat.unseen_messages_count > 0,
                  'font-weight-normal': chat.unseen_messages_count < 1
                }"
              >
                {{ chat.interlocutor.name }}
              </v-list-item-title>
              <v-list-item-subtitle>
                {{ chat.latest_message.message }}
              </v-list-item-subtitle>
            </v-list-item-content>

            <v-list-item-action-text>
              <template v-if="chat.unseen_messages_count < 1">
                {{ chat.latest_message.sent }}
              </template>
              <template v-else>
                <v-badge
                  :content="chat.unseen_messages_count"
                  inline
                />
              </template>
            </v-list-item-action-text>
          </v-list-item>
        </template>
      </v-list>
    </v-navigation-drawer>

    <!-- New Chat Dialog -->
    <v-dialog
      v-model="chatDialog"
      persistent
      width="520"
    >
      <v-form @submit.prevent="sendNewChat">
        <v-card>
          <v-card-title>New Chat</v-card-title>
          <v-card-text>
            <v-autocomplete
              label="Send to"
              outlined
              v-model="chatTo"
              :search-input.sync="userSearch"
              :loading="gettingUsers"
              :items="usersResult"
              item-value="id"
              item-text="name"
              clearable
              chips
              small-chips
            >
              <template #no-data>
                <v-list-item>
                  <v-list-item-title>
                    Search user...
                  </v-list-item-title>
                </v-list-item>
              </template>

              <template #selection="data">
                <v-chip
                  v-bind="data.attrs"
                  :input-value="data.selected"
                  close
                  @click="data.select"
                  @click:close="chatTo = null"
                >
                  <v-avatar
                    left
                    color="indigo"
                    class=" white--text"
                  >
                    {{ data.item.name.charAt(0) }}
                  </v-avatar>
                  {{ data.item.name }}
                </v-chip>
              </template>

              <template #item="{ item }">
                <v-list-item-avatar
                  color="indigo"
                  class="text-h5 font-weight-light white--text"
                >
                  {{ item.name.charAt(0) }}
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title v-text="item.name" />
                  <v-list-item-subtitle v-text="item.email" />
                </v-list-item-content>
              </template>
            </v-autocomplete>

            <v-textarea
              outlined
              placeholder="Type a message..."
              v-model="chatMessage"
            />
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn
              depressed
              @click="hideNewChatDialog"
            >
              Cancel
            </v-btn>

            <v-btn
              depressed
              color="primary"
              type="submit"
              :disabled="sendDisabled"
              :loading="sendingChat"
            >
              Send
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>
  </header>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import _ from 'lodash'

export default {
  data () {
    return {
      nav: null,
      chatDialog: false,

      isSearching: false,

      gettingUsers: false,
      userSearch: null,
      searchResult: null,

      chatTo: null,
      chatMessage: null,
      sendingChat: false,

      usersResult: []
    }
  },
  computed: {
    ...mapGetters({
      user: 'auth/USER',
      chats: 'chat/LIST'
    }),
    authenticated () {
      return !!this.user
    },
    userID () {
      return this.user?.id
    },
    userName () {
      return this.user?.name
    },
    userEmail () {
      return this.user?.email
    },
    _userAvatar () {
      return this.user?.avatar
    },
    userAvatar () {
      return {
        type: this._userAvatar ? 'image' : 'placeholder',
        avatar: this._userAvatar || this.userName[0].toUpperCase()
      }
    },
    sendDisabled () {
      return !this.chatTo ||
        !this.chatMessage
    }
  },
  watch: {
    userSearch: _.debounce(function (val) {
      if (val) {
        this.gettingUsers = true
        this._searchUser({
          search: val
        })
          .then((response) => {
            this.usersResult = response?.data
          })
          .finally(() => {
            this.gettingUsers = false
          })
      }
    }, 500)
  },
  methods: {
    ...mapActions({
      _logout: 'auth/LOGOUT',
      _searchUser: 'users/SEARCH',
      _sendChat: 'chat/SEND_NEW_MESSAGE',
      _getChats: 'chat/GET'
    }),
    toggleNav () {
      this.nav = !this.nav
    },
    showNewChatDialog () {
      this.chatDialog = true
    },
    hideNewChatDialog () {
      this.chatDialog = false
      this.chatTo = null
      this.chatMessage = null
    },
    sendNewChat () {
      this.sendingChat = true

      this._sendChat({
        to: this.chatTo,
        message: this.chatMessage
      })
        .then((response) => {
          if (response?.sent) {
            this._getChats()
            this.hideNewChatDialog()

            if (this.$router.currentRoute.params?.code !== response.code) {
              this.$router.push({
                name: 'chat.view',
                params: {
                  code: response.code
                }
              })
            }
          }
        })
        .finally(() => {
          this.sendingChat = false
        })
    },
    logout () {
      window.Echo.leave(`users.${this.userID}`)
      this._logout()
      this.$router.replace({
        name: 'auth.login'
      })
    }
  }
}
</script>
