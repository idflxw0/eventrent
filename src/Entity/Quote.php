<?php

namespace App\Entity;

use App\Repository\QuoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuoteRepository::class)]
class Quote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $requestedStartDate = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $requestedEndDate = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $eventCity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $estimatedAmount = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $validUntil = null;

    #[ORM\ManyToOne(inversedBy: 'quotes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: QuoteLine::class, mappedBy: 'quote', cascade: ['persist', 'remove'])]
    private Collection $lines;

    public function __construct()
    {
        $this->status = 'pending';
        $this->estimatedAmount = '0';
        $this->createdAt = new \DateTimeImmutable();
        $this->validUntil = new \DateTimeImmutable('+15 days');
        $this->lines = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getRequestedStartDate(): ?\DateTimeImmutable { return $this->requestedStartDate; }
    public function setRequestedStartDate(\DateTimeImmutable $requestedStartDate): static { $this->requestedStartDate = $requestedStartDate; return $this; }

    public function getRequestedEndDate(): ?\DateTimeImmutable { return $this->requestedEndDate; }
    public function setRequestedEndDate(\DateTimeImmutable $requestedEndDate): static { $this->requestedEndDate = $requestedEndDate; return $this; }

    public function getEventCity(): ?string { return $this->eventCity; }
    public function setEventCity(?string $eventCity): static { $this->eventCity = $eventCity; return $this; }

    public function getEstimatedAmount(): ?string { return $this->estimatedAmount; }
    public function setEstimatedAmount(string $estimatedAmount): static { $this->estimatedAmount = $estimatedAmount; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getValidUntil(): ?\DateTimeImmutable { return $this->validUntil; }
    public function setValidUntil(\DateTimeImmutable $validUntil): static { $this->validUntil = $validUntil; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getLines(): Collection { return $this->lines; }
    public function addLine(QuoteLine $line): static
    {
        if (!$this->lines->contains($line)) {
            $this->lines->add($line);
            $line->setQuote($this);
        }
        return $this;
    }
    public function removeLine(QuoteLine $line): static
    {
        if ($this->lines->removeElement($line) && $line->getQuote() === $this) {
            $line->setQuote(null);
        }
        return $this;
    }
}
