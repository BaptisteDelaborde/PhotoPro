<script setup lang="ts">
import {ref, computed, onMounted} from 'vue'
import {useRouter} from 'vue-router'
import {useAuthStore} from '../stores/auth'
import {apiGestion} from '../services/api'

type Gallery = {
  id: string | number
  titre: string
  type: string
  est_publiee: boolean
  cover?: string | null
  layout?: string
}

const router = useRouter()
const authStore = useAuthStore()

const galleries = ref<Gallery[]>([])
const search = ref('')
const filterType = ref<'Toutes' | 'Publique' | 'Privée'>('Toutes')

const photographeId = computed(() => authStore.photographerId)
const fileInput = ref<HTMLInputElement | null>(null)
const targetGalleryId = ref<string | number | null>(null)
const isUploadingCover = ref(false)

const fetchGalleries = async () => {
  try {
    const res = await apiGestion.getMesGaleries()
    console.log("🔍 Données brutes reçues :", res)
    
    let items = []
    
    // 1. Si c'est un tableau propre (comportement idéal)
    if (Array.isArray(res)) {
      items = res
    } 
    // 2. Si c'est emballé dans res.data
    else if (res && Array.isArray(res.data)) {
      items = res.data
    } 
    // 3. Si PHP a pollué le JSON et l'a transformé en texte brut
    else {
      let stringData = typeof res === 'string' ? res : (res && typeof res.data === 'string' ? res.data : null)
      
      if (stringData) {
        try {
          // On cherche le début '[' et la fin ']' du vrai JSON pour ignorer la pollution
          const startIndex = stringData.indexOf('[')
          const endIndex = stringData.lastIndexOf(']')
          
          if (startIndex !== -1 && endIndex !== -1) {
            const cleanJson = stringData.substring(startIndex, endIndex + 1)
            items = JSON.parse(cleanJson)
          } else {
            items = JSON.parse(stringData)
          }
        } catch (e) {
          console.error("Échec de l'extraction JSON :", stringData)
        }
      }
    }

    // 4. On affecte nos galeries à l'affichage
    galleries.value = items.map((g: any) => ({
      id: g.id,
      titre: g.title || g.titre || 'Sans titre',
      type: g.is_public ? 'Publique' : 'Privée',
      est_publiee: g.is_published,
      // Ta fameuse couverture !
      cover: g.cover_url || g.cover_photo_url || null,
      layout: g.layout || 'grid'
    }))
    
  } catch (error) {
    console.error("Impossible de charger les galeries", error)
  }
}

onMounted(async () => {
  if (authStore.isAuthenticated) {
    await fetchGalleries()
  }
});

const triggerCoverUpload = (id: string | number) => {
  targetGalleryId.value = id
  fileInput.value?.click()
}

const onCoverFileSelected = async (event: Event) => {
  const target = event.target as HTMLInputElement
  if (!target.files || target.files.length === 0 || !targetGalleryId.value || !photographeId.value) return

  const file = target.files[0]
  isUploadingCover.value = true

  try {
    const uploadRes = await apiGestion.uploadPhoto(file, photographeId.value, targetGalleryId.value)
    const newPhotoId = uploadRes.photo_id || (uploadRes.photos ? uploadRes.photos[0].id : null)

    if (!newPhotoId) throw new Error("ID de la photo introuvable")

    await apiGestion.updateGalerie(photographeId.value, targetGalleryId.value, {
      cover_photo_id: newPhotoId
    })

    await fetchGalleries()
  } catch (error) {
    console.error("Erreur couverture:", error)
    alert("Impossible de modifier la couverture.")
  } finally {
    isUploadingCover.value = false
    targetGalleryId.value = null
    if (fileInput.value) fileInput.value.value = ''
  }
}

const filteredGalleries = computed(() => {
  const q = search.value.trim().toLowerCase()
  return galleries.value.filter(g => {
    const matchType = filterType.value === 'Toutes' || g.type === filterType.value
    const matchQuery = !q || (g.titre && g.titre.toLowerCase().includes(q))
    return matchType && matchQuery
  })
})

const togglePublish = async (g: Gallery) => {
  try {
    const nouveauStatut = !g.est_publiee;

    //Appel à l'API pour changer le statut
    await apiGestion.updateGalerieStatus(g.id, {is_published: nouveauStatut});

    //Mise à jour de l'affichage local si succès
    g.est_publiee = nouveauStatut;
  } catch (error) {
    console.error(error);
    window.alert("Erreur lors de la modification du statut.");
  }
}

const goToCreate = () => {
  if (!authStore.isAuthenticated) {
    router.push('/connexion')
    return
  }
  router.push('/galeries/nouvelle')
}

const goToGalleryDetails = (id: string | number, title: string, layout?: string) => {
  router.push({path: `/galeries/${id}`, query: {title, layout: layout || 'grid'}})
}

const initials = (titre: string | undefined) => {
  if (!titre) return '?'
  return titre.split(' ').map(s => s.charAt(0).toUpperCase()).slice(0, 2).join('')
}

