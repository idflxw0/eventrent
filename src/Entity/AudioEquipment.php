<?php

namespace App\Entity;

use App\Repository\AudioEquipmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AudioEquipmentRepository::class)]
class AudioEquipment extends Equipment
{
    #[ORM\Column]
    #[Groups(['equipment:detail'])]
    private ?int $powerWatts = null;

    #[ORM\Column(length: 50)]
    #[Groups(['equipment:detail'])]
    private ?string $connectorType = null;

    #[ORM\Column]
    #[Groups(['equipment:detail'])]
    private ?int $channelCount = null;

    public function getPowerWatts(): ?int { return $this->powerWatts; }
    public function setPowerWatts(int $powerWatts): static { $this->powerWatts = $powerWatts; return $this; }

    public function getConnectorType(): ?string { return $this->connectorType; }
    public function setConnectorType(string $connectorType): static { $this->connectorType = $connectorType; return $this; }

    public function getChannelCount(): ?int { return $this->channelCount; }
    public function setChannelCount(int $channelCount): static { $this->channelCount = $channelCount; return $this; }
}
