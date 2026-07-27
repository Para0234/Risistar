<?php

namespace Risistar\Tests;

use PHPUnit\Framework\TestCase;
use FleetFunctions;

class FleetFunctionsTest extends TestCase
{
    const ID_SMALL_CARGO = 202;
    const ID_LARGE_CARGO = 203;
    const ID_CRUISER = 204;

    public static function setUpBeforeClass(): void
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
            self::ID_SMALL_CARGO => ['capacity' => 5000, 'speed' => 5000, 'speed2' => 10000, 'consumption' => 10, 'consumption2' => 20, 'tech' => 4],
            self::ID_LARGE_CARGO => ['capacity' => 25000, 'speed' => 7500, 'speed2' => 7500, 'consumption' => 50, 'consumption2' => 50, 'tech' => 1],
            self::ID_CRUISER => ['capacity' => 800, 'speed' => 15000, 'consumption' => 300, 'tech' => 2],
            211 => ['capacity' => 500, 'speed' => 4000, 'speed2' => 5000, 'consumption' => 1000, 'consumption2' => 1000, 'tech' => 5]
        ];
        $GLOBALS['resource'] = [
            108 => 'computer_tech',
            124 => 'expedition_tech'
        ];
    }

    public function testGetShipSpeedSmallCargoEngineUpgrade()
    {
        // Case 1: Impulse Tech < 5 -> Uses Combustion Tech with base speed 5000
        $playerCombustionOnly = [
            'combustion_tech' => 10,
            'impulse_motor_tech' => 4,
            'hyperspace_motor_tech' => 0
        ];
        $speedPtCombustion = FleetFunctions::GetFleetMaxSpeed(self::ID_SMALL_CARGO, $playerCombustionOnly);
        // Base 5000 * (1 + 0.1 * 10) = 10000
        $this->assertEquals(10000, $speedPtCombustion);

        // Case 2: Impulse Tech >= 5 -> Uses Impulse Tech with upgraded base speed (speed2 = 10000)
        $playerImpulse = [
            'combustion_tech' => 10,
            'impulse_motor_tech' => 5,
            'hyperspace_motor_tech' => 0
        ];
        $speedPtImpulse = FleetFunctions::GetFleetMaxSpeed(self::ID_SMALL_CARGO, $playerImpulse);
        $speedGtImpulse = FleetFunctions::GetFleetMaxSpeed(self::ID_LARGE_CARGO, $playerImpulse);

        // Base speed2 10000 * (1 + 0.2 * 5) = 20000
        $this->assertEquals(20000, $speedPtImpulse);

        // GT speed with Combustion 10: 7500 * (1 + 0.1 * 10) = 15000
        $this->assertEquals(15000, $speedGtImpulse);

        // PT must be faster than GT once Impulse Tech 5 is reached!
        $this->assertGreaterThan($speedGtImpulse, $speedPtImpulse);
    }

    public function testGetShipSpeedBomberEngineUpgrade()
    {
        // Case 1: Hyperspace Tech < 8 -> Uses Impulse Tech with base speed 4000
        $playerImpulseOnly = [
            'combustion_tech' => 0,
            'impulse_motor_tech' => 6,
            'hyperspace_motor_tech' => 7
        ];
        $speedBomberImpulse = FleetFunctions::GetFleetMaxSpeed(211, $playerImpulseOnly);
        // Base 4000 * (1 + 0.2 * 6) = 8800
        $this->assertEquals(8800, $speedBomberImpulse);

        // Case 2: Hyperspace Tech >= 8 -> Uses Hyperspace Tech with upgraded base speed (speed2 = 5000)
        $playerHyperspace = [
            'combustion_tech' => 0,
            'impulse_motor_tech' => 6,
            'hyperspace_motor_tech' => 8
        ];
        $speedBomberHyperspace = FleetFunctions::GetFleetMaxSpeed(211, $playerHyperspace);
        // Base speed2 5000 * (1 + 0.3 * 8) = 17000
        $this->assertEquals(17000, $speedBomberHyperspace);
    }

    public function testGetShipSpeedFallbackWhenSpeed2IsNull()
    {
        // Define ship with tech 5 but null speed2
        $GLOBALS['pricelist'][299] = ['capacity' => 500, 'speed' => 6000, 'speed2' => null, 'consumption' => 1000, 'consumption2' => 1000, 'tech' => 5];

        $player = [
            'combustion_tech' => 0,
            'impulse_motor_tech' => 6,
            'hyperspace_motor_tech' => 8
        ];

        // Should fall back to speed1 (6000) * (1 + 0.3 * 8) = 20400 instead of 0
        $speed = FleetFunctions::GetFleetMaxSpeed(299, $player);
        $this->assertEquals(20400, $speed);
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
