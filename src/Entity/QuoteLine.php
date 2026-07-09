<?php

namespace App\Entity;

use App\Repository\QuoteLineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuoteLineRepository::class)]
class QuoteLine
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
    private ?Quote $quote = null;

    #[ORM\ManyToOne(inversedBy: 'quoteLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipment $equipment = null;

    public function getId(): ?int { return $this->id; }

    public function getQuantity(): ?int { return $this->quantity; }
    public function setQuantity(int $quantity): static { $this->quantity = $quantity; return $this; }

    public function getUnitPricePerDay(): ?string { return $this->unitPricePerDay; }
    public function setUnitPricePerDay(string $unitPricePerDay): static { $this->unitPricePerDay = $unitPricePerDay; return $this; }

    public function getQuote(): ?Quote { return $this->quote; }
    public function setQuote(?Quote $quote): static { $this->quote = $quote; return $this; }

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
