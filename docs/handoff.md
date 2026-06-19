# Handoff — EventRent

## Goal

Build **EventRent**, a Symfony 7.x web app for renting AV/event equipment. Full spec is in `docs/cahier_des_charges_EventRent.md`. This is a school end-of-cycle project graded on:

- 15 Doctrine entities (CTI inheritance, 2+ ManyToMany, 8+ OneToMany)
- Security (3 roles, custom Voter)
- API endpoint `/api/v1/equipments` with Serializer groups
- Mailer (transactional emails)
- HttpClient (OpenWeatherMap integration)
- Form Events (PRE_SET_DATA / PRE_SUBMIT)
- QueryBuilder in repositories
- EasyAdmin back-office
- 10+ Twig pages
- 1 unit test + 1 functional test
- CI pipeline (PHPStan lvl 5, linter, tests)
- Online deployment
- **Bonus**: Mercure (real-time notifications), Messenger (async emails), CLI commands

---

## Current State of the Code

### Infrastructure — DONE
- `compose.yaml` — fully configured: PostgreSQL (`eventrent/eventrent`, port 5439), Adminer (8088), PHP container (8089), Messenger worker, Mercure (3000), Mailpit (1025/8025)
- `Dockerfile.dev` — PHP 8.4-cli-alpine with pdo_pgsql, APCu, Redis, AMQP
- `docker/entrypoint.sh` — auto-runs composer install, migrations, cache warmup on container start

### Entities — DONE (migration ran successfully, 58 SQL queries)

All 15 entities created and migrated:

| Entity | File | Status |
|---|---|---|
| User | `src/Entity/User.php` | ✅ |
| Category | `src/Entity/Category.php` | ✅ |
| Supplier | `src/Entity/Supplier.php` | ✅ |
| Accessory | `src/Entity/Accessory.php` | ✅ |
| Equipment | `src/Entity/Equipment.php` | ✅ CTI parent |
| AudioEquipment | `src/Entity/AudioEquipment.php` | ✅ CTI child |
| VideoEquipment | `src/Entity/VideoEquipment.php` | ✅ CTI child |
| Reservation | `src/Entity/Reservation.php` | ✅ |
| ReservationLine | `src/Entity/ReservationLine.php` | ✅ |
| Quote | `src/Entity/Quote.php` | ✅ |
| QuoteLine | `src/Entity/QuoteLine.php` | ✅ |
| Invoice | `src/Entity/Invoice.php` | ✅ |
| Review | `src/Entity/Review.php` | ✅ |
| Maintenance | `src/Entity/Maintenance.php` | ✅ |
| Notification | `src/Entity/Notification.php` | ✅ |

All repositories exist in `src/Repository/`.

Migration file: `migrations/Version20260615193559.php`

### Nothing else built yet
- No controllers
- No templates
- No security config (beyond what `make:user` generated)
- No fixtures
- No API
- No forms
- No EasyAdmin

---

## Files Actively Edited This Session

