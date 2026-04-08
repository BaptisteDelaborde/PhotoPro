import axios from 'axios'

// L'adresse de la Gateway
const API_BASE_URL = import.meta.env.VITE_API_BASE_URL

//instance d'Axios préconfigurée
const axiosInstance = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json'
  }
})

// Avant CHAQUE requête qui part de l'application, Axios passe par ici.
// S'il trouve un token dans le localStorage, il l'attache automatiquement dans l'en-tête "Authorization".
// C'est ce qui permet à AuthnMiddleware(PHP) de reconnaître !
axiosInstance.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// On exporte un  objet apiGestion qui contient toutes les actions possibles.
export const apiGestion = {
  // Met à jour si la galerie est publique/brouillon
  async updateGalerieStatus(id, donnees) {
    try {
      const res = await axiosInstance.patch(`/galeries/${id}/status`, donnees)
      return res.data
    } catch (error) {
      console.error('Erreur mise à jour statut:', error)
      throw error
    }
  },

  async creerGalerie(donnees) {
    try {
      const res = await axiosInstance.post('/galeries', donnees)
      return res.data
    } catch (error) {
      console.error('Erreur création galerie:', error)
      throw error
    }
  },

  async getMesGaleries() {
    try {
      const res = await axiosInstance.get('/galeries')
      return res.data
    } catch (error) {
      console.error('Erreur récupération des galeries:', error)
      throw error
    }
  },

  // Upload une photo directement DEPUIS l'intérieur d'une galerie
  async uploadPhoto(fichier, photographeId, galerieId) {
    // Utilisation de FormData car on envoie un fichier binaire (multipart/form-data), pas du JSON !
    const formData = new FormData();
    formData.append('photo', fichier);
    try {
      const res = await axiosInstance.post(`/photographes/${photographeId}/galeries/${galerieId}/photos`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      return res.data
    } catch (error) {
      console.error('Erreur upload photo:', error);
      throw error;
    }
  },

  // Upload une photo dans le stockage S3 du photographe
  async uploadPhotoToStorage(fichier, photographeId) {
    const formData = new FormData();
    formData.append('photo', fichier);
    try {
      const res = await axiosInstance.post(`/photographes/${photographeId}/photos`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      return res.data
    } catch (error) {
      console.error('Erreur upload stockage global:', error);
      throw error;
    }
  },

  async getStoragePhotos(photographeId) {
    try {
      const res = await axiosInstance.get(`/photographes/${photographeId}/photos`)
      return res.data
    } catch (error) {
      console.error('Erreur récupération stockage:', error);
      throw error;
    }
  },

  async deletePhoto(photographeId, galerieId, photoId) {
    try {
      const res = await axiosInstance.delete(`/photographes/${photographeId}/galeries/${galerieId}/photos/${photoId}`);
      return res.data;
    } catch (error) {
      console.error('Erreur suppression photo:', error);
      throw error;
    }
  },

  async deleteGalerie(photographeId, galerieId) {
    try {
      const res = await axiosInstance.delete(`/photographes/${photographeId}/galeries/${galerieId}`);
      return res.data;
    } catch (error) {
      console.error('Erreur suppression galerie:', error);
      throw error;
    }
  },

  async updateGalerie(photographeId, galerieId, donnees) {
    try {
      const res = await axiosInstance.put(`/photographes/${photographeId}/galeries/${galerieId}`, donnees)
      return res.data
    } catch (error) {
      console.error('Erreur mise à jour galerie:', error);
      throw error;
    }
  },

  // Récupère les photos d'une galerie spécifique
  async getGalleryPhotos(photographeId, galleryId) {
    try {
      const res = await axiosInstance.get(`/photographes/${photographeId}/galeries/${galleryId}/photos`)
      return res.data
    } catch (error) {
      console.error('Erreur récupération photos galerie:', error);
      throw error;
    }
  },

  // Quand l'utilisateur modifie son profil (email, nom, prénom, etc), on appelle cette fonction pour envoyer les nouvelles données au backend 
  async updateProfile(photographeId, donnees) {
    try {
      const res = await axiosInstance.patch(`/auth/photographes/${photographeId}`, donnees)
      return res.data
    } catch (error) {
      console.error('Erreur mise à jour profil:', error)
      throw error
    }
  },

  // Fonction générique pour faire un POST basique si besoin
  async post(endpoint, data) {
    const res = await axiosInstance.post(endpoint, data)
    return res.data
  },

  // Lie ou délie une photo du stockage global avec une galerie spécifique
  async linkPhotoToGallery(photographeId, photoId, galerieId, action = 'add') {
    console.log(`PATCH /photographes/${photographeId}/photos/${photoId} (Action: ${action})`);
    try {
      const res = await axiosInstance.patch(`/photographes/${photographeId}/photos/${photoId}`, {
        galerie_id: galerieId,
        action: action
      });
      return res.data;
    } catch (error) {
      console.error('Erreur liaison photo:', error);
      throw error;
    }
  },

  // Fonction générique pour faire un GET basique
  async get(endpoint) {
    const res = await axiosInstance.get(endpoint)
    return res.data
  },

  async getGalerie(galerieId) {
    try {
      const res = await axiosInstance.get(`/galeries/${galerieId}`)
      return res.data
    } catch (error) {
      console.error('Erreur récupération galerie:', error)
      throw error
    }
  }
};