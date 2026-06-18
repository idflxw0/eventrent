<?php

namespace App\Entity;

use App\Repository\VideoEquipmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoEquipmentRepository::class)]
class VideoEquipment extends Equipment
{
    #[ORM\Column(length: 20)]
    private ?string $resolution = null;

    #[ORM\Column]
    private ?int $brightnessLumens = null;

    #[ORM\Column(length: 50)]
    private ?string $projectionType = null;

    public function getResolution(): ?string { return $this->resolution; }
    public function setResolution(string $resolution): static { $this->resolution = $resolution; return $this; }

    public function getBrightnessLumens(): ?int { return $this->brightnessLumens; }
    public function setBrightnessLumens(int $brightnessLumens): static { $this->brightnessLumens = $brightnessLumens; return $this; }

    public function getProjectionType(): ?string { return $this->projectionType; }
    public function setProjectionType(string $projectionType): static { $this->projectionType = $projectionType; return $this; }
}
