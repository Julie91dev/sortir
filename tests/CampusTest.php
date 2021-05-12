<?php

namespace App\Tests;

use App\Entity\Campus;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CampusTest extends TestCase
{
    public function testIsTrue(): void
    {
        $user = new User();

        $user->setNom("nom")
            ->setPrenom("prenom")
            -> setTelephone("0102030405")
            ->setEmail("true@test.com")
            ->setPassword("password")
            ->setAdministrateur(true)
            ->setActif(true);

        $campus = new Campus();

        $campus->setNom("nom")
               ->addParticipant($user);

        $this->assertTrue($campus->getNom() === "nom");
        $this->assertTrue($campus->addParticipant($user) === $user);

    }

    public function testIsFalse(): void
    {
        $user = new User();

        $user->setNom("nom")
            ->setPrenom("prenom")
            -> setTelephone("0102030405")
            ->setEmail("true@test.com")
            ->setPassword("password")
            ->setAdministrateur(true)
            ->setActif(true);

        $user1 = new User();

        $user1->setNom("false")
            ->setPrenom("false")
            -> setTelephone("false")
            ->setEmail("false@test.com")
            ->setPassword("false")
            ->setAdministrateur(true)
            ->setActif(true);

        $campus = new Campus();

        $campus->setNom("nom")
            ->addParticipant($user);

        $this->assertFalse($campus->getNom() === "false");
        $this->assertFalse($campus->addParticipant($user) === $user1);

    }

    public function testIsEmpty(): void
    {
        $user = new User();
        $campus = new Campus();

        $this->assertEmpty($campus->getNom());
        $this->assertEmpty($campus->addParticipant($user));
    }
}
