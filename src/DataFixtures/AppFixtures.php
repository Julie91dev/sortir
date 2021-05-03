<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        // Utilisation d'un faker
        $faker = Factory::create('fr_FR');

        $admin = new User();
        $user = new User();

        //creation d'un utilisateur administrateur
        $admin->setNom($faker->lastName())
            ->setPrenom($faker->firstName())
            ->setTelephone($faker->phoneNumber())
            ->setEmail("admin@test.com")

            ->setAdministrateur(true)
            ->setActif(true);
        $password = $this->encoder->encodePassword($admin, 'password');
        $admin->setPassword($password);

        //creation d'un utilisateur
        $user->setNom($faker->lastName())
            ->setPrenom($faker->firstName())
            -> setTelephone($faker->phoneNumber())
            ->setEmail("user@test.com")
            ->setAdministrateur(false)
            ->setActif(true);

        $motdepasse = $this->encoder->encodePassword($user, 'motdepasse');
        $user->setPassword($motdepasse);

        $manager->persist($admin);
        $manager->persist($user);
        $manager->flush();
    }
}
