<?php

/**
 * Seed the isolated integration-test database (2moons_test).
 * Usage: docker exec -w /app risistar-web-1 php tests/bin/seed_test_database.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script can only be run from the command line.\n");
}

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2) . '/');
}
if (!defined('MODE')) {
    define('MODE', 'INSTALL');
}
if (!defined('DATABASE_VERSION')) {
    define('DATABASE_VERSION', 'OLD');
}
if (!defined('DATABASE_CONFIG_FILE')) {
    define('DATABASE_CONFIG_FILE', ROOT_PATH . 'includes/config.test.php');
}

if (!defined('MODE_BOOTSTRAPPED')) {
    define('MODE_BOOTSTRAPPED', true);
    require_once ROOT_PATH . 'includes/common.php';
    require_once ROOT_PATH . 'includes/vars.php';
    require_once ROOT_PATH . 'includes/classes/class.statbuilder.php';
    require_once ROOT_PATH . 'tests/functional/UniverseSeeder.php';
}

UniverseSeeder::seed(true, true);

if (!defined('SEED_TEST_DB_INCLUDED')) {
    echo "Test database seeded (2moons_test).\n";
}
