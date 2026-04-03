import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Importation des vues (pages)
import LoginView from '../views/LoginView.vue'
import StorageView from '../views/StorageView.vue'
import GalleriesListView from '../views/GalleriesListView.vue'
import GalleryCreateView from '../views/GalleryCreateView.vue'
import GalleryDetailView from '../views/GalleryDetailView.vue'
import RegisterView from "@/views/RegisterView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/connexion',
      name: 'login',
      component: LoginView
    },
    {
      path: '/register',
      name:'inscription',
      component: RegisterView
    },
    {
      path: '/stockage',
      name: 'stockage',
      component: StorageView,
      meta: { requiresAuth: true }
    },
    {
      path: '/galeries',
      name: 'galeries',
      component: GalleriesListView,
      meta: { requiresAuth: true }
    },
    {
      path: '/galeries/nouvelle',
      name: 'creer-galerie',
      component: GalleryCreateView,
      meta: { requiresAuth: true }
    },
    {
      path: '/galeries/:id',
      name: 'galerie-detail',
      component: GalleryDetailView,
      meta: { requiresAuth: true }
    },
    {
      path: '/',
      redirect: '/galeries'
    }
  ]
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/connexion')
  } else {
    next()
  }
})

export default router