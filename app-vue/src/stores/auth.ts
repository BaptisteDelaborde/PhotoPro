import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || null)
  const photographerId = ref(localStorage.getItem('photographer_id') || null)

  const isAuthenticated = computed(() => !!token.value)

  async function login(email: string, mdp: string) {
    try {
      const res = await axios.post('http://localhost:8081/auth/signin', {
          email: email,
          password: mdp
      });

      const data = res.data;

      token.value = data.payload.access_token;
      localStorage.setItem('token', data.payload.access_token);

      const pid = data.profile.id || "uuid_photo_123";
      photographerId.value = pid;
      localStorage.setItem('photographer_id', pid);
    } catch (error) {
      throw new Error('Identifiants invalides');
    }
  }

  async function register(email: string, password: string, role: number = 0) {
    try {
      const res = await axios.post('http://localhost:8081/auth/signup', {
          email: email,
          password: password,
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
    localStorage.removeItem('token');
    localStorage.removeItem('photographer_id');
  }

  return { token, photographerId, isAuthenticated, login, register, logout }
})

