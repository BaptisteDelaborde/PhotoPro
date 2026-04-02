import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || null)
  const photographerId = ref(localStorage.getItem('photographer_id') || null)

  const isAuthenticated = computed(() => !!token.value)

  async function login(email: string, mdp: string) {
    const res = await fetch('http://localhost:8081/auth/signin', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            email: email,
            password: mdp
        })
    });

    if (!res.ok) {
        throw new Error('Identifiants invalides');
    }

    const data = await res.json();

    token.value = data.payload.access_token;
    localStorage.setItem('token', data.payload.access_token);

    const pid = data.profile.id || "uuid_photo_123";
    photographerId.value = pid;
    localStorage.setItem('photographer_id', pid);
  }

  async function register(email: string, password: string, role: number = 0) {
    const res = await fetch('http://localhost:8081/auth/signup', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            email: email,
            password: password,
            role: role
        })
    });

    if (!res.ok) {
        const errorData = await res.json();
        throw new Error(errorData.error || 'Erreur lors de l\'inscription');
    }

    const data = await res.json();
    return data.profile;
  }

  function logout() {
    token.value = null;
    photographerId.value = null;
    localStorage.removeItem('token');
    localStorage.removeItem('photographer_id');
  }

  return { token, photographerId, isAuthenticated, login, register, logout }
})