- `compose.yaml` — full rewrite (added Mercure, renamed DB credentials, added env vars)
- `Dockerfile.dev` — base image fix (fpm → cli)
- `docker/entrypoint.sh` — removed broken `.env.example` block
- `src/Entity/*.php` — all 15 entities written/fixed; `User::$roles` default changed to `['ROLE_USER']`
- `src/Repository/*.php` — all 15 repositories created
- `src/DataFixtures/*.php` — 11 fixture files (one per domain), `DependentFixtureInterface` for ordering
- `src/Controller/SecurityController.php` — login + logout
- `src/Controller/RegistrationController.php` — registration form handling
- `src/Controller/AccountController.php` — profile page (reservations, quotes, invoices tabs)
- `src/Controller/HomeController.php` — landing page
- `src/Controller/AdminController.php` — stub redirect (will be replaced by EasyAdmin)
- `src/Form/RegistrationFormType.php` — registration form with email/names/phone/password
- `config/packages/security.yaml` — role hierarchy, form_login, logout, access_control
- `templates/base.html.twig` — Bootstrap 5 CDN, nav bar, flash messages
- `templates/security/login.html.twig` — login form
- `templates/registration/register.html.twig` — registration form
- `templates/account/index.html.twig` — profile with tabbed reservations/quotes/invoices
- `templates/home/index.html.twig` — landing page
- `src/Controller/CatalogController.php` — list + detail with QueryBuilder filters (uses Repository constants)
- `src/Repository/EquipmentRepository.php` — `search()`, `findOneWithRelations()`, `countByCategory()` with `Equipment::STATUS_*` and `self::SORT_*` constants
- `src/Entity/Equipment.php` — added `STATUS_*` / `TYPE_*` constants and `getType()` method
- `templates/catalog/index.html.twig` — dynamic grid, sidebar filters, pagination (uses `EqStatus`, `EqType`, `params`, `sortOptions`)
- `templates/catalog/show.html.twig` — dynamic spec sheet, reviews, booking panel (uses `EqStatus`, `EqType`)
- `docs/modele_donnees_EventRent.md` — rewritten with English field names, French prose
- `docs/guide_setup_EventRent.md` — rewritten with English entity/field names, French prose
- `docs/cahier_des_charges_EventRent.md` — section 6 updated with English entity names

---

## What Failed

- **Docker volume credential mismatch**: we renamed the DB from `app/my-super-secret-password` to `eventrent/eventrent` in `compose.yaml`, but the existing `database_data` volume was already initialized with the old credentials. The `php` container kept crashing on migrations. Fix: `docker compose down -v && docker compose up -d` to wipe and reinitialize the volume.
- **DoctrineFixturesBundle v4 `getReference()` requires 2 args**: the second argument is the class (`$this->getReference($name, Entity::class)`). Failing to pass it throws `Too few arguments`. All dependent fixtures now pass the exact class (e.g. `AudioEquipment::class` vs `VideoEquipment::class`).
- **PostgreSQL sequences not reset by `--purge-with-truncate`**: after multiple fixture reloads, IDs kept incrementing (started at 21, 31...). Fix: use `doctrine:schema:drop --full-database --force && doctrine:migrations:migrate` before loading fixtures to reset sequences to 1.
- **Docker volume sync stale after host edits**: editing files on the host doesn't always sync to the container; Twig caches the stale version. Fix: `docker compose restart php && docker compose exec php rm -rf var/cache/dev/twig` after significant file changes.
- **Twig `instanceof` test unavailable**: the Twig 3.x `instanceof` / `instance_of` test is not available in this version. Workaround: added `getType()` method on `Equipment` entity that returns `Equipment::TYPE_AUDIO` / `Equipment::TYPE_VIDEO`.

---

## Next Steps (in order)

### 1. Fixtures ✅ DONE

11 fixture files using `DependentFixtureInterface` for ordering:

```
src/DataFixtures/
├── UserFixtures.php           (indépendant)
├── CategoryFixtures.php       (indépendant)
├── SupplierFixtures.php       (indépendant)
├── AccessoryFixtures.php      (indépendant)
├── EquipmentFixtures.php      → Category, Supplier, Accessory
├── ReservationFixtures.php    → User, Equipment
├── QuoteFixtures.php          → User, Equipment
├── InvoiceFixtures.php        → Reservation
├── ReviewFixtures.php         → User, Equipment
├── MaintenanceFixtures.php    → User, Equipment
└── NotificationFixtures.php   → User
```

Test accounts: `admin@eventrent.com`/`admin123` (ROLE_ADMIN), `tech@eventrent.com`/`tech123` (ROLE_TECHNICIEN), `user@eventrent.com`/`user123` (ROLE_USER). All extras have `ROLE_USER`.

Full reset + load:
```bash
docker compose exec php sh -c "php bin/console doctrine:schema:drop --full-database --force && php bin/console doctrine:migrations:migrate --no-interaction && php bin/console doctrine:fixtures:load --no-interaction"
```

### 2. Security config ✅ DONE

