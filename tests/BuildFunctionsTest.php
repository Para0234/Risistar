<?php

namespace Risistar\Tests;

use PHPUnit\Framework\TestCase;
use BuildFunctions;

class BuildFunctionsTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__) . '/');
        }
        if (!defined('MODE')) {
            define('MODE', 'TEST');
        }

        require_once ROOT_PATH . 'includes/constants.php';
        require_once ROOT_PATH . 'includes/classes/class.BuildFunctions.php';

        $GLOBALS['resource'] = [
            901 => 'metal',
            902 => 'crystal',
            903 => 'deuterium',
            921 => 'darkmatter'
        ];
    }

    public function testGetBonusListReturnsArray()
    {
        $bonuses = BuildFunctions::getBonusList();
        $this->assertTrue(is_array($bonuses));
        $this->assertContains('Attack', $bonuses);
        $this->assertContains('Defensive', $bonuses);
        $this->assertContains('Shield', $bonuses);
        $this->assertContains('Resource', $bonuses);
        $this->assertContains('FlyTime', $bonuses);
    }

    public function testGetRestPriceCalculatesMissingResources()
    {
        $dummyUser = [
            'darkmatter' => 0
        ];
        $dummyPlanet = [
            'metal' => 500,
            'crystal' => 200,
            'deuterium' => 0
        ];
        
        $elementPrice = [
            901 => 1000, // Requires 1000 Metal
            902 => 100,  // Requires 100 Crystal
            903 => 50    // Requires 50 Deuterium
        ];

        $restPrice = BuildFunctions::getRestPrice($dummyUser, $dummyPlanet, 1, $elementPrice);

        // Player has 500 Metal -> needs 500 more
        $this->assertEquals(500, $restPrice[901]);
        // Player has 200 Crystal (needs 100) -> 0 missing
        $this->assertEquals(0, $restPrice[902]);
        // Player has 0 Deuterium -> needs 50 more
        $this->assertEquals(50, $restPrice[903]);
    }
}
