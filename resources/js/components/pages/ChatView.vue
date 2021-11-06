<template>
  <v-container class="fill-height pa-0">
    <v-overlay
      :value="chatLoading"
      opacity="1"
      absolute
      color="white"
    >
      <v-progress-circular
        indeterminate
        color="primary"
      />
    </v-overlay>

    <template v-if="!chatLoading">
      <v-row no-gutters>
        <v-col style="position: relative;">
          <template v-if="$vuetify.breakpoint.smAndUp">
            <v-toolbar flat>
              <v-toolbar-title
                class="text-subtitle-1 font-weight-bold"
              >
                {{ interlocutorName }}
              </v-toolbar-title>
            </v-toolbar>
          </template>
          <v-divider />
          <div
            class="chat-container pa-3"
            ref="chatContainer"
          >
            <v-row>
              <template v-for="(msg, m) in messages">
                <v-col
                  :key="`message-${m}`"
                  cols="12"
                >
                  <v-card
                    :class="{
                      'ml-auto': msg.sender.id === userID
                    }"
                    max-width="360"
                    outlined
                    :color="(msg.sender.id === userID) ? 'accent' : 'grey lighten-3'"
                    :dark="msg.sender.id === userID"
                  >
                    <v-list-item three-line>
                      <v-list-item-content>
                        <v-list-item-title>
                          <pre class=" text-pre-line text-wrap text-body-1">
                            {{ msg.message }}
                          </pre>
                        </v-list-item-title>
                        <v-list-item-subtitle class=" text-caption mt-3">
                          {{ msg.sent }}
                        </v-list-item-subtitle>
                      </v-list-item-content>
                    </v-list-item>
                  </v-card>
                </v-col>
              </template>
            </v-row>
          </div>
          <v-divider />
          <div class="typing-area pa-3">
            <v-text-field
              autofocus
              rows="1"
              hide-details
              rounded
              outlined
              flat
              dense
              placeholder="Type a message"
              @keypress.enter="sendMessage"
              v-model="message"
            >
              <template #append-outer>
                <v-btn
                  icon
                  class=" mt-n2"
                  @click="sendMessage"
                >
                  <v-icon>mdi-send-outline</v-icon>
                </v-btn>
              </template>
            </v-text-field>
          </div>
        </v-col>
      </v-row>
    </template>
  </v-container>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
export default {
  props: {
    code: {
      type: String,
      required: true
    }
  },
  beforeRouteUpdate (to, from, next) {
    this.leaveChannel(from.params.code)
    next()
    this.showChat(to.params.code)
  },
  beforeRouteEnter (to, from, next) {
    next(vm => {
      vm.showChat()
    })
  },
  beforeRouteLeave (to, from, next) {
    this.leaveChannel(this.code)
    next()
  },
  data () {
    return {
      scrollHeight: 0,
      chatLoading: false,

      chatInfo: null,
      message: null,
      messages: []
    }
  },
  computed: {
    ...mapGetters({
      user: 'auth/USER'
    }),
    userID () {
      return this.user?.id
    },
    chatParticipants () {
      return this.chatInfo?.participants
    },
    interlocutor () {
      return this.chatParticipants?.filter(p => {
        return p.id !== this.userID
      })
    },
    interlocutorName () {
      if (this.chatInfo) {
        if (this.interlocutor?.length > 1) {
          return null
        } else {
          return this.interlocutor[0]?.name
        }
      }

      return null
    }
  },
  methods: {
    ...mapActions({
      _showChat: 'chat/SHOW',
      _getMessages: 'chat/GET_MESSAGES',
      _sendMessage: 'chat/SEND_MESSAGE'
    }),
    scrollToBottom () {
      this.$nextTick(() => {
        const container = this.$el.querySelector('.chat-container')
        container.scrollTop = container.scrollHeight
      })
    },
    showChat (code) {
      this.chatLoading = true
      this.chatInfo = null
      this.messages = []

      this._showChat({
        code: code || this.code
      })
        .then((response) => {
          if (response) {
            this.chatInfo = response?.data
            this.getMessages()
            this.connectToChat()
            this.chatLoading = false
          }
        })
        .catch((e) => {
          if (e) {
            this.$router.replace({
              name: 'chat.home'
            })
          }
        })
    },
    getMessages () {
      this._getMessages({
        code: this.code
      })
        .then((response) => {
          this.messages = response?.data
          this.scrollToBottom()
        })
        .catch((e) => {
          if (e) {
            this.$router.replace({
              name: 'chat.home'
            })
          }
        })
    },
    sendMessage () {
      if (this.message) {
        this._sendMessage({
          sender: this.userID,
          code: this.code,
          message: this.message
        })
          .then((response) => {
            if (response.sent) {
              this.getMessages()
              this.message = null
            }
          })
      }
    },
    leaveChannel (code) {
      window.Echo.leave(`chats.${code}`)
    },
    connectToChat () {
      window.Echo.private(`chats.${this.code}`)
        .listen('.new-chat-message', (e) => {
          if (e) {
            this.getMessages()
          }
        })
    }
  },
  watch: {

  }
}
</script>

<style lang="scss">
  $bp-xs: 600px;
  $bp-sm: 601px;
  $bp-md: 961px;
  $bp-lg: 1265px;
  $bp-xl: 1905px;

  @media (max-width: $bp-xs) {
    .chat-container {
      height: calc(100vh - 56px - 64px);
    }
  }

  @media (min-width: $bp-sm) {
    .chat-container {
      height: calc(100vh - (3 * 64px));
    }
  }

  .chat-container {
    overflow-y: auto;
    width: 100%;
  }

  .typing-area {
    display: flex;
    align-items: center;
    bottom: 0;
    width: 100%;
    height: 64px;
  }
</style>
