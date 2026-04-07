<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { apiGestion } from '../services/api'

type Photo = {
  id: string | number
  title?: string
  file_name?: string
  storage_url?: string
  url?: string
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

const fileInput = ref<HTMLInputElement | null>(null)
const isUploading = ref(false)

const galleryId = route.params.id as string
const galleryTitle = (route.query.title as string) || (route.params.title as string) || 'Galerie'

const photographeId = computed(() => {
  return authStore.photographerId
})

const fetchPhotos = async () => {
  loading.value = true
  error.value = null
  try {
    const data = await apiGestion.getGalleryPhotos(photographeId.value, galleryId)
    photos.value = data.photos || []
  } catch (err) {
    error.value = 'Impossible de charger les photos'
    console.error('Erreur chargement photos:', err)
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  if (!authStore.isAuthenticated) {
    router.push('/connexion')
    return
  }

  gallery.value = {
    id: galleryId,
    title: galleryTitle,
    is_public: true,
    is_published: true
  }

  await fetchPhotos()
})

const handleFileUpload = async (event: Event) => {
  const target = event.target as HTMLInputElement
  if (!target.files || target.files.length === 0) return

  const file = target.files[0]
  isUploading.value = true

  try {
    await apiGestion.uploadPhoto(file, photographeId.value, galleryId)
    
    await fetchPhotos()
  } catch (err) {
    console.error('Erreur lors de l\'upload :', err)
    alert('Une erreur est survenue lors de l\'envoi de la photo.')
  } finally {
    isUploading.value = false
    if (fileInput.value) fileInput.value.value = ''
  }
}

const handleDelete = async (photoId: string | number) => {
  if (!window.confirm("Es-tu sûr de vouloir supprimer définitivement cette photo ?")) return

  try {
    await apiGestion.deletePhoto(photographeId.value, galleryId, photoId)
    
    photos.value = photos.value.filter(p => p.id !== photoId)
  } catch (err) {
    console.error('Erreur suppression :', err)
    alert('Impossible de supprimer la photo.')
  }
}

const goBack = () => {
  router.push('/galeries')
}

const getPhotoUrl = (photo: Photo) => {
  if (photo.url) {
    return photo.url
  }
  if (photo.storage_url) {
    return photo.storage_url
  }
  return `${import.meta.env.VITE_API_BASE_URL}/photos/${photo.id}/storage`
}
</script>

<template>
  <div class="gallery-detail">
    <header class="header">
      <div class="header-left">
        <button class="btn-back" @click="goBack">← Retour</button>
        <h1>{{ gallery?.title || 'Galerie' }}</h1>
      </div>

      <div class="header-actions">
        <input 
          type="file" 
          accept="image/*" 
          ref="fileInput" 
          class="hidden-input" 
          @change="handleFileUpload" 
        />
        <button 
          class="btn-primary btn-upload" 
          @click="fileInput?.click()" 
          :disabled="isUploading"
        >
          <span v-if="isUploading" class="spinner"></span>
          {{ isUploading ? 'Envoi en cours...' : '+ Ajouter une photo' }}
        </button>
      </div>
    </header>

    <div v-if="loading" class="loading">
      <p>Chargement des photos...</p>
    </div>

    <div v-else-if="error" class="error">
      <p>{{ error }}</p>
      <button class="btn-primary" @click="fetchPhotos">Réessayer</button>
    </div>

    <div v-else-if="photos.length" class="photos-grid">
      <div v-for="photo in photos" :key="photo.id" class="photo-card">
        <img :src="getPhotoUrl(photo)" :alt="photo.title || 'Photo'" class="photo-image" />
        
        <div class="photo-info">
          <p class="photo-title" :title="photo.file_name">{{ photo.title || photo.file_name || 'Sans titre' }}</p>
          <button class="btn-delete" @click.stop="handleDelete(photo.id)" title="Supprimer">
            🗑️
          </button>
        </div>

      </div>
    </div>

    <div v-else class="empty">
      <p>Aucune photo dans cette galerie</p>
      <button class="btn-primary" @click="fileInput?.click()">Ajouter la première photo</button>
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
  flex-wrap: wrap;
  gap: 16px;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.header h1 {
  margin: 0;
  font-size: 24px;
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

.hidden-input {
  display: none;
}

.btn-upload {
  display: flex;
  align-items: center;
  gap: 8px;
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
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
  display: flex;
  flex-direction: column;
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
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
}

.photo-title {
  margin: 0;
  font-size: 14px;
  color: #374151;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  flex: 1;
}

.btn-delete {
  background: #fee2e2;
  border: none;
  border-radius: 6px;
  padding: 6px;
  cursor: pointer;
  transition: background 0.2s;
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-delete:hover {
  background: #fca5a5;
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
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
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

.btn-primary:hover:not(:disabled) {
  opacity: 0.9;
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}
</style>