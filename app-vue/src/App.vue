<script setup lang="ts">
import { RouterView, RouterLink } from 'vue-router'
</script>

<template>
  <header class="navbar">
    <div class="nav-brand">PhotoPro Admin</div>
    <nav>
      <RouterLink to="/galeries">Mes Galeries</RouterLink>
      <RouterLink to="/stockage">Mon Stockage</RouterLink>
    </nav>
  </header>

  <main>
    <RouterView />
  </main>
</template>