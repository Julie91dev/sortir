<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminFixtures extends Fixture
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


        //creation d'un utilisateur administrateur
        $admin->setNom($faker->lastName())
            ->setPrenom($faker->firstName())
            ->setTelephone($faker->phoneNumber())
            ->setEmail("admin@test.com")
            ->setRoles(['roles' =>"ROLE_ADMIN"])
            ->setAdministrateur(true)
            ->setActif(true);
        $password = $this->encoder->encodePassword($admin, 'password');
        $admin->setPassword($password);


        for ($i = 0; $i<5; $i++){
            $administrateur = new User();
            $administrateur->setNom($faker->lastName())
                            ->setPrenom($faker->firstName())
                            ->setTelephone($faker->phoneNumber())
                            ->setEmail($faker->email())
                            ->setPassword($faker->password())
                            ->setAdministrateur(true)
                            ->setActif(true)
                            ->setRoles(['roles' =>"ROLE_ADMIN"]);

            $manager->persist($administrateur);
            }
        $manager->persist($admin);

        $manager->flush();
    }
}
