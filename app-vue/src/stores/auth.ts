import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'

// On récupère l'URL de base de l'API depuis les variables d'environnement (.env)
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL

// Fonction utilitaire pour lire l'intérieur d'un jeton JWT (qui est encodé en Base64)
function decodeToken(token: string | null): string | null {
  if (!token) return null
  try {
    const parts = token.split('.') // Un JWT a 3 parties séparées par des points
    if (parts.length < 2 || !parts[1]) {
        return null
    }

    // atob() décode la partie centrale (le payload) qui contient nos données
    const payload = JSON.parse(atob(parts[1]))
    return payload.data?.user || null
  } catch {
    return null
  }
}

// Création du store Pinia nommé 'auth'
export const useAuthStore = defineStore('auth', () => {
  // On initialise les variables en regardant d'abord dans le localStorage, ce quipermet à l'utilisateur de rester connecté même s'il rafraîchit la page
  const token = ref(localStorage.getItem('token') || null)
  const photographerId = ref(localStorage.getItem('photographer_id') || null)
  const userEmail = ref(localStorage.getItem('user_email') || null)
  const pseudo = ref(localStorage.getItem('pseudo') || null)

  // Renvoie 'true' si un token existe, 'false' sinon. Se met à jour toute seule.
  const isAuthenticated = computed(() => !!token.value)

  // ACTION : Se connecter
  async function login(email: string, mdp: string) {
    try {
      // Envoi de la requête au backend PHP
      const res = await axios.post(`${API_BASE_URL}/auth/signin`, {
          email: email,
          password: mdp
      });

      const data = res.data;

      //Sauvegarde du Token (en mémoire Vue + mémoire Navigateur)
      token.value = data.payload.access_token;
      localStorage.setItem('token', data.payload.access_token);

      //Extraction et sauvegarde de l'email depuis le JWT
      const extractedEmail = decodeToken(data.payload.access_token)
      userEmail.value = extractedEmail
      if (extractedEmail) {
        localStorage.setItem('user_email', extractedEmail)
      }

      // Sauvegarde de l'ID du photographe
      const pid = data.profile?.id || "uuid_photo_123";
      photographerId.value = pid;
      localStorage.setItem('photographer_id', pid);
      
      //Sauvegarde du pseudo
      const userPseudo = data.profile?.pseudo || extractedEmail?.split('@')[0] || 'Photographe';
      pseudo.value = userPseudo;
      localStorage.setItem('pseudo', userPseudo);

    } catch (error) {
      throw new Error('Identifiants invalides');
    }
  }

  // ACTION : S'inscrire
  async function register(email: string, mdp: string, firstName: string, lastName: string, registerPseudo: string, phone: string, role: number = 0) {
    try {
      // Envoie toutes les infos saisies dans le formulaire vers la route signup du backend
      const res = await axios.post(`${API_BASE_URL}/auth/signup`, {
          email: email,
          password: mdp,
          first_name: firstName,
          last_name: lastName,
          pseudo: registerPseudo,
          phone: phone,
          role: role
      });

      console.log('Register response:', res.data);
      return res.data.profile;
    } catch (error: any) {
      console.error('Register error:', error);
      // Récupère le message d'erreur précis renvoyé par PHP, ou un message générique
      const errorMsg = error.response?.data?.error || `Erreur serveur: ${error.response?.status || error.message}`;
      throw new Error(errorMsg);
    }
  }

  // ACTION : Se déconnecter
  function logout() {
    //vide la mémoire vive (Vue)
    token.value = null;
    photographerId.value = null;
    userEmail.value = null;
    pseudo.value = null;
    
    //vide la mémoire morte (Navigateur) pour empêcher la reconnexion automatique
    localStorage.removeItem('token');
    localStorage.removeItem('photographer_id');
    localStorage.removeItem('user_email');
    localStorage.removeItem('pseudo');
  }

  //rend ces variables et fonctions accessibles aux composants Vue
  return { token, photographerId, userEmail, pseudo, isAuthenticated, login, register, logout }
})