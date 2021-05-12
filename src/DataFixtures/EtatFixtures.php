<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class EtatFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $etat1 = new Etat();
        $etat2 = new Etat();
        $etat3 = new Etat();
        $etat4 = new Etat();
        $etat5 = new Etat();


        //creations de plusieurs campus
        $etat1->setLibelle("Ouverte");
        $etat2->setLibelle("Clôturée");
        $etat3->setLibelle("Activé, en cours");
        $etat4->setLibelle("Passee");
        $etat5->setLibelle("Annulée");



        $manager->persist($etat1);
        $manager->persist($etat2);
        $manager->persist($etat3);
        $manager->persist($etat4);
        $manager->persist($etat5);

        $manager->flush();
    }
}
