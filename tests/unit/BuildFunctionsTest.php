<?php

namespace Risistar\Tests\Unit;

use BuildFunctions;

class BuildFunctionsTest extends UnitTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::bootConstants();
        require_once self::rootPath() . 'includes/classes/class.BuildFunctions.php';

        $GLOBALS['resource'] = [
            901 => 'metal',
            902 => 'crystal',
            903 => 'deuterium',
            921 => 'darkmatter',
        ];
    }

    public function testGetBonusListReturnsArray()
    {
        $bonuses = BuildFunctions::getBonusList();
        $this->assertIsArray($bonuses);
        $this->assertContainsEquals('Attack', $bonuses);
        $this->assertContainsEquals('Defensive', $bonuses);
        $this->assertContainsEquals('Shield', $bonuses);
        $this->assertContainsEquals('Resource', $bonuses);
        $this->assertContainsEquals('FlyTime', $bonuses);
    }

    public function testGetRestPriceCalculatesMissingResources()
    {
        $dummyUser = ['darkmatter' => 0];
        $dummyPlanet = [
            'metal' => 500,
            'crystal' => 200,
            'deuterium' => 0,
        ];
        $elementPrice = [
            901 => 1000,
            902 => 100,
            903 => 50,
        ];

        $restPrice = BuildFunctions::getRestPrice($dummyUser, $dummyPlanet, 1, $elementPrice);

        $this->assertEquals(500, $restPrice[901]);
        $this->assertEquals(0, $restPrice[902]);
        $this->assertEquals(50, $restPrice[903]);
    }
}
