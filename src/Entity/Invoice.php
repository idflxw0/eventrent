<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank(message: 'Le numéro de facture est obligatoire.')]
    #[Assert\Length(max: 50, maxMessage: 'Le numéro ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $number = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le montant est obligatoire.')]
    #[Assert\Positive(message: 'Le montant doit être un nombre positif.')]
    private ?string $amount = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: ['pending', 'paid'], message: 'Statut de paiement invalide.')]
    private ?string $paymentStatus = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $issuedAt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dueDate = null;

    #[ORM\OneToOne(inversedBy: 'invoice')]
    #[ORM\JoinColumn(nullable: false, unique: true)]
    private ?Reservation $reservation = null;

    public function __construct()
    {
        $this->paymentStatus = 'pending';
        $this->issuedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getNumber(): ?string { return $this->number; }
    public function setNumber(string $number): static { $this->number = $number; return $this; }

    public function getAmount(): ?string { return $this->amount; }
    public function setAmount(string $amount): static { $this->amount = $amount; return $this; }

    public function getPaymentStatus(): ?string { return $this->paymentStatus; }
    public function setPaymentStatus(string $paymentStatus): static { $this->paymentStatus = $paymentStatus; return $this; }

    public function getIssuedAt(): ?\DateTimeImmutable { return $this->issuedAt; }
    public function setIssuedAt(\DateTimeImmutable $issuedAt): static { $this->issuedAt = $issuedAt; return $this; }

    public function getDueDate(): ?\DateTimeImmutable { return $this->dueDate; }
    public function setDueDate(\DateTimeImmutable $dueDate): static { $this->dueDate = $dueDate; return $this; }

    public function getReservation(): ?Reservation { return $this->reservation; }
    public function setReservation(?Reservation $reservation): static { $this->reservation = $reservation; return $this; }

    public function __toString(): string
    {
        return sprintf('Facture %s (%s €)', $this->number ?? '', $this->amount ?? '0.00');
    }
}
