<?php

namespace Risistar\Tests\Unit;

class SpyReportLanguageTest extends UnitTestCase
{
    public function testCounterEspionageTextsExistInFrenchAndEnglish(): void
    {
        foreach (['fr', 'en'] as $language) {
            $LNG = [];
            require self::rootPath() . 'language/' . $language . '/INGAME.php';

            foreach (['spy_destroyed_short', 'spy_counter', 'spy_counter_hint', 'op_active_spy_messages_mode'] as $key) {
                $this->assertNotEmpty($LNG[$key] ?? null, "Missing {$language} text {$key}");
            }
            $this->assertStringContainsString('37', sprintf($LNG['spy_counter'], 37), $language);
        }
    }
}
