<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const errorMsg = ref('')
const successMsg = ref('')

const handleRegister = async () => {
    errorMsg.value = ''
    successMsg.value = ''

    // Validation
    if (!email.value || !password.value || !confirmPassword.value) {
        errorMsg.value = 'Veuillez remplir tous les champs'
        return
    }

    if (password.value !== confirmPassword.value) {
        errorMsg.value = 'Les mots de passe ne correspondent pas'
        return
    }

    if (password.value.length < 6) {
        errorMsg.value = 'Le mot de passe doit contenir au moins 6 caractères'
        return
    }

    try {
        console.log('Tentative d\'inscription avec:', email.value)
        await authStore.register(email.value, password.value, 0)
        successMsg.value = 'Inscription réussie! Redirection vers la connexion...'
        router.push('/connexion')
    } catch (e) {
        errorMsg.value = e instanceof Error ? e.message : 'Erreur lors de l\'inscription'
        console.error('Erreur d\'inscription:', e)
    }
}

const goToLogin = () => {
    router.push('/connexion')
}
</script>

<template>
    <div class="container">
        <h2>Inscription Photographe</h2>
        <form @submit.prevent="handleRegister">
            <div class="form-group">
                <label for="email">Email :</label>
                <input id="email" v-model="email" type="email" required />
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input id="password" v-model="password" type="password" required />
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirmer le mot de passe :</label>
                <input id="confirmPassword" v-model="confirmPassword" type="password" required />
            </div>
            <button type="submit">S'inscrire</button>
            <div v-if="errorMsg" class="error-message">{{ errorMsg }}</div>
            <div v-if="successMsg" class="success-message">{{ successMsg }}</div>
        </form>
        <div class="login-link">
            Vous avez déjà un compte ? <button type="button" class="link-button" @click="goToLogin">Se connecter</button>
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

.success-message {
    color: #388e3c;
    margin-top: 15px;
    padding: 10px;
    background-color: #e8f5e9;
    border-radius: 5px;
    border-left: 4px solid #388e3c;
}

.login-link {
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

