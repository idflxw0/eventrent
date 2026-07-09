<?php

namespace App\Entity;

use App\Repository\VideoEquipmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VideoEquipmentRepository::class)]
class VideoEquipment extends Equipment
{
    #[ORM\Column(length: 20)]
    #[Groups(['equipment:detail'])]
    #[Assert\NotBlank(message: 'La résolution est obligatoire.')]
    #[Assert\Length(max: 20, maxMessage: 'La résolution ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $resolution = null;

    #[ORM\Column]
    #[Groups(['equipment:detail'])]
    #[Assert\Positive(message: 'La luminosité doit être un nombre positif.')]
    private ?int $brightnessLumens = null;

    #[ORM\Column(length: 50)]
    #[Groups(['equipment:detail'])]
    #[Assert\NotBlank(message: 'Le type de projection est obligatoire.')]
    #[Assert\Length(max: 50, maxMessage: 'Le type de projection ne peut pas dépasser {{ limit }} caractères.')]
    private ?string $projectionType = null;

    public function getResolution(): ?string { return $this->resolution; }
    public function setResolution(string $resolution): static { $this->resolution = $resolution; return $this; }

    public function getBrightnessLumens(): ?int { return $this->brightnessLumens; }
    public function setBrightnessLumens(int $brightnessLumens): static { $this->brightnessLumens = $brightnessLumens; return $this; }

    public function getProjectionType(): ?string { return $this->projectionType; }
    public function setProjectionType(string $projectionType): static { $this->projectionType = $projectionType; return $this; }
}
