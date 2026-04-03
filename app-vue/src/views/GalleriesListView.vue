<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { apiGestion } from '../services/api'

type Gallery = {
  id: string | number
  titre: string
  type: string
  est_publiee: boolean
  cover?: string | null
}

const router = useRouter()

const galleries = ref<Gallery[]>([])
const search = ref('')
const filterType = ref<'Toutes' | 'Publique' | 'Privée'>('Toutes')

const authStore = useAuthStore()

onMounted(async () => {
  if (authStore.isAuthenticated) {
    try {
      const data = await apiGestion.getMesGaleries()
      galleries.value = (Array.isArray(data) ? data : data.items || []).map((g: any) => ({
        id: g.id,
        titre: g.title || g.titre || 'Sans titre',
        type: g.is_public ? 'Publique' : 'Privée',
        est_publiee: g.is_public,
        cover: g.cover_photo_id ? `${import.meta.env.VITE_API_BASE_URL}/photos/${g.cover_photo_id}/storage` : null
      }))
    } catch (error) {
      console.error("Impossible de charger les galeries", error)
    }
  }
});

const filteredGalleries = computed(() => {
  const q = search.value.trim().toLowerCase()
  return galleries.value.filter(g => {
    const matchType = filterType.value === 'Toutes' || g.type === filterType.value
    const matchQuery = !q || (g.titre && g.titre.toLowerCase().includes(q))
    return matchType && matchQuery
  })
})

const goToCreate = () => {
  if (!authStore.isAuthenticated) {
    router.push('/connexion')
    return
  }
  router.push('/galeries/nouvelle')
}

const goToLogin = () => {
  router.push('/connexion')
}

const handleLogout = () => {
  authStore.logout()
  router.push('/connexion')
}

const goToGalleryDetails = (id: number) => {
  router.push(`/galeries/${id}`)
}

const showPreview = (msg: string) => {
  window.alert(msg)
}

const initials = (titre: string | undefined) => {
  if (!titre) return '?'
  return titre
    .split(' ')
    .map(s => s.charAt(0).toUpperCase())
    .slice(0, 2)
    .join('')
}
</script>

<template>
  <div class="gallery-page">
    <header class="header">
      <div class="header-left">
        <h1>Mes galeries</h1>
        <p class="lead">Organisez, publiez et partagez vos plus belles images</p>
      </div>

      <div class="header-right">
        <div class="search">
          <input v-model="search" placeholder="Rechercher une galerie..." />
        </div>

        <select v-model="filterType" class="filter">
          <option>Toutes</option>
          <option>Publique</option>
          <option>Privée</option>
        </select>

        <button v-if="authStore.isAuthenticated" class="btn-primary" @click="goToCreate">+ Nouvelle galerie</button>
      </div>
    </header>

    <section v-if="filteredGalleries.length" class="cards">
      <article
          v-for="g in filteredGalleries"
          :key="g.id"
          class="card"
          @click="goToGalleryDetails(g.id)"
          role="button"
          tabindex="0"
      >
        <div
            class="cover"
            :style="g.cover ? { backgroundImage: 'url(' + g.cover + ')' } : {}"
        >
          <div v-if="!g.cover" class="placeholder">{{ initials(g.titre) }}</div>
          <div class="overlay">
            <h3>{{ g.titre }}</h3>
            <div class="meta">
              <span class="type">{{ g.type }}</span>
              <span class="status">{{ g.est_publiee ? 'Publiée' : 'Brouillon' }}</span>
            </div>
          </div>
        </div>

        <div class="actions">
          <button class="btn-outline" @click.stop="goToGalleryDetails(g.id)">Voir</button>
          <button class="btn-ghost" @click.stop="showPreview('Prévisualisation : ' + g.titre)">Prévisualiser</button>
        </div>
      </article>
    </section>

    <div v-else class="empty">
      <p>Aucune galerie trouvée. Créez votre première galerie.</p>
      <button v-if="authStore.isAuthenticated" class="btn-primary" @click="goToCreate">Créer une galerie</button>
      <button v-else class="btn-primary" @click="goToLogin">Se connecter pour créer</button>
    </div>
  </div>
</template>

<style scoped>
.gallery-page {
  max-width: 1100px;
  margin: 28px auto;
  padding: 20px;
  font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  color: #0b1220;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
  margin-bottom: 18px;
  flex-wrap: wrap;
}

.header-left h1 {
  margin: 0;
  font-size: 22px;
  letter-spacing: -0.01em;
}

.lead {
  margin: 4px 0 0;
  color: #6b7280;
  font-size: 13px;
}

.header-right {
  display: flex;
  gap: 10px;
  align-items: center;
}

.search input {
  padding: 8px 10px;
  border-radius: 10px;
  border: 1px solid #e6edf3;
  background: #fff;
  min-width: 220px;
}

.filter {
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid #e6edf3;
  background: #fff;
}

.btn-primary {
  background: linear-gradient(90deg,#1f2937,#374151);
  color: #fff;
  border: none;
  padding: 8px 12px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
}

.cards {
  display: grid;
  gap: 18px;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
}

.card {
  display: flex;
  flex-direction: column;
  gap: 10px;
  cursor: pointer;
  border-radius: 12px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 8px 24px rgba(2,6,23,0.06);
  transition: transform .15s ease, box-shadow .15s ease;
}

.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px rgba(2,6,23,0.08);
}

.cover {
  height: 160px;
  background-size: cover;
  background-position: center;
  position: relative;
  display: flex;
  align-items: flex-end;
}

.placeholder {
  width: 100%;
  height: 100%;
  display:flex;
  align-items:center;
  justify-content:center;
  background: linear-gradient(135deg,#f3f4f6,#e5e7eb);
  color:#374151;
  font-weight:700;
  font-size:28px;
}

.overlay {
  width: 100%;
  padding: 12px;
  background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(2,6,23,0.45) 60%);
  color: #fff;
}

.overlay h3 {
  margin: 0;
  font-size: 16px;
  line-height: 1.1;
}

.meta {
  margin-top: 6px;
  display:flex;
  gap:8px;
  align-items:center;
  font-size:12px;
  opacity: 0.95;
}

.type {
  background: rgba(255,255,255,0.12);
  padding: 4px 8px;
  border-radius: 999px;
}

.status {
  background: rgba(255,255,255,0.08);
  padding: 4px 8px;
  border-radius: 999px;
}

.actions {
  display:flex;
  gap:10px;
  padding: 12px;
  align-items:center;
}

.btn-outline {
  background: transparent;
  border: 1px solid #e6edf3;
  padding: 8px 10px;
  border-radius: 8px;
  cursor: pointer;
}

.btn-ghost {
  background: transparent;
  border: 0;
  color: #374151;
  padding: 8px;
  cursor: pointer;
}

.empty {
  text-align: center;
  padding: 40px 20px;
  color: #6b7280;
  background: linear-gradient(180deg,#fbfdff,#ffffff);
  border-radius: 12px;
  border: 1px dashed #e6edf3;
}
</style>
