import { createRouter, createWebHistory } from 'vue-router'

// Importation des vues (pages)
import LoginView from '../views/LoginView.vue'
import StorageView from '../views/StorageView.vue'
import GalleriesListView from '../views/GalleriesListView.vue'
import GalleryCreateView from '../views/GalleryCreateView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/connexion',
      name: 'login',
      component: LoginView
    },
    {
      path: '/stockage',
      name: 'stockage',
      component: StorageView
    },
    {
      path: '/galeries',
      name: 'galeries',
      component: GalleriesListView
    },
    {
      path: '/galeries/nouvelle',
      name: 'creer-galerie',
      component: GalleryCreateView
    },
    {
      path: '/',
      redirect: '/galeries'
    }
  ]
})

export default router