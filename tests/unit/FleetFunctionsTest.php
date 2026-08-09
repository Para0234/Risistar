<?php

namespace Risistar\Tests\Unit;

use FleetFunctions;

class FleetFunctionsTest extends UnitTestCase
{
    const ID_SMALL_CARGO = 202;
    const ID_LARGE_CARGO = 203;
    const ID_CRUISER = 204;

    public static function setUpBeforeClass(): void
    {
        if (!defined('MIN_FLEET_TIME')) {
            define('MIN_FLEET_TIME', 5);
        }

        self::bootConstants();
        require_once self::rootPath() . 'includes/classes/class.FleetFunctions.php';

        $GLOBALS['pricelist'] = [
            self::ID_SMALL_CARGO => ['capacity' => 5000, 'speed' => 5000, 'speed2' => 10000, 'consumption' => 10, 'consumption2' => 20, 'tech' => 4],
            self::ID_LARGE_CARGO => ['capacity' => 25000, 'speed' => 7500, 'speed2' => 7500, 'consumption' => 50, 'consumption2' => 50, 'tech' => 1],
            self::ID_CRUISER => ['capacity' => 800, 'speed' => 15000, 'consumption' => 300, 'tech' => 2],
            211 => ['capacity' => 500, 'speed' => 4000, 'speed2' => 5000, 'consumption' => 1000, 'consumption2' => 1000, 'tech' => 5],
        ];
        $GLOBALS['resource'] = [
            108 => 'computer_tech',
            124 => 'expedition_tech',
        ];
    }

    public function testGetShipSpeedSmallCargoEngineUpgrade()
    {
        $playerCombustionOnly = [
            'combustion_tech' => 10,
            'impulse_motor_tech' => 4,
            'hyperspace_motor_tech' => 0,
        ];
        $speedPtCombustion = FleetFunctions::GetFleetMaxSpeed(self::ID_SMALL_CARGO, $playerCombustionOnly);
        $this->assertEquals(10000, $speedPtCombustion);

        $playerImpulse = [
            'combustion_tech' => 10,
            'impulse_motor_tech' => 5,
            'hyperspace_motor_tech' => 0,
        ];
        $speedPtImpulse = FleetFunctions::GetFleetMaxSpeed(self::ID_SMALL_CARGO, $playerImpulse);
        $speedGtImpulse = FleetFunctions::GetFleetMaxSpeed(self::ID_LARGE_CARGO, $playerImpulse);

        $this->assertEquals(20000, $speedPtImpulse);
        $this->assertEquals(15000, $speedGtImpulse);
        $this->assertGreaterThan($speedGtImpulse, $speedPtImpulse);
    }

    public function testGetShipSpeedBomberEngineUpgrade()
    {
        $playerImpulseOnly = [
            'combustion_tech' => 0,
            'impulse_motor_tech' => 6,
            'hyperspace_motor_tech' => 7,
        ];
        $speedBomberImpulse = FleetFunctions::GetFleetMaxSpeed(211, $playerImpulseOnly);
        $this->assertEquals(8800, $speedBomberImpulse);

        $playerHyperspace = [
            'combustion_tech' => 0,
            'impulse_motor_tech' => 6,
            'hyperspace_motor_tech' => 8,
        ];
        $speedBomberHyperspace = FleetFunctions::GetFleetMaxSpeed(211, $playerHyperspace);
        $this->assertEquals(17000, $speedBomberHyperspace);
    }

    public function testGetShipSpeedFallbackWhenSpeed2IsNull()
    {
        $GLOBALS['pricelist'][299] = ['capacity' => 500, 'speed' => 6000, 'speed2' => null, 'consumption' => 1000, 'consumption2' => 1000, 'tech' => 5];

        $player = [
            'combustion_tech' => 0,
            'impulse_motor_tech' => 6,
            'hyperspace_motor_tech' => 8,
        ];

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
        $this->assertEquals(5, FleetFunctions::GetTargetDistance([1, 1, 1], [1, 1, 1]));
        $this->assertEquals(1020, FleetFunctions::GetTargetDistance([1, 1, 1], [1, 1, 5]));
        $this->assertEquals(2890, FleetFunctions::GetTargetDistance([1, 1, 1], [1, 3, 1]));
        $this->assertEquals(20000, FleetFunctions::GetTargetDistance([1, 1, 1], [2, 1, 1]));
    }

    public function testGetFleetRoom()
    {
        $fleet = [
            self::ID_SMALL_CARGO => 10,
            self::ID_LARGE_CARGO => 2,
        ];

        $this->assertEquals(100000, FleetFunctions::GetFleetRoom($fleet));
    }

    public function testGetMissileRange()
    {
        $this->assertEquals(4, FleetFunctions::GetMissileRange(1));
        $this->assertEquals(19, FleetFunctions::GetMissileRange(4));
        $this->assertEquals(0, FleetFunctions::GetMissileRange(0));
    }
}
