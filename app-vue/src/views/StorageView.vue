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
    <div class="container">
        <h2>Mon Espace de Stockage</h2>

        <div class="upload-section">
            <input type="file" @change="handleFileChange" accept="image/*" />
            <button @click="uploadPhoto" :disabled="!selectedFile || isUploading">
                {{ isUploading ? 'Upload en cours...' : 'Uploader vers S3' }}
            </button>
        </div>

        <hr />

        <div class="photos-grid" v-if="uploadedPhotos.length > 0">
            <h3>Mes photos uploadées</h3>
            <div v-for="photo in uploadedPhotos" :key="photo.id_photo">
                <p>Photo S3 : {{ photo.url_s3 }}</p>
            </div>
        </div>
    </div>
</template>