- Role hierarchy: `ROLE_ADMIN > ROLE_TECHNICIEN > ROLE_USER`
- `form_login` on main firewall (`login_path: app_login`, `check_path: app_login`)
- `logout` (`path: app_logout`, target: `app_home`)
- Access control: `/admin` → ROLE_ADMIN, `/account` → ROLE_USER

### 3. Authentication pages ✅ DONE

- `SecurityController` — login + logout
- `RegistrationController` + `RegistrationFormType` — register with email/password/name/phone
- `AccountController` — profile page with reservations, quotes, invoices tabs
- `HomeController` — landing page at `/`
- `AdminController` — stub redirect `/admin` → `/` (will be replaced by EasyAdmin)
- `templates/base.html.twig` — Bootstrap 5 CDN, nav bar, flash messages
- `templates/security/login.html.twig`, `templates/registration/register.html.twig`, `templates/account/index.html.twig`, `templates/home/index.html.twig`

### 4. Equipment catalogue ✅ DONE

- `CatalogController` — list (`catalog_index`) + detail (`catalog_show`)
- `EquipmentRepository` — `search()` with QueryBuilder (cat, dispo, prix, tri, pagination), `findOneWithRelations()`, `countByCategory()`
- `Equipment` entity — constants: `STATUS_AVAILABLE`, `STATUS_MAINTENANCE`, `STATUS_OUT_OF_SERVICE`, `TYPE_AUDIO`, `TYPE_VIDEO`; `getType()` method
- `EquipmentRepository` — constants: `SORT_NAME`, `SORT_PRICE`, `SORT_PRICE_DESC`, `SORT_NEWEST`, `DEFAULT_LIMIT`
- `CatalogController` — constants: `PARAM_CAT`, `PARAM_DISPO`, `PARAM_PRIX_MAX`, `PARAM_SORT`, `PARAM_PAGE`, `PRICE_RANGE_MAX`
- Templates: `catalog/index.html.twig` (dynamic grid + filters), `catalog/show.html.twig` (dynamic spec sheet, reviews, booking panel)
- Templates use `EqStatus`, `EqType`, `params`, `sortOptions` variables — no hardcoded domain strings
- `EquipmentFixtures` + `MaintenanceFixtures` use `Equipment::STATUS_*` constants
- Nav link "Catalogue" wired to `catalog_index` route

### 5. Reservation flow
- `ReservationController` — form with Form Events (category → equipment dynamic filter)
- Availability check in `ReservationRepository` (QueryBuilder, no overlap)
- Price calculation on submit
- Custom Voter for cancellation (`src/Security/Voter/ReservationVoter.php`)

### 6. Quote flow
- `QuoteController` — similar to reservation but creates a `Quote` instead
- Admin validation in back-office

### 7. EasyAdmin back-office
```bash
docker compose exec php php bin/console make:admin:dashboard
docker compose exec php php bin/console make:admin:crud Equipment
# ... (see guide_setup section 22 for full list)
```

### 8. API endpoint
- `src/Controller/Api/EquipmentApiController.php`
- Route: `GET /api/v1/equipments` (list) + `GET /api/v1/equipments/{id}` (detail)
- Serializer groups: `equipment:list` and `equipment:detail`

### 9. Mailer
- Confirmation email on registration
- Confirmation email on reservation (with weather if outdoor)
- Quote approved/rejected email
- Use Messenger for async dispatch (`messenger:consume async` worker already running in Docker)

### 10. OpenWeatherMap integration
- `src/Service/WeatherService.php` using Symfony HttpClient
- Called during reservation creation if `venueType === 'outdoor'`

### 11. Mercure (real-time notifications)
- Publish to `/users/{id}/notifications` on `Notification` creation
- Front: `EventSource` in base Twig template, scoped JWT token

### 12. Tests
- 1 unit test: e.g. price calculation logic in a service
- 1 functional test: login form (`WebTestCase`)

### 13. CI pipeline
- `.github/workflows/ci.yml` — Symfony linter + PHPStan lvl 5 + PHPUnit

### 14. Deployment
- Platform.sh / Render / VPS — TBD
