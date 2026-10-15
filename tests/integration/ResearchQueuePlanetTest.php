<?php

namespace Risistar\Tests\Integration;

use Config;
use ReflectionMethod;
use ReflectionProperty;
use ResourceUpdate;
use ShowResearchPage;

/**
 * Integration tests for queued research paid by another planet of the player.
 */
class ResearchQueuePlanetTest extends GamePageTestCase
{
    private array $otherPlanet = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once self::rootPath() . 'includes/classes/class.BuildFunctions.php';
        require_once self::rootPath() . 'includes/classes/class.PlanetRessUpdate.php';
        require_once self::rootPath() . 'includes/pages/game/ShowResearchPage.class.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        // IntegrationTestCase::setUpBeforeClass() loads vars.php inside a function, so its resource entries are not global.
        $GLOBALS['resource'] += [901 => 'metal', 902 => 'crystal', 903 => 'deuterium', 911 => 'energy', 921 => 'darkmatter'];
        $GLOBALS['reslist']['ressources'] = [901, 902, 903, 911, 921];
        $GLOBALS['reslist']['resstype'] = [1 => [901, 902, 903], 2 => [911], 3 => [921]];
        $otherPlanet = self::$db->selectSingle('SELECT * FROM %%PLANETS%% WHERE id_owner = :owner AND id != :id AND planet_type = 1 AND destruyed = 0 ORDER BY id LIMIT 1;',
            [':owner' => $GLOBALS['USER']['id'], ':id' => $GLOBALS['PLANET']['id']]);
        if (empty($otherPlanet)) {
            $this->markTestSkipped('The test database needs a player with two planets.');
        }
        $this->otherPlanet = $otherPlanet;
        foreach ([$GLOBALS['PLANET']['id'], $this->otherPlanet['id']] as $id) {
            self::$db->update('UPDATE %%PLANETS%% SET laboratory = 20, metal = 1e9, crystal = 1e9, deuterium = 1e9 WHERE id = :id;', [':id' => $id]);
        }
        $GLOBALS['PLANET'] = self::$db->selectSingle('SELECT * FROM %%PLANETS%% WHERE id = :id;', [':id' => $GLOBALS['PLANET']['id']]);
        $this->otherPlanet = self::$db->selectSingle('SELECT * FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->otherPlanet['id']]);
    }

