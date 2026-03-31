<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const email = ref('')
const password = ref('')

const handleLogin = () => {
    // Plus tard, vous ferez un appel à votre API Identité ici
    console.log('Tentative de connexion avec:', email.value)

    // Simulation d'une connexion réussie
    router.push('/galeries')
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
        </form>
    </div>
</template>