<?php
/**
 * Integration-test database configuration (committed, shareable).
 * Used by PHPUnit integration suite and scripts/seed_test_database.php.
 * Does NOT affect the local game DB (2moons) used by the browser.
 */

$database					= array();
$database['host']			= 'db';
$database['port']			= '3306';
$database['user']			= '2moons';
$database['userpw']			= '2moons';
$database['databasename']	= '2moons_test';
$database['tableprefix']	= 'uni1_';
$salt						= 'zcaSMRfHYhX651lxmD4op/';
