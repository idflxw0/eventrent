<?php

namespace App\DataFixtures;

use App\Entity\AudioEquipment;
use App\Entity\Reservation;
use App\Entity\ReservationLine;
use App\Entity\User;
use App\Entity\VideoEquipment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{
    public const RES_COMPLETED       = 'res_completed';
    public const RES_CONFIRMED       = 'res_confirmed';
    public const RES_PENDING         = 'res_pending';
    public const RES_CANCELLED       = 'res_cancelled';
    public const RES_EXTRA_COMPLETED = 'res_extra_completed';

    private const A = AudioEquipment::class;
    private const V = VideoEquipment::class;

    public function load(ObjectManager $manager): void
    {
        $client = $this->getReference(UserFixtures::CLIENT, User::class);

        $res1 = new Reservation();
        $res1->setUser($client);
        $res1->setStartDate(new \DateTimeImmutable('2026-05-10'));
        $res1->setEndDate(new \DateTimeImmutable('2026-05-12'));
        $res1->setEventCity('Lyon');
        $res1->setVenueType('indoor');
        $res1->setStatus('completed');
        $res1->addLine($this->makeLine($res1, EquipmentFixtures::MACKIE, self::A, 2));
        $res1->addLine($this->makeLine($res1, EquipmentFixtures::SHURE, self::A, 2));
        $res1->addLine($this->makeLine($res1, EquipmentFixtures::YAMAHA, self::A, 1));
        $res1->setTotalAmount('1290.00');
        $manager->persist($res1);
        $this->addReference(self::RES_COMPLETED, $res1);

        $res2 = new Reservation();
        $res2->setUser($client);
        $res2->setStartDate(new \DateTimeImmutable('2026-06-01'));
        $res2->setEndDate(new \DateTimeImmutable('2026-06-03'));
        $res2->setEventCity('Marseille');
        $res2->setVenueType('outdoor');
        $res2->setStatus('confirmed');
        $res2->setWeatherForecast('Ensoleillé, 28°C, vent léger 10 km/h — Pas de risque de pluie');
        $res2->addLine($this->makeLine($res2, EquipmentFixtures::CROWN, self::A, 1));
        $res2->addLine($this->makeLine($res2, EquipmentFixtures::JBL_EON, self::A, 3));
        $res2->addLine($this->makeLine($res2, EquipmentFixtures::JBL_SUB, self::A, 1));
        $res2->setTotalAmount('1170.00');
        $manager->persist($res2);
        $this->addReference(self::RES_CONFIRMED, $res2);

        $res3 = new Reservation();
        $res3->setUser($client);
        $res3->setStartDate(new \DateTimeImmutable('2026-08-15'));
        $res3->setEndDate(new \DateTimeImmutable('2026-08-16'));
        $res3->setEventCity('Paris');
        $res3->setVenueType('indoor');
        $res3->addLine($this->makeLine($res3, EquipmentFixtures::EPSON, self::V, 1));
        $res3->addLine($this->makeLine($res3, EquipmentFixtures::BENQ, self::V, 1));
        $res3->setTotalAmount('640.00');
        $manager->persist($res3);
        $this->addReference(self::RES_PENDING, $res3);

        $res4 = new Reservation();
        $res4->setUser($client);
        $res4->setStartDate(new \DateTimeImmutable('2026-07-20'));
        $res4->setEndDate(new \DateTimeImmutable('2026-07-22'));
        $res4->setEventCity('Bordeaux');
        $res4->setVenueType('indoor');
        $res4->setStatus('cancelled');
        $res4->addLine($this->makeLine($res4, EquipmentFixtures::OPTOMA, self::V, 1));
        $res4->addLine($this->makeLine($res4, EquipmentFixtures::SONY, self::V, 1));
        $res4->setTotalAmount('1500.00');
        $manager->persist($res4);
        $this->addReference(self::RES_CANCELLED, $res4);

        $extra0 = $this->getReference(UserFixtures::EXTRA_0, User::class);
        $res5 = new Reservation();
        $res5->setUser($extra0);
        $res5->setStartDate(new \DateTimeImmutable('2026-04-05'));
        $res5->setEndDate(new \DateTimeImmutable('2026-04-06'));
        $res5->setEventCity('Nantes');
        $res5->setVenueType('indoor');
        $res5->setStatus('completed');
        $res5->addLine($this->makeLine($res5, EquipmentFixtures::EPSON, self::V, 1));
        $res5->setTotalAmount('200.00');
        $manager->persist($res5);
        $this->addReference(self::RES_EXTRA_COMPLETED, $res5);

        // Test fixture for app:reservations:close — confirmed, ended last week
        $resClose = new Reservation();
        $resClose->setUser($client);
        $resClose->setStartDate(new \DateTimeImmutable('-10 days'));
        $resClose->setEndDate(new \DateTimeImmutable('-3 days'));
        $resClose->setEventCity('Rennes');
        $resClose->setVenueType('indoor');
        $resClose->setStatus('confirmed');
        $resClose->addLine($this->makeLine($resClose, EquipmentFixtures::MACKIE, self::A, 1));
        $resClose->setTotalAmount('450.00');
        $manager->persist($resClose);

        $manager->flush();
    }

    private function makeLine(Reservation $res, string $equipRef, string $class, int $qty): ReservationLine
    {
        $equip = $this->getReference($equipRef, $class);
        $line = new ReservationLine();
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
