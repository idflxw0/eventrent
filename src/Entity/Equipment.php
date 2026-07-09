<?php

namespace App\Entity;

use App\Repository\EquipmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EquipmentRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'audio' => AudioEquipment::class,
    'video' => VideoEquipment::class,
])]
class Equipment
{
    public const STATUS_AVAILABLE    = 'available';
    public const STATUS_MAINTENANCE  = 'maintenance';
    public const STATUS_OUT_OF_SERVICE = 'out_of_service';
    public const TYPE_AUDIO = 'audio';
    public const TYPE_VIDEO = 'video';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['equipment:list', 'equipment:detail'])]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(['equipment:list', 'equipment:detail'])]
    #[Assert\NotBlank(message: 'La référence est obligatoire.')]
    #[Assert\Length(max: 50, maxMessage: 'La référence ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $reference = null;

    #[ORM\Column(length: 150)]
    #[Groups(['equipment:list', 'equipment:detail'])]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(max: 150, maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['equipment:detail'])]
    #[Assert\Length(max: 5000, maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['equipment:list', 'equipment:detail'])]
    #[Assert\NotBlank(message: 'Le prix journalier est obligatoire.')]
    #[Assert\Positive(message: 'Le prix journalier doit être un nombre positif.')]
    private ?string $dailyPrice = null;

    #[ORM\Column(length: 20)]
    #[Groups(['equipment:list', 'equipment:detail'])]
    #[Assert\Choice(
        choices: [self::STATUS_AVAILABLE, self::STATUS_MAINTENANCE, self::STATUS_OUT_OF_SERVICE],
        message: 'Statut de disponibilité invalide.'
    )]
    private ?string $availabilityStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['equipment:list', 'equipment:detail'])]
    private ?string $photo = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['equipment:detail'])]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\ManyToOne(inversedBy: 'equipments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['equipment:list', 'equipment:detail'])]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'equipments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['equipment:detail'])]
    private ?Supplier $supplier = null;

    #[ORM\ManyToMany(targetEntity: Accessory::class, inversedBy: 'equipments')]
    #[Groups(['equipment:detail'])]
    private Collection $accessories;

    #[ORM\OneToMany(targetEntity: ReservationLine::class, mappedBy: 'equipment')]
    private Collection $reservationLines;

    #[ORM\OneToMany(targetEntity: QuoteLine::class, mappedBy: 'equipment')]
    private Collection $quoteLines;

    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'equipment')]
    #[Groups(['equipment:detail'])]
    private Collection $reviews;

    #[ORM\OneToMany(targetEntity: Maintenance::class, mappedBy: 'equipment')]
    private Collection $maintenances;

    public function __construct()
    {
        $this->availabilityStatus = self::STATUS_AVAILABLE;
        $this->addedAt = new \DateTimeImmutable();
        $this->accessories = new ArrayCollection();
        $this->reservationLines = new ArrayCollection();
        $this->quoteLines = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->maintenances = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getType(): string
    {
        return $this instanceof VideoEquipment ? self::TYPE_VIDEO : self::TYPE_AUDIO;
    }

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

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->name ?? '', $this->reference ?? '');
    }
}
