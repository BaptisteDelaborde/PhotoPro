<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

const config = useRuntimeConfig()
const apiBase = import.meta.server ? config.apiInternalUrl : config.public.apiFrontofficeUrl

const route = useRoute()
const galerieId = route.params.id

const { data: photos, pending, error } = await useFetch(`${apiBase}/galeries/${galerieId}/photos`)

const photosWithUrls = computed(() => {
  if (!photos.value) return []
  return photos.value
})

const isLightboxOpen = ref(false)
const selectedIndex = ref(0)

const openLightbox = (index) => {
  selectedIndex.value = index
  isLightboxOpen.value = true
}
</script>

<template>
  <div class="container">
    <NuxtLink to="/" class="back-btn">← Retour aux galeries</NuxtLink>

    <h1>Photos de la galerie</h1>

    <div v-if="error" style="color: red;">
      Erreur lors du chargement des photos : {{ error.message }}
    </div>

    <div v-else-if="pending">
      Chargement des photos...
    </div>

    <div v-else-if="photosWithUrls && photosWithUrls.length > 0">
      <div class="photo-grid">
        <div
            v-for="(photo, index) in photosWithUrls"
            :key="photo.id"
            class="photo-item"
            @click="openLightbox(index)"
        >
          <img :src="photo.url" :alt="photo.title || 'Photo'" loading="lazy" />
        </div>
      </div>
    </div>

    <div v-else>
      <p>Cette galerie ne contient pas encore de photos.</p>
    </div>

    <ClientOnly>
      <Teleport to="body">
        <ImageLightbox
            v-if="isLightboxOpen"
            :images="photosWithUrls"
            :initial-index="selectedIndex"
            @close="isLightboxOpen = false"
        />
      </Teleport>
    </ClientOnly>
  </div>
</template>

<style scoped>
.container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 20px;
  font-family: sans-serif;
}
.back-btn {
  display: inline-block;
  margin-bottom: 20px;
  color: #2c3e50;
  text-decoration: none;
  font-weight: bold;
}
.back-btn:hover { text-decoration: underline; }
.photo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 15px;
  margin-top: 20px;
}
.photo-item {
  cursor: pointer;
  overflow: hidden;
  border-radius: 8px;
  aspect-ratio: 1 / 1;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.photo-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}
.photo-item:hover img {
  transform: scale(1.05);
}
</style>