# Guide de setup — EventRent

Ce document regroupe : la mise en place du projet (Symfony, Docker, packages), puis pour **chaque entité**, la liste exacte des champs et relations à saisir quand `make:entity` te pose ses questions dans le terminal.

---

## 1. Prérequis

```bash
php -v          # 8.2+
composer -V
symfony version  # si absent : curl -sS https://get.symfony.com/cli/installer | bash
docker compose version
```

## 2. Créer le projet

```bash
symfony new eventrent --version="7.2.*" --webapp
cd eventrent
```

## 3. docker-compose.yml (services)

```yaml
services:
  database:
    image: postgres:16-alpine
    environment:
      POSTGRES_DB: eventrent
      POSTGRES_USER: eventrent
      POSTGRES_PASSWORD: eventrent
    ports:
      - "5432:5432"
    volumes:
      - database_data:/var/lib/postgresql/data

  mercure:
    image: dunglas/mercure
    environment:
      SERVER_NAME: ':80'
      MERCURE_PUBLISHER_JWT_KEY: '!ChangeThisMercureHubJWTSecretKey!'
      MERCURE_SUBSCRIBER_JWT_KEY: '!ChangeThisMercureHubJWTSecretKey!'
      MERCURE_EXTRA_DIRECTIVES: |
        cors_origins *
    ports:
      - "3000:80"

  mailpit:
    image: axllent/mailpit
    ports:
      - "8025:8025"
      - "1025:1025"

volumes:
  database_data:
```

## 4. `.env.local`

```
DATABASE_URL="postgresql://eventrent:eventrent@127.0.0.1:5432/eventrent?serverVersion=16&charset=utf8"

MERCURE_URL=http://127.0.0.1:3000/.well-known/mercure
MERCURE_PUBLIC_URL=http://127.0.0.1:3000/.well-known/mercure
MERCURE_JWT_SECRET="!ChangeThisMercureHubJWTSecretKey!"

MAILER_DSN=smtp://127.0.0.1:1025
```

## 5. Lancer les services et installer les packages

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

## 6. Conventions à appliquer pendant la création des entités

Quelques règles transverses, à appliquer manuellement après chaque `make:entity` (le maker ne pose pas ces questions) :

- **Dates** : choisis toujours `datetime_immutable` (et `date_immutable` pour les champs `date`), c'est la recommandation actuelle de Doctrine. Pour les valeurs "par défaut = maintenant", initialise-les dans le constructeur de l'entité (`$this->dateCreation = new \DateTimeImmutable();`).
- **Champs "unique"** (`Categorie.nom`, `Equipement.reference`, `Facture.numero`...) : le maker ne demande pas l'unicité. Ajoute `unique: true` à la main dans l'attribut `#[ORM\Column(...)]` généré.
- **Valeurs par défaut de type "statut"** (`en_attente`, `disponible`...) : initialise-les dans le constructeur plutôt que via `options: ['default' => ...]`, c'est plus lisible et testable.
- **Decimal** : à chaque fois que le maker demande precision/scale pour un `decimal`, réponds `10` et `2`.

---

## 7. Ordre de création des entités

L'ordre compte : pour créer une relation `ManyToOne` vers une entité, celle-ci doit déjà exister. Voici l'ordre qui évite les blocages :

1. `User` (via `make:user`)
2. `Categorie`
3. `Fournisseur`
4. `Accessoire`
5. `Equipement` + `MaterielAudio` + `MaterielVideo` (héritage — section spéciale)
6. `Reservation`
7. `LigneReservation`
8. `Devis`
9. `LigneDevis`
10. `Facture`
11. `Avis`
12. `Maintenance`
13. `Notification`

---

## 8. `User`

```bash
symfony console make:user
```

Réponses aux prompts :
- Nom de la classe : `User`
- Stocker le mot de passe (hash) ? : `yes`
- Champ d'unicité : `email`

Cela génère `id`, `email` (string 180, unique), `roles` (json), `password` (string).

Relance ensuite `symfony console make:entity User` pour ajouter les champs métier manquants :

