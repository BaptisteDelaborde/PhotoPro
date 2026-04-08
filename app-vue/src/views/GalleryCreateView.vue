<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { apiGestion } from '../services/api'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// On relie chaque input du HTML à une variable ref() via v-model
const titre = ref('')
const description = ref('')
const typeGalerie = ref('publique')
const miseEnPage = ref('grille')

// Les champs clients
const nomClient = ref('')
const emailClient = ref('')

const errorMsg = ref('')

const submitGallery = async () => {
    try {
      // On construit l'objet de données (Payload) à envoyer à l'API
        const payload = {
            title: titre.value,
            description: description.value,
            is_public: typeGalerie.value === 'publique',
            layout: miseEnPage.value,
            // Si c'est privé, on envoie le nom, sinon on envoie 'null'
            client_name: typeGalerie.value === 'privee' ? nomClient.value : null,
            client_email: typeGalerie.value === 'privee' ? emailClient.value : null,
            photographe_id: authStore.photographerId 
        }

        console.log('Données envoyées à l\'API :', payload)
        
        await apiGestion.creerGalerie(payload) 
        
        // Redirection vers le dashboard après succès
        router.push('/galeries')
    } catch (error) {
        errorMsg.value = 'Une erreur est survenue lors de la création de la galerie.'
        console.error('Erreur lors de la création de la galerie :', error)
    }
}
</script>

<template>
    <div class="form-page">
        <header class="header">
            <h1>Créer une nouvelle galerie</h1>
            <p class="lead">Personnalisez l'espace pour vos clients</p>
        </header>

        <form class="card-form" @submit.prevent="submitGallery">
            <div class="form-row">
                <div class="form-group flex-1">
                    <label>Titre de la galerie</label>
                    <input v-model="titre" type="text" placeholder="Ex: Mariage de Sophie & Thomas" required />
                </div>
                <div class="form-group flex-1">
                    <label>Visibilité</label>
                    <select v-model="typeGalerie">
                        <option value="publique">Publique</option>
                        <option value="privee">Privée</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea v-model="description" rows="3" placeholder="Quelques mots sur cette séance..."></textarea>
            </div>

            <div class="form-group w-50">
                <label>Mise en page</label>
                <select v-model="miseEnPage">
                    <option value="grille">Grille classique</option>
                    <option value="maconnerie">Maçonnerie (Masonry)</option>
                </select>
            </div>

            <transition name="fade">
                <fieldset v-if="typeGalerie === 'privee'" class="fieldset-prive">
                    <legend>Informations Client</legend>
                    <div class="form-row">
                        <div class="form-group flex-1">
                            <label>Nom du client</label>
                            <input v-model="nomClient" type="text" placeholder="Ex: M. Dupont" required />
                        </div>
                        <div class="form-group flex-1">
                            <label>Email du client</label>
                            <input v-model="emailClient" type="email" placeholder="client@email.com" required />
                        </div>
                    </div>
                    <p class="hint">Un email contenant le lien et le mot de passe sera automatiquement envoyé à la publication.</p>
                </fieldset>
            </transition>

            <div class="form-actions">
                <button type="button" class="btn-cancel" @click="router.push('/galeries')">Annuler</button>
                <button type="submit" class="btn-submit">Créer la galerie</button>
            </div>

            <p v-if="errorMsg" class="error-msg">{{ errorMsg }}</p>
        </form>
    </div>
</template>

<style scoped>
.form-page {
  max-width: 800px;
  margin: 28px auto;
  padding: 20px;
  font-family: Inter, system-ui, -apple-system, sans-serif;
  color: #0b1220;
}

.header {
  margin-bottom: 24px;
}

.header h1 {
  margin: 0;
  font-size: 24px;
  letter-spacing: -0.01em;
}

.lead {
  margin: 4px 0 0;
  color: #6b7280;
  font-size: 14px;
}

.card-form {
  background: #fff;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(2,6,23,0.06);
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-row {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
}

.flex-1 {
  flex: 1;
  min-width: 250px;
}

.w-50 {
  width: 50%;
  min-width: 250px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-group label {
  font-size: 13px;
  font-weight: 500;
  color: #374151;
}

input, select, textarea {
  padding: 10px 14px;
  border-radius: 10px;
  border: 1px solid #e6edf3;
  background: #f9fafb;
  font-family: inherit;
  font-size: 14px;
  transition: border-color 0.2s;
}

input:focus, select:focus, textarea:focus {
  outline: none;
  border-color: #9ca3af;
  background: #fff;
}

textarea {
  resize: vertical;
}

.fieldset-prive {
  border: 1px solid #e5e7eb;
  padding: 20px;
  border-radius: 10px;
  background: #fdfdfd;
  margin-top: 10px;
}

.fieldset-prive legend {
  font-weight: 600;
  font-size: 14px;
  color: #111827;
  padding: 0 8px;
}

.hint {
  font-size: 12px;
  color: #6b7280;
  margin-top: 14px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 10px;
}

.btn-cancel {
  background: transparent;
  border: 1px solid #e6edf3;
  color: #4b5563;
  padding: 10px 20px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 500;
}

.btn-submit {
  background: linear-gradient(90deg, #1f2937, #374151);
  color: #fff;
  border: none;
  padding: 10px 24px;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(31,41,55,0.2);
}

.btn-submit:hover {
  background: linear-gradient(90deg, #111827, #1f2937);
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.error-msg {
  color: #dc2626;
  font-size: 14px;
  margin-top: 10px;
  text-align: center;
}
</style>
