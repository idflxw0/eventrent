<?php

namespace App\DataFixtures;

use App\Entity\AudioEquipment;
use App\Entity\Maintenance;
use App\Entity\User;
use App\Entity\VideoEquipment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MaintenanceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $tech = $this->getReference(UserFixtures::TECHNICIAN, User::class);

        $m1 = new Maintenance();
        $m1->setEquipment($this->getReference(EquipmentFixtures::MACKIE, AudioEquipment::class));
        $m1->setTechnician($tech);
        $m1->setInterventionType('repair');
        $m1->setDescription('Remplacement du haut-parleur grillé sur la voie gauche. Vérification ampli interne OK.');
        $m1->setInterventionDate(new \DateTimeImmutable('2026-04-15 10:00:00'));
        $m1->setStatusAfterIntervention('available');
        $manager->persist($m1);

        $m2 = new Maintenance();
        $m2->setEquipment($this->getReference(EquipmentFixtures::EPSON, VideoEquipment::class));
        $m2->setTechnician($tech);
        $m2->setInterventionType('inspection');
        $m2->setDescription('Contrôle périodique : lampe à 1200 h (max 5000 h), filtre à air nettoyé, focus vérifié.');
        $m2->setInterventionDate(new \DateTimeImmutable('2026-05-20 14:00:00'));
        $m2->setStatusAfterIntervention('available');
        $manager->persist($m2);

        $m3 = new Maintenance();
        $m3->setEquipment($this->getReference(EquipmentFixtures::CROWN, AudioEquipment::class));
        $m3->setTechnician($tech);
        $m3->setInterventionType('breakdown');
        $m3->setDescription('Court-circuit canal droit. Pièces commandées, en attente de livraison.');
        $m3->setInterventionDate(new \DateTimeImmutable('2026-06-10 08:30:00'));
        $m3->setStatusAfterIntervention('maintenance');
        $manager->persist($m3);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class, EquipmentFixtures::class];
    }
}
