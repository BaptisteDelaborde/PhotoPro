# Tests API — PhotoPro

## Prérequis

Lancer l'application en local via Docker :

```bash
docker compose up -d --build
```

---

## Importer la collection
Sélectionner le dossier `bruno/` à la racine du projet

---

## Sélectionner l'environnement

Dans Bruno, en haut à droite, sélectionner l'environnement local.

---

## Lancer les tests

Les requêtes sont conçues pour être exécutées dans l'ordre. Les tokens et identifiants sont extraits automatiquement de chaque réponse et injectés dans les suivantes. Rien n'est à renseigner manuellement.

### Backoffice (port 8081)

| # | Nom | Description |
|---|-----|-------------|
| 01 | Signup | Création d'un compte photographe |
| 02 | Signin | Authentification — extrait automatiquement le token JWT |
| 03 | Créer Galerie Privée | Création d'une galerie privée avec email client |
| 04 | Upload Photo | Upload d'une photo dans la galerie privée |
| 05 | Créer Galerie Publique | Création d'une galerie publique |
| 06 | Publier galerie | Publication de la galerie privée — déclenche l'envoi d'un mail au client |

### Frontoffice (port 8082)

| # | Nom | Description |
|---|-----|-------------|
| 01 | Galeries publiques | Liste de toutes les galeries publiques publiées |
| 02 | Galerie privée par code | Accès à une galerie privée via son code d'accès |

---

## Vérifier les mails (MailCatcher)

Lors de la publication de la galerie privée (test backoffice 06), un mail est envoyé automatiquement au client avec l'URL et le code d'accès à la galerie.

Accéder à l'interface MailCatcher :

```
http://localhost:1081
```

---