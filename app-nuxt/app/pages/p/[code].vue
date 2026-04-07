<template>
  <div class="container">
    <div v-if="error" class="error-msg">
      <h2>Oups !</h2>
      <p>Désolé, ce lien est invalide ou la galerie n'est pas disponible.</p>
    </div>

    <div v-else-if="pending">
      <p>Vérification de votre accès sécurisé...</p>
    </div>

    <div v-else-if="galerie">
      <div class="header-prive">
        <h1>{{ galerie.title }}</h1>
        <span class="badge">Accès Privé</span>
      </div>

      <p v-if="galerie.description" class="description">{{ galerie.description }}</p>

      <hr class="divider">

      <div v-if="photosError" class="error-msg">Erreur lors du chargement des photos.</div>
      <div v-else-if="photosPending">Chargement de vos photos...</div>

      <div v-else-if="photos && photos.length > 0" class="photo-grid">
        <div
            v-for="(photo, index) in photos"
            :key="photo.id"
            class="photo-item"
            @click="openLightbox(index)"
        >
          <img :src="`${config.public.s3Url}/${photo.s3_key}`" :alt="photo.title || 'Photo'" loading="lazy" />
        </div>
      </div>

      <div v-else class="empty-state">
        <p>Le photographe n'a pas encore ajouté de photos à cette galerie.</p>
      </div>
    </div>

    <ClientOnly>
      <Teleport to="body">
        <ImageLightbox
            v-if="isLightboxOpen"
            :images="formattedPhotosForLightbox"
            :initial-index="selectedIndex"
            @close="isLightboxOpen = false"
        />
      </Teleport>
    </ClientOnly>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

const config = useRuntimeConfig()
const route = useRoute()
const codeAcces = route.params.code

const { data: galerie, pending, error } = await useFetch(`${config.public.apiFrontofficeUrl}/galeries/code/${codeAcces}`)

const { data: photos, pending: photosPending, error: photosError } = await useFetch(() => {
  return galerie.value ? `${config.public.apiFrontofficeUrl}/galeries/${galerie.value.id}/photos` : null
})

const isLightboxOpen = ref(false)
const selectedIndex = ref(0)

const formattedPhotosForLightbox = computed(() => {
  if (!photos.value) return []
  return photos.value.map(photo => ({
    url: `${config.public.s3Url}/${photo.s3_key || photo.file_name}`,
    title: photo.title || ''
  }))
})

const openLightbox = (index) => {
  selectedIndex.value = index
  isLightboxOpen.value = true
}
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px;
  font-family: 'Helvetica Neue', Arial, sans-serif;
}
.header-prive {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 10px;
}
.header-prive h1 { margin: 0; }
.badge {
  background-color: #2c3e50;
  color: white;
  font-size: 0.8rem;
  padding: 4px 12px;
  border-radius: 20px;
  text-transform: uppercase;
  letter-spacing: 1px;
}
.description { color: #666; font-size: 1.1rem; margin-bottom: 30px;}
.divider { border: 0; height: 1px; background: #eee; margin: 30px 0; }
.error-msg { color: #e74c3c; text-align: center; margin-top: 50px; padding: 20px; background: #fdf0ed; border-radius: 8px;}
.empty-state { text-align: center; color: #7f8c8d; padding: 50px; background: #f9f9f9; border-radius: 8px;}
.photo-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}
.photo-item {
  cursor: pointer;
  overflow: hidden;
  border-radius: 8px;
  aspect-ratio: 1 / 1;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.photo-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}
.photo-item:hover img {
  transform: scale(1.08);
}
</style>
