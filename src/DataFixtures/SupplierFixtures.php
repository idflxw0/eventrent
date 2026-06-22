<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Supplier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SupplierFixtures extends Fixture implements DependentFixtureInterface
{
    public const SUP_0 = 'supplier_0';
    public const SUP_1 = 'supplier_1';
    public const SUP_2 = 'supplier_2';

    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }

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

        // Supplier ↔ Category ManyToMany: link each supplier to the categories they cover
        /** @var Category $audio */
        $audio = $this->getReference(CategoryFixtures::CAT_0, Category::class);
        $video = $this->getReference(CategoryFixtures::CAT_1, Category::class);
        $micro = $this->getReference(CategoryFixtures::CAT_2, Category::class);
        $ampli = $this->getReference(CategoryFixtures::CAT_3, Category::class);
        $mixer = $this->getReference(CategoryFixtures::CAT_4, Category::class);

        /** @var Supplier $sup0 */
        $sup0 = $this->getReference(self::SUP_0, Supplier::class);
        $sup1 = $this->getReference(self::SUP_1, Supplier::class);
        $sup2 = $this->getReference(self::SUP_2, Supplier::class);

        // AudioPro covers audio categories
        $audio->addSupplier($sup0);
        $micro->addSupplier($sup0);
        $ampli->addSupplier($sup0);
        $mixer->addSupplier($sup0);

        // VisionTech covers video
        $video->addSupplier($sup1);

        // EventPlus covers everything
        $audio->addSupplier($sup2);
        $video->addSupplier($sup2);
        $micro->addSupplier($sup2);

        $manager->flush();
    }
}