    public function testOrderOnAnAbandonedPlanetIsDropped(): void
    {
        self::$db->update('UPDATE %%PLANETS%% SET destruyed = :time WHERE id = :id;', [':time' => TIMESTAMP + 86400, ':id' => $this->otherPlanet['id']]);
        $spy = (int) $GLOBALS['USER']['spy_tech'];
        $computer = (int) $GLOBALS['USER']['computer_tech'];

        $user = $this->nextResearch([
            [106, $spy + 1, $this->otherPlanet['id']],
            [106, $spy + 2, $GLOBALS['PLANET']['id']],
            [108, $computer + 1, $GLOBALS['PLANET']['id']],
        ]);

        // The later spy order goes too, the computer order starts on the current planet
        $queue = unserialize($user['b_tech_queue']);
        $this->assertSame([108], array_column($queue, 0));
        $this->assertEquals(108, $user['b_tech_id']);
        $this->assertEquals($this->otherPlanet['metal'], self::$db->selectSingle('SELECT metal FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->otherPlanet['id']], 'metal'));
    }

    public function testOrderOnADeletedPlanetEmptiesTheQueue(): void
    {
        $user = $this->nextResearch([[106, (int) $GLOBALS['USER']['spy_tech'] + 1, 2147483647]]);

        $this->assertSame('', $user['b_tech_queue']);
        $this->assertEquals(0, $user['b_tech']);
    }

    public function testOrderOnAnotherPlanetIsPaidThere(): void
    {
        $user = $this->nextResearch([[106, (int) $GLOBALS['USER']['spy_tech'] + 1, $this->otherPlanet['id']]]);

        $this->assertEquals($this->otherPlanet['id'], $user['b_tech_planet']);
        $this->assertLessThan($this->otherPlanet['metal'], self::$db->selectSingle('SELECT metal FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->otherPlanet['id']], 'metal'));
    }

    public function testQueueListsAnOrderOnAnAbandonedPlanet(): void
    {
        $level = (int) $GLOBALS['USER']['spy_tech'];
        $GLOBALS['USER']['PLANETS'] = [$GLOBALS['PLANET']['id'] => $GLOBALS['PLANET']];
        $GLOBALS['USER']['b_tech'] = TIMESTAMP + 3600;
        $GLOBALS['USER']['b_tech_queue'] = serialize([
            [106, $level + 1, 3600, TIMESTAMP + 3600, $GLOBALS['PLANET']['id']],
            [106, $level + 2, 3600, TIMESTAMP + 7200, $this->otherPlanet['id']],
        ]);

        $page = $this->page(ShowResearchPage::class);
        $queue = (new ReflectionMethod(ShowResearchPage::class, 'getQueueData'))->invoke($page)['queue'];

        $this->assertCount(2, $queue);
        $this->assertSame('', $queue[1]['planet']);
    }

    public function testRemovingAnOrderKeepsALaterOrderOnADeletedPlanet(): void
    {
        $GLOBALS['USER']['factor'] = getFactors($GLOBALS['USER']);
        $GLOBALS['USER']['b_tech'] = TIMESTAMP + 3600;
        $GLOBALS['USER']['b_tech_queue'] = serialize([
            [106, (int) $GLOBALS['USER']['spy_tech'] + 1, 3600, TIMESTAMP + 3600, $GLOBALS['PLANET']['id']],
            [108, (int) $GLOBALS['USER']['computer_tech'] + 1, 3600, TIMESTAMP + 7200, $GLOBALS['PLANET']['id']],
            [109, (int) $GLOBALS['USER']['military_tech'] + 1, 3600, TIMESTAMP + 10800, 2147483647],
        ]);

        $page = $this->page(ShowResearchPage::class);
        (new ReflectionProperty(ShowResearchPage::class, 'ecoObj'))->setValue($page, new ResourceUpdate());
        (new ReflectionMethod(ShowResearchPage::class, 'RemoveBuildingFromQueue'))->invoke($page, 2);

        $queue = unserialize($GLOBALS['USER']['b_tech_queue']);
        $this->assertSame([106, 109], array_column($queue, 0));
    }

    public function testCancellingAnOrderDropsALaterOrderOnADeletedPlanet(): void
    {
        $GLOBALS['USER']['factor'] = getFactors($GLOBALS['USER']);
        $GLOBALS['USER']['b_tech'] = TIMESTAMP + 3600;
        $GLOBALS['USER']['b_tech_id'] = 106;
        $GLOBALS['USER']['b_tech_planet'] = $GLOBALS['PLANET']['id'];
        $GLOBALS['USER']['b_tech_queue'] = serialize([
            [106, (int) $GLOBALS['USER']['spy_tech'] + 1, 3600, TIMESTAMP + 3600, $GLOBALS['PLANET']['id']],
            [109, (int) $GLOBALS['USER']['military_tech'] + 1, 3600, TIMESTAMP + 7200, 2147483647],
            [108, (int) $GLOBALS['USER']['computer_tech'] + 1, 3600, TIMESTAMP + 10800, $GLOBALS['PLANET']['id']],
        ]);

        $page = $this->page(ShowResearchPage::class, ['save']);
        $eco = new ResourceUpdate();
        $eco->setData($GLOBALS['USER'], $GLOBALS['PLANET']);
        (new ReflectionProperty(ShowResearchPage::class, 'ecoObj'))->setValue($page, $eco);
        (new ReflectionMethod(ShowResearchPage::class, 'CancelBuildingFromQueue'))->invoke($page);

        $this->assertEquals(108, $GLOBALS['USER']['b_tech_id']);
        $this->assertSame([108], array_column(unserialize($GLOBALS['USER']['b_tech_queue']), 0));
    }

    public function testCancellingAnOrderRecalculatesTheTimesOfLaterOrders(): void
    {
        $GLOBALS['USER']['factor'] = getFactors($GLOBALS['USER']);
        $GLOBALS['USER']['b_tech'] = TIMESTAMP + 3600;
        $GLOBALS['USER']['b_tech_id'] = 106;
        $GLOBALS['USER']['b_tech_planet'] = $GLOBALS['PLANET']['id'];
        $GLOBALS['USER']['b_tech_queue'] = serialize([
            [106, (int) $GLOBALS['USER']['spy_tech'] + 1, 3600, TIMESTAMP + 3600, $GLOBALS['PLANET']['id']],
            [108, (int) $GLOBALS['USER']['computer_tech'] + 1, 3600, TIMESTAMP + 7200, $this->otherPlanet['id']],
            [109, (int) $GLOBALS['USER']['military_tech'] + 1, 3600, TIMESTAMP + 10800, $GLOBALS['PLANET']['id']],
        ]);

        $page = $this->page(ShowResearchPage::class, ['save']);
        $eco = new ResourceUpdate();
        $eco->setData($GLOBALS['USER'], $GLOBALS['PLANET']);
        (new ReflectionProperty(ShowResearchPage::class, 'ecoObj'))->setValue($page, $eco);
        (new ReflectionMethod(ShowResearchPage::class, 'CancelBuildingFromQueue'))->invoke($page);

        $queue = unserialize($GLOBALS['USER']['b_tech_queue']);
        $this->assertSame([108, 109], array_column($queue, 0));
        $this->assertGreaterThan(Config::get()->min_build_time, $queue[1][3] - $queue[0][3]);
    }

    public function testRemovingAnOrderRecalculatesTheTimesOfLaterOrders(): void
    {
        $GLOBALS['USER']['intergalactic_tech'] = 1;
        $GLOBALS['USER']['factor'] = getFactors($GLOBALS['USER']);
        $GLOBALS['USER']['b_tech'] = TIMESTAMP + 3600;
        $GLOBALS['USER']['b_tech_queue'] = serialize([
            [106, (int) $GLOBALS['USER']['spy_tech'] + 1, 3600, TIMESTAMP + 3600, $GLOBALS['PLANET']['id']],
            [108, (int) $GLOBALS['USER']['computer_tech'] + 1, 3600, TIMESTAMP + 7200, $GLOBALS['PLANET']['id']],
            [109, (int) $GLOBALS['USER']['military_tech'] + 1, 3600, TIMESTAMP + 10800, $this->otherPlanet['id']],
        ]);

        $page = $this->page(ShowResearchPage::class);
        (new ReflectionProperty(ShowResearchPage::class, 'ecoObj'))->setValue($page, new ResourceUpdate());
        (new ReflectionMethod(ShowResearchPage::class, 'RemoveBuildingFromQueue'))->invoke($page, 2);

        $queue = unserialize($GLOBALS['USER']['b_tech_queue']);
        $this->assertSame([106, 109], array_column($queue, 0));
        $this->assertGreaterThan(Config::get()->min_build_time, $queue[1][3] - $queue[0][3]);
    }

    private function nextResearch(array $orders): array
    {
        $queue = [];
        $end = TIMESTAMP - 1;
        foreach ($orders as [$element, $level, $planetId]) {
            $queue[] = [$element, $level, 3600, $end, $planetId];
            $end += 3600;
        }
        $user = $GLOBALS['USER'];
        $user['factor'] = getFactors($user);
        $user['b_tech'] = TIMESTAMP - 1;
        $user['b_tech_queue'] = serialize($queue);

        $eco = new ResourceUpdate();
        $eco->setData($user, $GLOBALS['PLANET']);
        $eco->SetNextQueueTechOnTop();

        return $eco->getData()[0];
    }
}
