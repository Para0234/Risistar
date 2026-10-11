<?php

require_once ROOT_PATH . 'includes/classes/cronjob/CronjobTask.interface.php';

/**
 * One-time import of the spy reports that were sent as inbox messages before
 * spy_reports existed. migration_9 adds this job; it switches itself off once a
 * pass finds nothing left, so pages never scan or parse old messages.
 */
class SpyReportImportCronjob implements CronjobTask
{
	private const BATCH = 200;
	private const TIME_BUDGET = 20;

	public function run()
	{
		$migrator = new SpyReportMigrator();
		$deadline = microtime(true) + self::TIME_BUDGET;
		$afterId = 0;
		do {
			$batch = $migrator->migrateBatch($afterId, self::BATCH);
			$afterId = $batch['last_id'];
		} while ($batch['scanned'] === self::BATCH && microtime(true) < $deadline);

		// Out of time: the next run starts over, imported messages are no longer selected.
		if ($batch['scanned'] === self::BATCH) {
			return;
		}

		Database::get()->update('UPDATE %%CRONJOBS%% SET isActive = 0 WHERE class = :class;', array(
			':class' => self::class,
		));
	}
}
