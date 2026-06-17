<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN = 'user_admin';
    public const TECHNICIAN = 'user_tech';
    public const CLIENT = 'user_client';
    public const EXTRA_0 = 'user_extra_0';
    public const EXTRA_1 = 'user_extra_1';
    public const EXTRA_2 = 'user_extra_2';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@eventrent.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setFirstName('Thomas');
        $admin->setLastName('Durand');
        $admin->setPhone('0601020304');
        $admin->setActive(true);
        $manager->persist($admin);
        $this->addReference(self::ADMIN, $admin);

        $tech = new User();
        $tech->setEmail('tech@eventrent.com');
        $tech->setPassword($this->passwordHasher->hashPassword($tech, 'tech123'));
        $tech->setRoles(['ROLE_TECHNICIEN']);
        $tech->setFirstName('Marie');
        $tech->setLastName('Laurent');
        $tech->setPhone('0605060708');
        $tech->setActive(true);
        $manager->persist($tech);
        $this->addReference(self::TECHNICIAN, $tech);

        $client = new User();
        $client->setEmail('user@eventrent.com');
        $client->setPassword($this->passwordHasher->hashPassword($client, 'user123'));
        $client->setRoles(['ROLE_USER']);
        $client->setFirstName('Jean');
        $client->setLastName('Dupont');
        $client->setPhone('0609101112');
        $client->setActive(true);
        $manager->persist($client);
        $this->addReference(self::CLIENT, $client);

        $faker = Factory::create('fr_FR');
        $extras = [self::EXTRA_0, self::EXTRA_1, self::EXTRA_2];
        foreach ($extras as $ref) {
            $u = new User();
            $u->setEmail($faker->unique()->safeEmail());
            $u->setPassword($this->passwordHasher->hashPassword($u, 'password123'));
            $u->setRoles(['ROLE_USER']);
            $u->setFirstName($faker->firstName());
            $u->setLastName($faker->lastName());
            $u->setPhone($faker->phoneNumber());
            $u->setActive(true);
            $manager->persist($u);
            $this->addReference($ref, $u);
        }

        $manager->flush();
    }
}
