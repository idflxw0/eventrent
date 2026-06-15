# Guide de setup — EventRent

Ce document regroupe : la mise en place du projet (Symfony, Docker, packages), puis pour **chaque entité**, la liste exacte des champs et relations à saisir quand `make:entity` te pose ses questions dans le terminal.

---

## 1. Prérequis

```bash
php -v          # 8.2+
composer -V
docker compose version
```

## 2. Créer le projet

```bash
symfony new eventrent --version="7.2.*" --webapp
cd eventrent
```

## 3. `.env.local`

```
DATABASE_URL="postgresql://eventrent:eventrent@127.0.0.1:5439/eventrent?serverVersion=16&charset=utf8"

MERCURE_URL=http://localhost:3000/.well-known/mercure
MERCURE_PUBLIC_URL=http://localhost:3000/.well-known/mercure
MERCURE_JWT_SECRET="!ChangeThisMercureHubJWTSecretKey!"

MAILER_DSN=smtp://127.0.0.1:1025
```

## 4. Lancer les services et installer les packages

```bash
docker compose up -d

composer require symfony/serializer-pack
composer require symfony/mercure-bundle
composer require symfony/http-client
composer require easycorp/easyadmin-bundle

composer require --dev orm-fixtures
composer require --dev fakerphp/faker
composer require --dev symfony/test-pack
composer require --dev phpstan/phpstan phpstan/extension-installer phpstan/phpstan-symfony phpstan/phpstan-doctrine
```

---

## 5. Conventions à appliquer pendant la création des entités

Quelques règles transverses, à appliquer manuellement après chaque `make:entity` (le maker ne pose pas ces questions) :

- **Dates** : choisis toujours `datetime_immutable` (et `date_immutable` pour les champs `date`), c'est la recommandation actuelle de Doctrine. Pour les valeurs "par défaut = maintenant", initialise-les dans le constructeur de l'entité (`$this->createdAt = new \DateTimeImmutable();`).
- **Champs "unique"** (`Category.name`, `Equipment.reference`, `Invoice.number`...) : le maker ne demande pas l'unicité. Ajoute `unique: true` à la main dans l'attribut `#[ORM\Column(...)]` généré.
- **Valeurs par défaut de type "statut"** (`pending`, `available`...) : initialise-les dans le constructeur plutôt que via `options: ['default' => ...]`, c'est plus lisible et testable.
- **Decimal** : à chaque fois que le maker demande precision/scale pour un `decimal`, réponds `10` et `2`.

---

## 6. Ordre de création des entités

L'ordre compte : pour créer une relation `ManyToOne` vers une entité, celle-ci doit déjà exister.

1. `User` (via `make:user`)
2. `Category`
3. `Supplier`
4. `Accessory`
5. `Equipment` + `AudioEquipment` + `VideoEquipment` (héritage — section spéciale)
6. `Reservation`
7. `ReservationLine`
8. `Quote`
9. `QuoteLine`
10. `Invoice`
11. `Review`
12. `Maintenance`
13. `Notification`

---

## 7. `User`

```bash
php bin/console make:user
```

Réponses aux prompts :

- Nom de la classe : `User`
- Stocker le mot de passe (hash) ? : `yes`
- Champ d'unicité : `email`

Cela génère `id`, `email` (string 180, unique), `roles` (json), `password` (string).

Relance ensuite `php bin/console make:entity User` pour ajouter les champs métier manquants :

| Champ        | Type à entrer      | Détails      | Nullable |
| ------------ | ------------------ | ------------ | -------- |
| lastName     | string             | longueur 100 | non      |
| firstName    | string             | longueur 100 | non      |
| phone        | string             | longueur 20  | oui      |
| registeredAt | datetime_immutable | —            | non      |
| active       | boolean            | —            | non      |

Puis dans `src/Entity/User.php`, ajoute dans le constructeur :

```php
$this->registeredAt = new \DateTimeImmutable();
$this->active = true;
```

---

## 8. `Category`

```bash
php bin/console make:entity Category
```

| Champ       | Type à entrer | Détails                                   | Nullable |
| ----------- | ------------- | ----------------------------------------- | -------- |
| name        | string        | longueur 100 (+ `unique: true` à la main) | non      |
| description | text          | —                                         | oui      |

