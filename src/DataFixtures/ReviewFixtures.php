<?php

namespace App\DataFixtures;

use App\Entity\AudioEquipment;
use App\Entity\Review;
use App\Entity\User;
use App\Entity\VideoEquipment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $client = $this->getReference(UserFixtures::CLIENT, User::class);
        $extra  = $this->getReference(UserFixtures::EXTRA_0, User::class);

        $r1 = new Review();
        $r1->setUser($client);
        $r1->setEquipment($this->getReference(EquipmentFixtures::MACKIE, AudioEquipment::class));
        $r1->setRating(5);
        $r1->setComment('Excellent son, très fiable. Utilisé pour un mariage, la sono était parfaite.');
        $manager->persist($r1);

        $r2 = new Review();
        $r2->setUser($client);
        $r2->setEquipment($this->getReference(EquipmentFixtures::SHURE, AudioEquipment::class));
        $r2->setRating(4);
        $r2->setComment('Bonne qualité audio, un léger souffle en fin de soirée mais rien de gênant.');
        $manager->persist($r2);

        $r3 = new Review();
        $r3->setUser($client);
        $r3->setEquipment($this->getReference(EquipmentFixtures::YAMAHA, AudioEquipment::class));
        $r3->setRating(5);
        $r3->setComment('Console très intuitive, les compresseurs intégrés font la différence sur les voix.');
        $manager->persist($r3);

        $r4 = new Review();
        $r4->setUser($extra);
        $r4->setEquipment($this->getReference(EquipmentFixtures::EPSON, VideoEquipment::class));
        $r4->setRating(4);
        $r4->setComment('Très bon projecteur, image nette même en salle éclairée. Un peu lourd à installer.');
        $manager->persist($r4);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class, EquipmentFixtures::class];
    }
}
