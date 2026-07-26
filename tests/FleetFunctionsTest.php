<?php

namespace Risistar\Tests;

use PHPUnit\Framework\TestCase;
use FleetFunctions;

class FleetFunctionsTest extends TestCase
{
    const ID_SMALL_CARGO = 202;
    const ID_LARGE_CARGO = 203;
    const ID_CRUISER = 204;

    public static function setUpBeforeClass()
    {
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__) . '/');
        }
        if (!defined('MODE')) {
            define('MODE', 'TEST');
        }
        if (!defined('MIN_FLEET_TIME')) {
            define('MIN_FLEET_TIME', 5);
        }

        require_once ROOT_PATH . 'includes/constants.php';
        require_once ROOT_PATH . 'includes/classes/class.FleetFunctions.php';

        $GLOBALS['pricelist'] = [
            self::ID_SMALL_CARGO => ['capacity' => 5000, 'speed' => 10000, 'consumption' => 20, 'tech' => 1],
            self::ID_LARGE_CARGO => ['capacity' => 25000, 'speed' => 7500, 'consumption' => 300, 'tech' => 1],
            self::ID_CRUISER => ['capacity' => 800, 'speed' => 15000, 'consumption' => 300, 'tech' => 2]
        ];
        $GLOBALS['resource'] = [
            108 => 'computer_tech',
            124 => 'expedition_tech'
        ];
    }

    public function testCheckUserSpeed()
    {
        $this->assertTrue(FleetFunctions::CheckUserSpeed(10));
        $this->assertTrue(FleetFunctions::CheckUserSpeed(1));
        $this->assertFalse(FleetFunctions::CheckUserSpeed(0));
        $this->assertFalse(FleetFunctions::CheckUserSpeed(11));
    }

    public function testGetTargetDistance()
    {
        // Same planet -> 5
        $this->assertEquals(5, FleetFunctions::GetTargetDistance([1, 1, 1], [1, 1, 1]));

        // Same system, different planet (planet 1 to 5)
        $this->assertEquals(1020, FleetFunctions::GetTargetDistance([1, 1, 1], [1, 1, 5]));

        // Same galaxy, different system (system 1 to 3)
        $this->assertEquals(2890, FleetFunctions::GetTargetDistance([1, 1, 1], [1, 3, 1]));

        // Different galaxy (galaxy 1 to 2)
        $this->assertEquals(20000, FleetFunctions::GetTargetDistance([1, 1, 1], [2, 1, 1]));
    }

    public function testGetFleetRoom()
    {
        // Fleet: 10 Small Cargo + 2 Large Cargo
        $fleet = [
            self::ID_SMALL_CARGO => 10,
            self::ID_LARGE_CARGO => 2
        ];

        $totalRoom = FleetFunctions::GetFleetRoom($fleet);
        // (10 * 5000) + (2 * 25000) = 100 000
        $this->assertEquals(100000, $totalRoom);
    }

    public function testGetMissileRange()
    {
        $this->assertEquals(4, FleetFunctions::GetMissileRange(1));
        $this->assertEquals(19, FleetFunctions::GetMissileRange(4));
        $this->assertEquals(0, FleetFunctions::GetMissileRange(0));
    }
}
