import Vue from 'vue'
import VueRouter from 'vue-router'

// =======================================================================
// NAV COMPONENTS
// =======================================================================
import DesktopChatNav from '../components/navs/desktop/ChatsNav'

// =======================================================================
// PAGE COMPONENTS
// =======================================================================
import AuthLogin from '../components/pages/AuthLogin'
import AuthRegister from '../components/pages/AuthRegister'
import ChatMain from '../components/pages/ChatMain'
import ChatHome from '../components/pages/ChatHome'
import ChatView from '../components/pages/ChatView'

Vue.use(VueRouter)

const AppName = 'Chat App'

const routes = [
  {
    path: '/login',
    name: 'auth.login',
    components: {
      default: AuthLogin
    },
    meta: {
      title: 'Login',
      guest: true
    }
  },
  {
    path: '/register',
    name: 'auth.register',
    components: {
      default: AuthRegister
    },
    meta: {
      title: 'Register',
      guest: true
    }
  },
  {
    path: '/',
    components: {
      default: ChatMain,
      navigation: DesktopChatNav
    },
    children: [
      {
        path: '/',
        name: 'chat.home',
        component: ChatHome,
        meta: {
          title: 'Chats',
          auth: true
        }
      },
      {
        path: '/m/:code',
        name: 'chat.view',
        component: ChatView,
        props: true,
        meta: {
          title: 'Chats',
          auth: true
        }
      }
    ]
  }
]

const router = new VueRouter({
  mode: 'history', // Navigation Mode
  base: '/', // Base Route
  scrollBehavior (to, from, savedPosition) {
    return { x: 0, y: 0 } // Scroll Behavior on each navigation change
  },
  routes: routes
})

router.beforeEach((to, from, next) => {
  // Change Title on route change
  document.title = `${to.meta?.title} | ${AppName}`

  const userToken = localStorage.getItem('chat-app-token')

  if (to.matched.some(record => record.meta.auth)) {
    if (!userToken) {
      next({
        name: 'auth.login'
      })
    }
    next()
  } else if (to.matched.some(record => record.meta.guest)) {
    if (userToken) {
      next({
        name: 'chat.home'
      })
    }
    next()
  }

  next()
})

export default router
