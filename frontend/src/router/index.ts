import { createRouter, createWebHistory } from 'vue-router'
import Login from '@/views/Login.vue'
import Dashboard from '../views/Dashboard.vue'
import Users from '../views/Users.vue'

const routes = [
  {
    path: '/',
    redirect: '/login',
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
  },
  {
    path: '/users',
    name: 'Users',
    component: Users,
  },
]


const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
