<?php

namespace Risistar\Tests\Integration;

use SpyReportMigrator;
use SpyReportStore;

class SpyReportPurgeTest extends IntegrationTestCase
{
	private int $ownerId = 0;
	private int $messageId = 0;

	protected function setUp(): void
	{
		parent::setUp();
		$this->requireDatabase();

		$row = self::$db->selectSingle('SELECT id FROM %%USERS%% ORDER BY id ASC LIMIT 1;');
		if (empty($row['id'])) {
			$this->markTestSkipped('No user in test database');
		}
		$this->ownerId = (int) $row['id'];
		$this->cleanup();
	}

	protected function tearDown(): void
	{
		if (self::$db !== null && $this->ownerId > 0) {
			$this->cleanup();
		}
		parent::tearDown();
	}

	public function testDeleteOlderThanDoesNotResurrectMigratedReport(): void
	{
		$oldTime = TIMESTAMP - 172800;
		$this->messageId = $this->insertSpyMessage($oldTime);
		SpyReportStore::insert(array(
			'message_id'		=> $this->messageId,
			'owner_id'			=> $this->ownerId,
			'universe'			=> 1,
			'report_time'		=> $oldTime,
			'viewed'			=> 1,
			'galaxy'			=> 9,
			'system'			=> 99,
			'planet'			=> 15,
			'planet_type'		=> 1,
			'planet_name'		=> 'PurgeTestPlanet',
			'payload'			=> array(),
		));

		SpyReportStore::deleteOlderThan($this->ownerId, TIMESTAMP - 86400);
		(new SpyReportMigrator())->migrateBatch($this->messageId - 1, 10);

		$report = self::$db->selectSingle(
			'SELECT report_id FROM %%SPY_REPORTS%% WHERE owner_id = :ownerId AND planet_name = :name;',
			array(
				':ownerId'	=> $this->ownerId,
				':name'		=> 'PurgeTestPlanet',
			)
		);
		$message = self::$db->selectSingle(
			'SELECT message_deleted FROM %%MESSAGES%% WHERE message_id = :messageId;',
			array(':messageId' => $this->messageId)
		);

		$this->assertEmpty($report);
		$this->assertNotNull($message);
		$this->assertNotNull($message['message_deleted']);
	}

	private function insertSpyMessage(int $messageTime): int
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
				':subject'		=> 'PurgeTestSpy',
				':text'			=> '<div class="spyRaport"><div class="spyRaportHead"><a href="game.php?page=galaxy&amp;galaxy=9&amp;system=99">Planète Principale [9:99:15]</a></div></div>',
			)
		);
		return (int) self::$db->lastInsertId();
	}

	private function cleanup(): void
	{
		self::$db->delete(
			'DELETE FROM %%SPY_REPORTS%% WHERE owner_id = :ownerId AND planet_name = :name;',
			array(
				':ownerId'	=> $this->ownerId,
				':name'		=> 'PurgeTestPlanet',
			)
		);
		self::$db->delete(
			'DELETE FROM %%MESSAGES%% WHERE message_owner = :ownerId AND message_subject = :subject;',
			array(
				':ownerId'	=> $this->ownerId,
				':subject'	=> 'PurgeTestSpy',
			)
		);
	}
}
