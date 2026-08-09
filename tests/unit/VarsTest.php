<?php

namespace Risistar\Tests\Unit;

class VarsTest extends UnitTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::bootConstants();
    }

    public function testResourceElementTypesDefined()
    {
        $this->assertTrue(defined('ELEMENT_BUILD'));
        $this->assertTrue(defined('ELEMENT_TECH'));
        $this->assertTrue(defined('ELEMENT_FLEET'));
        $this->assertTrue(defined('ELEMENT_DEFENSIVE'));
    }

    public function testGameConstantsDefined()
    {
        $this->assertTrue(defined('TIMESTAMP'));
        $this->assertTrue(defined('BASH_ON'));
        $this->assertTrue(defined('BASH_TIME'));
    }
}
