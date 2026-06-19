<?php

namespace App\Tests\Unit;

use App\Entity\AudioEquipment;
use App\Entity\Equipment;
use App\Entity\VideoEquipment;
use PHPUnit\Framework\TestCase;

class EquipmentTypeTest extends TestCase
{
    public function testAudioEquipmentReturnsAudioType(): void
    {
        $audio = new AudioEquipment();
        $this->assertSame(Equipment::TYPE_AUDIO, $audio->getType());
    }

    public function testVideoEquipmentReturnsVideoType(): void
    {
        $video = new VideoEquipment();
        $this->assertSame(Equipment::TYPE_VIDEO, $video->getType());
    }

    public function testDefaultAvailabilityStatusIsAvailable(): void
    {
        $audio = new AudioEquipment();
        $this->assertSame(Equipment::STATUS_AVAILABLE, $audio->getAvailabilityStatus());
    }

    public function testDailyPriceCalculation(): void
    {
        $audio = new AudioEquipment();
        $audio->setDailyPrice('150.00');
        $qty = 2;
        $days = 3;

        $total = (float) $audio->getDailyPrice() * $qty * $days;

        $this->assertSame(900.00, $total);
    }
}
