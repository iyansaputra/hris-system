import { createRouter, createWebHistory } from 'vue-router'

import MainLayout from '../layout/MainLayout.vue' 
import DashboardManager from '../views/DashboardManager.vue'
import Login from '../views/Login.vue'

const routes = [
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { requiresGuest: true } 
  },

  {
    path: '/', 
    component: MainLayout, 
    meta: { requiresAuth: true }, 
    children: [
      {
        path: '', 
        name: 'home',
        component: DashboardManager
      },
      {
      path: 'rekap-absensi',
      name: 'rekapAbsensi',
      component: () => import('../views/RekapAbsensiView.vue')
      },
      {
        path: 'leave-approve',
        name: 'leaveApprove',
        component: () => import('../views/LeaveApprovalView.vue')
      },
      {
        path: 'leave-request',
        name: 'leaveRequest',
        component: () => import('../views/LeaveRequestView.vue')
      },
      {
        path: 'update-profile',
        name: 'updateProfile',
        component: () => import('../views/ProfileView.vue')
      }
    ]
  },
  
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

router.beforeEach((to, from, next) => {
  const loggedIn = localStorage.getItem('token');

  if (to.matched.some(record => record.meta.requiresAuth) && !loggedIn) {
    next('/login'); 
  } 

  else if (to.matched.some(record => record.meta.requiresGuest) && loggedIn) {
    next('/'); 
  }

  else {
    next();
  }
});

export default router