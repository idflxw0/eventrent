# EventRent — Plateforme de location de matériel audiovisuel

EventRent est une application web de location d'équipements audiovisuels et événementiels développée avec **Symfony 7.x**. Elle permet aux clients de parcourir un catalogue, de faire des demandes de devis ou de réserver directement du matériel (micros, enceintes, projecteurs, écrans…), pendant qu'un back-office d'administration et un espace technicien gèrent le parc et la maintenance.

**Application déployée : https://eventrent.pnzcorp.me/**

---

## Fonctionnalités

| Fonctionnalité | Détail |
|---|---|
| Catalogue | Filtres par catégorie, disponibilité, prix — pagination — fiches détaillées |
| Réservations | Formulaire dynamique (Form Events), vérification de disponibilité, calcul du prix |
| Devis | Demande en ligne, validation/refus par l'admin, conversion en réservation |
| Factures | Générées automatiquement à chaque réservation |
| Avis clients | Notation des équipements après une réservation terminée |
| Météo | Intégration OpenWeatherMap pour les événements en extérieur |
| Emails | Confirmation d'inscription, réservation, devis, assignation technicien (asynchrones via Messenger) |
| Notifications temps réel | Mercure — cloche de notification dans la navbar |
| Back-office | EasyAdmin — gestion complète du catalogue, utilisateurs, réservations, devis, factures, avis, maintenance |
| Espace technicien | Tableau de bord `/technicien` listant les interventions assignées |
| API REST | `GET /api/v1/equipments` et `GET /api/v1/equipments/{id}` |
| CLI | `app:quotes:expire` et `app:reservations:close` |

---

## Stack technique

| Composant | Technologie |
|---|---|
| Framework | Symfony 7.x / PHP 8.4 |
| Base de données | PostgreSQL 16 — Doctrine ORM (CTI inheritance) |
| Templates | Twig (héritage, filtres personnalisés `\|status_label`, `\|price_eur`) |
| Back-office | EasyAdminBundle 5 |
| Emails | Symfony Mailer + Messenger (transport Doctrine, async) |
| Temps réel | Mercure |
| API externe | OpenWeatherMap via Symfony HttpClient |
| Sécurité | Security Component — 3 rôles, Voter personnalisé (`ReservationVoter`) |
| Tests | PHPUnit — 1 test unitaire + 1 test fonctionnel |
| CI | GitHub Actions — lint + PHPStan niveau 5 + tests |
| Conteneurs | Docker Compose (PHP, PostgreSQL, Mercure, Mailpit, Messenger worker) |

---

## Prérequis

- [Docker](https://docs.docker.com/get-docker/) ≥ 24
- [Docker Compose](https://docs.docker.com/compose/) v2

---

## Installation locale

### 1. Cloner le dépôt

```bash
git clone <url-du-repo>
cd eventrent
```

### 2. Configurer l'environnement

```bash
cp .env.example .env
```

Les valeurs par défaut dans `.env.example` fonctionnent sans modification pour un environnement local Docker.

### 3. Démarrer les conteneurs

```bash
docker compose up -d
```

Au premier démarrage, le conteneur `php` exécute automatiquement :
- `composer install`
- `doctrine:migrations:migrate`
- `cache:warmup`

Attendre environ 30 secondes, puis vérifier que tout est en ordre :

```bash
docker compose ps
```

### 4. Charger les fixtures

```bash
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

L'application est prête sur **http://localhost:8089**.

---

## URLs locales

| Service | URL | Description |
|---|---|---|
| Application | http://localhost:8089 | Interface principale |
| Adminer | http://localhost:8088 | Explorateur de base de données |
| Mailpit | http://localhost:8025 | Visualisation des emails envoyés |
| Mercure | http://localhost:3000 | Hub temps réel |

---

## Comptes de test

| Email | Mot de passe | Rôle | Accès |
|---|---|---|---|
| admin@eventrent.com | admin123 | `ROLE_ADMIN` | Back-office `/admin`, toutes les fonctionnalités |
| tech@eventrent.com | tech123 | `ROLE_TECHNICIEN` | Espace technicien `/technicien`, fonctionnalités client |
| user@eventrent.com | user123 | `ROLE_USER` | Catalogue, réservations, devis, compte |

> Les rôles suivent la hiérarchie : `ROLE_ADMIN` > `ROLE_TECHNICIEN` > `ROLE_USER`.

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

- **Test unitaire** : `tests/Unit/EquipmentTypeTest.php` — vérifie la logique CTI et le calcul de prix
- **Test fonctionnel** : `tests/Functional/LoginTest.php` — scénarios de connexion avec `WebTestCase`

---

## Analyse statique

```bash
docker compose exec php vendor/bin/phpstan analyse --level=5 src/
```

---

## Commandes CLI (bonus)

### Expirer les devis en attente depuis plus de 15 jours

```bash
docker compose exec php php bin/console app:quotes:expire
docker compose exec php php bin/console app:quotes:expire --dry-run  # aperçu sans modification
```

### Clôturer les réservations confirmées dont la date de fin est passée

```bash
docker compose exec php php bin/console app:reservations:close
docker compose exec php php bin/console app:reservations:close --dry-run
```

---

## API REST

### Liste des équipements

```
GET /api/v1/equipments
```

Retourne un tableau JSON avec le groupe de sérialisation `equipment:list` (nom, prix, statut, catégorie).

### Détail d'un équipement

```
GET /api/v1/equipments/{id}
```

Retourne le détail complet avec le groupe `equipment:detail` (description, accessoires, avis, spécifications audio/vidéo).

---

## Intégration continue (CI)

Le pipeline GitHub Actions (`.github/workflows/ci.yml`) s'exécute à chaque push sur `main` :

1. **Lint** — `lint:twig`, `lint:yaml`, `lint:container`
2. **PHPStan** — analyse statique niveau 5
3. **Tests** — migrations + fixtures + PHPUnit

---

## Déploiement

**URL de production : https://eventrent.pnzcorp.me/**

Hébergé sur un VPS Hetzner. Variables d'environnement requises en production :

| Variable | Description |
|---|---|
| `APP_SECRET` | Clé secrète Symfony (32 caractères) |
| `DATABASE_URL` | DSN PostgreSQL |
| `MAILER_DSN` | DSN SMTP |
| `MERCURE_URL` | URL interne du hub Mercure |
| `MERCURE_PUBLIC_URL` | URL publique du hub Mercure |
| `MERCURE_JWT_SECRET` | Secret JWT Mercure |
| `OPENWEATHER_API_KEY` | Clé API OpenWeatherMap (optionnel) |
