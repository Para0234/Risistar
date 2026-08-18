<?php

/**
 * Drop, recreate, import schema, and seed the integration-test database (2moons_test).
 * Does NOT touch the game database (2moons).
 *
 * Usage: docker exec -w /app risistar-web-1 php scripts/reset_test_database.php
 */

define('ROOT_PATH', dirname(__DIR__) . '/');

$database = [];
require ROOT_PATH . 'includes/config.test.php';

$testDb = $database['databasename'];
$prefix = $database['tableprefix'];
$host = $database['host'];
$port = $database['port'];
$user = $database['user'];
$pass = $database['userpw'];

$admin = new PDO(
    "mysql:host={$host};port={$port};charset=utf8",
    $user,
    $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "==> Recreating database {$testDb}...\n";
$admin->exec("DROP DATABASE IF EXISTS `{$testDb}`");
$admin->exec("CREATE DATABASE `{$testDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");

$schemaPath = ROOT_PATH . 'install/install.sql';
if (!is_readable($schemaPath)) {
    fwrite(STDERR, "Cannot read {$schemaPath}\n");
    exit(1);
}

$sql = str_replace('%PREFIX%', $prefix, file_get_contents($schemaPath));
$testDbHandle = new PDO(
    "mysql:host={$host};port={$port};dbname={$testDb};charset=utf8",
    $user,
    $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "==> Importing schema...\n";
foreach (explode(";\n", $sql) as $query) {
    $query = trim($query);
    if ($query === '' || strncmp($query, '--', 2) === 0) {
        continue;
    }
    $testDbHandle->exec($query);
}

echo "==> Seeding test users and planets...\n";
define('SEED_TEST_DB_INCLUDED', true);
require ROOT_PATH . 'scripts/seed_test_database.php';

echo "==> Done. Integration tests use {$testDb}; browser game still uses 2moons.\n";
