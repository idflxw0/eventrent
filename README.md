# EventRent

Application web de location d'équipements audiovisuels et événementiels, développée avec Symfony 7.x.

## Prérequis

- [Docker](https://docs.docker.com/get-docker/) ≥ 24
- [Docker Compose](https://docs.docker.com/compose/) v2

## Installation locale

```bash
# 1. Cloner le dépôt
git clone <url-du-repo>
cd eventrent

# 2. Copier le fichier d'environnement
cp .env.example .env

# 3. Démarrer les conteneurs
docker compose up -d

# 4. Charger les fixtures (base vide → données de test)
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

L'application est disponible sur **http://localhost:8089** dès que le conteneur `php` a terminé ses migrations (environ 30 secondes au premier démarrage).

## URLs de développement

| Service | URL |
|---|---|
| Application | http://localhost:8089 |
| Adminer (base de données) | http://localhost:8088 |
| Mailpit (emails) | http://localhost:8025 |
| Mercure (hub temps réel) | http://localhost:3000 |

## Comptes de test

| Email | Mot de passe | Rôle |
|---|---|---|
| admin@eventrent.com | admin123 | Administrateur |
| tech@eventrent.com | tech123 | Technicien |
| user@eventrent.com | user123 | Client |

## Réinitialiser la base de données

```bash
docker compose exec php sh -c "
  php bin/console doctrine:schema:drop --full-database --force &&
  php bin/console doctrine:migrations:migrate --no-interaction &&
  php bin/console doctrine:fixtures:load --no-interaction
"
```

## Exécuter les tests

```bash
docker compose exec php php bin/phpunit
```

## Analyse statique (PHPStan niveau 5)

```bash
docker compose exec php vendor/bin/phpstan analyse --level=5 src/
```

## Mettre à jour le seed de production

Le fichier `docker/seed.sql` contient un dump des données de test (catalogue, utilisateurs, réservations…).
**À chaque fois que vous modifiez les fixtures**, régénérez-le et committez-le :

```bash
# 1. Recharger les fixtures en local
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction

# 2. Régénérer le dump
docker exec eventrent-database-1 pg_dump -U eventrent -d eventrent --data-only --clean \
  --exclude-table=doctrine_migration_versions \
  --exclude-table=messenger_messages \
  2>/dev/null > docker/seed.sql

# 3. Committer
git add docker/seed.sql
git commit -m "chore: update seed data"
git push
```

## Déploiement en production (VPS Hetzner)

```bash
# Sur le VPS, depuis /var/www/eventrent :
git pull origin main
composer install --no-dev --optimize-autoloader --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction --env=prod
psql -U eventrent -d eventrent < docker/seed.sql
php bin/console cache:clear --env=prod
```

Variables d'environnement à configurer en production (voir `.env.example`) :

- `APP_SECRET` — clé secrète Symfony (32 caractères aléatoires)
- `DATABASE_URL` — DSN PostgreSQL
- `MAILER_DSN` — DSN du serveur SMTP
- `MERCURE_URL` / `MERCURE_PUBLIC_URL` / `MERCURE_JWT_SECRET`
- `OPENWEATHER_API_KEY` — clé OpenWeatherMap (optionnel)

## URL de déploiement

**https://eventrent.pnzcorp.me/**

## Architecture technique

- **Framework** : Symfony 7.x / PHP 8.4
- **Base de données** : PostgreSQL 16 (Doctrine ORM, CTI inheritance)
- **Admin** : EasyAdmin Bundle 5
- **Emails** : Symfony Mailer (async via Messenger / Doctrine transport)
- **Temps réel** : Mercure
- **Météo** : OpenWeatherMap via Symfony HttpClient
- **Tests** : PHPUnit (unit + fonctionnel)
- **CI** : GitHub Actions (lint + PHPStan + tests)
