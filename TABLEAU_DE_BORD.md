# Tableau de bord — PhotoPro

## Équipe

| Prénom | Rôle principal |
|---|---|
| Baptiste | Architecture générale, backend gateway frontoffice, tests Bruno, notifications RabbitMQ/mail, intégration Nuxt, API commentaires, docker-compose |
| Ryad | Service de stockage S3 (app-storage), gateway backoffice upload, intégration S3 dans app-galerie |
| Quentin | Frontend Vue.js (backoffice photographe) : login, inscription, axios intercepteur JWT, galeries, visualisation photos |
| Dio | Frontend Nuxt SSR (espace client) : visualisation galeries publiques, lightbox, galerie par code privé, CSS |
| Mattéo | Application mobile, gateway backoffice |
| Léo | Backend app-galerie : actions CRUD galeries, routes, accès galerie par code, configuration JWT/docker-compose |

---

## URLs de déploiement (Docketu)

| Service | URL |
|---|---|
| Espace client (Nuxt SSR) | http://docketu.iutnc.univ-lorraine.fr:21859 |
| Backoffice photographe (Vue.js) | http://docketu.iutnc.univ-lorraine.fr:21858 |
| Gateway frontoffice (API publique) | http://docketu.iutnc.univ-lorraine.fr:21856 |
| Gateway backoffice (API protégée) | http://docketu.iutnc.univ-lorraine.fr:21857 |

---

## Identifiants de test

| Rôle | Email | Mot de passe |
|---|---|---|
| Photographe | alice.dubois@mail.com | password |

---

## Fonctionnalités implémentées

### Backend (PHP / Slim)
- Authentification JWT (inscription, connexion)
- CRUD galeries (créer, lire, modifier, supprimer)
- Gestion des photos (upload vers S3, suppression)
- Publication / dépublication d'une galerie
- Accès galerie privée par code d'accès
- Galeries publiques (liste)
- Commentaires sur photos (ajouter, lister)
- Notifications email via RabbitMQ + MailHog (publication galerie)
- Architecture microservices : app-auth, app-galerie, app-storage, app-mail, gateway-frontoffice, gateway-backoffice

### Frontend backoffice photographe (Vue.js)
- Connexion / inscription
- Liste des galeries du photographe connecté
- Création d'une galerie
- Visualisation des photos d'une galerie
- Interception JWT automatique via axios

### Espace client (Nuxt SSR)
- Liste des galeries publiques
- Visualisation d'une galerie publique avec lightbox
- Accès à une galerie privée via URL avec code (`/p/[code]`)

### Infrastructure
- Docker Compose complet (8 services)
- Variables d'environnement séparées (`.env` local, `.env.docketu`)
- SSR Nuxt : appels internes via réseau Docker (gateway_frontoffice:80)

---

## Ports locaux

| Service | Port |
|---|---|
| Gateway frontoffice | 8082 |
| Gateway backoffice | 8083 |
| Backoffice Vue.js | 5173 |
| Espace client Nuxt | 3001 |
| Stockage S3 (MinIO) | 8333 |
| MailHog | 8025 |
| Adminer | 8080 |
