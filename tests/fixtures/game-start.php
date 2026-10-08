<?php

/**
 * Starts a game request like game.php does, for the session ID given as first argument
 * and an optional planet switch (cp) as second argument, then prints the loaded player and planet.
 * It is sent as an AJAX request, so due fleets are not processed.
 */

$root = dirname(__DIR__, 2) . '/';
define('MODE', 'INGAME');
define('ROOT_PATH', $root);
define('DATABASE_CONFIG_FILE', $root . 'includes/config.test.php');
chdir($root);
set_include_path($root);

$_SERVER['PHP_SELF'] = $root . 'game.php';
$_SERVER['SCRIPT_NAME'] = '/game.php';
$_SERVER['SCRIPT_FILENAME'] = $root . 'game.php';
$_SERVER['DOCUMENT_ROOT'] = $root;
$_SERVER['REQUEST_URI'] = '/game.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'Risistar integration test';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = $_POST = $_REQUEST = ['ajax' => 1] + (isset($argv[2]) ? ['cp' => (int) $argv[2]] : []);
session_id($argv[1]);

ob_start();
register_shutdown_function(static function () {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    echo json_encode([
        'bana' => $GLOBALS['USER']['bana'] ?? null,
        'planet' => $GLOBALS['PLANET']['id'] ?? null,
    ]);
});

require 'includes/pages/game/AbstractGamePage.class.php';
require 'includes/pages/game/ShowErrorPage.class.php';
require 'includes/common.php';

// No page runs here, so commit the request like a page would at its end
GameRequest::finish();