| Champ | Type à entrer | Détails | Nullable |
|---|---|---|---|
| nom | string | longueur 100 | non |
| prenom | string | longueur 100 | non |
| telephone | string | longueur 20 | oui |
| dateInscription | datetime_immutable | — | non |
| actif | boolean | — | non |

---

## 9. `Categorie`

```bash
symfony console make:entity Categorie
```

| Champ | Type à entrer | Détails | Nullable |
|---|---|---|---|
| nom | string | longueur 100 (+ `unique: true` à la main) | non |
| description | text | — | oui |

**Relations à ajouter ici** : aucune. La relation avec `Equipement` sera créée depuis `Equipement` (étape 12) — accepte la proposition d'ajouter le côté inverse à ce moment-là.

---

## 10. `Fournisseur`

```bash
symfony console make:entity Fournisseur
```

| Champ | Type à entrer | Détails | Nullable |
|---|---|---|---|
| nom | string | longueur 150 | non |
| email | string | longueur 180 | oui |
| telephone | string | longueur 20 | oui |
| adresse | string | longueur 255 | oui |

**Relations à ajouter ici** : aucune (idem Categorie, côté inverse ajouté depuis `Equipement`).

---

## 11. `Accessoire`

```bash
symfony console make:entity Accessoire
```

| Champ | Type à entrer | Détails | Nullable |
|---|---|---|---|
| nom | string | longueur 150 | non |
| description | text | — | oui |

**Relations à ajouter ici** : aucune. Le ManyToMany avec `Equipement` sera créé depuis `Equipement` (étape 12).

---

## 12. `Equipement` + héritage (`MaterielAudio` / `MaterielVideo`)

### 12.1 Champs propres et relations de `Equipement`

```bash
symfony console make:entity Equipement
```

| Champ | Type à entrer | Détails | Nullable |
|---|---|---|---|
| reference | string | longueur 50 (+ `unique: true` à la main) | non |
| nom | string | longueur 150 | non |
| description | text | — | oui |
| prixJournalier | decimal | precision 10, scale 2 | non |
| statutDisponibilite | string | longueur 20 (défaut `'disponible'` dans le constructeur) | non |
| photo | string | longueur 255 | oui |
| dateAjout | datetime_immutable | — | non |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type | Nullable | Côté inverse |
|---|---|---|---|---|
| categorie | Categorie | ManyToOne | non | oui → propriété `equipements` sur `Categorie` |
| fournisseur | Fournisseur | ManyToOne | non | oui → propriété `equipements` sur `Fournisseur` |
| accessoires | Accessoire | ManyToMany | — | oui → propriété `equipements` sur `Accessoire` |

### 12.2 Mise en place de l'héritage (édition manuelle de `Equipement.php`)

Ajoute ces attributs **au niveau de la classe** `Equipement`, au-dessus de `#[ORM\Entity(...)]` généré :

```php
#[ORM\Entity(repositoryClass: EquipementRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'audio' => MaterielAudio::class,
    'video' => MaterielVideo::class,
])]
class Equipement
{
    // ... champs générés à l'étape 12.1, ne change rien ici
}
```

N'oublie pas les `use MaterielAudio;` / `use MaterielVideo;` en haut du fichier (même namespace `App\Entity`, donc pas obligatoire si même dossier, mais vérifie selon ton IDE).

### 12.3 Créer `MaterielAudio.php` et `MaterielVideo.php` (à la main, pas de `make:entity`)

`src/Entity/MaterielAudio.php` :

```php
<?php

namespace App\Entity;

use App\Repository\MaterielAudioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterielAudioRepository::class)]
class MaterielAudio extends Equipement
{
    #[ORM\Column]
    private ?int $puissanceWatts = null;

    #[ORM\Column(length: 50)]
    private ?string $typeConnectique = null;

    #[ORM\Column]
    private ?int $nombreCanaux = null;

    public function getPuissanceWatts(): ?int
    {
        return $this->puissanceWatts;
    }

    public function setPuissanceWatts(int $puissanceWatts): static
    {
        $this->puissanceWatts = $puissanceWatts;
        return $this;
    }

    public function getTypeConnectique(): ?string
    {
        return $this->typeConnectique;
    }

    public function setTypeConnectique(string $typeConnectique): static
    {
        $this->typeConnectique = $typeConnectique;
        return $this;
    }

    public function getNombreCanaux(): ?int
    {
        return $this->nombreCanaux;
    }

    public function setNombreCanaux(int $nombreCanaux): static
    {
        $this->nombreCanaux = $nombreCanaux;
        return $this;
    }
}
```

