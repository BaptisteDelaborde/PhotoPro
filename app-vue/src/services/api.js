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

  async uploadPhoto(fichier, galerieId) {
    console.log("POST /photos", fichier.name);
    const formData = new FormData();
    formData.append('file', fichier);
    formData.append('gallery_id', galerieId);

    try {
      const res = await axiosInstance.post('/photos', formData, {
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

