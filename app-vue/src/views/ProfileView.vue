<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { apiGestion } from '../services/api'

const authStore = useAuthStore()
const isEditing = ref(false)
const description = ref("Photographe passionné...") // À charger depuis l'API
const profileImageUrl = ref("")

const photographeId = computed(() => authStore.photographerId)

const handleProfileImageUpload = async (event: Event) => {
    const target = event.target as HTMLInputElement
    if (!target.files?.length) return

    try {
        const res = await apiGestion.uploadPhotoToStorage(target.files[0], photographeId.value)
        profileImageUrl.value = res.url
        alert("Photo de profil mise à jour !")
    } catch (e) {
        console.error(e)
    }
}

const saveProfile = async () => {
    try {
        isEditing.value = false
        alert("Profil enregistré")
    } catch (e) {
        alert("Erreur lors de l'enregistrement")
    }
}
</script>

<template>
    <div class="container profile-page">
        <h2>Mon Profil Professionnel</h2>

        <div class="profile-header">
            <div class="avatar-container">
                <img :src="profileImageUrl || 'https://via.placeholder.com/150'" class="avatar" />
                <label class="btn-upload-avatar">
                    Modifier la photo
                    <input type="file" @change="handleProfileImageUpload" hidden accept="image/*" />
                </label>
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
    </div>
</template>

<style scoped>
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
</style>