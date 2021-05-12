<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CampusFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $campus1 = new Campus();
        $campus2 = new Campus();
        $campus3 = new Campus();


        //creations de plusieurs campus
        $campus1->setNom("Saint herblain");
        $campus2->setNom("Chartres de bretagne");
        $campus3->setNom("La roche sur yon");


        $manager->persist($campus1);
        $manager->persist($campus2);
        $manager->persist($campus3);

        $manager->flush();
    }
}
