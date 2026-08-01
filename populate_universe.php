<?php

/**
 * Risistar - Universe Population & Functional Scenario Generator
 * 
 * Point d'entrée principal pour le peuplement de l'univers et l'injection
 * de scénarios de test fonctionnels (attaques imminentes, AG, destruction de lune, etc.).
 * 
 * Utilisation :
 * - Aide CLI : php populate_universe.php --help
 * - Seeder global : php populate_universe.php [--reset]
 * - Injection de scénario : php populate_universe.php --scenario=incoming_attacks --count=3 --eta=10
 */

define('MODE', 'INSTALL');
define('DATABASE_VERSION', 'OLD');
define('ROOT_PATH', str_replace('\\', '/', dirname(__FILE__)) . '/');
set_include_path(ROOT_PATH);

// Charger l'environnement Risistar
require_once ROOT_PATH . 'includes/common.php';
require_once ROOT_PATH . 'includes/vars.php';
require_once ROOT_PATH . 'includes/classes/class.statbuilder.php';

// Charger le gestionnaire de scénarios fonctionnels
require_once ROOT_PATH . 'tests/functional/ScenarioManager.php';

$isCli = (PHP_SAPI === 'cli');

// Sécurité : Bloquer l'accès Web pour tout utilisateur non-administrateur
if (!$isCli) {
    $session = Session::create();
    $db = Database::get();

    $userAuthLevel = 0;
    if (!empty($session->userId)) {
        $userData = $db->selectSingle("SELECT authlevel FROM %%USERS%% WHERE id = :userId;", [
            ':userId' => $session->userId
        ]);
        if (!empty($userData)) {
            $userAuthLevel = (int) $userData['authlevel'];
        }
    }

    if ($userAuthLevel < AUTH_ADM) {
        HTTP::sendHeader('HTTP/1.1 403 Forbidden');
        echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>403 Accès Refusé</title></head><body style="background:#0f172a;color:#f8fafc;font-family:sans-serif;padding:50px;text-align:center;">';
        echo '<h1 style="color:#ef4444;">403 Accès Refusé</h1>';
        echo '<p>Seuls les administrateurs connectés ou la ligne de commande (CLI) peuvent exécuter ce script.</p>';
        echo '</body></html>';
        exit;
    }
}

// Transmettre la requête au gestionnaire de scénarios
$args = $isCli ? ($argv ?? []) : array_merge($_GET, $_POST);
ScenarioManager::handleRequest($args, $isCli);
