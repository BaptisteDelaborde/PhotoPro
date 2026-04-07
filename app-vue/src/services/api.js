import axios from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL

const axiosInstance = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json'
  }
})

axiosInstance.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export const apiGestion = {

  async creerGalerie(donnees) {
    console.log("POST /galeries", donnees);
    try {
      const res = await axiosInstance.post('/galeries', donnees)
      return res.data
    } catch (error) {
      console.error('Erreur création galerie:', error)
      throw error
    }
  },

  async uploadPhoto(fichier, photographeId, galerieId) {
    console.log(`POST /photographes/${photographeId}/galeries/${galerieId}/photos`, fichier.name);
    
    const formData = new FormData();
    formData.append('photo', fichier);

    try {
      const res = await axiosInstance.post(`/photographes/${photographeId}/galeries/${galerieId}/photos`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return res.data
    } catch (error) {
      console.error('Erreur upload photo:', error)
      throw error
    }
  },

  async deletePhoto(photographeId, galerieId, photoId) {
    console.log(`DELETE /photographes/${photographeId}/galeries/${galerieId}/photos/${photoId}`);
    try {
      const res = await axiosInstance.delete(`/photographes/${photographeId}/galeries/${galerieId}/photos/${photoId}`);
      return res.data;
    } catch (error) {
      console.error('Erreur suppression photo:', error);
      throw error;
    }
  },

  async getMesGaleries() {
    console.log("GET /galeries (mes galeries)");
    try {
      const res = await axiosInstance.get('/galeries')
      return res.data
    } catch (error) {
      console.error('Erreur récupération des galeries:', error)
      throw error
    }
  },

  async getGalleryPhotos(photographeId, galleryId) {
    console.log(`GET /photographes/${photographeId}/galeries/${galleryId}/photos`);
    try {
      const res = await axiosInstance.get(`/photographes/${photographeId}/galeries/${galleryId}/photos`)
      return res.data
    } catch (error) {
      console.error('Erreur récupération photos galerie:', error)
      throw error
    }
  },

  async post(endpoint, data) {
    try {
      const res = await axiosInstance.post(endpoint, data)
      return res.data
    } catch (error) {
      console.error('Erreur POST:', error)
      throw error
    }
  },

  async get(endpoint) {
    try {
      const res = await axiosInstance.get(endpoint)
      return res.data
    } catch (error) {
      console.error('Erreur GET:', error)
      throw error
    }
  }
};