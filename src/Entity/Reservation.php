<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column(length: 100)]
    private ?string $eventCity = null;

    #[ORM\Column(length: 20)]
    private ?string $venueType = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalAmount = null;

    #[ORM\Column(length: 255, nullable: true)]
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
        $this->status = 'pending';
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
