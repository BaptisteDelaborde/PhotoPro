<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const galleries = ref([
    { id: 1, titre: 'Mariage Dupont', type: 'Privée', est_publiee: false },
    { id: 2, titre: 'Paysages d\'Auvergne', type: 'Publique', est_publiee: true }
])

const goToCreate = () => {
    router.push('/galeries/nouvelle')
}

const goToGalleryDetails = (id: number) => {
    console.log(`Ouverture de la galerie n°${id}`);
    alert(`Bientôt : Édition de la galerie ${id}`);
}
</script>

<template>
    <div class="container">
        <h2>Mes Galeries</h2>
        <button @click="goToCreate" class="btn-create">Créer une nouvelle galerie</button>

        <ul class="gallery-list">
            <li v-for="galerie in galleries" :key="galerie.id" class="gallery-item"
                @click="goToGalleryDetails(galerie.id)">
                <span class="gallery-title"><strong>{{ galerie.titre }}</strong></span>
                <span class="gallery-type">- {{ galerie.type }}</span>
                <span class="gallery-status">
                    <span v-if="galerie.est_publiee" class="status-published">(Publiée)</span>
                    <span v-else class="status-draft">(Brouillon)</span>
                </span>
            </li>
        </ul>
    </div>
</template>

<style scoped>
.gallery-list {
    list-style-type: none;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 20px;
}

.gallery-item {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    align-items: center;
    background-color: #181a1f;
    color: #d1d5db;
    padding: 15px 20px;
    border-radius: 6px;
    border: 1px solid #2d313a;
    cursor: pointer;
    transition: background-color 0.2s ease, border-color 0.2s ease;
}

.gallery-item:hover {
    background-color: #252830;
    border-color: #3b82f6;
}

.gallery-title {
    text-align: left;
    color: #ffffff;
}

.gallery-type {
    text-align: left;
}

.gallery-status {
    text-align: right;
    color: #9ca3af;
}

.btn-create {
    background-color: #3b82f6;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
}

.btn-create:hover {
    background-color: #2563eb;
}
</style>