const deleteGallery = async (id: string | number, titre: string) => {
  if (!window.confirm(`Êtes-vous sûr de vouloir supprimer la galerie "${titre}" ?\n\nVos photos resteront dans votre stockage global.`)) return

  try {
    await apiGestion.deleteGalerie(photographeId.value, id)
    // On met à jour l'affichage en retirant la galerie supprimée
    galleries.value = galleries.value.filter(g => g.id !== id)
  } catch (err) {
    console.error(err)
    alert("Impossible de supprimer la galerie.")
  }
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
        <div class="search"><input v-model="search" placeholder="Rechercher..."/></div>
        <select v-model="filterType" class="filter">
          <option>Toutes</option>
          <option>Publique</option>
          <option>Privée</option>
        </select>
        <button v-if="authStore.isAuthenticated" class="btn-primary" @click="goToCreate">+ Nouvelle galerie</button>
      </div>
    </header>

    <input type="file" ref="fileInput" class="hidden-input" @change="onCoverFileSelected" accept="image/*"/>
    <div v-if="isUploadingCover" class="global-loader"><span class="spinner"></span> Mise à jour...</div>

    <section v-if="filteredGalleries.length" class="cards">
      <article v-for="g in filteredGalleries" :key="g.id" class="card" @click="goToGalleryDetails(g.id, g.titre, g.layout)"
               role="button"
               tabindex="0">
        <div class="cover" :style="g.cover ? { backgroundImage: 'url(' + g.cover + ')' } : {}">
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
          <button class="btn-outline" @click.stop="goToGalleryDetails(g.id, g.titre, g.layout)">Voir</button>

          <button class="btn-outline" :class="g.est_publiee ? 'btn-danger' : 'btn-success'"
                  @click.stop="togglePublish(g)">
            {{ g.est_publiee ? 'Dépublier' : 'Publier' }}
          </button>
          <button class="btn-ghost" @click.stop="deleteGallery(g.id, g.titre)">Supprimer</button>
        </div>
      </article>
    </section>

    <div v-else class="empty">
      <p>Aucune galerie trouvée.</p>
      <button class="btn-primary" @click="goToCreate">Créer une galerie</button>
    </div>
  </div>
</template>

<style scoped>
.gallery-page {
  max-width: 1100px;
  margin: 28px auto;
  padding: 20px;
  font-family: Inter, sans-serif;
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
}

.btn-delete { background: #fee2e2; border: none; border-radius: 6px; padding: 6px 10px; cursor: pointer; transition: background 0.2s; font-size: 14px; margin-left: auto; }
.btn-delete:hover { background: #fca5a5; }

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
  min-width: 220px;
}

.filter {
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid #e6edf3;
}

.btn-primary {
  background: linear-gradient(90deg, #1f2937, #374151);
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
  cursor: pointer;
  border-radius: 12px;
  overflow: hidden;
  background: #fff;
  box-shadow: 0 8px 24px rgba(2, 6, 23, 0.06);
  transition: all .15s ease;
  position: relative;
}

.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px rgba(2, 6, 23, 0.08);
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
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f4f6;
  color: #374151;
  font-weight: 700;
  font-size: 28px;
}

.overlay {
  width: 100%;
  padding: 12px;
  background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgba(2, 6, 23, 0.45) 60%);
  color: #fff;
}

.overlay h3 {
  margin: 0;
  font-size: 16px;
}

.meta {
  margin-top: 6px;
  display: flex;
  gap: 8px;
  align-items: center;
  font-size: 12px;
}

.type, .status {
  background: rgba(255, 255, 255, 0.12);
  padding: 4px 8px;
  border-radius: 999px;
}

.actions {
  display: flex;
  gap: 10px;
  padding: 12px;
  align-items: center;
  justify-content: space-between;
}

.btn-outline {
  border: 1px solid #e6edf3;
  padding: 6px 10px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 13px;
}

.btn-ghost {
  background: transparent;
  border: 0;
  color: #374151;
  padding: 8px;
  cursor: pointer;
  font-size: 13px;
}

.btn-success {
  color: #166534;
  border-color: #bbf7d0;
  background: #f0fdf4;
}

.btn-danger {
  color: #991b1b;
  border-color: #fecaca;
  background: #fef2f2;
}

.empty {
  text-align: center;
  padding: 40px 20px;
  color: #6b7280;
  border: 1px dashed #e6edf3;
  border-radius: 12px;
}

/* Nos styles spécifiques couverture */
.hidden-input {
  display: none;
}

.btn-edit-cover {
  position: absolute;
  top: 10px;
  right: 10px;
  background: rgba(255, 255, 255, 0.9);
  border: none;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0;
  transition: opacity 0.2s;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card:hover .btn-edit-cover {
  opacity: 1;
}

.global-loader {
  background: #dbeafe;
  color: #1e40af;
  padding: 10px;
  border-radius: 8px;
  text-align: center;
  margin-bottom: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 14px;
}

.spinner {
  width: 14px;
  height: 14px;
  border: 2px solid #3b82f6;
  border-top-color: transparent;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>