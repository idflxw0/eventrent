<?php

namespace App\DataFixtures;

use App\Entity\Supplier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SupplierFixtures extends Fixture
{
    public const SUP_0 = 'supplier_0';
    public const SUP_1 = 'supplier_1';
    public const SUP_2 = 'supplier_2';

    public function load(ObjectManager $manager): void
    {
        $defs = [
            self::SUP_0 => ['AudioPro France', 'contact@audiopro.fr', '0140506070', '12 rue du Faubourg Saint-Antoine, 75012 Paris'],
            self::SUP_1 => ['VisionTech Distribution', 'info@visiontech.fr', '0472753344', '45 avenue Jean Jaurès, 69007 Lyon'],
            self::SUP_2 => ['EventPlus Équipement', 'contact@eventplus.fr', '0556789012', '8 place de la Bourse, 33000 Bordeaux'],
        ];

        foreach ($defs as $ref => [$name, $email, $phone, $address]) {
            $s = new Supplier();
            $s->setName($name);
            $s->setEmail($email);
            $s->setPhone($phone);
            $s->setAddress($address);
            $manager->persist($s);
            $this->addReference($ref, $s);
        }

        $manager->flush();
    }
}
