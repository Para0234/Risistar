<?php

namespace Risistar\Tests\Unit;

use ReflectionClass;
use ReflectionMethod;
use ShowResearchPage;

class ShowResearchPageTest extends UnitTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::bootConstants();
        require_once self::rootPath() . 'includes/pages/game/ShowResearchPage.class.php';
    }

    public function testDestroyModeDoesNotQueueOrSpendResources(): void
    {
        $metal = 5000;
        $GLOBALS['PLANET'] = [
            'id' => 1,
            'metal' => $metal,
            'crystal' => 3000,
            'deuterium' => 1000,
            'laboratory' => 1,
            'b_building' => 0,
        ];
        $GLOBALS['USER'] = [
            'id' => 1,
            'b_tech_queue' => '',
            'b_tech' => 0,
            'b_tech_id' => 0,
            'b_tech_planet' => 0,
            'spy_tech' => 0,
            'laboratory' => 1,
        ];

        $page = (new ReflectionClass(ShowResearchPage::class))->newInstanceWithoutConstructor();
        $ecoObj = (new ReflectionClass(ShowResearchPage::class))->getParentClass()->getProperty('ecoObj');
        $ecoObj->setAccessible(true);
        $ecoObj->setValue($page, new \stdClass());

        $method = new ReflectionMethod(ShowResearchPage::class, 'AddBuildingToQueue');
        $method->setAccessible(true);

        $result = $method->invoke($page, 106, false);

        $this->assertFalse($result);
        $this->assertSame($metal, $GLOBALS['PLANET']['metal']);
        $this->assertSame('', $GLOBALS['USER']['b_tech_queue']);
    }
}
