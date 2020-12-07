<?php

/**
 * PHP version 7.4
 * tests/Entity/UserTest.php
 *
 * @category EntityTests
 * @package  MiW\Results\Tests
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace MiW\Results\Tests\Entity;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

use Faker\Factory;
use MiW\Results\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 *
 * @package MiW\Results\Tests\Entity
 * @group   users
 */
class UserTest extends TestCase
{
    /**
     * @var User $user
     */
    private $user;
    private $faker;
    private $username;
    private $email;
    private $password;

    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->user = new User();
        $this->faker = Factory::create();
        $this->username = $this->faker->firstName;
        $this->email = $this->faker->email;
        $this->password = $this->faker->password;
    }

    /**
     * @covers \MiW\Results\Entity\User::__construct()
     */
    public function testConstructor(): void
    {
        $this->user = new User(
            $this->username,
            $this->email,
            $this->password,
            true,
            false,
        );
        $this->assertEquals($this->username, $this->user->getUsername());
        $this->assertEquals($this->email, $this->user->getEmail());
        $this->assertEquals(true, $this->user->isEnabled());
        $this->assertEquals(false, $this->user->isAdmin());

    }

    /**
     * @covers \MiW\Results\Entity\User::getId()
     */
    public function testGetId(): void
    {
        $this->assertEquals($this->user->getID(), $this->user->getID());
    }

    /**
     * @covers \MiW\Results\Entity\User::setUsername()
     * @covers \MiW\Results\Entity\User::getUsername()
     */
    public function testGetSetUsername(): void
    {
        $this->username = $this->faker->firstName;
        $this->user->setUsername($this->username);
        $this->assertEquals($this->username, $this->user->getUsername());
    }

    /**
     * @covers \MiW\Results\Entity\User::getEmail()
     * @covers \MiW\Results\Entity\User::setEmail()
     */
    public function testGetSetEmail(): void
    {
        $this->email = $this->faker->email;
        $this->user->setEmail($this->email);
        $this->assertEquals($this->email, $this->user->getEmail());
    }

    /**
     * @covers \MiW\Results\Entity\User::setEnabled()
     * @covers \MiW\Results\Entity\User::isEnabled()
     */
    public function testIsSetEnabled(): void
    {
        $this->user->setEnabled(false);
        $this->assertEquals(false, $this->user->isEnabled());
    }

    /**
     * @covers \MiW\Results\Entity\User::setIsAdmin()
     * @covers \MiW\Results\Entity\User::isAdmin
     */
    public function testIsSetAdmin(): void
    {
        $this->user->setIsAdmin(false);
        $this->assertEquals(false, $this->user->isAdmin());
    }

    /**
     * @covers \MiW\Results\Entity\User::setPassword()
     * @covers \MiW\Results\Entity\User::validatePassword()
     */
    public function testSetValidatePassword(): void
    {
        $this->password = $this->faker->password;
        $this->user->setPassword($this->password);
        $this->assertEquals(true, $this->user->validatePassword($this->password));
    }

    /**
     * @covers \MiW\Results\Entity\User::__toString()
     */
    public function testToString(): void
    {
        $candidate = sprintf(
            '%3d - %20s - %30s - %1d - %1d',
            $this->user->getId(),
            utf8_encode($this->user->getUsername()),
            utf8_encode($this->user->getEmail()),
            $this->user->isEnabled(),
            $this->user->isAdmin()
        );
        $this->assertEquals($candidate, $this->user->__toString());
    }

    /**
     * @covers \MiW\Results\Entity\User::jsonSerialize()
     */
    public function testJsonSerialize(): void
    {
        $candidate = array(
            'id'            => $this->user->getId(),
            'username'      => utf8_encode($this->user->getUsername()),
            'email'         => utf8_encode($this->user->getEmail()),
            'enabled'       => $this->user->isEnabled(),
            'admin'         => $this->user->isAdmin()
        );
        $this->assertEquals($candidate, $this->user->jsonSerialize());
    }
}
