import { createRouter, createWebHistory } from 'vue-router'
import dashboard from '../components/dashboard.vue'
import login from '../components/login.vue'
import register from '../components/register.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: login
    },
    {
      path: '/dashboard',
      name: 'Dashboard',
      component: dashboard
    },
    {
      path: '/register',
      name: 'Register',
      component: register
    },
    // Catch-all route for handling undefined paths or 404 errors
    {
      path: '/:catchAll(.*)',
      redirect: '/login' // Redirect any undefined paths to the login page
    }
  ]
})


export default router
