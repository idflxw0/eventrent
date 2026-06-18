<?php

namespace App\DataFixtures;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NotificationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $admin  = $this->getReference(UserFixtures::ADMIN, User::class);
        $tech   = $this->getReference(UserFixtures::TECHNICIAN, User::class);
        $client = $this->getReference(UserFixtures::CLIENT, User::class);

        $defs = [
            [$client, 'Votre réservation à Lyon du 10 au 12 mai 2026 est confirmée.', 'reservation_confirmed', true,  '2026-05-09 16:30:00'],
            [$client, 'Votre devis pour Nice (septembre 2026) a bien été reçu.', 'quote_received', false, null],
            [$client, 'Votre devis pour Lille (octobre 2026) a été approuvé ! Transformez-le en réservation.', 'quote_approved', false, '2026-06-14 11:00:00'],
            [$client, 'Facture INV-2026-000123 disponible dans votre espace client.', 'invoice_available', true,  '2026-05-10 14:35:00'],
            [$tech,   'Maintenance assignée : Amplificateur Crown XLS2500 — court-circuit canal droit.', 'maintenance_assigned', false, '2026-06-10 08:35:00'],
            [$admin,  'Nouvelle réservation de Jean Dupont à Paris (15-16 août 2026) en attente.', 'new_reservation', false, null],
            [$client, 'Rappel : votre réservation à Marseille est dans 2 jours.', 'reservation_reminder', true,  '2026-05-30 08:00:00'],
        ];

        foreach ($defs as [$user, $msg, $type, $read, $date]) {
            $n = new Notification();
            $n->setUser($user);
            $n->setMessage($msg);
            $n->setType($type);
            $n->setRead($read);
            if ($date) {
                $n->setCreatedAt(new \DateTimeImmutable($date));
            }
            $manager->persist($n);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