`src/Entity/MaterielVideo.php` :

```php
<?php

namespace App\Entity;

use App\Repository\MaterielVideoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaterielVideoRepository::class)]
class MaterielVideo extends Equipement
{
    #[ORM\Column(length: 20)]
    private ?string $resolution = null;

    #[ORM\Column]
    private ?int $luminositeLumens = null;

    #[ORM\Column(length: 50)]
    private ?string $typeProjection = null;

    public function getResolution(): ?string
    {
        return $this->resolution;
    }

    public function setResolution(string $resolution): static
    {
        $this->resolution = $resolution;
        return $this;
    }

    public function getLuminositeLumens(): ?int
    {
        return $this->luminositeLumens;
    }

    public function setLuminositeLumens(int $luminositeLumens): static
    {
        $this->luminositeLumens = $luminositeLumens;
        return $this;
    }

    public function getTypeProjection(): ?string
    {
        return $this->typeProjection;
    }

    public function setTypeProjection(string $typeProjection): static
    {
        $this->typeProjection = $typeProjection;
        return $this;
    }
}
```

Crée aussi les repositories vides correspondants (`src/Repository/MaterielAudioRepository.php` et `MaterielVideoRepository.php`), sur le modèle de `EquipementRepository` mais en changeant le type d'entité gérée :

```php
<?php

namespace App\Repository;

use App\Entity\MaterielAudio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MaterielAudioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MaterielAudio::class);
    }
}
```

(idem pour `MaterielVideoRepository` avec `MaterielVideo::class`)

---

## 13. `Reservation`

```bash
symfony console make:entity Reservation
```

| Champ | Type à entrer | Détails | Nullable |
|---|---|---|---|
| dateDebut | date_immutable | — | non |
| dateFin | date_immutable | — | non |
| villeEvenement | string | longueur 100 | non |
| typeLieu | string | longueur 20 | non |
| statut | string | longueur 20 (défaut `'en_attente'` dans le constructeur) | non |
| montantTotal | decimal | precision 10, scale 2 (défaut `0` dans le constructeur) | non |
| previsionMeteo | string | longueur 255 | oui |
| dateCreation | datetime_immutable | — | non |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type | Nullable | Côté inverse |
|---|---|---|---|---|
| user | User | ManyToOne | non | oui → propriété `reservations` sur `User` |

---

## 14. `LigneReservation`

```bash
symfony console make:entity LigneReservation
```

| Champ | Type à entrer | Détails | Nullable |
|---|---|---|---|
| quantite | integer | — | non |
| prixUnitaireJour | decimal | precision 10, scale 2 | non |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type | Nullable | Côté inverse |
|---|---|---|---|---|
| reservation | Reservation | ManyToOne | non | oui → propriété `ligneReservations` sur `Reservation` |
| equipement | Equipement | ManyToOne | non | oui → propriété `ligneReservations` sur `Equipement` |

---

## 15. `Devis`

```bash
symfony console make:entity Devis
```

| Champ | Type à entrer | Détails | Nullable |
|---|---|---|---|
| dateDebutSouhaitee | date_immutable | — | non |
| dateFinSouhaitee | date_immutable | — | non |
| villeEvenement | string | longueur 100 | oui |
| montantEstime | decimal | precision 10, scale 2 (défaut `0`) | non |
| statut | string | longueur 20 (défaut `'en_attente'`) | non |
| dateCreation | datetime_immutable | — | non |
| dateValidite | date_immutable | — | non |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type | Nullable | Côté inverse |
|---|---|---|---|---|
| user | User | ManyToOne | non | oui → propriété `devis` sur `User` |

---

## 16. `LigneDevis`

```bash
symfony console make:entity LigneDevis
```

