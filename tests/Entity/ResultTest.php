<?php

/**
 * PHP version 7.4
 * tests/Entity/ResultTest.php
 *
 * @category EntityTests
 * @package  MiW\Results\Tests
 * @license  https://opensource.org/licenses/MIT MIT License
 * @link     http://www.etsisi.upm.es/ ETS de Ingeniería de Sistemas Informáticos
 */

namespace MiW\Results\Tests\Entity;

use Faker\Factory;
use MiW\Results\Entity\Result;
use MiW\Results\Entity\User;

/**
 * Class ResultTest
 *
 * @package MiW\Results\Tests\Entity
 */
class ResultTest extends \PHPUnit\Framework\TestCase
{
    private $faker;
    /**
     * @var User $user
     */
    private $user;

    /**
     * @var Result $result
     */
    private $result;

    private const USERNAME = 'uSeR ñ¿?Ñ';
    private const POINTS = 2018;

    /**
     * @var \DateTime $time
     */
    private $time;

    /**
     * Sets up the fixture.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->user = new User();
        $this->user->setUsername(self::USERNAME);
        $this->time = new \DateTime('now');
        $this->faker = Factory::create();
        $this->result = new Result(
            self::POINTS,
            $this->user,
            $this->time
        );
    }

    /**
     * Implement testConstructor
     *
     * @covers \MiW\Results\Entity\Result::__construct()
     * @covers \MiW\Results\Entity\Result::getId()
     * @covers \MiW\Results\Entity\Result::getResult()
     * @covers \MiW\Results\Entity\Result::getFormattedTime()
     *
     * @return void
     */
    public function testConstructor(): void
    {
        $this->result = new Result(
            self::POINTS,
            $this->user,
            $this->time
        );
        $this->assertIsInt($this->result->getId());
        $this->assertEquals(self::POINTS, $this->result->getResult());
        $this->assertIsString($this->result->getFormattedTime());
    }

    /**
     * Implement testGet_Id().
     *
     * @covers \MiW\Results\Entity\Result::getId()
     * @return void
     */
    public function testGetId():void
    {
        $this->assertIsInt($this->result->getId());
    }

    /**
     * Implement testUsername().
     *
     * @covers \MiW\Results\Entity\Result::setResult
     * @covers \MiW\Results\Entity\Result::getResult
     * @return void
     */
    public function testResult(): void
    {
       $result = $this->faker->numberBetween(0, 1000);
       $this->result->setResult($result);
       $this->assertEquals($result, $this->result->getResult());
    }

    /**
     * Implement testTime().
     *
     * @covers \MiW\Results\Entity\Result::setTime
     * @covers \MiW\Results\Entity\Result::getFormattedTime
     * @return void
     */
    public function testTime(): void
    {
        $this->result->setTime(new \DateTime('now'));
        $this->assertIsString($this->result->getFormattedTime());
    }

    /**
     * Implement testTo_String().
     *
     * @covers \MiW\Results\Entity\Result::__toString
     * @return void
     */
    public function testToString(): void
    {
        $candidate = sprintf(
            '%3d - %3d - %22s - %s',
            $this->result->getId(),
            $this->result->getResult(),
            $this->user->getUsername(),
            $this->result->getFormattedTime()
        );
        $this->assertEquals($candidate, $this->result->__toString());
    }

    /**
     * Implement testJson_Serialize().
     *
     * @covers \MiW\Results\Entity\Result::jsonSerialize
     * @return void
     */
    public function testJsonSerialize(): void
    {
        $candidate = array(
            'id'     => $this->result->getId(),
            'result' => $this->result->getResult(),
            'user'   => $this->user,
            'time'   => $this->result->getFormattedTime()
        );
        $this->assertEquals($candidate, $this->result->jsonSerialize());
    }
}
