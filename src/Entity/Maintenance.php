<?php

namespace App\Entity;

use App\Repository\MaintenanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaintenanceRepository::class)]
class Maintenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $interventionType = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $interventionDate = null;

    #[ORM\Column(length: 20)]
    private ?string $statusAfterIntervention = null;

    #[ORM\ManyToOne(inversedBy: 'maintenances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipment $equipment = null;

    #[ORM\ManyToOne(inversedBy: 'maintenances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $technician = null;

    public function __construct()
    {
        $this->interventionDate = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getInterventionType(): ?string { return $this->interventionType; }
    public function setInterventionType(string $interventionType): static { $this->interventionType = $interventionType; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }

    public function getInterventionDate(): ?\DateTimeImmutable { return $this->interventionDate; }
    public function setInterventionDate(\DateTimeImmutable $interventionDate): static { $this->interventionDate = $interventionDate; return $this; }

    public function getStatusAfterIntervention(): ?string { return $this->statusAfterIntervention; }
    public function setStatusAfterIntervention(string $statusAfterIntervention): static { $this->statusAfterIntervention = $statusAfterIntervention; return $this; }

    public function getEquipment(): ?Equipment { return $this->equipment; }
    public function setEquipment(?Equipment $equipment): static { $this->equipment = $equipment; return $this; }

    public function getTechnician(): ?User { return $this->technician; }
    public function setTechnician(?User $technician): static { $this->technician = $technician; return $this; }
}
