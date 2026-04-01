<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const errorMsg = ref('')

const handleLogin = async () => {
    try {
        console.log('Tentative de connexion avec:', email.value)
        await authStore.login(email.value, password.value)
        router.push('/galeries')
    } catch (e) {
        errorMsg.value = 'Échec de la connexion. Veuillez vérifier vos identifiants.'
        console.error('Erreur de connexion:', e)
    }
}
</script>

<template>
    <div class="container">
        <h2>Connexion Backoffice Photographe</h2>
        <form @submit.prevent="handleLogin">
            <div class="form-group">
                <label for="email">Email :</label>
                <input id="email" v-model="email" type="email" required />
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input id="password" v-model="password" type="password" required />
            </div>
            <button type="submit">Se connecter</button>
            <div v-if="errorMsg" class="error-message">{{ errorMsg }}</div>
        </form>
    </div>
</template>

<style scoped>
.error-message {
    color: red;
    margin-top: 10px;
}
</style>
