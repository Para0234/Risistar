<?php

namespace Risistar\Tests\Unit;

class CalculateMaxPlanetFieldsTest extends UnitTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::bootConstants();
        require_once self::rootPath() . 'includes/GeneralFunctions.php';
    }

    protected function setUp(): void
    {
        $GLOBALS['resource'] = [
            33 => 'terraformer',
            41 => 'mondbasis',
        ];
        $GLOBALS['USER'] = [
            'class_ability_lunar' => 0,
        ];
    }

    public function testMoonWithoutLunarMinerKeepsThreeFieldsPerMoonbaseLevel(): void
    {
        $planet = ['field_max' => 1, 'terraformer' => 0, 'mondbasis' => 5];

        $this->assertSame(16, CalculateMaxPlanetFields($planet));
    }

    public function testLunarMinerLevelFiveAddsFiveFieldsPerMoonbaseLevel(): void
    {
        $GLOBALS['USER']['class_ability_lunar'] = 5;
        $planet = ['field_max' => 1, 'terraformer' => 0, 'mondbasis' => 5];

        $this->assertSame(41, CalculateMaxPlanetFields($planet));
    }
}
