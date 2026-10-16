<?php

namespace Risistar\Tests\Integration;

use MissionCaseDestruction;

/**
 * Base class for tests where player 0 destroys a moon of player 1. The moon is a planet of player 1 turned into a moon.
 */
abstract class MoonDestructionTestCase extends MissionTestCase
{
    protected array $parentPlanet = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once self::rootPath() . 'includes/classes/class.BuildFunctions.php';
        require_once self::rootPath() . 'includes/classes/class.MissionFunctions.php';
        require_once self::rootPath() . 'includes/classes/missions/Mission.interface.php';
        require_once self::rootPath() . 'includes/classes/class.PlanetRessUpdate.php';
        require_once self::rootPath() . 'includes/classes/missions/MissionCaseDestruction.class.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        // IntegrationTestCase::setUpBeforeClass() loads vars.php inside a function, so its resource entries are not global.
        $GLOBALS['resource'] += [901 => 'metal', 902 => 'crystal', 903 => 'deuterium', 911 => 'energy', 921 => 'darkmatter'];
        $GLOBALS['reslist']['ressources'] = [901, 902, 903, 911, 921];
        $GLOBALS['reslist']['resstype'] = [1 => [901, 902, 903], 2 => [911], 3 => [921]];
        $this->parentPlanet = $this->target;
        $moon = self::$db->selectSingle('SELECT * FROM %%PLANETS%% WHERE id_owner = :owner AND id != :main AND planet_type = :type ORDER BY id LIMIT 1;',
            [':owner' => $this->parentPlanet['id_owner'], ':main' => $this->parentPlanet['id'], ':type' => '1']);
        if (empty($moon)) {
            $this->markTestSkipped('The test database needs a second player with two planets.');
        }
        $this->target = $moon;
        $fields = ['planet_type = :type', 'diameter = 4000', 'metal = 0', 'crystal = 0', 'deuterium = 0', 'last_update = :time'];
        foreach (array_unique(array_merge($GLOBALS['reslist']['fleet'], $GLOBALS['reslist']['defense'])) as $element) {
            $fields[] = $GLOBALS['resource'][$element] . ' = 0';
        }
        self::$db->update('UPDATE %%PLANETS%% SET ' . implode(', ', $fields) . ' WHERE id = :id;',
            [':type' => '3', ':time' => TIMESTAMP, ':id' => $this->target['id']]);
        self::$db->update('UPDATE %%PLANETS%% SET id_luna = :moon WHERE id = :id;',
            [':moon' => $this->target['id'], ':id' => $this->parentPlanet['id']]);
        $this->target = self::$db->selectSingle('SELECT * FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->target['id']]);
        foreach ([214, 233] as $ship) {
            self::$db->update('UPDATE %%PLANETS%% SET ' . $GLOBALS['resource'][$ship] . ' = 1000 WHERE id = :id;',
                [':id' => $GLOBALS['PLANET']['id']]);
        }
    }

    protected function destroyMoon(): array
    {
        $fleet = $this->sendFleet(9, [214 => 500, 233 => 100], TIMESTAMP - 10);
        (new MissionCaseDestruction($fleet))->TargetEvent();
        $this->assertEmpty(self::$db->selectSingle('SELECT id FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->target['id']]));
        return $fleet;
    }
}
