<?php

namespace Risistar\Tests;

use PHPUnit\Framework\TestCase;
use Database;
use Cronjob;

class CronjobTest extends TestCase
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
        require_once ROOT_PATH . 'includes/classes/Database.class.php';
        require_once ROOT_PATH . 'includes/classes/Cronjob.class.php';
    }

    protected function tearDown(): void
    {
        Database::get()->update("UPDATE %%CRONJOBS%% SET `lock` = NULL, lockedAt = NULL WHERE cronjobID = 1;");
    }

    public static function tearDownAfterClass(): void
    {
        Database::get()->update("UPDATE %%CRONJOBS%% SET `lock` = NULL, lockedAt = NULL;");
    }

    public function testLockedCronjobIsIgnoredAndFailsToExecute()
    {
        $db = Database::get();

        // Recent lock with an old nextTime: reproduces overnight late start.
        // clearStaleLocks must use lockedAt, not nextTime, so this lock stays.
        $oldNextTime = time() - 3600;
        $recentLockedAt = time() - 120;
        $fakeLock = md5('recent_cronjob_lock_1');

        $db->update("UPDATE %%CRONJOBS%% SET `lock` = :lock, lockedAt = :lockedAt, `nextTime` = :nextTime WHERE cronjobID = 1;", [
            ':lock'     => $fakeLock,
            ':lockedAt' => $recentLockedAt,
            ':nextTime' => $oldNextTime,
        ]);

        $jobsTodo = Cronjob::getDueJobs();

        $this->assertNotContains(1, $jobsTodo, "Cronjob #1 should be omitted from getDueJobs() when locked recently.");

        $executed = Cronjob::execute(1);
        $this->assertFalse($executed, "Cronjob #1 should fail to execute when locked recently (< 15 min).");

        $currentLock = $db->selectSingle("SELECT `lock` FROM %%CRONJOBS%% WHERE cronjobID = 1;", [], 'lock');
        $this->assertSame($fakeLock, $currentLock, "Recent lock must not be cleared just because nextTime is old.");
    }

    public function testStaleLockAutoReleaseAndRecovery()
    {
        $db = Database::get();

        $staleTime = time() - 3600;
        $staleLock = md5('stale_lock_token');

        $db->update("UPDATE %%CRONJOBS%% SET `lock` = :lock, lockedAt = :lockedAt, `nextTime` = :nextTime WHERE cronjobID = 1;", [
            ':lock'     => $staleLock,
            ':lockedAt' => $staleTime,
            ':nextTime' => $staleTime,
        ]);

        Cronjob::clearStaleLocks(900);

        $currentLock = $db->selectSingle("SELECT `lock` FROM %%CRONJOBS%% WHERE cronjobID = 1;", [], 'lock');
        $this->assertNull($currentLock, "Stale lock should be automatically cleared.");

        $currentLockedAt = $db->selectSingle("SELECT lockedAt FROM %%CRONJOBS%% WHERE cronjobID = 1;", [], 'lockedAt');
        $this->assertNull($currentLockedAt, "lockedAt should be cleared with the stale lock.");

        $jobsTodo = Cronjob::getDueJobs();
        $this->assertContains(1, $jobsTodo, "Job #1 should be in getDueJobs() after stale lock is cleared.");
        $this->assertEquals(Cronjob::getDueJobs(), Cronjob::getNeedTodoExecutedJobs(), "Deprecated alias getNeedTodoExecutedJobs() should return same result as getDueJobs().");
    }

    public function testOrphanLockWithoutLockedAtIsCleared()
    {
        $db = Database::get();

        $orphanLock = md5('orphan_lock_token');

        $db->update("UPDATE %%CRONJOBS%% SET `lock` = :lock, lockedAt = NULL, `nextTime` = :nextTime WHERE cronjobID = 1;", [
            ':lock'     => $orphanLock,
            ':nextTime' => time() - 100,
        ]);

        Cronjob::clearStaleLocks(900);

        $currentLock = $db->selectSingle("SELECT `lock` FROM %%CRONJOBS%% WHERE cronjobID = 1;", [], 'lock');
        $this->assertNull($currentLock, "Pre-migration / orphan locks with lockedAt NULL should be cleared.");
    }

    public function testTaskExceptionIsCaughtAndLockIsReleased()
    {
        $db = Database::get();

        $db->update("UPDATE %%CRONJOBS%% SET `lock` = NULL, lockedAt = NULL, `nextTime` = :nextTime WHERE cronjobID = 1;", [
            ':nextTime' => time() - 100
        ]);

        $executed = Cronjob::execute(1);
        $this->assertTrue($executed, "Valid cronjob execution should return true.");

        $row = $db->selectSingle("SELECT `lock`, lockedAt FROM %%CRONJOBS%% WHERE cronjobID = 1;");
        $this->assertNull($row['lock'], "Lock should be released to NULL in finally block.");
        $this->assertNull($row['lockedAt'], "lockedAt should be released to NULL in finally block.");
    }

    /**
     * Test demonstrating the exact reproduction of the old buggy behavior (+24h) vs the fixed behavior.
     */
    public function testOldBuggyLogicBehaviorVsFixed()
    {
        $midnightTimestamp = strtotime('2026-08-02 00:19:00');
        $cronTabString = '*/5 * * * *';

        // 1. OLD CODE BEHAVIOR:
        // At 00:19, hour is 0.
        // The old code did: if (!$nhour) { ... }
        // In PHP, !0 evaluates to TRUE, which triggered the fallback error block and added +86400s (+24h).
        $rtimeHour = 0; // Hour 00
        $oldLogicBugTriggered = (!$rtimeHour); // !0 == true!
        $oldCalculatedNextTime = strtotime('2026-08-02 00:00:00') + 86400; // 2026-08-03 00:00:00 (+24h)

        $this->assertTrue($oldLogicBugTriggered, "The old code interpreted hour 0 as falsy (!0 === true).");
        $this->assertEquals(
            strtotime('2026-08-03 00:00:00'),
            $oldCalculatedNextTime,
            "The old code added +24h, shifting execution to the next day."
        );

        // 2. FIXED CODE BEHAVIOR:
        // The fixed code does: if ($nhour === false) { ... }
        // (0 === false) evaluates to FALSE, so hour 0 is preserved as a valid hour.
        $newLogicBugTriggered = ($rtimeHour === false); // 0 === false is FALSE!
        $this->assertFalse($newLogicBugTriggered, "The fixed code no longer triggers the error fallback for hour 0.");

        // 3. VERIFICATION WITH FIXED tdCron ENGINE:
        require_once ROOT_PATH . 'includes/libs/tdcron/class.tdcron.php';
        $actualNextTime = \tdCron::getNextOccurrence($cronTabString, $midnightTimestamp + 60);

        // The next calculated slot is 00:20 on the SAME day (difference of 60 seconds instead of 86400s)
        $expectedNextTimeSameDay = strtotime('2026-08-02 00:20:00');
        $this->assertEquals($expectedNextTimeSameDay, $actualNextTime);
        $this->assertNotEquals($oldCalculatedNextTime, $actualNextTime);
        $this->assertEquals(60, $actualNextTime - $midnightTimestamp, "Delay to next execution should be 60s (00:20) and not 86400s (+24h).");
    }
}
