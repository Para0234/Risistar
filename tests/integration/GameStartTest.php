<?php

namespace Risistar\Tests\Integration;

use ReflectionClass;
use Session;

/**
 * Integration tests for what a game request loads at start (tests/fixtures/game-start.php, in its own process).
 */
class GameStartTest extends IntegrationTestCase
{
    private array $user = [];
    private array $planets = [];
    private string $sessionId = '';

    protected function setUp(): void
    {
        $this->requireDatabase();
        $this->user = self::$db->selectSingle('SELECT * FROM %%USERS%% WHERE authlevel = 0 ORDER BY id LIMIT 1;');
        // The main planet first, so $this->planets[1] is always a colony
        $this->planets = self::$db->select('SELECT * FROM %%PLANETS%% WHERE id_owner = :owner AND planet_type = 1 AND destruyed = 0 ORDER BY id = :main DESC, id;',
            [':owner' => $this->user['id'], ':main' => $this->user['id_planet']]);
        if (count($this->planets) < 2) {
            $this->markTestSkipped('The test database needs a player with two planets.');
        }
    }

    protected function tearDown(): void
    {
        if ($this->sessionId !== '') {
            self::$db->delete('DELETE FROM %%SESSION%% WHERE sessionID = :id;', [':id' => $this->sessionId]);
            @unlink(CACHE_PATH . 'sessions/sess_' . $this->sessionId);
        }
        if ($this->user) {
            self::$db->update('UPDATE %%USERS%% SET bana = :bana, banaday = :banaday, onlinetime = :onlinetime, user_lastip = :ip, user_lastclient = :client WHERE id = :id;', [
                ':bana' => $this->user['bana'], ':banaday' => $this->user['banaday'], ':onlinetime' => $this->user['onlinetime'],
                ':ip' => $this->user['user_lastip'], ':client' => $this->user['user_lastclient'], ':id' => $this->user['id'],
            ]);
        }
        foreach ($this->planets as $planet) {
            self::$db->update('UPDATE %%PLANETS%% SET destruyed = :destruyed WHERE id = :id;', [':destruyed' => $planet['destruyed'], ':id' => $planet['id']]);
        }
    }

    public function testExpiredBanIsLifted(): void
    {
        $this->setBan(TIMESTAMP - 1);

        $this->assertSame(0, (int) $this->startRequest($this->planets[0]['id'])['bana']);
        $this->assertEquals(0, $this->savedUser()['bana']);
    }

    public function testRunningBanIsKept(): void
    {
        $this->setBan(TIMESTAMP + 3600);

        $this->assertSame(1, (int) $this->startRequest($this->planets[0]['id'])['bana']);
        $this->assertEquals(1, $this->savedUser()['bana']);
    }

    public function testAbandonedPlanetIsNotLoadedFromTheSession(): void
    {
        $this->abandon($this->planets[1]['id']);

        $this->assertEquals($this->user['id_planet'], $this->startRequest($this->planets[1]['id'])['planet']);
    }

    public function testAbandonedPlanetCannotBeSelected(): void
    {
        $this->abandon($this->planets[1]['id']);

        $this->assertNotEquals($this->planets[1]['id'], $this->startRequest($this->user['id_planet'], $this->planets[1]['id'])['planet']);
    }

    public function testOtherPlanetCanStillBeSelected(): void
    {
        $this->assertEquals($this->planets[1]['id'], $this->startRequest($this->user['id_planet'], $this->planets[1]['id'])['planet']);
    }

    private function startRequest(int $planetId, ?int $selected = null): array
    {
        $this->sessionId = 'test' . bin2hex(random_bytes(12));
        $session = (new ReflectionClass(Session::class))->newInstanceWithoutConstructor();
        $session->userId = $this->user['id'];
        $session->planetId = $planetId;
        $session->sessionId = $this->sessionId;
        $session->lastActivity = time();
        @mkdir(CACHE_PATH . 'sessions', 0770, true);
        file_put_contents(CACHE_PATH . 'sessions/sess_' . $this->sessionId, 'obj|' . serialize(serialize($session)));
        self::$db->replace('REPLACE INTO %%SESSION%% (sessionID, userID, lastonline, created, userIP) VALUES (:id, :user, :time, :time, :ip);',
            [':id' => $this->sessionId, ':user' => $this->user['id'], ':time' => time(), ':ip' => '127.0.0.1']);

        $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(self::rootPath() . 'tests/fixtures/game-start.php') . ' ' . escapeshellarg($this->sessionId)
            . ($selected !== null ? ' ' . (int) $selected : '');
        $output = shell_exec($command . ' 2>/dev/null');
        $result = json_decode(trim((string) $output), true);
        $this->assertIsArray($result, 'Unexpected output: ' . $output);
        return $result;
    }

    private function setBan(int $until): void
    {
        self::$db->update('UPDATE %%USERS%% SET bana = 1, banaday = :until WHERE id = :id;', [':until' => $until, ':id' => $this->user['id']]);
    }

    private function abandon(int $planetId): void
    {
        self::$db->update('UPDATE %%PLANETS%% SET destruyed = :time WHERE id = :id;', [':time' => TIMESTAMP + 86400, ':id' => $planetId]);
    }

    private function savedUser(): array
    {
        return self::$db->selectSingle('SELECT bana, banaday FROM %%USERS%% WHERE id = :id;', [':id' => $this->user['id']]);
    }
}
