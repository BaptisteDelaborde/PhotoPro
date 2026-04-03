<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { apiGestion } from '../services/api'

type Photo = {
  id: string | number
  title?: string
  file_name?: string
  storage_url?: string
  uploaded_at?: string
}

type Gallery = {
  id: string | number
  title: string
  description?: string
  is_public: boolean
  is_published: boolean
}

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const gallery = ref<Gallery | null>(null)
const photos = ref<Photo[]>([])
const loading = ref(true)
const error = ref<string | null>(null)

const galleryId = route.params.id as string

onMounted(async () => {
  if (!authStore.isAuthenticated) {
    router.push('/connexion')
    return
  }

  try {
    const data = await apiGestion.getGalleryPhotos(galleryId)
    photos.value = (Array.isArray(data) ? data : data.items || [])

    gallery.value = {
      id: galleryId,
      title: 'Galerie',
      is_public: true,
      is_published: true
    }
  } catch (err) {
    error.value = 'Impossible de charger les photos'
    console.error('Erreur chargement photos:', err)
  } finally {
    loading.value = false
  }
})

const goBack = () => {
  router.push('/galeries')
}

const getPhotoUrl = (photo: Photo) => {
  if (photo.storage_url) {
    return photo.storage_url
  }
  return `${import.meta.env.VITE_API_BASE_URL}/photos/${photo.id}/storage`
}
</script>

<template>
  <div class="gallery-detail">
    <header class="header">
      <button class="btn-back" @click="goBack">← Retour</button>
      <h1>{{ gallery?.title || 'Galerie' }}</h1>
      <div style="width: 40px;"></div>
    </header>

    <div v-if="loading" class="loading">
      <p>Chargement des photos...</p>
    </div>

    <div v-else-if="error" class="error">
      <p>{{ error }}</p>
      <button class="btn-primary" @click="goBack">Retour aux galeries</button>
    </div>

    <div v-else-if="photos.length" class="photos-grid">
      <div v-for="photo in photos" :key="photo.id" class="photo-card">
        <img :src="getPhotoUrl(photo)" :alt="photo.title || 'Photo'" class="photo-image" />
        <div class="photo-info">
          <p class="photo-title">{{ photo.title || photo.file_name || 'Sans titre' }}</p>
        </div>
      </div>
    </div>

    <div v-else class="empty">
      <p>Aucune photo dans cette galerie</p>
      <button class="btn-primary" @click="goBack">Retour aux galeries</button>
    </div>
  </div>
</template>

<style scoped>
.gallery-detail {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  color: #0b1220;
  min-height: 100vh;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 32px;
  padding-bottom: 16px;
  border-bottom: 1px solid #e6edf3;
}

.header h1 {
  margin: 0;
  font-size: 24px;
  flex: 1;
  text-align: center;
}

.btn-back {
  background: transparent;
  border: none;
  color: #374151;
  font-size: 16px;
  cursor: pointer;
  padding: 8px 12px;
  border-radius: 8px;
  transition: background 0.2s;
}

.btn-back:hover {
  background: #f3f4f6;
}

.photos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
  margin-top: 24px;
}

.photo-card {
  border-radius: 12px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 4px 12px rgba(2, 6, 23, 0.08);
  transition: transform 0.15s ease, box-shadow 0.15s ease;
  cursor: pointer;
}

.photo-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px rgba(2, 6, 23, 0.12);
}

.photo-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
  display: block;
}

.photo-info {
  padding: 12px;
}

.photo-title {
  margin: 0;
  font-size: 14px;
  color: #374151;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.loading,
.error,
.empty {
  text-align: center;
  padding: 60px 20px;
  color: #6b7280;
}

.error {
  color: #991b1b;
  background: #fee2e2;
  border-radius: 12px;
  padding: 40px;
}

.empty {
  background: linear-gradient(180deg, #fbfdff, #ffffff);
  border-radius: 12px;
  border: 1px dashed #e6edf3;
}

.btn-primary {
  background: linear-gradient(90deg, #1f2937, #374151);
  color: #fff;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  margin-top: 16px;
}

.btn-primary:hover {
  opacity: 0.9;
}
</style>

