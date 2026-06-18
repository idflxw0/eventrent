<?php

namespace App\DataFixtures;

use App\Entity\Accessory;
use App\Entity\AudioEquipment;
use App\Entity\Category;
use App\Entity\Equipment;
use App\Entity\Supplier;
use App\Entity\VideoEquipment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EquipmentFixtures extends Fixture implements DependentFixtureInterface
{
    public const MACKIE  = 'equip_mackie';
    public const SHURE   = 'equip_shure';
    public const YAMAHA  = 'equip_yamaha';
    public const CROWN   = 'equip_crown';
    public const JBL_EON = 'equip_jbl_eon';
    public const JBL_SUB = 'equip_jbl_sub';
    public const EPSON   = 'equip_epson';
    public const OPTOMA  = 'equip_optoma';
    public const BENQ    = 'equip_benq';
    public const SONY    = 'equip_sony';

    public function load(ObjectManager $manager): void
    {
        $audioDefs = [
            self::MACKIE  => ['AUDIO-001', 'Enceinte active Mackie SRM450', 'Enceinte amplifiée 2 voies, idéale pour sonorisation de soirées et concerts', '150.00', SupplierFixtures::SUP_0, CategoryFixtures::CAT_0, 1000, 'XLR',    2,  [AccessoryFixtures::ACC_0, AccessoryFixtures::ACC_1]],
            self::SHURE   => ['AUDIO-002', 'Micro sans fil Shure BLX24/SM58', 'Système micro HF avec récepteur, capsule dynamique SM58, portée 100 m', '25.00', SupplierFixtures::SUP_0, CategoryFixtures::CAT_2, 0,    'XLR',    1,  [AccessoryFixtures::ACC_1]],
            self::YAMAHA  => ['AUDIO-003', 'Table de mixage Yamaha MG16XU', 'Console 16 canaux avec effets SPX intégrés, compresseurs, USB audio', '80.00', SupplierFixtures::SUP_0, CategoryFixtures::CAT_4, 0,    'XLR',    16, [AccessoryFixtures::ACC_1]],
            self::CROWN   => ['AUDIO-004', 'Amplificateur Crown XLS2500', 'Ampli de puissance classe D, 2×775 W sous 4Ω, DSP intégré', '90.00', SupplierFixtures::SUP_2, CategoryFixtures::CAT_3, 1500, 'Speakon', 2,  [AccessoryFixtures::ACC_1]],
            self::JBL_EON => ['AUDIO-005', 'Enceinte passive JBL EON715', 'Enceinte passive 15", 650 W RMS, robuste et polyvalente', '60.00', SupplierFixtures::SUP_2, CategoryFixtures::CAT_0, 650,  'Speakon', 1,  [AccessoryFixtures::ACC_0, AccessoryFixtures::ACC_1]],
            self::JBL_SUB => ['AUDIO-006', 'Caisson de basses JBL PRX818XLFW', 'Subwoofer 18" amplifié 1500 W, réponse en fréquence 30-103 Hz', '120.00', SupplierFixtures::SUP_2, CategoryFixtures::CAT_0, 1500, 'XLR',    1,  [AccessoryFixtures::ACC_1]],
        ];

        foreach ($audioDefs as $ref => [$code, $name, $desc, $price, $supRef, $catRef, $watts, $conn, $ch, $accRefs]) {
            $e = new AudioEquipment();
            $e->setReference($code);
            $e->setName($name);
            $e->setDescription($desc);
            $e->setDailyPrice($price);
            $e->setSupplier($this->getReference($supRef, Supplier::class));
            $e->setCategory($this->getReference($catRef, Category::class));
            $e->setPowerWatts($watts);
            $e->setConnectorType($conn);
            $e->setChannelCount($ch);
            foreach ($accRefs as $ar) {
                $e->addAccessory($this->getReference($ar, Accessory::class));
            }
            $manager->persist($e);
            $this->addReference($ref, $e);
        }

        $videoDefs = [
            self::EPSON  => ['VIDEO-001', 'Vidéoprojecteur Epson EH-TW9400', 'Projecteur home cinéma 4K UHD, HDR10, 2600 lumens, LCD 3 puces', '200.00', SupplierFixtures::SUP_1, CategoryFixtures::CAT_1, '3840x2160', 2600, 'LCD',   [AccessoryFixtures::ACC_2, AccessoryFixtures::ACC_4, AccessoryFixtures::ACC_5]],
            self::OPTOMA => ['VIDEO-002', 'Vidéoprojecteur Optoma UHD38', 'Projecteur 4K gaming/cinéma, 4000 lumens, DLP, faible latence', '150.00', SupplierFixtures::SUP_1, CategoryFixtures::CAT_1, '3840x2160', 4000, 'DLP',   [AccessoryFixtures::ACC_2, AccessoryFixtures::ACC_3, AccessoryFixtures::ACC_5]],
            self::BENQ   => ['VIDEO-003', 'Vidéoprojecteur BenQ TK850', 'Projecteur 4K HDR, 3000 lumens, compensation HDR-Pro', '120.00', SupplierFixtures::SUP_1, CategoryFixtures::CAT_1, '3840x2160', 3000, 'DLP',   [AccessoryFixtures::ACC_2, AccessoryFixtures::ACC_5]],
            self::SONY   => ['VIDEO-004', 'Vidéoprojecteur Sony VPL-PHZ60', 'Projecteur laser pro WUXGA, 6000 lumens, durée de vie laser 20000 h', '350.00', SupplierFixtures::SUP_1, CategoryFixtures::CAT_1, '1920x1200', 6000, 'Laser', [AccessoryFixtures::ACC_2, AccessoryFixtures::ACC_4]],
        ];

        foreach ($videoDefs as $ref => [$code, $name, $desc, $price, $supRef, $catRef, $res, $lum, $proj, $accRefs]) {
            $e = new VideoEquipment();
            $e->setReference($code);
            $e->setName($name);
            $e->setDescription($desc);
            $e->setDailyPrice($price);
            $e->setSupplier($this->getReference($supRef, Supplier::class));
            $e->setCategory($this->getReference($catRef, Category::class));
            $e->setResolution($res);
            $e->setBrightnessLumens($lum);
            $e->setProjectionType($proj);
            foreach ($accRefs as $ar) {
                $e->addAccessory($this->getReference($ar, Accessory::class));
            }
            $manager->persist($e);
            $this->addReference($ref, $e);
        }

        $this->getReference(self::CROWN, AudioEquipment::class)->setAvailabilityStatus(Equipment::STATUS_MAINTENANCE);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CategoryFixtures::class, SupplierFixtures::class, AccessoryFixtures::class];
    }
}
