<?php

/**
 * Parse leftover inbox spy HTML into spy_reports. Does not delete messages.
 * The SpyReportImportCronjob added by migration_9 does the same in the background.
 *
 * Usage: docker exec -w /app risistar-web-1 php scripts/migrate_spy_reports.php [chunk]
 */

// scripts/ is inside the web root: refuse to run from a browser.
if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit;
}

define('MODE', 'TEST');
define('ROOT_PATH', dirname(__DIR__) . '/');

chdir(ROOT_PATH);

require 'includes/common.php';
require 'includes/vars.php';

$limit = isset($argv[1]) ? max(1, (int) $argv[1]) : 100;
$total = 0;
$afterId = 0;
$migrator = new SpyReportMigrator();

do {
	$batch = $migrator->migrateBatch($afterId, $limit);
	$afterId = $batch['last_id'];
	$n = $batch['imported'];
	$total += $n;
	echo "chunk={$n} total={$total}\n";
} while ($batch['scanned'] >= $limit);

echo "done total={$total}\n";
