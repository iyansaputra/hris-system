import { createRouter, createWebHistory } from 'vue-router'

//Import View
import HomeView from '../views/HomeView.vue'
import MainLayout from '../layout/MainLayout.vue'
import Login from '../views/Login.vue'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/dashboard', 
    component: MainLayout, 
    children: [ 
      { path: '', 
        component: HomeView 
      } 
    ]
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
