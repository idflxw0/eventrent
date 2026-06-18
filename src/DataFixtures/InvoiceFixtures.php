<?php

namespace App\DataFixtures;

use App\Entity\Invoice;
use App\Entity\Reservation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InvoiceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $res1 = $this->getReference(ReservationFixtures::RES_COMPLETED, Reservation::class);
        $inv1 = new Invoice();
        $inv1->setReservation($res1);
        $inv1->setNumber('INV-2026-000123');
        $inv1->setAmount('1290.00');
        $inv1->setPaymentStatus('paid');
        $inv1->setIssuedAt(new \DateTimeImmutable('2026-05-10 14:30:00'));
        $inv1->setDueDate(new \DateTimeImmutable('2026-06-09'));
        $manager->persist($inv1);

        $res2 = $this->getReference(ReservationFixtures::RES_CONFIRMED, Reservation::class);
        $inv2 = new Invoice();
        $inv2->setReservation($res2);
        $inv2->setNumber('INV-2026-000124');
        $inv2->setAmount('1170.00');
        $inv2->setPaymentStatus('pending');
        $inv2->setIssuedAt(new \DateTimeImmutable('2026-06-01 09:15:00'));
        $inv2->setDueDate(new \DateTimeImmutable('2026-07-01'));
        $manager->persist($inv2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ReservationFixtures::class];
    }
}
