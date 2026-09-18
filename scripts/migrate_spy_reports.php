<?php

/**
 * Parse leftover inbox spy HTML into spy_reports. Does not delete messages.
 *
 * Usage: docker exec -w /app risistar-web-1 php scripts/migrate_spy_reports.php [chunk]
 */

define('MODE', 'TEST');
define('ROOT_PATH', dirname(__DIR__) . '/');

chdir(ROOT_PATH);

require 'includes/common.php';
require 'includes/vars.php';

$limit = isset($argv[1]) ? max(1, (int) $argv[1]) : 100;
$total = 0;

do {
	$n = (new SpyReportMigrator())->migrateAll($limit);
	$total += $n;
	echo "chunk={$n} total={$total}\n";
} while ($n >= $limit);

echo "done total={$total}\n";
