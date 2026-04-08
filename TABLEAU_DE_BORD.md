# Tableau de bord — PhotoPro

**Dépôt Git :** https://github.com/BaptisteDelaborde/PhotoPro

---

## Lancer le projet en local

### 1. Cloner le dépôt

```bash
git clone https://github.com/BaptisteDelaborde/PhotoPro.git
cd PhotoPro
```

### 2. Créer le fichier `.env` à la racine

Le fichier `.env` n'est pas versionné. Il doit être créé à la racine du projet avec le contenu suivant :

```env
PORT_GATEWAY_FRONTOFFICE=8082
PORT_GATEWAY_BACKOFFICE=8081
PORT_FRONTEND=5173
PORT_NUXT=3001
PORT_ADMINER=8080
PORT_RABBITMQ_UI=15672
PORT_RABBITMQ_AMQP=5672
PORT_MAILCATCHER=1081
PORT_S3=8333
VITE_API_BASE_URL=http://localhost:8081
S3_EXTERNAL_ENDPOINT=http://localhost:8333
NUXT_PUBLIC_API_FRONTOFFICE_URL=http://localhost:8082
NUXT_FRONTEND_URL=http://localhost:3001
```

### 3. Lancer tous les services

```bash
docker compose up -d --build
```
---

## Équipe

| Prénom   | Rôle principal                                                                                                                                                                                         |
| -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Baptiste | Gateway frontoffice (CORS, error middleware, proxy), notifications RabbitMQ/mail (URL `/p/{code}`, env local/docketu), API commentaires, filtre galeries par photographe (Nuxt + DI), docker-compose, tests Bruno |
| Ryad     | Service de stockage S3 (app-storage), gateway backoffice upload, intégration S3 dans app-galerie                                                                                                       |
| Quentin  | Frontend Vue.js (backoffice photographe) : login, inscription, axios intercepteur JWT, galeries, visualisation photos                                                                                  |
| Burak      | Frontend Nuxt SSR (espace client) : visualisation galeries publiques, lightbox, galerie par code privé, CSS                                                                                            |
| Mattéo   | Application mobile, gateway backoffice, microservice d'authentification                                                                                                                                |
| Léo      | Backend app-galerie : actions CRUD galeries, routes, accès galerie par code, configuration JWT/docker-compose, microservice d'authentification                                                         |

---

## Accès aux services

### En local

| Service              | URL                                  |
| -------------------- | ------------------------------------ |
| Espace client Nuxt   | http://localhost:3001                |
| Backoffice Vue.js    | http://localhost:5173                |
| Gateway frontoffice  | http://localhost:8082                |
| Gateway backoffice   | http://localhost:8081                |
| Stockage S3          | http://localhost:8333                |
| MailCatcher          | http://localhost:1081                |
| Adminer              | http://localhost:8080                |
| RabbitMQ UI          | http://localhost:15672               |

### Sur Docketu

| Service              | URL                                               |
| -------------------- | ------------------------------------------------- |
| Espace client Nuxt   | http://docketu.iutnc.univ-lorraine.fr:21859       |
| Backoffice Vue.js    | http://docketu.iutnc.univ-lorraine.fr:21858       |
| Gateway frontoffice  | http://docketu.iutnc.univ-lorraine.fr:21856       |
| Gateway backoffice   | http://docketu.iutnc.univ-lorraine.fr:21857       |
| MailCatcher          | http://docketu.iutnc.univ-lorraine.fr:21863       |

---

## Identifiants de test

| Rôle        | Email                   | Mot de passe |
| ----------- | ----------------------- | ------------ |
| Photographe | alice.dubois@mail.com   | password     |
| Photographe | thomas.moreau@mail.com  | password     |
| Photographe | sophie.laurent@mail.com | password     |

---

## Fonctionnalités implémentées

### Backend — Authentification (app-auth)

- Inscription photographe (email, mot de passe, prénom, nom, pseudo, téléphone)
- Connexion avec génération de tokens JWT (access + refresh)
- Rafraîchissement de token (`/refresh`)
- Validation de token (utilisée par les gateways)

### Backend — Galeries & Photos (app-galerie)

- Création de galerie publique ou privée (titre, description, layout, code accès auto-généré, email client)
- Lecture, modification et suppression d'une galerie
- Publication / dépublication (avec vérification ≥ 1 photo obligatoire)
- Liste des galeries d'un photographe (authentifié)
- Liste des galeries publiques publiées (avec filtre par photographe)
- Accès à une galerie privée par code d'accès (`/galeries/code/{code}`)
- Upload de photos vers l'espace personnel du photographe (pot commun)
- Récupération des photos de l'espace personnel
- Liaison photo → galerie
- Suppression d'une photo d'une galerie
- Récupération des photos d'une galerie
- Ajout et lecture de commentaires sur une photo
- Liste des photographes

### Backend — Notifications (app-mail)

- Envoi d'email via RabbitMQ (AMQP) + Symfony Mailer + MailCatcher
- Événements couverts : publication, dépublication, modification d'une galerie privée
- Lien direct dans le mail vers `/p/{code}` (Nuxt)
- URL adaptée à l'environnement (local ou docketu) via `FRONTEND_URL`

### Backend — Stockage (app-storage)

- Upload de fichiers images vers SeaweedFS
- Accès aux fichiers via URL publique S3

### Backend — Gateways

- **Gateway frontoffice** : proxy public vers app-galerie et app-auth, CORS, error middleware, routes protégées pour upload mobile (JWT)
- **Gateway backoffice** : proxy authentifié vers app-galerie et app-auth, actions spécialisées upload/delete photo, CORS

### Frontend backoffice photographe (Vue.js)

- Connexion et inscription avec store Pinia persistant
- Protection des routes (guard — redirige vers login si non connecté)
- Intercepteur axios JWT automatique (ajout du Bearer token sur chaque requête)
- Liste des galeries avec filtres (toutes / publique / privée, recherche par titre)
- Création d'une galerie (layout masonry ou grille, visibilité, code d'accès, email client)
- Détail d'une galerie avec ses photos
- Ajout de photos à une galerie depuis l'espace personnel
- Suppression d'une photo dans une galerie
- Upload de photos vers l'espace personnel (glisser-déposer ou sélection)
- Vue espace de stockage (toutes les photos uploadées)
- Page profil photographe
- Publication / dépublication d'une galerie

### Application Mobile (Flutter)

- Liste des galeries publiques avec pull-to-refresh
- Saisie d'un code pour accéder à une galerie privée
- Détail d'une galerie (photos en grille 3 colonnes)
- Visualisation d'une photo en grand avec navigation (avant / après) et Hero animation
- Ajout de commentaires sur les photos d'une galerie privée (anonyme)
- Cache local hors-ligne via Isar DB
- Architecture Clean (Domain / Data / Presentation)
- State management Riverpod, navigation GoRouter
- Client HTTP Dio avec interceptors

### Espace client (Nuxt SSR)

- Page d'accueil : accès galerie privée par code + liste galeries publiques
- Filtre des galeries par photographe
- Détail d'une galerie publique avec lightbox photos (navigation, plein écran)
- Accès galerie privée via URL directe `/p/[code]` (fetch côté client)
- Accès galerie privée via lien reçu par mail

