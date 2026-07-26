<?php

namespace Risistar\Tests;

use PHPUnit\Framework\TestCase;
use ResourceUpdate;

class ResourceUpdateTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__) . '/');
        }
        if (!defined('MODE')) {
            define('MODE', 'TEST');
        }
        if (!defined('TIMESTAMP')) {
            define('TIMESTAMP', time());
        }

        require_once ROOT_PATH . 'includes/constants.php';
        require_once ROOT_PATH . 'includes/classes/class.PlanetRessUpdate.php';
    }

    public function testResourceUpdateInstanceCreation()
    {
        $updater = new ResourceUpdate(true, true);
        $this->assertInstanceOf(ResourceUpdate::class, $updater);
    }

    public function testResourceUpdateDataGetSet()
    {
        $updater = new ResourceUpdate();
        $dummyUser = [
            'id' => 1,
            'universe' => 1,
            'urlaubs_modus' => 0,
            'b_tech' => 0,
            'factor' => ['Resource' => 1, 'Energy' => 1]
        ];
        $dummyPlanet = [
            'id' => 1,
            'planet_type' => 1,
            'last_update' => TIMESTAMP - 3600,
            'metal' => 1000,
            'crystal' => 500,
            'deuterium' => 100,
            'metal_perhour' => 1000,
            'crystal_perhour' => 500,
            'deuterium_perhour' => 200,
            'metal_max' => 100000,
            'crystal_max' => 100000,
            'deuterium_max' => 100000,
            'b_building' => 0,
            'b_hangar' => 0
        ];

        $updater->setData($dummyUser, $dummyPlanet);
        list($retUser, $retPlanet) = $updater->getData();

        $this->assertEquals(1, $retUser['id']);
        $this->assertEquals(1000, $retPlanet['metal']);
    }
}
