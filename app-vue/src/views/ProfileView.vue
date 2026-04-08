<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { apiGestion } from '../services/api'

type Photo = {
    id: string | number
    file_name?: string
    storage_url?: string
    url?: string
}

const authStore = useAuthStore()
// Variables qui stockent les champs textuels du profil
const isEditing = ref(false)
const description = ref("Photographe passionné...")
const profileImageUrl = ref("")

const photographeId = computed(() => authStore.photographerId)

// --- Variables pour la modale ---
const showStorageModal = ref(false)
const storagePhotos = ref<Photo[]>([])
const loadingStorage = ref(false)

// Chargement initial (GET) : On vient piocher dans le microservice app-auth
onMounted(async () => {
    try {
        const data = await apiGestion.get(`/auth/photographes/${photographeId.value}`)
        description.value = data.description || ''
        if (data.profile_image_url) {
            profileImageUrl.value = getPhotoUrl({ url: data.profile_image_url } as Photo)
        }
    } catch (error) {
        console.error("Erreur chargement profil:", error)
    }
})

// Ouvre la modale et récupère les images de S3
const openStorageModal = async () => {
    showStorageModal.value = true
    loadingStorage.value = true
    try {
        const data = await apiGestion.getStoragePhotos(photographeId.value)
        storagePhotos.value = data.photos || data || []
    } catch (err) {
        alert("Impossible de charger votre bibliothèque")
    } finally {
        loadingStorage.value = false
    }
}

// Se déclenche quand on CLIQUE sur une image de la modale
const selectPhoto = async (photo: Photo) => {
    const nouvelleUrl = photo.url || photo.storage_url
    try {
        await apiGestion.updateProfile(photographeId.value, { profile_image_url: nouvelleUrl })
        profileImageUrl.value = getPhotoUrl(photo)
        showStorageModal.value = false
    } catch (e) {
        console.dir(e)
        const serverError = e.response?.data?.error || e.response?.data?.message || e.message
        alert(`Erreur Serveur : ${serverError}`)
    }
}

// Se déclenche quand on clique sur le bouton "Enregistrer" de la description
const saveProfile = async () => {
    try {
        await apiGestion.updateProfile(photographeId.value, { description: description.value })
        isEditing.value = false
        alert("Profil enregistré")
    } catch (e) {
        alert("Erreur lors de l'enregistrement")
    }
}

const getPhotoUrl = (photo: Photo) => {
    let link = photo.url || photo.storage_url || '';
    if (link.includes('http://localhost:8333/photopro-galeries/http')) {
        link = link.replace('http://localhost:8333/photopro-galeries/', '');
    }
    if (link && !link.startsWith('http')) {
        return `http://localhost:8333/photopro-galeries/${link}`;
    }
    return link;
}
</script>

<template>
    <div class="container profile-page">
        <h2>Mon Profil Professionnel</h2>

        <div class="profile-header">
            <div class="avatar-container">
                <img :src="profileImageUrl || 'https://via.placeholder.com/150'" class="avatar" />
                <button class="btn-upload-avatar" style="background: none; border: none; padding: 0;"
                    @click="openStorageModal">
                    Modifier la photo
                </button>
            </div>

            <div class="user-info">
                <h3>{{ authStore.pseudo }}</h3>
                <p class="email">{{ authStore.userEmail }}</p>
            </div>
        </div>

        <hr />

        <div class="profile-section">
            <div class="section-header">
                <h3>Ma Description</h3>
                <button class="btn-small" @click="isEditing = !isEditing">
                    {{ isEditing ? 'Annuler' : 'Modifier' }}
                </button>
            </div>

            <div v-if="!isEditing" class="description-text">
                {{ description }}
            </div>

            <div v-else class="edit-area">
                <textarea v-model="description" class="form-control" rows="5"></textarea>
                <button class="btn-primary" @click="saveProfile" style="margin-top: 10px;">Enregistrer</button>
            </div>
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
                        <p>Aucune photo disponible. Allez dans l'onglet Stockage pour uploader des images.</p>
                    </div>
                    <div v-else class="storage-grid">
                        <div v-for="photo in storagePhotos" :key="photo.id" class="storage-item"
                            @click="selectPhoto(photo)">
                            <img :src="getPhotoUrl(photo)" :alt="photo.file_name" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
/* TON CSS D'ORIGINE INTACT */
.profile-header {
    display: flex;
    gap: 2rem;
    align-items: center;
    margin-bottom: 2rem;
}

.avatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #3498db;
}

.avatar-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: center;
}

.btn-upload-avatar {
    font-size: 0.8rem;
    cursor: pointer;
    color: #3498db;
    text-decoration: underline;
}

.btn-small {
    width: auto;
    padding: 5px 15px;
    font-size: 0.8rem;
}

.description-text {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

/* AJOUT DU CSS DE LA MODALE */
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
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

.storage-item img {
    width: 100%;
    height: 140px;
    object-fit: cover;
    display: block;
}

.loading,
.empty {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}
</style>