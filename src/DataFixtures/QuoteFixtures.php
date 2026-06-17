<?php

namespace App\DataFixtures;

use App\Entity\AudioEquipment;
use App\Entity\Quote;
use App\Entity\QuoteLine;
use App\Entity\User;
use App\Entity\VideoEquipment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class QuoteFixtures extends Fixture implements DependentFixtureInterface
{
    private const A = AudioEquipment::class;
    private const V = VideoEquipment::class;

    public function load(ObjectManager $manager): void
    {
        $client = $this->getReference(UserFixtures::CLIENT, User::class);

        $qt1 = new Quote();
        $qt1->setUser($client);
        $qt1->setRequestedStartDate(new \DateTimeImmutable('2026-09-01'));
        $qt1->setRequestedEndDate(new \DateTimeImmutable('2026-09-03'));
        $qt1->setEventCity('Nice');
        $qt1->addLine($this->makeLine($qt1, EquipmentFixtures::MACKIE, self::A, 4));
        $qt1->addLine($this->makeLine($qt1, EquipmentFixtures::YAMAHA, self::A, 1));
        $qt1->setEstimatedAmount('2040.00');
        $manager->persist($qt1);

        $qt2 = new Quote();
        $qt2->setUser($client);
        $qt2->setRequestedStartDate(new \DateTimeImmutable('2026-10-05'));
        $qt2->setRequestedEndDate(new \DateTimeImmutable('2026-10-06'));
        $qt2->setEventCity('Lille');
        $qt2->setStatus('approved');
        $qt2->addLine($this->makeLine($qt2, EquipmentFixtures::SONY, self::V, 2));
        $qt2->addLine($this->makeLine($qt2, EquipmentFixtures::EPSON, self::V, 1));
        $qt2->setEstimatedAmount('1800.00');
        $manager->persist($qt2);

        $qt3 = new Quote();
        $qt3->setUser($client);
        $qt3->setRequestedStartDate(new \DateTimeImmutable('2026-04-10'));
        $qt3->setRequestedEndDate(new \DateTimeImmutable('2026-04-12'));
        $qt3->setEventCity('Toulouse');
        $qt3->setStatus('expired');
        $qt3->setEstimatedAmount('225.00');
        $qt3->setCreatedAt(new \DateTimeImmutable('2026-03-20'));
        $qt3->setValidUntil(new \DateTimeImmutable('2026-04-04'));
        $qt3->addLine($this->makeLine($qt3, EquipmentFixtures::SHURE, self::A, 3));
        $manager->persist($qt3);

        $manager->flush();
    }

    private function makeLine(Quote $quote, string $equipRef, string $class, int $qty): QuoteLine
    {
        $equip = $this->getReference($equipRef, $class);
        $line = new QuoteLine();
        $line->setEquipment($equip);
        $line->setQuantity($qty);
        $line->setUnitPricePerDay($equip->getDailyPrice());
        return $line;
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class, EquipmentFixtures::class];
    }
}
