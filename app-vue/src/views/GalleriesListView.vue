<script setup lang="ts">
// Importation des outils réactifs de Vue.js
import {ref, computed, onMounted} from 'vue'
import {useRouter} from 'vue-router'
// Importation du store Pinia (qui garde en mémoire l'utilisateur connecté)
import {useAuthStore} from '../stores/auth'
// Importation des appels API (axios)
import {apiGestion} from '../services/api'

// Définition de la structure d'une galerie pour Typescript
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

// Variables réactives (si elles changent, l'interface se met à jour automatiquement)
const galleries = ref<Gallery[]>([]) // Liste de toutes les galeries
const search = ref('') // Texte de la barre de recherche
const filterType = ref<'Toutes' | 'Publique' | 'Privée'>('Toutes') // Filtre du menu déroulant

// On récupère l'ID du photographe depuis le store (en temps réel grâce à computed)
const photographeId = computed(() => authStore.photographerId)
const fileInput = ref<HTMLInputElement | null>(null)
const targetGalleryId = ref<string | number | null>(null)
const isUploadingCover = ref(false)

// Références pour gérer l'upload de la photo de couverture
const fileInput = ref<HTMLInputElement | null>(null) // Fait le lien avec le <input type="file"> caché
const targetGalleryId = ref<string | number | null>(null) // Mémorise la galerie qu'on est en train de modifier
const isUploadingCover = ref(false) // Permet d'afficher un loader pendant l'envoi

// Fonction principale : Récupère les galeries depuis le backend PHP
const fetchGalleries = async () => {
  try {
    const res = await apiGestion.getMesGaleries()
    console.log("🔍 Données brutes reçues :", res)
    
    let items = []
    
    // Ce bloc if/else gère les différentes façons dont le backend pourrait renvoyer le JSON
    // (parfois double-encodé, parfois dans un objet 'data', parfois un tableau direct)
    if (Array.isArray(res)) {
      items = res
    } 
    else if (res && Array.isArray(res.data)) {
      items = res.data
    } 
    else {
      let stringData = typeof res === 'string' ? res : (res && typeof res.data === 'string' ? res.data : null)
      
      if (stringData) {
        try {
          // On nettoie la chaîne pour forcer l'extraction d'un tableau JSON valide
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

    // On transforme les données brutes du backend en format propre pour notre composant
    galleries.value = items.map((g: any) => ({
      id: g.id,
      titre: g.title || g.titre || 'Sans titre',
      type: g.is_public ? 'Publique' : 'Privée',
      est_publiee: g.is_published,
      cover: g.cover_url || g.cover_photo_url || null,
      layout: g.layout || 'grid'
    }))
    
  } catch (error) {
    console.error("Impossible de charger les galeries", error)
  }
}

// Se déclenche automatiquement quand la page s'affiche pour la première fois
onMounted(async () => {
  if (authStore.isAuthenticated) {
    await fetchGalleries()
  }
});

// -- GESTION DE LA COUVERTURE DE LA GALERIE --
// Quand on clique sur le bouton, on simule un clic sur l'input type="file" caché
const triggerCoverUpload = (id: string | number) => {
  targetGalleryId.value = id
  fileInput.value?.click()
}

// Quand l'utilisateur a choisi un fichier sur son ordinateur
const onCoverFileSelected = async (event: Event) => {
  const target = event.target as HTMLInputElement
  if (!target.files || target.files.length === 0 || !targetGalleryId.value || !photographeId.value) return

  const file = target.files[0] // On récupère le fichier physique
  isUploadingCover.value = true // On active le loader

  try {
    //On envoie la photo sur le stockage (S3)
    const uploadRes = await apiGestion.uploadPhoto(file, photographeId.value, targetGalleryId.value)
    const newPhotoId = uploadRes.photo_id || (uploadRes.photos ? uploadRes.photos[0].id : null)

    if (!newPhotoId) throw new Error("ID de la photo introuvable")

    //On met à jour la galerie pour lui assigner cette nouvelle photo en couverture
    await apiGestion.updateGalerie(photographeId.value, targetGalleryId.value, {
      cover_photo_id: newPhotoId
    })

    //On recharge la liste pour afficher la nouvelle image
    await fetchGalleries()
  } catch (error) {
    console.error("Erreur couverture:", error)
    alert("Impossible de modifier la couverture.")
  } finally {
    isUploadingCover.value = false // On retire le loader
    targetGalleryId.value = null
    if (fileInput.value) fileInput.value.value = '' // On vide l'input pour pouvoir re-sélectionner le même fichier si besoin
  }
}

// computed permet de créer une variable qui se recalcule toute seule
// à chaque fois que "search" ou "filterType" changent.
const filteredGalleries = computed(() => {
  const q = search.value.trim().toLowerCase()
  return galleries.value.filter(g => {
    // Vérifie si le type correspond (Publique/Privée)
    const matchType = filterType.value === 'Toutes' || g.type === filterType.value
    // Vérifie si le titre contient le texte recherché
    const matchQuery = !q || (g.titre && g.titre.toLowerCase().includes(q))
    return matchType && matchQuery
  })
})

// Inverse le statut publié/brouillon
const togglePublish = async (g: Gallery) => {
  try {
    const nouveauStatut = !g.est_publiee;
    // Appel API pour mettre à jour la BDD
    await apiGestion.updateGalerieStatus(g.id, {is_published: nouveauStatut});
    // Si l'API répond OK, on met à jour l'affichage en local
    g.est_publiee = nouveauStatut;
  } catch (error) {
    console.error(error);
    window.alert("Erreur lors de la modification du statut.");
  }
}

// Redirections via vue-router
const goToCreate = () => {
  if (!authStore.isAuthenticated) {
    router.push('/connexion')
    return
  }
  router.push('/galeries/nouvelle')
}

const goToGalleryDetails = (id: string | number, title: string, layout?: string) => {
  // On passe des infos dans l'URL (query params) pour éviter un chargement blanc sur la page suivante
  router.push({path: `/galeries/${id}`, query: {title, layout: layout || 'grid'}})
}

// Génère les initiales (ex: "Mariage Sophie" -> "MS") pour la miniature si pas de couverture
const initials = (titre: string | undefined) => {
  if (!titre) return '?'
  return titre.split(' ').map(s => s.charAt(0).toUpperCase()).slice(0, 2).join('')
}

// Suppression avec alerte de confirmation native
const deleteGallery = async (id: string | number, titre: string) => {
  if (!window.confirm(`Êtes-vous sûr de vouloir supprimer la galerie "${titre}" ?\n\nVos photos resteront dans votre stockage global.`)) return

  try {
    await apiGestion.deleteGalerie(photographeId.value, id)
    // On retire la galerie de la liste locale sans avoir à recharger toute la page
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
      <article v-for="g in filteredGalleries" :key="g.id" class="card" @click="goToGalleryDetails(g.id, g.titre, g.layout)" role="button" tabindex="0">
        
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