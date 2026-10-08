<?php

namespace Risistar\Tests\Integration;

use FleetFunctions;

/**
 * Base class for tests that send fleets directly with FleetFunctions::sendFleet(), from player 0 to player 1.
 */
abstract class MissionTestCase extends GamePageTestCase
{
    protected array $target = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->target = self::$db->selectSingle('SELECT * FROM %%PLANETS%% WHERE id = :id;',
            [':id' => $this->players(2)[1]['id_planet']]);
    }

    protected function sendFleet(int $mission, array $ships, int $arrival): array
    {
        $start = $GLOBALS['PLANET'];
        FleetFunctions::sendFleet($ships, $mission, $GLOBALS['USER']['id'], $start['id'],
            $start['galaxy'], $start['system'], $start['planet'], $start['planet_type'],
            $this->target['id_owner'], $this->target['id'], $this->target['galaxy'], $this->target['system'],
            $this->target['planet'], $this->target['planet_type'], [901 => 0, 902 => 0, 903 => 0],
            $arrival, $arrival, TIMESTAMP + 1000);
        return self::$db->selectSingle('SELECT * FROM %%FLEETS%% WHERE fleet_owner = :owner ORDER BY fleet_id DESC LIMIT 1;',
            [':owner' => $GLOBALS['USER']['id']]);
    }
}
