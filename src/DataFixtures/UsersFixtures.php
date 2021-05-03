<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersFixtures extends Fixture
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

        $user = new User();


        //creation d'un utilisateur
        $user->setNom($faker->lastName())
            ->setPrenom($faker->firstName())
            ->setTelephone($faker->phoneNumber())
            ->setEmail("user@test.com")
            ->setAdministrateur(false)
            ->setActif(true)
            ->setRoles(['roles' =>"ROLE_USER"]);


        $motdepasse = $this->encoder->encodePassword($user, 'motdepasse');
        $user->setPassword($motdepasse);

        for ($i = 0; $i < 10; $i++){
            $utilisateur = new User();
            $utilisateur->setNom($faker->lastName())
                        ->setPrenom($faker->firstName())
                        ->setTelephone($faker->phoneNumber())
                        ->setEmail($faker->email())
                        ->setPassword($faker->password())
                        ->setAdministrateur(false)
                        ->setActif(true)
                        ->setRoles(['roles' =>"ROLE_USER"]);
            $manager->persist($utilisateur);
        }
        $manager->persist($user);
        $manager->flush();
    }
}