**Relations à ajouter ici** : aucune. La relation avec `Equipment` sera créée depuis `Equipment` (étape 11) — accepte la proposition d'ajouter le côté inverse à ce moment-là.

---

## 9. `Supplier`

```bash
php bin/console make:entity Supplier
```

| Champ   | Type à entrer | Détails      | Nullable |
| ------- | ------------- | ------------ | -------- |
| name    | string        | longueur 150 | non      |
| email   | string        | longueur 180 | oui      |
| phone   | string        | longueur 20  | oui      |
| address | string        | longueur 255 | oui      |

**Relations à ajouter ici** : aucune (idem Category, côté inverse ajouté depuis `Equipment`).

---

## 10. `Accessory`

```bash
php bin/console make:entity Accessory
```

| Champ       | Type à entrer | Détails      | Nullable |
| ----------- | ------------- | ------------ | -------- |
| name        | string        | longueur 150 | non      |
| description | text          | —            | oui      |

**Relations à ajouter ici** : aucune. Le ManyToMany avec `Equipment` sera créé depuis `Equipment` (étape 11).

---

## 11. `Equipment` + héritage (`AudioEquipment` / `VideoEquipment`)

### 11.1 Champs propres et relations de `Equipment`

```bash
php bin/console make:entity Equipment
```

| Champ              | Type à entrer      | Détails                                                 | Nullable |
| ------------------ | ------------------ | ------------------------------------------------------- | -------- |
| reference          | string             | longueur 50 (+ `unique: true` à la main)                | non      |
| name               | string             | longueur 150                                            | non      |
| description        | text               | —                                                       | oui      |
| dailyPrice         | decimal            | precision 10, scale 2                                   | non      |
| availabilityStatus | string             | longueur 20 (défaut `'available'` dans le constructeur) | non      |
| photo              | string             | longueur 255                                            | oui      |
| addedAt            | datetime_immutable | —                                                       | non      |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type       | Nullable | Côté inverse                                 |
| -------------- | ------------ | ---------- | -------- | -------------------------------------------- |
| category       | Category     | ManyToOne  | non      | oui → propriété `equipments` sur `Category`  |
| supplier       | Supplier     | ManyToOne  | non      | oui → propriété `equipments` sur `Supplier`  |
| accessories    | Accessory    | ManyToMany | —        | oui → propriété `equipments` sur `Accessory` |

### 11.2 Mise en place de l'héritage (édition manuelle de `Equipment.php`)

Ajoute ces attributs **au niveau de la classe** `Equipment`, au-dessus de `#[ORM\Entity(...)]` généré :

```php
#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'audio' => AudioEquipment::class,
    'video' => VideoEquipment::class,
])]
class Equipment
{
    // ... champs générés à l'étape 11.1
}
```

### 11.3 Créer `AudioEquipment.php` et `VideoEquipment.php` (à la main, pas de `make:entity`)

`src/Entity/AudioEquipment.php` :

```php
<?php

namespace App\Entity;

use App\Repository\AudioEquipmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AudioEquipmentRepository::class)]
class AudioEquipment extends Equipment
{
    #[ORM\Column]
    private ?int $powerWatts = null;

    #[ORM\Column(length: 50)]
    private ?string $connectorType = null;

    #[ORM\Column]
    private ?int $channelCount = null;

    public function getPowerWatts(): ?int { return $this->powerWatts; }
    public function setPowerWatts(int $powerWatts): static { $this->powerWatts = $powerWatts; return $this; }

    public function getConnectorType(): ?string { return $this->connectorType; }
    public function setConnectorType(string $connectorType): static { $this->connectorType = $connectorType; return $this; }

    public function getChannelCount(): ?int { return $this->channelCount; }
    public function setChannelCount(int $channelCount): static { $this->channelCount = $channelCount; return $this; }
}
```

`src/Entity/VideoEquipment.php` :

