<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull(message: 'La date de début est obligatoire.')]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotNull(message: 'La date de fin est obligatoire.')]
    #[Assert\GreaterThan(propertyPath: 'startDate', message: 'La date de fin doit être postérieure à la date de début.')]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'La ville de l\'événement est obligatoire.')]
    #[Assert\Length(max: 100, maxMessage: 'La ville ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $eventCity = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: ['indoor', 'outdoor'], message: 'Type de lieu invalide.')]
    private ?string $venueType = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: ['confirmed', 'completed', 'cancelled'], message: 'Statut de réservation invalide.')]
    private ?string $status = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\PositiveOrZero(message: 'Le montant total doit être positif ou nul.')]
    private ?string $totalAmount = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Les prévisions météo ne peuvent pas dépasser {{ limit }} caractères.')]
    private ?string $weatherForecast = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: ReservationLine::class, mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private Collection $lines;

    #[ORM\OneToOne(mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private ?Invoice $invoice = null;

    public function __construct()
    {
        $this->status = 'confirmed';
        $this->totalAmount = '0';
        $this->createdAt = new \DateTimeImmutable();
        $this->lines = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getStartDate(): ?\DateTimeImmutable { return $this->startDate; }
    public function setStartDate(\DateTimeImmutable $startDate): static { $this->startDate = $startDate; return $this; }

    public function getEndDate(): ?\DateTimeImmutable { return $this->endDate; }
    public function setEndDate(\DateTimeImmutable $endDate): static { $this->endDate = $endDate; return $this; }

    public function getEventCity(): ?string { return $this->eventCity; }
    public function setEventCity(string $eventCity): static { $this->eventCity = $eventCity; return $this; }

    public function getVenueType(): ?string { return $this->venueType; }
    public function setVenueType(string $venueType): static { $this->venueType = $venueType; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getTotalAmount(): ?string { return $this->totalAmount; }
    public function setTotalAmount(string $totalAmount): static { $this->totalAmount = $totalAmount; return $this; }

    public function getWeatherForecast(): ?string { return $this->weatherForecast; }
    public function setWeatherForecast(?string $weatherForecast): static { $this->weatherForecast = $weatherForecast; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getLines(): Collection { return $this->lines; }
    public function addLine(ReservationLine $line): static
    {
        if (!$this->lines->contains($line)) {
            $this->lines->add($line);
            $line->setReservation($this);
        }
        return $this;
    }
    public function removeLine(ReservationLine $line): static
    {
        if ($this->lines->removeElement($line) && $line->getReservation() === $this) {
            $line->setReservation(null);
        }
        return $this;
    }

    public function getInvoice(): ?Invoice { return $this->invoice; }

    public function __toString(): string
    {
        return sprintf(
            'Réservation #%d - %s (%s)',
            $this->id ?? 0,
            $this->user?->getLastName() ?? 'Client inconnu',
            $this->startDate?->format('d/m/Y') ?? 'Date inconnue'
        );
    }
}
