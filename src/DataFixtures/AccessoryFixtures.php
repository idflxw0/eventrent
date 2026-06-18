<?php

namespace App\DataFixtures;

use App\Entity\Accessory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AccessoryFixtures extends Fixture
{
    public const ACC_0 = 'acc_0';
    public const ACC_1 = 'acc_1';
    public const ACC_2 = 'acc_2';
    public const ACC_3 = 'acc_3';
    public const ACC_4 = 'acc_4';
    public const ACC_5 = 'acc_5';

    public function load(ObjectManager $manager): void
    {
        $defs = [
            self::ACC_0 => ['Pied d\'enceinte télescopique', 'Pied réglable de 1,20 m à 2,10 m, charge max 50 kg'],
            self::ACC_1 => ['Câble XLR 10 mètres', 'Câble symétrique XLR mâle/femelle, blindage haute densité'],
            self::ACC_2 => ['Câble HDMI 5 mètres', 'Câble HDMI 2.1 haute vitesse, compatible 4K/60 Hz'],
            self::ACC_3 => ['Écran de projection 2×2 m', 'Écran sur trépied, toile mate blanche, format carré'],
            self::ACC_4 => ['Support plafond universel', 'Support orientable pour vidéoprojecteur, charge max 15 kg'],
            self::ACC_5 => ['Télécommande sans fil', 'Télécommande infrarouge universelle pour vidéoprojecteurs'],
        ];

        foreach ($defs as $ref => [$name, $desc]) {
            $a = new Accessory();
            $a->setName($name);
            $a->setDescription($desc);
            $manager->persist($a);
            $this->addReference($ref, $a);
        }

        $manager->flush();
    }
}
