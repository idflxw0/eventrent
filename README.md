# EventRent — Plateforme de location de matériel audiovisuel

EventRent est une application web de location d'équipements audiovisuels et événementiels développée avec **Symfony 7.x**. Elle permet aux clients de parcourir un catalogue, de faire des demandes de devis ou de réserver directement du matériel (micros, enceintes, projecteurs, écrans…), pendant qu'un back-office d'administration et un espace technicien gèrent le parc et la maintenance.

**Application déployée : https://eventrent.pnzcorp.me/**

**Dépôt GitHub : https://github.com/idflxw0/eventrent**

---

## Fonctionnalités

| Fonctionnalité           | Détail                                                                                                    |
| ------------------------ | --------------------------------------------------------------------------------------------------------- |
| Catalogue                | Filtres par catégorie, disponibilité, prix — pagination — fiches détaillées                               |
| Réservations             | Formulaire dynamique (Form Events), vérification de disponibilité, calcul du prix                         |
| Devis                    | Demande en ligne, validation/refus par l'admin, conversion en réservation                                 |
| Factures                 | Générées automatiquement à chaque réservation confirmée                                                   |
| Avis clients             | Notation des équipements après une réservation terminée                                                   |
| Météo                    | Intégration OpenWeatherMap pour les événements en extérieur                                               |
| Emails                   | Confirmation d'inscription, réservation, devis, assignation technicien (asynchrones via Messenger)        |
| Notifications temps réel | Mercure — cloche de notification dans la navbar, mise à jour sans rechargement                            |
| Back-office              | EasyAdmin — gestion complète du catalogue, utilisateurs, réservations, devis, factures, avis, maintenance |
| Espace technicien        | Tableau de bord `/technicien` listant les interventions assignées                                         |
| API REST                 | `GET /api/v1/equipments` et `GET /api/v1/equipments/{id}` avec groupes de sérialisation                   |
| CLI                      | `app:quotes:expire` et `app:reservations:close` (tâches de maintenance planifiées)                        |

---

## Stack technique

| Composant        | Technologie                                                                                |
| ---------------- | ------------------------------------------------------------------------------------------ |
| Framework        | Symfony 7.x / PHP 8.4                                                                      |
| Base de données  | PostgreSQL 16 — Doctrine ORM (JOINED inheritance)                                          |
| Templates        | Twig (héritage de templates, filtres personnalisés `\|status_label`, `\|price_eur`)        |
| Back-office      | EasyAdminBundle 5                                                                          |
| Emails           | Symfony Mailer + Messenger (transport Doctrine, worker asynchrone)                         |
| Temps réel       | Mercure (SSE — Server-Sent Events)                                                         |
| API externe      | OpenWeatherMap via Symfony HttpClient                                                      |
| Sécurité         | Security Component — 3 rôles, Voter personnalisé (`ReservationVoter`)                      |
| Tests            | PHPUnit — tests unitaires + tests fonctionnels (WebTestCase)                               |
| Analyse statique | PHPStan niveau 5                                                                           |
| CI               | GitHub Actions — lint + PHPStan + tests automatisés                                        |
| Conteneurs       | Docker Compose (6 services : PHP, PostgreSQL, Mercure, Mailpit, Messenger worker, Adminer) |

---

## Prérequis

