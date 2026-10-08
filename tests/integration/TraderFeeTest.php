<?php

namespace Risistar\Tests\Integration;

use Config;
use ShowTraderPage;

/**
 * Integration tests for resource trades at the merchant.
 */
class TraderFeeTest extends GamePageTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once self::rootPath() . 'includes/pages/game/ShowTraderPage.class.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        // IntegrationTestCase::setUpBeforeClass() loads vars.php inside a function, so its resource entries are not global.
        $GLOBALS['resource'] += [901 => 'metal', 902 => 'crystal', 903 => 'deuterium', 911 => 'energy', 921 => 'darkmatter'];
        $GLOBALS['reslist']['ressources'] = [901, 902, 903, 911, 921];
        $GLOBALS['reslist']['resstype'] = [1 => [901, 902, 903], 2 => [911], 3 => [921]];
        $GLOBALS['USER']['darkmatter'] = Config::get()->darkmatter_cost_trader * 2 + 100;
        $GLOBALS['PLANET']['metal'] = 1000;
        $GLOBALS['PLANET']['crystal'] = 0;
        $GLOBALS['PLANET']['deuterium'] = 0;
        $GLOBALS['_REQUEST'] = ['resource' => 901];
    }

    public function testRefusedTradeChangesNothing(): void
    {
        $before = $GLOBALS['PLANET'];
        $matter = $GLOBALS['USER']['darkmatter'];
        $GLOBALS['_REQUEST']['trade'] = [902 => 100, 903 => 1000];
        $this->send();
        $this->assertSame($before, $GLOBALS['PLANET']);
        $this->assertSame($matter, $GLOBALS['USER']['darkmatter']);
    }

    public function testValidTradePaysTheFeeOnce(): void
    {
        $matter = $GLOBALS['USER']['darkmatter'];
        $GLOBALS['_REQUEST']['trade'] = [902 => 100, 903 => 100];
        $this->send();
        $this->assertEquals(400, $GLOBALS['PLANET']['metal']);
        $this->assertEquals(100, $GLOBALS['PLANET']['crystal']);
        $this->assertEquals(100, $GLOBALS['PLANET']['deuterium']);
        $this->assertEquals($matter - Config::get()->darkmatter_cost_trader, $GLOBALS['USER']['darkmatter']);
    }

    private function send(): void
    {
        $this->callPage($this->page(ShowTraderPage::class), 'send');
    }
}
