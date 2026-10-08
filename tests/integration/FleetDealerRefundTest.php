<?php

namespace Risistar\Tests\Integration;

use Config;
use ReflectionProperty;
use ShowFleetDealerPage;

/**
 * Integration tests for selling ships to the fleet dealer.
 */
class FleetDealerRefundTest extends GamePageTestCase
{
    private array $configData = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once self::rootPath() . 'includes/classes/class.BuildFunctions.php';
        require_once self::rootPath() . 'includes/pages/game/ShowFleetDealerPage.class.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        // IntegrationTestCase::setUpBeforeClass() loads vars.php inside a function, so its resource entries are not global.
        $GLOBALS['resource'] += [901 => 'metal', 902 => 'crystal', 903 => 'deuterium', 911 => 'energy', 921 => 'darkmatter'];
        $GLOBALS['reslist']['ressources'] = [901, 902, 903, 911, 921];
        $GLOBALS['reslist']['resstype'] = [1 => [901, 902, 903], 2 => [911], 3 => [921]];
        $property = new ReflectionProperty(Config::class, 'configData');
        $this->configData = $property->getValue(Config::get());
        $config = $this->configData;
        $config['trade_charge'] = 20;
        $config['trade_allowed_ships'] = '202';
        $property->setValue(Config::get(), $config);
    }

    protected function tearDown(): void
    {
        (new ReflectionProperty(Config::class, 'configData'))->setValue(Config::get(), $this->configData);
        parent::tearDown();
    }

    public function testRefundUsesTheBuildPrice(): void
    {
        // Engineer level 5 builds a small cargo for 1400 metal and 1400 crystal (30% off 2000)
        $GLOBALS['USER']['class_ability_engineer'] = 5;
        $GLOBALS['PLANET']['small_ship_cargo'] = 10;
        foreach (['metal', 'crystal', 'deuterium'] as $field) {
            $GLOBALS['PLANET'][$field] = 0;
        }
        self::$db->update('UPDATE %%PLANETS%% SET small_ship_cargo = 10 WHERE id = :id;', [':id' => $GLOBALS['PLANET']['id']]);
        $GLOBALS['_REQUEST'] = ['shipID' => 202, 'count' => 2];

        $this->callPage($this->page(ShowFleetDealerPage::class), 'send');

        // 2 ships at 1400, minus the 20% trade charge
        $this->assertEquals(2240, $GLOBALS['PLANET']['metal']);
        $this->assertEquals(2240, $GLOBALS['PLANET']['crystal']);
        $this->assertEquals(0, $GLOBALS['PLANET']['deuterium']);
        $this->assertEquals(8, $GLOBALS['PLANET']['small_ship_cargo']);
        $this->assertEquals(8, self::$db->selectSingle('SELECT small_ship_cargo FROM %%PLANETS%% WHERE id = :id;',
            [':id' => $GLOBALS['PLANET']['id']], 'small_ship_cargo'));
    }

    public function testShownPriceIsTheBuildPrice(): void
    {
        $GLOBALS['USER']['class_ability_engineer'] = 5;

        $this->callPage($this->page(ShowFleetDealerPage::class), 'show');

        $this->assertEquals(1400, $this->assigned['CostInfos'][202][2][901]);
        $this->assertEquals(1400, $this->assigned['CostInfos'][202][2][902]);
    }
}
