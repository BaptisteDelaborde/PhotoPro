<script setup>
const config = useRuntimeConfig()
const apiBase = import.meta.server ? config.apiInternalUrl : config.public.apiFrontofficeUrl

const { data: galeries, pending, error } = await useFetch(`${apiBase}/galeries/publiques`)
</script>

<template>
  <div class="container">
    <h1>Galeries Publiques</h1>

    <div v-if="error" style="color: red;">
      Erreur : {{ error.message }}
    </div>

    <div v-else-if="pending">
      Chargement des galeries...
    </div>

    <div v-else-if="galeries && galeries.length > 0">
      <NuxtLink
          v-for="galerie in galeries"
          :key="galerie.id"
          :to="`/galerie/${galerie.id}`"
          class="galerie-card"
          style="display: block; text-decoration: none; color: inherit;"
      >

        <h2>{{ galerie.title }}</h2>

        <p v-if="galerie.description" class="description">
          {{ galerie.description }}
        </p>

        <img
            v-if="galerie.cover_photo_id"
            :src="`${config.public.s3Url}/${galerie.cover_photo_id}`"
            :alt="galerie.title"
            class="cover-image"
        />
        <div v-else class="no-image">
          Aucune photo de couverture
        </div>

      </NuxtLink>
    </div>

    <div v-else>
      Aucune galerie publique pour le moment.
    </div>
  </div>
</template>

<style src="../assets/css/main.css" scoped></style>