- [Docker](https://docs.docker.com/get-docker/) ≥ 24
- [Docker Compose](https://docs.docker.com/compose/) v2 (`docker compose` — sans tiret)

---

## Installation locale

### 1. Cloner le dépôt

```bash
git clone https://github.com/idflxw0/eventrent.git
cd eventrent
```

### 2. Configurer l'environnement

```bash
cp .env.example .env
```

Les valeurs par défaut dans `.env.example` fonctionnent sans modification pour un environnement local Docker. Optionnellement, renseigner `OPENWEATHER_API_KEY` pour activer les prévisions météo (clé gratuite sur [openweathermap.org](https://openweathermap.org/api)).

### 3. Démarrer les conteneurs

```bash
docker compose up -d
```

Au premier démarrage, le conteneur `php` exécute automatiquement :

- `composer install`
- `doctrine:migrations:migrate`
- `cache:warmup`

Attendre environ 30 secondes que la base de données soit prête, puis vérifier :

```bash
docker compose ps
```

Tous les services doivent afficher le statut `running` (ou `healthy` pour `database`).

### 4. Charger les fixtures

```bash
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

L'application est prête sur **http://localhost:8089**.

---

## Services Docker

| Service            | URL locale            | Description                                            |
| ------------------ | --------------------- | ------------------------------------------------------ |
| `php`              | http://localhost:8089 | Application Symfony (PHP 8.4 + FrankenPHP)             |
| `database`         | `localhost:5439`      | PostgreSQL 16                                          |
| `adminer`          | http://localhost:8088 | Explorateur de base de données                         |
| `mailer`           | http://localhost:8025 | Mailpit — visualisation des emails envoyés             |
| `mercure`          | http://localhost:3000 | Hub Mercure (temps réel)                               |
| `messenger-worker` | —                     | Worker asynchrone Messenger (emails en file d'attente) |

---

## Comptes de test

| Email               | Mot de passe | Rôle              | Accès                                                    |
| ------------------- | ------------ | ----------------- | -------------------------------------------------------- |
| admin@eventrent.com | admin123     | `ROLE_ADMIN`      | Back-office `/admin`, toutes les fonctionnalités         |
| tech@eventrent.com  | tech123      | `ROLE_TECHNICIEN` | Espace technicien `/technicien` + fonctionnalités client |
| user@eventrent.com  | user123      | `ROLE_USER`       | Catalogue, réservations, devis, espace personnel         |

> Hiérarchie des rôles : `ROLE_ADMIN` > `ROLE_TECHNICIEN` > `ROLE_USER`.

Ces comptes sont également disponibles sur l'environnement de production (voir section **Accès à l'environnement de production** ci-dessous).

---

## Réinitialiser la base de données

```bash
docker compose exec php sh -c "
  php bin/console doctrine:schema:drop --full-database --force &&
  php bin/console doctrine:migrations:migrate --no-interaction &&
  php bin/console doctrine:fixtures:load --no-interaction
"
```

---

## Exécuter les tests

```bash
docker compose exec php php bin/phpunit
```

| Fichier                            | Type                      | Description                                         |
| ---------------------------------- | ------------------------- | --------------------------------------------------- |
| `tests/Unit/EquipmentTypeTest.php` | Unitaire                  | Logique d'héritage CTI et calcul de prix journalier |
| `tests/Functional/LoginTest.php`   | Fonctionnel (WebTestCase) | Scénarios de connexion, accès public/protégé        |

---

## Analyse statique

```bash
docker compose exec php vendor/bin/phpstan analyse --level=5 src/
```

Configuration : `phpstan.neon` (niveau 5, extension `phpstan-doctrine`).

---

## Commandes CLI

### Expirer les devis en attente depuis plus de 15 jours

```bash
docker compose exec php php bin/console app:quotes:expire

# Aperçu sans modification
docker compose exec php php bin/console app:quotes:expire --dry-run
```

### Clôturer les réservations confirmées dont la date de fin est passée

```bash
docker compose exec php php bin/console app:reservations:close

# Aperçu sans modification
docker compose exec php php bin/console app:reservations:close --dry-run
```

---

## API REST

Base URL : `https://eventrent.pnzcorp.me/api/v1` (production) ou `http://localhost:8089/api/v1` (local)

### Liste des équipements

```
GET /api/v1/equipments
```

Retourne un tableau JSON avec le groupe de sérialisation `equipment:list` (id, référence, nom, prix/jour, statut, catégorie).

### Détail d'un équipement

```
GET /api/v1/equipments/{id}
```

Retourne le détail complet avec le groupe `equipment:detail` (description, fournisseur, accessoires, avis clients, spécifications audio ou vidéo).

Exemple de requête :

```bash
curl https://eventrent.pnzcorp.me/api/v1/equipments
curl https://eventrent.pnzcorp.me/api/v1/equipments/1
```

---

## Intégration continue (CI)

Le pipeline GitHub Actions (`.github/workflows/ci.yml`) s'exécute à chaque push sur `main` :

1. **Lint** — `lint:twig`, `lint:yaml`, `lint:container`
2. **PHPStan** — analyse statique niveau 5
3. **Base de données** — création + migrations
4. **Fixtures** — chargement du jeu de données de test
5. **Tests** — PHPUnit (unitaires + fonctionnels)

---

## Accès à l'environnement de production

### Application

| URL                                | Description            |
| ---------------------------------- | ---------------------- |
| https://eventrent.pnzcorp.me/      | Application principale |
| https://eventrent.pnzcorp.me/admin | Back-office EasyAdmin  |

### Comptes de test (production)

| Email               | Mot de passe | Rôle                                  |
| ------------------- | ------------ | ------------------------------------- |
| admin@eventrent.com | admin123     | `ROLE_ADMIN` — back-office complet    |
| tech@eventrent.com  | tech123      | `ROLE_TECHNICIEN` — espace technicien |
| user@eventrent.com  | user123      | `ROLE_USER` — espace client           |

### Base de données

L'interface d'administration de la base de données (Adminer) est accessible uniquement depuis le réseau privé du serveur (pas d'accès public). Pour toute consultation de la base en production, contacter le développeur.

---

## Déploiement

**URL de production : https://eventrent.pnzcorp.me/**

Hébergé sur un homelab avec Cloudflare Tunnel + Traefik comme reverse proxy. Variables d'environnement requises en production :

| Variable                  | Description                                                                   |
| ------------------------- | ----------------------------------------------------------------------------- |
| `APP_ENV`                 | `prod`                                                                        |
| `APP_SECRET`              | Clé secrète Symfony (32 caractères aléatoires)                                |
| `DATABASE_URL`            | DSN PostgreSQL (`postgresql://user:pass@host:5432/db`)                        |
| `MAILER_DSN`              | DSN SMTP (`smtp://user:pass@host:587`)                                        |
| `MESSENGER_TRANSPORT_DSN` | Transport Messenger (`doctrine://default`)                                    |
| `MERCURE_URL`             | URL interne du hub Mercure (pour publication)                                 |
| `MERCURE_PUBLIC_URL`      | URL publique du hub Mercure (pour le navigateur)                              |
| `MERCURE_JWT_SECRET`      | Secret JWT partagé avec le hub Mercure                                        |
| `OPENWEATHER_API_KEY`     | Clé API OpenWeatherMap (optionnel — désactive la météo si absent)             |
| `DEFAULT_URI`             | URL de base pour la génération d'URLs en CLI (`https://eventrent.pnzcorp.me`) |
