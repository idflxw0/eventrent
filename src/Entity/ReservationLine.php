<?php

namespace App\Entity;

use App\Repository\ReservationLineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationLineRepository::class)]
class ReservationLine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'La quantité doit être un nombre positif.')]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\Positive(message: 'Le prix unitaire doit être un nombre positif.')]
    private ?string $unitPricePerDay = null;

    #[ORM\ManyToOne(inversedBy: 'lines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reservation $reservation = null;

    #[ORM\ManyToOne(inversedBy: 'reservationLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipment $equipment = null;

    public function getId(): ?int { return $this->id; }

    public function getQuantity(): ?int { return $this->quantity; }
    public function setQuantity(int $quantity): static { $this->quantity = $quantity; return $this; }

    public function getUnitPricePerDay(): ?string { return $this->unitPricePerDay; }
    public function setUnitPricePerDay(string $unitPricePerDay): static { $this->unitPricePerDay = $unitPricePerDay; return $this; }

    public function getReservation(): ?Reservation { return $this->reservation; }
    public function setReservation(?Reservation $reservation): static { $this->reservation = $reservation; return $this; }

    public function getEquipment(): ?Equipment { return $this->equipment; }
    public function setEquipment(?Equipment $equipment): static { $this->equipment = $equipment; return $this; }

    public function __toString(): string
    {
        return sprintf(
            '%dx %s (%s €/jour)',
            $this->quantity ?? 1,
            $this->equipment?->getName() ?? 'Équipement inconnu',
            $this->unitPricePerDay ?? '0.00'
        );
    }
}
