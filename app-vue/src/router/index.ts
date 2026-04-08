import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

import LoginView from '../views/LoginView.vue'
import StorageView from '../views/StorageView.vue'
import GalleriesListView from '../views/GalleriesListView.vue'
import GalleryCreateView from '../views/GalleryCreateView.vue'
import GalleryDetailView from '../views/GalleryDetailView.vue'
import RegisterView from "@/views/RegisterView.vue";


const router = createRouter({
  //permet d'avoir des urls sans le symbole # (ex: /galeries au lieu de /#/galeries)
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/connexion',     // L'URL tapée dans le navigateur
      name: 'login',          // Nom technique pour faire des redirections faciles
      component: LoginView    // Le fichier affiché
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
      // ":id" est un paramètre dynamique. Si l'URL est /galeries/123, id vaudra "123".
      path: '/galeries/:id',
      name: 'galerie-detail',
      component: GalleryDetailView,
      meta: { requiresAuth: true }
    },
    {
      path: '/profil',
      name: 'profil',
      //Ce composant n'est téléchargé par le navigateur 
      // QUE lorsque l'utilisateur clique sur la page Profil. Ça accélère le chargement initial.
      component: () => import('../views/ProfileView.vue'), 
      meta: { requiresAuth: true }
    },
    {
      path: '/',
      redirect: '/galeries'
    }
  ]
})

// Cette fonction s'exécute avant chaque changement de page
router.beforeEach((to, from, next) => {
  // On récupère le store d'authentification
  const authStore = useAuthStore()

  // Si la route visée ("to") possède la balise "requiresAuth: true" 
  // ET que l'utilisateur N'EST PAS connecté ("!authStore.isAuthenticated")
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/connexion') // On le renvoie de force sur la page de connexion
  } else {
    next() // Sinon, on le laisse passer
  }
})

export default router