import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL

function decodeToken(token: string | null): string | null {
  if (!token) return null
  try {
    const parts = token.split('.')
    const payload = JSON.parse(atob(parts[1]))
    return payload.data?.user || null
  } catch {
    return null
  }
}

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || null)
  const photographerId = ref(localStorage.getItem('photographer_id') || null)
  const userEmail = ref(localStorage.getItem('user_email') || null)

  const isAuthenticated = computed(() => !!token.value)

  async function login(email: string, mdp: string) {
    try {
      const res = await axios.post(`${API_BASE_URL}/auth/signin`, {
          email: email,
          password: mdp
      });

      const data = res.data;

      token.value = data.payload.access_token;
      localStorage.setItem('token', data.payload.access_token);

      const extractedEmail = decodeToken(data.payload.access_token)
      userEmail.value = extractedEmail
      if (extractedEmail) {
        localStorage.setItem('user_email', extractedEmail)
      }

      const pid = data.profile.id || "uuid_photo_123";
      photographerId.value = pid;
      localStorage.setItem('photographer_id', pid);
    } catch (error) {
      throw new Error('Identifiants invalides');
    }
  }

  async function register(email: string, mdp: string, firstName: string, lastName: string, pseudo: string, phone: string, role: number = 0) {
    try {
      const res = await axios.post(`${API_BASE_URL}/auth/signup`, {
          email: email,
          password: mdp,
          first_name: firstName,
          last_name: lastName,
          pseudo: pseudo,
          phone: phone,
          role: role
      });

      console.log('Register response:', res.data);
      return res.data.profile;
    } catch (error: any) {
      console.error('Register error:', error);
      const errorMsg = error.response?.data?.error || `Erreur serveur: ${error.response?.status || error.message}`;
      throw new Error(errorMsg);
    }
  }

  function logout() {
    token.value = null;
    photographerId.value = null;
    userEmail.value = null;
    localStorage.removeItem('token');
    localStorage.removeItem('photographer_id');
    localStorage.removeItem('user_email');
  }

  return { token, photographerId, userEmail, isAuthenticated, login, register, logout }
})

