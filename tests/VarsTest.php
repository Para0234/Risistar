<?php

namespace Risistar\Tests;

use PHPUnit\Framework\TestCase;

class VarsTest extends TestCase
{
    protected static $loadedVars = [];

    public static function setUpBeforeClass(): void
    {
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__) . '/');
        }
        if (!defined('MODE')) {
            define('MODE', 'TEST');
        }
        
        require_once ROOT_PATH . 'includes/constants.php';
    }

    public function testResourceConstantsDefined()
    {
        $this->assertTrue(defined('ELEMENT_BUILD'));
        $this->assertTrue(defined('ELEMENT_TECH'));
        $this->assertTrue(defined('ELEMENT_FLEET'));
        $this->assertTrue(defined('ELEMENT_DEFENSIVE'));
    }

    public function testCoreResourceIDs()
    {
        $resource = [
            901 => 'metal',
            902 => 'crystal',
            903 => 'deuterium',
            911 => 'energy',
            921 => 'darkmatter'
        ];

        $this->assertEquals('metal', $resource[901]);
        $this->assertEquals('crystal', $resource[902]);
        $this->assertEquals('deuterium', $resource[903]);
        $this->assertEquals('darkmatter', $resource[921]);
    }
}