```php
<?php

namespace App\Entity;

use App\Repository\VideoEquipmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoEquipmentRepository::class)]
class VideoEquipment extends Equipment
{
    #[ORM\Column(length: 20)]
    private ?string $resolution = null;

    #[ORM\Column]
    private ?int $brightnessLumens = null;

    #[ORM\Column(length: 50)]
    private ?string $projectionType = null;

    public function getResolution(): ?string { return $this->resolution; }
    public function setResolution(string $resolution): static { $this->resolution = $resolution; return $this; }

    public function getBrightnessLumens(): ?int { return $this->brightnessLumens; }
    public function setBrightnessLumens(int $brightnessLumens): static { $this->brightnessLumens = $brightnessLumens; return $this; }

    public function getProjectionType(): ?string { return $this->projectionType; }
    public function setProjectionType(string $projectionType): static { $this->projectionType = $projectionType; return $this; }
}
```

Crée aussi les repositories vides (`src/Repository/AudioEquipmentRepository.php` et `VideoEquipmentRepository.php`) sur le modèle de `EquipmentRepository` en changeant le type d'entité gérée :

```php
<?php

namespace App\Repository;

use App\Entity\AudioEquipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AudioEquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AudioEquipment::class);
    }
}
```

(idem pour `VideoEquipmentRepository` avec `VideoEquipment::class`)

---

## 12. `Reservation`

```bash
php bin/console make:entity Reservation
```

| Champ           | Type à entrer      | Détails                                                 | Nullable |
| --------------- | ------------------ | ------------------------------------------------------- | -------- |
| startDate       | date_immutable     | —                                                       | non      |
| endDate         | date_immutable     | —                                                       | non      |
| eventCity       | string             | longueur 100                                            | non      |
| venueType       | string             | longueur 20 (défaut `'indoor'` dans le constructeur)    | non      |
| status          | string             | longueur 20 (défaut `'pending'` dans le constructeur)   | non      |
| totalAmount     | decimal            | precision 10, scale 2 (défaut `0` dans le constructeur) | non      |
| weatherForecast | string             | longueur 255                                            | oui      |
| createdAt       | datetime_immutable | —                                                       | non      |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type      | Nullable | Côté inverse                              |
| -------------- | ------------ | --------- | -------- | ----------------------------------------- |
| user           | User         | ManyToOne | non      | oui → propriété `reservations` sur `User` |

---

## 13. `ReservationLine`

```bash
php bin/console make:entity ReservationLine
```

| Champ           | Type à entrer | Détails               | Nullable |
| --------------- | ------------- | --------------------- | -------- |
| quantity        | integer       | —                     | non      |
| unitPricePerDay | decimal       | precision 10, scale 2 | non      |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type      | Nullable | Côté inverse                                       |
| -------------- | ------------ | --------- | -------- | -------------------------------------------------- |
| reservation    | Reservation  | ManyToOne | non      | oui → propriété `lines` sur `Reservation`          |
| equipment      | Equipment    | ManyToOne | non      | oui → propriété `reservationLines` sur `Equipment` |

---

## 14. `Quote`

```bash
php bin/console make:entity Quote
```

| Champ              | Type à entrer      | Détails                            | Nullable |
| ------------------ | ------------------ | ---------------------------------- | -------- |
| requestedStartDate | date_immutable     | —                                  | non      |
| requestedEndDate   | date_immutable     | —                                  | non      |
| eventCity          | string             | longueur 100                       | oui      |
| estimatedAmount    | decimal            | precision 10, scale 2 (défaut `0`) | non      |
| status             | string             | longueur 20 (défaut `'pending'`)   | non      |
| createdAt          | datetime_immutable | —                                  | non      |
| validUntil         | date_immutable     | —                                  | non      |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type      | Nullable | Côté inverse                        |
| -------------- | ------------ | --------- | -------- | ----------------------------------- |
| user           | User         | ManyToOne | non      | oui → propriété `quotes` sur `User` |

---

## 15. `QuoteLine`

```bash
php bin/console make:entity QuoteLine
```

| Champ           | Type à entrer | Détails               | Nullable |
| --------------- | ------------- | --------------------- | -------- |
| quantity        | integer       | —                     | non      |
| unitPricePerDay | decimal       | precision 10, scale 2 | non      |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type      | Nullable | Côté inverse                                 |
| -------------- | ------------ | --------- | -------- | -------------------------------------------- |
| quote          | Quote        | ManyToOne | non      | oui → propriété `lines` sur `Quote`          |
| equipment      | Equipment    | ManyToOne | non      | oui → propriété `quoteLines` sur `Equipment` |

---

## 16. `Invoice`

