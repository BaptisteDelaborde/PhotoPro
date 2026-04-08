<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

// Champs spécifiques au schéma SQL "photographes"
const firstName = ref('')
const lastName = ref('')
const pseudo = ref('')
const phone = ref('')

// Champs de la table "users"
const email = ref('')
const password = ref('')
const confirmPassword = ref('')

const errorMsg = ref('')
const successMsg = ref('')

// Fonction déclenchée à la soumission du formulaire d'inscription
const handleRegister = async () => {
    errorMsg.value = ''
    successMsg.value = ''

    // Vérification de sécurité basique
    if (!email.value || !password.value || !confirmPassword.value || !firstName.value || !lastName.value || !pseudo.value) {
        errorMsg.value = 'Veuillez remplir tous les champs obligatoires (*)'
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
        console.log("Tentative d'inscription pour le photographe:", firstName.value, lastName.value)
        // On passe toutes les valeurs au store.
        await authStore.register(email.value, password.value, firstName.value, lastName.value, pseudo.value, phone.value, 0)

        successMsg.value = 'Inscription réussie ! Redirection vers la connexion...'
        // setTimeout permet d'attendre 1.5 seconde pour laisser le temps 
        // à l'utilisateur de lire le message vert de succès avant de changer de page.
        setTimeout(() => {
            router.push('/connexion')
        }, 1500)
    } catch (e) {
        errorMsg.value = e instanceof Error ? e.message : "Erreur lors de l'inscription"
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

            <div class="form-row">
                <div class="form-group flex-1">
                    <label for="firstName">Prénom * :</label>
                    <input id="firstName" v-model="firstName" type="text" placeholder="Ex: Jean" required />
                </div>
                <div class="form-group flex-1">
                    <label for="lastName">Nom * :</label>
                    <input id="lastName" v-model="lastName" type="text" placeholder="Ex: Dupont" required />
                </div>
            </div>

            <div class="form-group">
                <label for="pseudo">Pseudo (utilisé dans vos liens) * :</label>
                <input id="pseudo" v-model="pseudo" type="text" placeholder="Ex: jean_photo" required />
            </div>

            <div class="form-group">
                <label for="phone">Téléphone :</label>
                <input id="phone" v-model="phone" type="tel" placeholder="Ex: 0612345678" />
            </div>

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;" />

            <div class="form-group">
                <label for="email">Email * :</label>
                <input id="email" v-model="email" type="email" required />
            </div>

            <div class="form-group">
                <label for="password">Mot de passe * :</label>
                <input id="password" v-model="password" type="password" required />
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirmer le mot de passe * :</label>
                <input id="confirmPassword" v-model="confirmPassword" type="password" required />
            </div>

            <button type="submit">Créer mon compte professionnel</button>

            <p v-if="errorMsg" class="error-message">{{ errorMsg }}</p>
            <p v-if="successMsg" class="success-message">{{ successMsg }}</p>
        </form>

        <div class="login-link">
            Vous avez déjà un compte ? <button type="button" class="link-button" @click="goToLogin">Se
                connecter</button>
        </div>
    </div>
</template>

<style scoped>
.container {
    max-width: 450px;
    margin: 50px auto;
    padding: 30px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    color: #333;
    margin-bottom: 30px;
}

.form-row {
    display: flex;
    gap: 15px;
}

.flex-1 {
    flex: 1;
}

.form-group {
    margin-bottom: 15px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
    font-size: 14px;
}

input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    box-sizing: border-box;
    transition: border-color 0.2s;
}

input:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
}

button[type="submit"] {
    width: 100%;
    padding: 12px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    font-size: 15px;
    margin-top: 20px;
    transition: background-color 0.2s;
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
    font-size: 14px;
}

.success-message {
    color: #388e3c;
    margin-top: 15px;
    padding: 10px;
    background-color: #e8f5e9;
    border-radius: 5px;
    border-left: 4px solid #388e3c;
    font-size: 14px;
}

.login-link {
    text-align: center;
    margin-top: 20px;
    color: #666;
    font-size: 14px;
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