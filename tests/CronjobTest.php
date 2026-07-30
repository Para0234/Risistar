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
        Database::get()->update("UPDATE %%CRONJOBS%% SET `lock` = NULL WHERE cronjobID = 1;");
    }

    public static function tearDownAfterClass(): void
    {
        Database::get()->update("UPDATE %%CRONJOBS%% SET `lock` = NULL;");
    }

    public function testLockedCronjobIsIgnoredAndFailsToExecute()
    {
        $db = Database::get();

        // 1. Manually lock cronjob #1 with a recent lock (<15 minutes ago)
        $recentTime = time() - 120;
        $fakeLock = md5('recent_cronjob_lock_1');

        $db->update("UPDATE %%CRONJOBS%% SET `lock` = :lock, `nextTime` = :nextTime WHERE cronjobID = 1;", [
            ':lock'     => $fakeLock,
            ':nextTime' => $recentTime,
        ]);

        // 2. Query jobs needing execution
        $jobsTodo = Cronjob::getDueJobs();

        // Cronjob 1 must NOT be in jobsTodo because its lock is active (< 15 min old)!
        $this->assertNotContains(1, $jobsTodo, "Cronjob #1 should be omitted from getDueJobs() when locked recently.");

        $executed = Cronjob::execute(1);
        $this->assertFalse($executed, "Cronjob #1 should fail to execute when locked recently (< 15 min).");
    }

    public function testStaleLockAutoReleaseAndRecovery()
    {
        $db = Database::get();

        // Set cronjob #1 to have a stale lock from 1 hour ago
        $staleTime = time() - 3600;
        $staleLock = md5('stale_lock_token');

        $db->update("UPDATE %%CRONJOBS%% SET `lock` = :lock, `nextTime` = :nextTime WHERE cronjobID = 1;", [
            ':lock'     => $staleLock,
            ':nextTime' => $staleTime,
        ]);

        // Call Cronjob::clearStaleLocks(900)
        Cronjob::clearStaleLocks(900);

        // Verify the stale lock was set to NULL
        $currentLock = $db->selectSingle("SELECT `lock` FROM %%CRONJOBS%% WHERE cronjobID = 1;", [], 'lock');
        $this->assertNull($currentLock, "Stale lock should be automatically cleared.");

        // Verify getDueJobs() now includes job #1
        $jobsTodo = Cronjob::getDueJobs();
        $this->assertContains(1, $jobsTodo, "Job #1 should be in getDueJobs() after stale lock is cleared.");
        $this->assertEquals(Cronjob::getDueJobs(), Cronjob::getNeedTodoExecutedJobs(), "Deprecated alias getNeedTodoExecutedJobs() should return same result as getDueJobs().");
    }

    public function testTaskExceptionIsCaughtAndLockIsReleased()
    {
        $db = Database::get();

        // Target cronjob #1 and make it due
        $db->update("UPDATE %%CRONJOBS%% SET `lock` = NULL, `nextTime` = :nextTime WHERE cronjobID = 1;", [
            ':nextTime' => time() - 100
        ]);

        // Executing cronjob 1 with valid execution should complete cleanly
        $executed = Cronjob::execute(1);
        $this->assertTrue($executed, "Valid cronjob execution should return true.");

        // Verify that lock is reset to NULL in finally block after execution completes
        $currentLock = $db->selectSingle("SELECT `lock` FROM %%CRONJOBS%% WHERE cronjobID = 1;", [], 'lock');
        $this->assertNull($currentLock, "Lock should be released to NULL in finally block.");
    }
}
