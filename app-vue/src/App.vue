<script setup lang="ts">
import { RouterView, RouterLink } from 'vue-router'
import {useAuthStore} from "@/stores/auth.ts";

const authStore = useAuthStore();
</script>

<template>
  <header class="navbar">
    <div class="nav-brand">PhotoPro Admin</div>
    <nav>
      <RouterLink v-if="authStore.isAuthenticated" to="/galeries">Mes Galeries</RouterLink>
      <RouterLink v-if="authStore.isAuthenticated" to="/stockage">Mon Stockage</RouterLink>
      <RouterLink v-if="!authStore.isAuthenticated" to="/connexion">Connexion</RouterLink>
      <RouterLink v-if="!authStore.isAuthenticated" to="/register">Inscription</RouterLink>
      <RouterLink v-if="authStore.isAuthenticated" to="/connexion" @click="authStore.logout">Déconnexion</RouterLink>
    </nav>
  </header>

  <main>
    <RouterView />
  </main>
</template>