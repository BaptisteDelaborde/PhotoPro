<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'


const config = useRuntimeConfig()
const apiBase = import.meta.server ? config.apiInternalUrl : config.public.apiFrontofficeUrl
const router = useRouter()

const getPhotoUrl = (galerie) => {
  let link = galerie.cover_url || galerie.cover_photo_url || galerie.cover_photo || galerie.cover_photo_id || '';
  if (!link) return '';

  if (link.includes('http://localhost:8333/photopro-galeries/http')) {
    link = link.replace('http://localhost:8333/photopro-galeries/', '');
  }

  if (!link.startsWith('http')) {
    if (link.includes('/')) {
      return `${config.public.s3Url}/${link}`;
    } else {
      return `${config.public.s3Url}/photopro-galeries/${link}`;
    }
  }
  return link;
}

const inputCode = ref('')
const errorMsg = ref('')

const goToPrivateGallery = () => {
  if (inputCode.value.trim().length > 0) {
    router.push(`/p/${inputCode.value.trim()}`)
  } else {
    errorMsg.value = "Veuillez saisir un code valide."
  }
}

const selectedPhotographer = ref('')

const { data: photographes } = await useFetch(`${apiBase}/photographes`)

const { data: galeries, pending, error, refresh } = await useFetch(`${apiBase}/galeries/publiques`, {
  query: { photographer_id: selectedPhotographer }
})
</script>

<template>
  <div class="container">

    <section class="private-access-section">
      <div class="access-card">
        <h2>Accès Client</h2>
        <p>Saisissez le code d'accès de votre galerie privée.</p>

        <form @submit.prevent="goToPrivateGallery" class="access-form">
          <input
              v-model="inputCode"
              type="text"
              placeholder="Ex: ABCD12"
              required
              class="code-input"
          />
          <button type="submit" class="submit-btn">Accéder</button>
        </form>

        <p v-if="errorMsg" class="error-text">{{ errorMsg }}</p>
      </div>
    </section>

    <hr class="section-divider">

    <header class="public-header">
      <h1>Galeries Publiques</h1>

      <div class="filter-bar">
        <label for="photographer">Filtrer par :</label>
        <select id="photographer" v-model="selectedPhotographer" @change="refresh" class="filter-select">
          <option value="">Tous les photographes</option>

          <option
              v-for="photographe in photographes"
              :key="photographe.id"
              :value="photographe.id"
          >
            {{ photographe.first_name }} {{ photographe.last_name }}
          </option>

        </select>
      </div>
    </header>

    <div v-if="error" style="color: red; margin-top: 20px;">
      Erreur : {{ error.message }}
    </div>
    <div v-else-if="pending" style="margin-top: 20px;">
      Chargement des galeries...
    </div>

    <div v-else-if="galeries && galeries.length > 0" class="galerie-grid">
      <NuxtLink
          v-for="galerie in galeries"
          :key="galerie.id"
          :to="`/galerie/${galerie.id}`"
          class="galerie-card"
          style="display: block; text-decoration: none; color: inherit;"
      >

        <h2>{{ galerie.title }}</h2>

        <p v-if="galerie.description" class="description">
          {{ galerie.description }}
        </p>

        <img
            v-if="getPhotoUrl(galerie)"
            :src="getPhotoUrl(galerie)"
            :alt="galerie.title"
            class="cover-image"
        />
        <div v-else class="no-image">
          Aucune photo de couverture
        </div>

      </NuxtLink>
    </div>

    <div v-else style="margin-top: 20px;">
      Aucune galerie publique pour le moment.
    </div>
  </div>
</template>

<style src="../assets/css/main.css" scoped></style>

<style scoped>
.container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 20px;
}

.private-access-section { display: flex; justify-content: center; margin-bottom: 40px; }
.access-card { background: #f8f9fa; padding: 30px; border-radius: 8px; text-align: center; width: 100%; max-width: 450px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
.access-form { display: flex; gap: 10px; margin-top: 20px; justify-content: center; }
.code-input { padding: 10px 15px; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem; width: 60%; text-transform: uppercase; }
.submit-btn { background-color: #2c3e50; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; }
.submit-btn:hover { background-color: #34495e; }
.error-text { color: red; margin-top: 10px; font-size: 0.9rem; }

.section-divider { border: 0; height: 1px; background: #ddd; margin: 40px 0; }

.public-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
.public-header h1 { margin: 0; }
.filter-bar { display: flex; align-items: center; gap: 10px; }
.filter-select { padding: 8px; border-radius: 4px; border: 1px solid #ccc; }

.galerie-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 25px;
  margin-top: 30px;
}

.galerie-card {
  border: 1px solid #eee;
  border-radius: 8px;
  padding: 20px;
  transition: transform 0.2s, box-shadow 0.2s;
}
.galerie-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 15px rgba(0,0,0,0.1);
}

.cover-image { width: 100%; height: 200px; object-fit: cover; border-radius: 4px; margin-top: 15px; }
.no-image { width: 100%; height: 200px; background-color: #f4f4f4; color: #888; display: flex; align-items: center; justify-content: center; border-radius: 4px; margin-top: 15px; }
</style>