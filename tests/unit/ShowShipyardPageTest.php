<?php

namespace Risistar\Tests\Unit;

class ShowShipyardPageTest extends UnitTestCase
{
    /**
     * redirectTo() exits, so a queue change sent by POST must be counted before it.
     */
    public function testQueueTelemetryIsRecordedBeforeThePostRedirect(): void
    {
        $source = file_get_contents(self::rootPath() . 'includes/pages/game/ShowShipyardPage.class.php');
        $action = strpos($source, "PlayerTelemetry::action('queue.shipyard')");
        $redirect = strpos($source, "\$this->redirectTo('game.php?page=shipyard&mode='");

        $this->assertNotFalse($action);
        $this->assertNotFalse($redirect);
        $this->assertLessThan($redirect, $action);
    }
}
