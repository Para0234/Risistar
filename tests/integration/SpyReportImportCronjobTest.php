<?php

namespace Risistar\Tests\Integration;

use SpyReportImportCronjob;
use SpyReportMigrator;

class SpyReportImportCronjobTest extends IntegrationTestCase
{
	private const SUBJECT = 'ImportTestSpy';
	private const PLANET = 'ImportTestPlanet';

	private int $ownerId = 0;
	private bool $hadCronjob = false;

	protected function setUp(): void
	{
		parent::setUp();
		$this->requireDatabase();
		require_once self::rootPath() . 'includes/classes/cronjob/SpyReportImportCronjob.class.php';

		$row = self::$db->selectSingle('SELECT id FROM %%USERS%% ORDER BY id ASC LIMIT 1;');
		if (empty($row['id'])) {
			$this->markTestSkipped('No user in test database');
		}
		$this->ownerId = (int) $row['id'];
		$this->hadCronjob = $this->cronjobCount() > 0;
		$this->cleanup();
	}

	protected function tearDown(): void
	{
		if (self::$db !== null && $this->ownerId > 0) {
			$this->cleanup();
			if (!$this->hadCronjob) {
				self::$db->delete('DELETE FROM %%CRONJOBS%% WHERE class = :class;', array(':class' => 'SpyReportImportCronjob'));
			}
		}
		parent::tearDown();
	}

	public function testMigration9CanRunTwice(): void
	{
		$this->runMigration9();
		$this->runMigration9();

		$this->assertSame(1, $this->cronjobCount());
		$this->assertNotEmpty(self::$db->nativeQuery("SHOW TABLES LIKE '" . DB_PREFIX . "spy_reports';"));
	}

	public function testCronjobImportsOldReportsAndThenSwitchesItselfOff(): void
	{
		$this->runMigration9();
		$this->insertSpyMessage(TIMESTAMP - 3600, $this->spyHtml(4, 44, 4, 'La probabilité de la destruction de la/des sonde(s) d\'espionnage est : 37 %'));

		(new SpyReportImportCronjob())->run();

		$report = self::$db->selectSingle(
			'SELECT galaxy, `system`, planet, target_chance, spy_destroyed, viewed FROM %%SPY_REPORTS%% WHERE owner_id = :ownerId AND planet_name = :name;',
			array(':ownerId' => $this->ownerId, ':name' => self::PLANET)
		);
		$this->assertSame(array(4, 44, 4, 37, 0, 1), array_map('intval', array_values($report)));
		$active = self::$db->selectSingle('SELECT isActive FROM %%CRONJOBS%% WHERE class = :class;', array(':class' => 'SpyReportImportCronjob'), 'isActive');
		$this->assertSame(0, (int) $active);
	}

	public function testUnreadableMessageDoesNotStopTheNextBatch(): void
	{
		$broken = $this->insertSpyMessage(TIMESTAMP - 7200, '<div class="spyRaport">no coordinates</div>');
		$good = $this->insertSpyMessage(TIMESTAMP - 3600, $this->spyHtml(5, 55, 5, ''));
		$migrator = new SpyReportMigrator();

		$first = $migrator->migrateBatch($broken - 1, 1);
		$second = $migrator->migrateBatch($first['last_id'], 1);

		$this->assertSame(array('scanned' => 1, 'imported' => 0, 'last_id' => $broken), $first);
		$this->assertSame(array('scanned' => 1, 'imported' => 1, 'last_id' => $good), $second);
	}

	private function runMigration9(): void
	{
		// Same split and prefix as the upgrade in install/index.php.
		$data = file_get_contents(self::rootPath() . 'install/migrations/migration_9.sql');
		foreach (array_filter(explode(";\n", str_replace('%PREFIX%', DB_PREFIX, $data))) as $query) {
			self::$db->nativeQuery(trim($query));
		}
	}

	private function cronjobCount(): int
	{
		return (int) self::$db->selectSingle(
			'SELECT COUNT(*) AS n FROM %%CRONJOBS%% WHERE class = :class;',
			array(':class' => 'SpyReportImportCronjob'),
			'n'
		);
	}

	private function spyHtml(int $galaxy, int $system, int $planet, string $footer): string
	{
		return '<div class="spyRaport"><div class="spyRaportHead"><a href="game.php?page=galaxy&amp;galaxy=' . $galaxy . '&amp;system=' . $system . '">'
			. self::PLANET . ' [' . $galaxy . ':' . $system . ':' . $planet . ']</a></div>'
			. '<div class="spyRaportFooter">' . $footer . '</div></div>';
	}

	private function insertSpyMessage(int $messageTime, string $text): int
	{
		self::$db->insert(
			'INSERT INTO %%MESSAGES%% SET
				message_owner		= :ownerId,
				message_sender		= 0,
				message_time		= :messageTime,
				message_type		= 0,
				message_from		= :from,
				message_subject		= :subject,
				message_text		= :text,
				message_unread		= 0,
				message_universe	= 1;',
			array(
				':ownerId'		=> $this->ownerId,
				':messageTime'	=> $messageTime,
				':from'			=> 'QG',
				':subject'		=> self::SUBJECT,
				':text'			=> $text,
			)
		);
		return (int) self::$db->lastInsertId();
	}

	private function cleanup(): void
	{
		self::$db->delete(
			'DELETE FROM %%SPY_REPORTS%% WHERE owner_id = :ownerId AND planet_name = :name;',
			array(':ownerId' => $this->ownerId, ':name' => self::PLANET)
		);
		self::$db->delete(
			'DELETE FROM %%MESSAGES%% WHERE message_owner = :ownerId AND message_subject = :subject;',
			array(':ownerId' => $this->ownerId, ':subject' => self::SUBJECT)
		);
	}
}
