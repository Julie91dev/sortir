<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserUnitTest extends TestCase
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

        $this->assertTrue($user->getNom() === "nom");
        $this->assertTrue($user->getPrenom() === "prenom");
        $this->assertTrue($user->getTelephone() === "0102030405");
        $this->assertTrue($user->getEmail() === "true@test.com");
        $this->assertTrue($user->getPassword() === "password");
        $this->assertTrue($user->getAdministrateur() === true);
        $this->assertTrue($user->getActif() === true);
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

        $this->assertFalse($user->getNom() === "false");
        $this->assertFalse($user->getPrenom() === "false");
        $this->assertFalse($user->getTelephone() === "0504030201");
        $this->assertFalse($user->getEmail() === "false@test.com");
        $this->assertFalse($user->getPassword() === "false");
        $this->assertFalse($user->getAdministrateur() === false);
        $this->assertFalse($user->getActif() === false);
    }

    public function testIsEmpty(): void
    {
        $user = new User();


        $this->assertEmpty($user->getNom());
        $this->assertEmpty($user->getPrenom());
        $this->assertEmpty($user->getTelephone());
        $this->assertEmpty($user->getEmail());
        $this->assertEmpty($user->getPassword());
        $this->assertEmpty($user->getAdministrateur());
        $this->assertEmpty($user->getActif());
    }
}
