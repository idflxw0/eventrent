<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CAT_0 = 'cat_0';
    public const CAT_1 = 'cat_1';
    public const CAT_2 = 'cat_2';
    public const CAT_3 = 'cat_3';
    public const CAT_4 = 'cat_4';

    public function load(ObjectManager $manager): void
    {
        $defs = [
            self::CAT_0 => ['Sonorisation', 'Enceintes, caissons de basses et systèmes de diffusion audio'],
            self::CAT_1 => ['Vidéoprojection', 'Vidéoprojecteurs et systèmes de projection sur grand écran'],
            self::CAT_2 => ['Microphones', 'Micros filaires, sans fil, cravates et systèmes HF'],
            self::CAT_3 => ['Amplification', 'Amplificateurs de puissance pour sonorisation'],
            self::CAT_4 => ['Tables de mixage', 'Consoles et tables de mixage analogiques et numériques'],
        ];

        foreach ($defs as $ref => [$name, $desc]) {
            $c = new Category();
            $c->setName($name);
            $c->setDescription($desc);
            $manager->persist($c);
            $this->addReference($ref, $c);
        }

        $manager->flush();
    }
}
