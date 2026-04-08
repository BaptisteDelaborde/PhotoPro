<script setup>
import { ref, onMounted, computed } from 'vue'
import { apiGestion } from '@/services/api.js'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const photographeId = computed(() => authStore.photographerId)

const selectedFile = ref(null)
const isUploading = ref(false)
const uploadedPhotos = ref([])
const loading = ref(true)

const fetchStoragePhotos = async () => {
    loading.value = true
    try {
        const data = await apiGestion.getStoragePhotos(photographeId.value)
        uploadedPhotos.value = data.photos || data || []
    } catch (error) {
        console.error("Erreur de chargement du stockage", error)
    } finally {
        loading.value = false
    }
}

onMounted(async () => {
    if (authStore.isAuthenticated) {
        await fetchStoragePhotos()
    }
})

const handleFileChange = (event) => {
    const target = event.target
    if (target.files && target.files.length > 0) {
        selectedFile.value = target.files[0]
    }
}

const uploadPhoto = async () => {
    if (!selectedFile.value || !photographeId.value) return

    isUploading.value = true

    try {
        await apiGestion.uploadPhotoToStorage(selectedFile.value, photographeId.value)

        await fetchStoragePhotos()
    } catch (error) {
        console.error("Erreur d'upload", error)
        alert("Erreur lors de l'envoi de la photo.")
    } finally {
        isUploading.value = false
        selectedFile.value = null
    }
}

const getPhotoUrl = (photo) => {
    if (photo.url) return photo.url;

    if (photo.storage_url) {
        if (photo.storage_url.startsWith('http')) {
            return photo.storage_url;
        }
        return `http://localhost:8333/photopro-galeries/${photo.storage_url}`;
    }
    return '';
}
</script>

<template>
    <div class="storage-page">
        <header class="header">
            <h1>Espace de Stockage</h1>
            <p class="lead">Gérez et transférez vos photos sur vos serveurs sécurisés</p>
        </header>

        <section class="upload-card">
            <div class="drop-zone" :class="{ 'has-file': selectedFile }">
                <h3>Glissez-déposez ou sélectionnez une image</h3>
                <p v-if="selectedFile" class="file-name">{{ selectedFile.name }}</p>
                <p v-else class="instructions">Formats supportés: JPG, PNG, WEBP (Max: 10Mo)</p>

                <input type="file" @change="handleFileChange" accept="image/*" class="file-input" />
            </div>

            <button class="btn-primary" @click="uploadPhoto" :disabled="!selectedFile || isUploading">
                <span v-if="isUploading" class="spinner"></span>
                {{ isUploading ? 'Transfert S3 en cours...' : 'Envoyer vers le cloud' }}
            </button>
        </section>

        <section class="gallery-section">
            <div class="section-title">
                <h2>Ma Bibliothèque</h2>
                <span class="badge" v-if="!loading">{{ uploadedPhotos.length }} fichier(s)</span>
            </div>

            <div v-if="loading" class="loading-state">Chargement de votre bibliothèque...</div>

            <div v-else-if="uploadedPhotos.length > 0" class="photos-grid">
                <div v-for="photo in uploadedPhotos" :key="photo.id" class="photo-item">
                    <div class="photo-preview">
                        <img :src="getPhotoUrl(photo)" class="photo-img" alt="Photo" />
                    </div>
                    <div class="photo-info">
                        <p class="photo-url" :title="getPhotoUrl(photo)">Lien S3 actif</p>
                        <p class="photo-id">Statut: {{ photo.in_galleries_count > 0 ? `Dans ${photo.in_galleries_count}
                            galerie(s)` : 'Non assignée' }}</p>
                    </div>
                </div>
            </div>

            <div v-else class="empty-state">
                Aucune photo dans votre stockage pour le moment.
            </div>
        </section>
    </div>
</template>

<style scoped>
.storage-page {
    max-width: 900px;
    margin: 28px auto;
    padding: 20px;
    font-family: Inter, sans-serif;
    color: #0b1220;
}

.header {
    margin-bottom: 30px;
}

.header h1 {
    margin: 0;
    font-size: 26px;
    letter-spacing: -0.01em;
}

.lead {
    margin: 6px 0 0;
    color: #6b7280;
    font-size: 14px;
}

.upload-card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(2, 6, 23, 0.06);
    display: flex;
    flex-direction: column;
    gap: 20px;
    align-items: center;
}

.drop-zone {
    width: 100%;
    border: 2px dashed #e5e7eb;
    border-radius: 12px;
    padding: 40px 20px;
    text-align: center;
    position: relative;
    background: #fafafa;
    transition: all 0.2s ease;
    cursor: pointer;
}

.drop-zone:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.drop-zone.has-file {
    border-color: #10b981;
    background: #ecfdf5;
}

.icon-cloud {
    font-size: 40px;
    margin-bottom: 12px;
    opacity: 0.8;
}

.drop-zone h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #374151;
}

.instructions {
    margin: 6px 0 0;
    color: #9ca3af;
    font-size: 13px;
}

.file-name {
    margin: 10px 0 0;
    color: #059669;
    font-weight: 500;
    font-size: 14px;
}

.file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.btn-primary {
    background: linear-gradient(90deg, #1f2937, #374151);
    color: #fff;
    border: none;
    padding: 12px 28px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    font-size: 15px;
    box-shadow: 0 4px 12px rgba(31, 41, 55, 0.2);
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-primary:not(:disabled):hover {
    background: linear-gradient(90deg, #111827, #1f2937);
    transform: translateY(-2px);
}

.btn-primary:disabled {
    background: #d1d5db;
    box-shadow: none;
    cursor: not-allowed;
    color: #9ca3af;
}

.spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.gallery-section {
    margin-top: 40px;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.section-title h2 {
    font-size: 18px;
    margin: 0;
}

.badge {
    background: #e5e7eb;
    color: #374151;
    font-size: 12px;
    padding: 4px 10px;
    border-radius: 999px;
    font-weight: 600;
}

.photos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 16px;
}

.photo-item {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(2, 6, 23, 0.04);
    border: 1px solid #f3f4f6;
    transition: transform 0.15s;
}

.photo-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(2, 6, 23, 0.08);
}

.photo-preview {
    height: 150px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.photo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-info {
    padding: 12px;
}

.photo-url {
    font-size: 12px;
    color: #059669;
    margin: 0 0 6px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}

.photo-id {
    margin: 0;
    font-size: 11px;
    color: #6b7280;
    font-weight: 500;
}

.loading-state,
.empty-state {
    text-align: center;
    padding: 40px;
    color: #6b7280;
    background: #f9fafb;
    border-radius: 12px;
    border: 1px dashed #e5e7eb;
}
</style>