| Champ | Type à entrer | Détails | Nullable |
|---|---|---|---|
| quantite | integer | — | non |
| prixUnitaireJour | decimal | precision 10, scale 2 | non |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type | Nullable | Côté inverse |
|---|---|---|---|---|
| devis | Devis | ManyToOne | non | oui → propriété `ligneDevis` sur `Devis` |
| equipement | Equipement | ManyToOne | non | oui → propriété `ligneDevis` sur `Equipement` |

---

## 17. `Facture`

```bash
symfony console make:entity Facture
```

| Champ | Type à entrer | Détails | Nullable |
|---|---|---|---|
| numero | string | longueur 50 (+ `unique: true` à la main) | non |
| montant | decimal | precision 10, scale 2 | non |
| statutPaiement | string | longueur 20 (défaut `'en_attente'`) | non |
| dateEmission | datetime_immutable | — | non |
| dateEcheance | date_immutable | — | non |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type | Nullable | Côté inverse |
|---|---|---|---|---|
| reservation | Reservation | OneToOne | non | oui → propriété `facture` sur `Reservation` |

Après génération, vérifie que la colonne `reservation_id` côté `Facture` est bien `unique: true` (le maker l'ajoute normalement pour une OneToOne, sinon ajoute-le à la main).

---

## 18. `Avis`

```bash
symfony console make:entity Avis
```

| Champ | Type à entrer | Détails | Nullable |
|---|---|---|---|
| note | integer | — | non |
| commentaire | text | — | oui |
| dateCreation | datetime_immutable | — | non |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type | Nullable | Côté inverse |
|---|---|---|---|---|
| user | User | ManyToOne | non | oui → propriété `avis` sur `User` |
| equipement | Equipement | ManyToOne | non | oui → propriété `avis` sur `Equipement` |

---

## 19. `Maintenance`

```bash
symfony console make:entity Maintenance
```

| Champ | Type à entrer | Détails | Nullable |
|---|---|---|---|
| typeIntervention | string | longueur 20 | non |
| description | text | — | non |
| dateIntervention | datetime_immutable | — | non |
| statutApresIntervention | string | longueur 20 | non |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type | Nullable | Côté inverse |
|---|---|---|---|---|
| equipement | Equipement | ManyToOne | non | oui → propriété `maintenances` sur `Equipement` |
| technicien | User | ManyToOne | non | oui → propriété `maintenances` sur `User` |

Pour la relation `technicien`, quand le maker demande l'entité cible, choisis `User` puis nomme bien la propriété `technicien` (pas `user`) pour que ce soit lisible dans le code.

---

## 20. `Notification`

```bash
symfony console make:entity Notification
```

| Champ | Type à entrer | Détails | Nullable |
|---|---|---|---|
| message | text | — | non |
| type | string | longueur 50 | oui |
| lu | boolean | — (défaut `false`) | non |
| dateCreation | datetime_immutable | — | non |

**Relations à ajouter ici** :

| Champ relation | Entité cible | Type | Nullable | Côté inverse |
|---|---|---|---|---|
| user | User | ManyToOne | non | oui → propriété `notifications` sur `User` |

---

## 21. Migration et base de données

```bash
symfony console doctrine:database:create   # souvent inutile, le service postgres crée déjà la DB
symfony console make:migration
symfony console doctrine:migrations:migrate
```

## 22. Fixtures

```bash
symfony console make:fixtures AppFixtures
# édite src/DataFixtures/AppFixtures.php avec Faker
symfony console doctrine:fixtures:load
```

## 23. Back-office EasyAdmin

```bash
symfony console make:admin:dashboard
symfony console make:admin:crud Equipement
symfony console make:admin:crud Categorie
symfony console make:admin:crud Fournisseur
symfony console make:admin:crud Accessoire
symfony console make:admin:crud Reservation
symfony console make:admin:crud Devis
symfony console make:admin:crud Facture
symfony console make:admin:crud Avis
symfony console make:admin:crud Maintenance
symfony console make:admin:crud User
```

## 24. Tests

```bash
symfony console make:test
php bin/phpunit
```

## 25. Lancer en local

```bash
symfony server:start -d
# app    : https://127.0.0.1:8000
# mailpit: http://127.0.0.1:8025
```
