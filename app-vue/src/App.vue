<script setup lang="ts">
import { RouterView, RouterLink } from 'vue-router'
import { useAuthStore } from "@/stores/auth.ts";

const authStore = useAuthStore();

const confirmLogout = (event: Event) => {
  if (!window.confirm("Êtes-vous sûr de vouloir vous déconnecter ?")) {
    event.preventDefault();
  } else {
    authStore.logout();
  }
};
</script>

<template>
  <header class="navbar">
    <div class="nav-brand">PhotoPro Admin</div>
    <div class="user-status" v-if="authStore.isAuthenticated">
      <span class="status-label">Connecté en tant que:</span>
      <span class="user-email">{{ authStore.pseudo }}</span>
    </div>
    <nav>
      <RouterLink v-if="authStore.isAuthenticated" to="/galeries">Mes Galeries</RouterLink>
      <RouterLink v-if="authStore.isAuthenticated" to="/stockage">Mon Stockage</RouterLink>
      <RouterLink v-if="authStore.isAuthenticated" to="/profil">Mon Profil</RouterLink>

      <RouterLink v-if="!authStore.isAuthenticated" to="/connexion">Connexion</RouterLink>
      <RouterLink v-if="!authStore.isAuthenticated" to="/register">Inscription</RouterLink>
      <RouterLink v-if="authStore.isAuthenticated" to="/connexion" @click="confirmLogout">Déconnexion</RouterLink>
    </nav>
  </header>

  <main>
    <RouterView />
  </main>
</template>