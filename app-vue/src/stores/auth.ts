import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || null)
  const photographerId = ref(localStorage.getItem('photographer_id') || null)

  const isAuthenticated = computed(() => !!token.value)

  async function login(email: string, mdp: string) {
    const res = await fetch('http://localhost:8081/signin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Basic ${btoa(email + ':' + mdp)}`
        }
    });

    if (!res.ok) {
        throw new Error('Identifiants invalides');
    }

    const data = await res.json();

    token.value = data.access_token;
    localStorage.setItem('token', data.access_token);

    const pid = data.photographer_id || "uuid_photo_123";
    photographerId.value = pid;
    localStorage.setItem('photographer_id', pid);
  }

  function logout() {
    token.value = null;
    photographerId.value = null;
    localStorage.removeItem('token');
    localStorage.removeItem('photographer_id');
  }

  return { token, photographerId, isAuthenticated, login, logout }
})

