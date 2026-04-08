# PhotoPro

Plateforme de gestion de galeries photos entre photographes et clients.

## Fonctionnalités

- Inscription et authentification des photographes (JWT)
- Création et gestion de galeries publiques et privées
- Upload de photos avec stockage S3
- Accès aux galeries privées via code sécurisé
- Commentaires visiteurs sur les photos
- Notifications email à la publication d'une galerie
- Application mobile pour la consultation

## Architecture

```
PhotoPro/
├── app-mobile/              # Application Flutter (iOS/Android/Web)
├── app-nuxt/                # Frontend public SSR (Nuxt.js)
├── app-vue/                 # Backoffice photographe (Vue.js)
├── back/
│   ├── app-auth/            # Service d'authentification
│   ├── app-galerie/         # Service de gestion des galeries
│   ├── app-mail/            # Service d'envoi d'emails
│   ├── app-storage/         # Service de stockage S3
│   ├── gateway-backoffice/  # Passerelle backoffice
│   └── gateway-frontoffice/ # Passerelle frontoffice
├── sql/                     # Schémas et données de seed PostgreSQL
├── S3/                      # Configuration SeaweedFS
└── docker-compose.yml
```

**Stack :** PHP 8.4 (Slim 4) · Vue.js 3 · Nuxt.js 4 · Flutter · PostgreSQL · RabbitMQ · SeaweedFS · Docker

## Démarrage rapide

**Prérequis :** Docker, Docker Compose

```bash
git clone https://github.com/BaptisteDelaborde/PhotoPro.git
cd PhotoPro
docker compose up -d --build
```

## Services

| Service              | URL locale                       |
|----------------------|----------------------------------|
| Backoffice Vue.js    | http://localhost:5173            |
| Frontend Nuxt        | http://localhost:3001            |
| Gateway backoffice   | http://localhost:8081            |
| Gateway frontoffice  | http://localhost:8082            |
| Adminer (BDD)        | http://localhost:8080            |
| MailCatcher          | http://localhost:1081            |
| RabbitMQ UI          | http://localhost:15672           |
| S3 (SeaweedFS)       | http://localhost:8333            |

## Configuration

Copier et adapter le fichier `.env` à la racine. Les variables principales :

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

Pour un déploiement sur Docketu, utiliser `.env.docketu`.

## Tests API

Les collections Bruno se trouvent dans [bruno/](bruno/). Importer le dossier dans Bruno IDE pour accéder à l'ensemble des requêtes.

Comptes de test disponibles :
- `alice.dubois@mail.com` / `password`
- `thomas.moreau@mail.com` / `password`
- `sophie.laurent@mail.com` / `password`

## Équipe
- Baptiste Delaborde
- Léo Clerc 
- Mattéo Cadet
- Quentin Dieudonne
- Ryad Haddad 
- Burak Ozen 
