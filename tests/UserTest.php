<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testIsTrue()
    {
        $user = new User();

        $user->setEmail("test@test.com")
             ->setFirstName("firstName")
             ->setLastName("lastName")
             ->setPassword("password")
             ->setGender(User::FEMMME)
             ->setMode("light")
             ->setRoles([User::ROLE_USER]);

        $this->assertTrue($user->getEmail() === "test@test.com");
        $this->assertTrue($user->getFirstName() === "firstName");
        $this->assertTrue($user->getLastName() === "lastName");
        $this->assertTrue($user->getPassword() === "password");
        $this->assertTrue($user->getGender() === "Femme");
        $this->assertTrue($user->getMode() === "light");
        $this->assertTrue($user->getRoles() === ["ROLE_USER"]);
        $this->assertTrue($user->getFullName() === "firstName lastName");
    }

    public function testIsFalse()
    {
        $user = new User();

        $user->setEmail("test@test.com")
             ->setFirstName("firstName")
             ->setLastName("lastName")
             ->setPassword("password")
             ->setGender(User::FEMMME)
             ->setMode("light")
             ->setRoles([User::ROLE_USER]);

        $this->assertFalse($user->getEmail() === "false@test.com");
        $this->assertFalse($user->getFirstName() === "falseName");
        $this->assertFalse($user->getLastName() === "falseName");
        $this->assertFalse($user->getPassword() === "false");
        $this->assertFalse($user->getGender() === "false");
        $this->assertFalse($user->getMode() === "false");
        $this->assertFalse($user->getRoles() === ["ROLE_false"]);
        $this->assertFalse($user->getFullName() === "firstName lastfalse");
    }

    public function testIsEmpty()
    {
        $user = new User();

        $this->assertEmpty($user->getEmail());
        $this->assertEmpty($user->getFirstName());
        $this->assertEmpty($user->getLastName());
        $this->assertEmpty($user->getPassword());
        $this->assertEmpty($user->getGender());
        $this->assertEmpty($user->getMode());
    }
}
