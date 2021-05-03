<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthTest extends WebTestCase
{

    public function testLoginIsSuccess(): void
    {

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findByEmail("toto@toto.com");
        $user = loginUser($testUser);
       $this->assertResponseIsSuccessful();
    }
   /* public function testLoginWithBadPassword(): void
    {
        $this->assertNull($this->auth->login("admin@test.com", "toto"));
    }
    public function testLoginWithEmpty(): void
    {
        $this->assertEmpty($this->auth->login());
    }
    public function testLoginIsSuccess(): void
    {
        $this->assertNull($this->auth->login("admin@test.com", "password"));
    }*/
}
