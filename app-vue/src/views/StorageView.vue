<script setup>
import { ref } from 'vue'
import { apiGestion } from '@/services/api.js'

const selectedFile = ref(null)
const isUploading = ref(false)
const uploadedPhotos = ref([])

const handleFileChange = (event) => {
    const target = event.target
    if (target.files && target.files.length > 0) {
        selectedFile.value = target.files[0]
    }
}

const uploadPhoto = async () => {
    if (!selectedFile.value) return

    isUploading.value = true

    try {
        const reponse = await apiGestion.uploadPhoto(selectedFile.value)

        if (reponse.status === 201) {
            alert('Upload réussi ! ID: ' + reponse.data.id_photo)
            uploadedPhotos.value.push(reponse.data)
        }
    } catch (error) {
        console.error("Erreur d'upload", error)
    } finally {
        isUploading.value = false
        selectedFile.value = null
    }
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
                <div class="icon-cloud">☁️</div>
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

        <section class="gallery-section" v-if="uploadedPhotos.length > 0">
            <div class="section-title">
                <h2>Importations récentes</h2>
                <span class="badge">{{ uploadedPhotos.length }} fichier(s)</span>
            </div>

            <div class="photos-grid">
                <div v-for="photo in uploadedPhotos" :key="photo.id_photo" class="photo-item">
                    <div class="photo-preview">
                        <!-- Simulate preview or logic here -->
                        <div class="photo-placeholder">S3</div>
                    </div>
                    <div class="photo-info">
                        <p class="photo-url" title="Lien S3">{{ photo.url_s3 }}</p>
                        <p class="photo-id">ID: {{ photo.id_photo }}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
.storage-page {
  max-width: 900px;
  margin: 28px auto;
  padding: 20px;
  font-family: Inter, system-ui, -apple-system, sans-serif;
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
  box-shadow: 0 8px 24px rgba(2,6,23,0.06);
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
  box-shadow: 0 4px 12px rgba(31,41,55,0.2);
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
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
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
  box-shadow: 0 4px 12px rgba(2,6,23,0.04);
  border: 1px solid #f3f4f6;
  transition: transform 0.15s;
}

.photo-item:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(2,6,23,0.08);
}

.photo-preview {
  height: 120px;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
}

.photo-placeholder {
  font-size: 24px;
  font-weight: 800;
  color: #d1d5db;
}

.photo-info {
  padding: 12px;
}

.photo-url {
  font-size: 12px;
  color: #374151;
  margin: 0 0 6px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-family: monospace;
  background: #f9fafb;
  padding: 4px 6px;
  border-radius: 4px;
  border: 1px solid #e5e7eb;
}

.photo-id {
  margin: 0;
  font-size: 11px;
  color: #9ca3af;
  font-weight: 500;
}
</style>
