<?php

namespace App\Entity;

use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'audio' => AudioEquipment::class,
    'video' => VideoEquipment::class,
])]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $reference = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $dailyPrice = null;

    #[ORM\Column(length: 20)]
    private ?string $availabilityStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\ManyToOne(inversedBy: 'equipments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'equipments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Supplier $supplier = null;

    #[ORM\ManyToMany(targetEntity: Accessory::class, inversedBy: 'equipments')]
    private Collection $accessories;

    #[ORM\OneToMany(targetEntity: ReservationLine::class, mappedBy: 'equipment')]
    private Collection $reservationLines;

    #[ORM\OneToMany(targetEntity: QuoteLine::class, mappedBy: 'equipment')]
    private Collection $quoteLines;

    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'equipment')]
    private Collection $reviews;

    #[ORM\OneToMany(targetEntity: Maintenance::class, mappedBy: 'equipment')]
    private Collection $maintenances;

    public function __construct()
    {
        $this->availabilityStatus = 'available';
        $this->addedAt = new \DateTimeImmutable();
        $this->accessories = new ArrayCollection();
        $this->reservationLines = new ArrayCollection();
        $this->quoteLines = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->maintenances = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getReference(): ?string { return $this->reference; }
    public function setReference(string $reference): static { $this->reference = $reference; return $this; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getDailyPrice(): ?string { return $this->dailyPrice; }
    public function setDailyPrice(string $dailyPrice): static { $this->dailyPrice = $dailyPrice; return $this; }

    public function getAvailabilityStatus(): ?string { return $this->availabilityStatus; }
    public function setAvailabilityStatus(string $availabilityStatus): static { $this->availabilityStatus = $availabilityStatus; return $this; }

    public function getPhoto(): ?string { return $this->photo; }
    public function setPhoto(?string $photo): static { $this->photo = $photo; return $this; }

    public function getAddedAt(): ?\DateTimeImmutable { return $this->addedAt; }
    public function setAddedAt(\DateTimeImmutable $addedAt): static { $this->addedAt = $addedAt; return $this; }

    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $category): static { $this->category = $category; return $this; }

    public function getSupplier(): ?Supplier { return $this->supplier; }
    public function setSupplier(?Supplier $supplier): static { $this->supplier = $supplier; return $this; }

    public function getAccessories(): Collection { return $this->accessories; }
    public function addAccessory(Accessory $accessory): static
    {
        if (!$this->accessories->contains($accessory)) {
            $this->accessories->add($accessory);
        }
        return $this;
    }
    public function removeAccessory(Accessory $accessory): static { $this->accessories->removeElement($accessory); return $this; }

    public function getReservationLines(): Collection { return $this->reservationLines; }
    public function getQuoteLines(): Collection { return $this->quoteLines; }
    public function getReviews(): Collection { return $this->reviews; }
    public function getMaintenances(): Collection { return $this->maintenances; }
}
