<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const titre = ref('')
const description = ref('')
const typeGalerie = ref('publique')
const miseEnPage = ref('grille')

const nomClient = ref('')
const emailClient = ref('')

const submitGallery = () => {
    const payload = {
        titre: titre.value,
        description: description.value,
        type: typeGalerie.value,
        miseEnPage: miseEnPage.value,
        client: typeGalerie.value === 'privee' ? {
            nom: nomClient.value,
            email: emailClient.value
        } : null
    }

    console.log('Données envoyées à l\'API :', payload)
    router.push('/galeries')
}
</script>

<template>
    <div class="container">
        <h2>Créer une nouvelle galerie</h2>

        <form @submit.prevent="submitGallery">
            <div class="form-group">
                <label>Titre :</label>
                <input v-model="titre" type="text" required />
            </div>

            <div class="form-group">
                <label>Description :</label>
                <textarea v-model="description"></textarea>
            </div>

            <div class="form-group">
                <label>Visibilité :</label>
                <select v-model="typeGalerie">
                    <option value="publique">Publique</option>
                    <option value="privee">Privée</option>
                </select>
            </div>

            <div class="form-group">
                <label>Mise en page :</label>
                <select v-model="miseEnPage">
                    <option value="grille">Grille classique</option>
                    <option value="maconnerie">Maçonnerie (Masonry)</option>
                </select>
            </div>

            <fieldset v-if="typeGalerie === 'privee'">
                <legend>Informations du client</legend>
                <div class="form-group">
                    <label>Nom du client :</label>
                    <input v-model="nomClient" type="text" required />
                </div>
                <div class="form-group">
                    <label>Email du client (obligatoire) :</label>
                    <input v-model="emailClient" type="email" required />
                </div>
            </fieldset>

            <button type="submit">Enregistrer la galerie</button>
        </form>
    </div>
</template>