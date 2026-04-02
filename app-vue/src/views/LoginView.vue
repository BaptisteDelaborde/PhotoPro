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

const goToRegister = () => {
    router.push('/register')
}
</script>

<template>
    <div class="container">
        <h2>Connexion Photographe</h2>
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
        <div class="register-link">
            Pas encore de compte ? <button type="button" class="link-button" @click="goToRegister">S'inscrire</button>
        </div>
    </div>
</template>

<style scoped>
.container {
    max-width: 400px;
    margin: 50px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    box-sizing: border-box;
}

input[type="email"]:focus,
input[type="password"]:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
}

button[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    font-size: 14px;
    margin-top: 20px;
}

button[type="submit"]:hover {
    background-color: #45a049;
}

.error-message {
    color: #d32f2f;
    margin-top: 15px;
    padding: 10px;
    background-color: #ffebee;
    border-radius: 5px;
    border-left: 4px solid #d32f2f;
}

.register-link {
    text-align: center;
    margin-top: 20px;
    color: #666;
}

.link-button {
    background: none;
    border: none;
    color: #4CAF50;
    cursor: pointer;
    font-weight: bold;
    text-decoration: underline;
    padding: 0;
    font-size: 14px;
}

.link-button:hover {
    color: #45a049;
}
</style>

