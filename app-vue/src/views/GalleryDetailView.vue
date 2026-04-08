<script setup lang="ts">
import {ref, onMounted, computed} from 'vue'
import {useRouter, useRoute} from 'vue-router'
import {useAuthStore} from '../stores/auth'
import {apiGestion} from '../services/api'
// Définitions TypeScript
type Photo = {
  id: string | number
  title?: string
  file_name?: string
  storage_url?: string
  url?: string
  uploaded_at?: string
  galerie_id?: string | null
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

// Variables pour la Modale de Sélection S3
const showStorageModal = ref(false)
const storagePhotos = ref<Photo[]>([])
const loadingStorage = ref(false)
// Utilisation de Set pour stocker des ID uniques (les photos sélectionnées en cochant)
const selectedPhotos = ref<Set<string | number>>(new Set())
const isAddingPhotos = ref(false)

// Extraction des données depuis l'URL
const galleryId = route.params.id as string
const galleryTitle = (route.query.title as string) || (route.params.title as string) || 'Galerie'
const routeLayout = (route.query.layout as string) || undefined

const photographeId = computed(() => authStore.photographerId)

// Récupère les infos texte de la galerie
const fetchGalleryMeta = async () => {
  try {
    const data = await apiGestion.getGalerie(galleryId)
    const g = data.galerie || data.item || data
    if (g && typeof g === 'object') {
      gallery.value = {
        id: galleryId,
        title: g.title || g.titre || galleryTitle,
        description: g.description,
        is_public: !!g.is_public,
        is_published: !!g.is_published,
        layout: g.layout || routeLayout || 'grid'
      }
    }
  } catch {
    // fallback
    if (gallery.value) {
      gallery.value.layout = gallery.value.layout || routeLayout || 'grid'
    }
  }
}

// Récupère les photos liées à cette galerie
const fetchPhotos = async () => {
  loading.value = true
  error.value = null
  try {
    const data = await apiGestion.getGalleryPhotos(photographeId.value, galleryId)
    photos.value = data.photos || []
  } catch (err) {
    error.value = 'Impossible de charger les photos'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  if (!authStore.isAuthenticated) {
    router.push('/connexion')
    return
  }
  await fetchGalleryMeta()
  await fetchPhotos()
})

// Ouvre la fenêtre S3 et filtre les images pour ne pas afficher celles déjà présentes
const openStorageModal = async () => {
  showStorageModal.value = true
  loadingStorage.value = true
  selectedPhotos.value.clear()
  try {
    const data = await apiGestion.getStoragePhotos(photographeId.value)
    const allPhotos = data.photos || data || []
    // On crée un Set des IDs des photos déjà dans la galerie
    const currentPhotoIds = new Set(photos.value.map(p => p.id));
    // On ne garde que les photos S3 qui NE SONT PAS dans currentPhotoIds
    storagePhotos.value = allPhotos.filter((p: Photo) => !currentPhotoIds.has(p.id));  } catch (err) {
    alert("Impossible de charger votre bibliothèque")
  } finally {
    loadingStorage.value = false
  }
}

// Fonction appelée quand on clique sur une photo
const togglePhotoSelection = (id: string | number) => {
  if (selectedPhotos.value.has(id)) {
    selectedPhotos.value.delete(id)
  } else {
    selectedPhotos.value.add(id)
  }
}

// Valide la sélection et envoie au backend
const confirmSelection = async () => {
  if (selectedPhotos.value.size === 0) return
  isAddingPhotos.value = true

  try {
    // Array.from(set) convertit le Set en tableau classique.
    // On crée un tableau de "Promesses" API (une requête PATCH par photo)
    const promises = Array.from(selectedPhotos.value).map(photoId =>
        apiGestion.linkPhotoToGallery(photographeId.value, photoId, galleryId)
    )
    await Promise.all(promises)

    showStorageModal.value = false
    await fetchPhotos()
  } catch (err) {
    alert("Une erreur est survenue lors de l'ajout.")
  } finally {
    isAddingPhotos.value = false
  }
}

const setAsCover = async (photoId: string | number) => {
  try {
    await apiGestion.updateGalerie(photographeId.value, galleryId, {
      cover_photo_id: photoId
    })
    alert('Image définie comme couverture avec succès!')
  } catch (err) {
    alert('Erreur lors de la modification de la couverture.')
  }
}

const handleRemoveFromGallery = async (photoId: string | number) => {
  if (!window.confirm("Voulez-vous retirer cette photo de la galerie ? (Elle restera dans votre stockage global)")) return

  try {
    await apiGestion.linkPhotoToGallery(photographeId.value, photoId, galleryId, 'remove')
    
    photos.value = photos.value.filter(p => p.id !== photoId)
  } catch (err) {
    alert('Impossible de retirer la photo.')
  }
}

const goBack = () => router.push('/galeries')

// Nettoie et formate l'URL de l'image si elle vient du serveur local ou S3
const getPhotoUrl = (photo: Photo) => {
  let link = photo.url || photo.storage_url || photo.s3_key || '';

  // Supprime la duplication localhost si elle existe
  if (link.includes('http://localhost:8333/photopro-galeries/http')) {
    link = link.replace('http://localhost:8333/photopro-galeries/', '');
  }

  // Si c'est un chemin relatif (ex: /photos/123), on ajoute l'hôte
  if (link && !link.startsWith('http')) {
    return `http://localhost:8333/photopro-galeries/${link}`;
  }

  return link;
}

// Définit la disposition d'affichage (grille classique ou maçonnerie/Pinterest)
const normalizedLayout = computed<'grid' | 'masonry'>(() => {
  const l = (gallery.value?.layout || 'grid').toString().toLowerCase()
  return l === 'masonry' ? 'masonry' : 'grid'
})
</script>

<template>
  <div class="gallery-detail">
    <header class="header">
      <div class="header-left">
        <button class="btn-back" @click="goBack">← Retour</button>
        <h1 class="titregalerie">{{ gallery?.title || 'Galerie' }}</h1>
      </div>

      <div class="header-actions">
        <button class="btn-primary btn-upload" @click="openStorageModal">
          + Ajouter depuis mon stockage
        </button>
      </div>
    </header>

    <div v-if="loading" class="loading">Chargement des photos...</div>
    <div v-else-if="error" class="error"><p>{{ error }}</p>
      <button class="btn-primary" @click="fetchPhotos">Réessayer</button>
    </div>

    <div v-else-if="photos.length" :class="['photos-grid', normalizedLayout === 'masonry' ? 'photos-grid--masonry' : 'photos-grid--grid']">
      <div v-for="photo in photos" :key="photo.id" class="photo-card">
        <img :src="getPhotoUrl(photo)" :alt="photo.title || 'Photo'" class="photo-image"/>

        <div class="photo-info">
          <p class="photo-title" :title="photo.file_name">{{ photo.title || photo.file_name || 'Sans titre' }}</p>
          
          <div class="photo-actions">
            <button class="btn-cover" @click.stop="setAsCover(photo.id)" title="Définir comme couverture">
              Définir comme couverture
            </button>
            <button class="btn-delete" @click.stop="handleRemoveFromGallery(photo.id)" title="Retirer de la galerie">
              Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="empty">
      <p>Aucune photo dans cette galerie</p>
      <button class="btn-primary" @click="openStorageModal">Ajouter la première photo</button>
    </div>

    <div v-if="showStorageModal" class="modal-overlay" @click.self="showStorageModal = false">
      <div class="modal-content">
        <header class="modal-header">
          <h2>Ma Bibliothèque S3</h2>
          <button class="btn-close" @click="showStorageModal = false">✕</button>
        </header>

        <div class="modal-body">
          <div v-if="loadingStorage" class="loading">Chargement de votre bibliothèque...</div>
          <div v-else-if="storagePhotos.length === 0" class="empty">
            <p>Aucune nouvelle photo disponible. Allez dans l'onglet Stockage pour uploader des images.</p>
          </div>
          <div v-else class="storage-grid">
            <div
                v-for="photo in storagePhotos"
                :key="photo.id"
                class="storage-item"
                :class="{ 'selected': selectedPhotos.has(photo.id) }"
                @click="togglePhotoSelection(photo.id)"
            >
              <img :src="getPhotoUrl(photo)" :alt="photo.file_name"/>
              <div v-if="selectedPhotos.has(photo.id)" class="check-icon">✓</div>
            </div>
          </div>
        </div>

        <footer class="modal-footer">
          <button class="btn-outline" @click="showStorageModal = false">Annuler</button>
          <button class="btn-primary" @click="confirmSelection" :disabled="selectedPhotos.size === 0 || isAddingPhotos">
            <span v-if="isAddingPhotos" class="spinner"></span>
            {{ isAddingPhotos ? 'Ajout en cours...' : `Ajouter ${selectedPhotos.size} photo(s)` }}
          </button>
        </footer>
      </div>
    </div>

  </div>
</template>

<style scoped>
.gallery-detail {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
  font-family: Inter, sans-serif;
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
  gap: 12px;
  min-width: 0;
}

.titregalerie{
  padding-left:50px;
}

.header h1 {
  margin: 0;
  font-size: 24px;
  white-space: nowrap;
  text-overflow: ellipsis;
}

.btn-back {
  background: transparent;
  border: none;
  color: #374151;
  font-size: 16px;
  cursor: pointer;
  padding: 8px 8px 8px 0;
  border-radius: 8px;
  transition: background 0.2s;
}

.btn-back:hover {
  background: #f3f4f6;
}

.btn-primary {
  background: linear-gradient(90deg, #1f2937, #374151);
  color: #fff;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-outline {
  border: 1px solid #e6edf3;
  background: #fff;
  padding: 10px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: 500;
}

.photos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
  margin-top: 24px;
}

.photos-grid--grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.photos-grid--masonry {
  display: block;
  column-count: 3;
  column-gap: 20px;
}

@media (max-width: 1100px) {
  .photos-grid--masonry { column-count: 2; }
}

@media (max-width: 700px) {
  .photos-grid--masonry { column-count: 1; }
}

.photos-grid--masonry .photo-card {
  break-inside: avoid;
  display: inline-block;
  width: 100%;
  margin: 0 0 20px;
}

.photos-grid--masonry .photo-image {
  height: auto;
}

.photo-card {
  border-radius: 12px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 4px 12px rgba(2, 6, 23, 0.08);
  transition: transform 0.15s ease;
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

.photos-grid--masonry .photo-info {
  flex-direction: column;
  align-items: flex-start;
  gap: 12px;
}

.photos-grid--masonry .photo-title {
  white-space: normal;
  overflow: visible;
  text-overflow: clip;
}

.photos-grid--masonry .photo-actions {
  display: flex;
  gap: 8px;
  width: 100%;
  justify-content: flex-start;
  flex-wrap: wrap;
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
}

.btn-delete:hover {
  background: #fca5a5;
}

.loading, .error, .empty {
  text-align: center;
  padding: 60px 20px;
  color: #6b7280;
}

.empty {
  background: linear-gradient(180deg, #fbfdff, #ffffff);
  border-radius: 12px;
  border: 1px dashed #e6edf3;
}

/* 🌟 STYLES DE LA MODALE */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(11, 18, 32, 0.7);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: #fff;
  width: 90%;
  max-width: 1000px;
  height: 85vh;
  border-radius: 16px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 24px 48px rgba(0, 0, 0, 0.2);
}

.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h2 {
  margin: 0;
  font-size: 18px;
}

.btn-close {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #6b7280;
}

.modal-body {
  padding: 20px;
  flex: 1;
  overflow-y: auto;
  background: #f9fafb;
}

.modal-footer {
  padding: 16px 24px;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  background: #fff;
}

.storage-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 16px;
}

.storage-item {
  position: relative;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  border: 3px solid transparent;
  transition: all 0.2s;
  background: #fff;
}

.storage-item:hover {
  transform: scale(1.02);
}

.storage-item.selected {
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

.storage-item img {
  width: 100%;
  height: 140px;
  object-fit: cover;
  display: block;
}

.check-icon {
  position: absolute;
  top: 8px;
  right: 8px;
  background: #3b82f6;
  color: #fff;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 14px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>