```bash
php bin/console make:entity Invoice
```

| Champ         | Type à entrer      | Détails                                  | Nullable |
| ------------- | ------------------ | ---------------------------------------- | -------- |
| number        | string             | longueur 50 (+ `unique: true` à la main) | non      |
| amount        | decimal            | precision 10, scale 2                    | non      |
| paymentStatus | string             | longueur 20 (défaut `'pending'`)         | non      |
| issuedAt      | datetime_immutable | —                                        | non      |
| dueDate       | date_immutable     | —                                        | non      |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type     | Nullable | Côté inverse                                |
| -------------- | ------------ | -------- | -------- | ------------------------------------------- |
| reservation    | Reservation  | OneToOne | non      | oui → propriété `invoice` sur `Reservation` |

Après génération, vérifie que la colonne `reservation_id` côté `Invoice` est bien `unique: true` (le maker l'ajoute normalement pour une OneToOne, sinon ajoute-le à la main).

---

## 17. `Review`

```bash
php bin/console make:entity Review
```

| Champ     | Type à entrer      | Détails | Nullable |
| --------- | ------------------ | ------- | -------- |
| rating    | integer            | —       | non      |
| comment   | text               | —       | oui      |
| createdAt | datetime_immutable | —       | non      |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type      | Nullable | Côté inverse                              |
| -------------- | ------------ | --------- | -------- | ----------------------------------------- |
| user           | User         | ManyToOne | non      | oui → propriété `reviews` sur `User`      |
| equipment      | Equipment    | ManyToOne | non      | oui → propriété `reviews` sur `Equipment` |

---

## 18. `Maintenance`

```bash
php bin/console make:entity Maintenance
```

| Champ                   | Type à entrer      | Détails     | Nullable |
| ----------------------- | ------------------ | ----------- | -------- |
| interventionType        | string             | longueur 20 | non      |
| description             | text               | —           | non      |
| interventionDate        | datetime_immutable | —           | non      |
| statusAfterIntervention | string             | longueur 20 | non      |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type      | Nullable | Côté inverse                                   |
| -------------- | ------------ | --------- | -------- | ---------------------------------------------- |
| equipment      | Equipment    | ManyToOne | non      | oui → propriété `maintenances` sur `Equipment` |
| technician     | User         | ManyToOne | non      | oui → propriété `maintenances` sur `User`      |

Pour la relation `technician`, quand le maker demande l'entité cible, choisis `User` puis nomme bien la propriété `technician` (pas `user`) pour que ce soit lisible dans le code.

---

## 19. `Notification`

```bash
php bin/console make:entity Notification
```

| Champ     | Type à entrer      | Détails            | Nullable |
| --------- | ------------------ | ------------------ | -------- |
| message   | text               | —                  | non      |
| type      | string             | longueur 50        | oui      |
| read      | boolean            | — (défaut `false`) | non      |
| createdAt | datetime_immutable | —                  | non      |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type      | Nullable | Côté inverse                               |
| -------------- | ------------ | --------- | -------- | ------------------------------------------ |
| user           | User         | ManyToOne | non      | oui → propriété `notifications` sur `User` |

---

## 20. Migration et base de données

```bash
php bin/console doctrine:database:create   # souvent inutile, le service postgres crée déjà la DB
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## 21. Fixtures

```bash
php bin/console make:fixtures AppFixtures
# édite src/DataFixtures/AppFixtures.php avec Faker
php bin/console doctrine:fixtures:load
```

## 22. Back-office EasyAdmin

```bash
php bin/console make:admin:dashboard
php bin/console make:admin:crud Equipment
php bin/console make:admin:crud Category
php bin/console make:admin:crud Supplier
php bin/console make:admin:crud Accessory
php bin/console make:admin:crud Reservation
php bin/console make:admin:crud Quote
php bin/console make:admin:crud Invoice
php bin/console make:admin:crud Review
php bin/console make:admin:crud Maintenance
php bin/console make:admin:crud User
```

## 23. Tests

```bash
php bin/console make:test
php bin/phpunit
```

## 24. Lancer en local

```bash
# via Docker (recommandé) :
docker compose up -d
# app     : http://localhost:8089
# mailpit : http://localhost:8025
# adminer : http://localhost:8088
# mercure : http://localhost:3000
```
