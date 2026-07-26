<?php

/**
 * Risistar - Universe Seeder & Population Script
 * 
 * Ce script peuple le monde de Risistar avec des joueurs, des planètes, des colonies,
 * des lunes, des flottes stationnées, des défenses, des alliances, des flottes en vol
 * et des champs de débris.
 *
 * Utilisation :
 * - Via Navigateur : Accéder à http://localhost:8080/populate_universe.php (ou votre URL locale)
 * - Via CLI : php populate_universe.php [--reset] [--confirm]
 */

define('MODE', 'INSTALL');
define('DATABASE_VERSION', 'OLD');
define('ROOT_PATH', str_replace('\\', '/', dirname(__FILE__)) . '/');
set_include_path(ROOT_PATH);

// Charger l'environnement Risistar
require_once ROOT_PATH . 'includes/common.php';
require_once ROOT_PATH . 'includes/vars.php';
require_once ROOT_PATH . 'includes/classes/class.statbuilder.php';

$isCli = (PHP_SAPI === 'cli');

// Sécurité : Bloquer l'accès Web pour tout utilisateur non-administrateur
if (!$isCli) {
    $session = Session::create();
    $db = Database::get();
    
    $userAuthLevel = 0;
    if (!empty($session->userId)) {
        $userData = $db->selectSingle("SELECT authlevel FROM %%USERS%% WHERE id = :userId;", array(
            ':userId' => $session->userId
        ));
        if (!empty($userData)) {
            $userAuthLevel = (int) $userData['authlevel'];
        }
    }

    // Seuls les administrateurs (AUTH_ADM = 3) peuvent exécuter ce script via le Web
    if ($userAuthLevel < AUTH_ADM) {
        HTTP::sendHeader('HTTP/1.1 403 Forbidden');
        echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>403 Accès Refusé</title></head><body style="background:#0f172a;color:#f8fafc;font-family:sans-serif;padding:50px;text-align:center;">';
        echo '<h1 style="color:#ef4444;">403 Accès Refusé</h1>';
        echo '<p>Seuls les administrateurs connectés ou la ligne de commande (CLI) peuvent exécuter ce script.</p>';
        echo '</body></html>';
        exit;
    }
}

$confirm = $isCli ? true : (isset($_GET['confirm']) && $_GET['confirm'] == '1');
$doReset = $isCli ? (isset($argv) && (in_array('--reset', $argv) || in_array('reset', $argv))) : (isset($_GET['reset']) && $_GET['reset'] == '1');

// Formattage web / CLI
function outputHeader($isCli) {
    if (!$isCli) {
        echo '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Risistar - Peuplement de l\'Univers</title>
    <style>
        body { font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 30px; }
        .container { max-width: 900px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); border: 1px solid #334155; }
        h1 { color: #38bdf8; margin-top: 0; display: flex; align-items: center; gap: 10px; }
        .btn { display: inline-block; padding: 12px 24px; background: #0284c7; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; margin-right: 10px; transition: background 0.2s; border: none; cursor: pointer; }
        .btn:hover { background: #0369a1; }
        .btn-danger { background: #dc2626; }
        .btn-danger:hover { background: #b91c1c; }
        .log-entry { padding: 8px 12px; border-bottom: 1px solid #334155; font-family: monospace; font-size: 14px; }
        .log-success { color: #4ade80; }
        .log-info { color: #38bdf8; }
        .log-warn { color: #fbbf24; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-card { background: #0f172a; padding: 15px; border-radius: 8px; border: 1px solid #334155; text-align: center; }
        .stat-value { font-size: 28px; font-weight: bold; color: #38bdf8; }
        .stat-label { color: #94a3b8; font-size: 13px; text-transform: uppercase; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #334155; text-align: left; }
        th { background: #0f172a; color: #38bdf8; }
    </style>
</head>
<body>
<div class="container">';
    }
}

function outputFooter($isCli) {
    if (!$isCli) {
        echo '</div></body></html>';
    }
}

function logMessage($msg, $type = 'info') {
    global $isCli;
    if ($isCli) {
        $prefix = ($type === 'success') ? '[SUCCESS] ' : (($type === 'warn') ? '[WARN] ' : '[INFO] ');
        echo $prefix . strip_tags($msg) . "\n";
    } else {
        echo '<div class="log-entry log-' . $type . '">' . $msg . '</div>';
        flush();
    }
}

// Mode d'attente / confirmation web
if (!$confirm) {
    outputHeader($isCli);
    echo '<h1>🚀 Peuplement de l\'Univers Risistar</h1>';
    echo '<p>Ce script va générer automatiquement un univers vivant avec :</p>';
    echo '<ul>
        <li><b>20 Joueurs</b> (Empereurs, Amiraux, Corsaires, Débutants) avec mot de passe <code>password123</code></li>
        <li><b>4 Alliances</b> (Imperium Galactique, Alliance des Etoiles, Pirates du Cosmos, Risistar Vanguard)</li>
        <li><b>Des dizaines de planètes & colonies</b> avec mines, usines, laboratoires et réserves de ressources</li>
        <li><b>Des Lunes</b> équipées de bases lunaires, phalanges et portes de saut</li>
        <li><b>Des flottes massives & défenses</b> stationnées sur les planètes et lunes</li>
        <li><b>12+ flottes actuellement en vol</b> (Attaques, Transports, Espionnages, Recyclages, Expéditions)</li>
        <li><b>Champs de débris (TF)</b> exploitables dans la galaxie</li>
        <li><b>Mise à jour automatique des classements & statistiques</b></li>
    </ul>';

    echo '<div style="margin-top: 30px;">';
    echo '<a href="populate_universe.php?confirm=1" class="btn">🚀 Lancer le Peuplement (Ajouter)</a>';
    echo '<a href="populate_universe.php?confirm=1&reset=1" class="btn btn-danger" onclick="return confirm(\'Êtes-vous sûr de vouloir réinitialiser l\\\'univers (supprimer les comptes non-admin) ?\');">⚠️ Réinitialiser & Peupler</a>';
    echo '<a href="game.php" class="btn" style="background:#475569;">Retour au Jeu</a>';
    echo '</div>';

    outputFooter($isCli);
    exit;
}

// Début du traitement
outputHeader($isCli);
logMessage("🚀 Initialisation du peuplement de l'univers...", 'info', $isCli);

$db = Database::get();
$universe = ROOT_UNI;
$config = Config::get($universe);

// Option de réinitialisation de la BDD (conserve les comptes ROOT/ADMIN)
if ($doReset) {
    logMessage("🧹 Nettoyage des données existantes (hors superadmins)...", 'warn', $isCli);
    
    // Récupérer les IDs des admins à préserver
    $adminIds = $db->select("SELECT id FROM %%USERS%% WHERE authlevel >= 3;");
    $preserveIds = array_column($adminIds, 'id');
    if (!in_array(ROOT_USER, $preserveIds)) {
        $preserveIds[] = ROOT_USER;
    }
    
    $preserveSql = implode(',', array_map('intval', $preserveIds));

    // Supprimer les utilisateurs non-admins
    $db->delete("DELETE FROM %%USERS%% WHERE id NOT IN ({$preserveSql});");
    $db->delete("DELETE FROM %%PLANETS%% WHERE id_owner NOT IN ({$preserveSql});");
    $db->delete("DELETE FROM %%FLEETS%%;");
    $db->delete("DELETE FROM %%FLEETS_EVENT%%;");
    $db->delete("DELETE FROM %%LOG_FLEETS%%;");
    $db->delete("DELETE FROM %%ALLIANCE%%;");
    $db->delete("DELETE FROM %%ALLIANCE_RANK%%;");
    $db->delete("DELETE FROM %%ALLIANCE_REQUEST%%;");
    $db->delete("DELETE FROM %%BUDDY%%;");
    $db->delete("DELETE FROM %%NOTES%%;");
    $db->delete("DELETE FROM %%MESSAGES%%;");
    $db->delete("DELETE FROM %%STATPOINTS%%;");

    logMessage("✅ Réinitialisation terminée.", 'success', $isCli);
}

// 1. Définition des Alliances
$alliancesData = [
    [
        'name' => 'Imperium Galactique',
        'tag' => 'IMP',
        'desc' => 'Domination, ordre et discipline à travers tout l\'univers Risistar.',
    ],
    [
        'name' => 'Alliance des Etoiles',
        'tag' => 'ADE',
        'desc' => 'Union d\'explorateurs et de scientifiques unis pour la paix spatiale.',
    ],
    [
        'name' => 'Pirates du Cosmos',
        'tag' => 'PIR',
        'desc' => 'Liberté totale, attaques surprises et pillages de flottes.',
    ],
    [
        'name' => 'Risistar Vanguard',
        'tag' => 'RIS',
        'desc' => 'La force industrielle et technologique dominante.',
    ],
];

// 2. Définition des Profils de Joueurs à générer
$playersConfig = [
    // COMPTE ADMINISTRATEUR / JOUEUR PRINCIPAL PRÊT À L'EMPLOI
    [
        'name' => 'Admin',
        'tier' => 1,
        'authlevel' => 3, // Admin
        'ally_index' => 0, // IMP
        'colonies_count' => 3,
        'moons_count' => 2,
        'darkmatter' => 1000000,
    ],
    [
        'name' => 'PlayerOne',
        'tier' => 1,
        'authlevel' => 0, // Joueur standard top niveau
        'ally_index' => 1, // ADE
        'colonies_count' => 3,
        'moons_count' => 2,
        'darkmatter' => 750000,
    ],

    // TIER 1 : TOP EMPEREURS / AMIRAUX (Fortes flottes, recherches élevées, colonies + lunes)
    [
        'name' => 'LordVader',
        'tier' => 1,
        'authlevel' => 0,
        'ally_index' => 0, // IMP
        'colonies_count' => 3,
        'moons_count' => 2,
        'darkmatter' => 500000,
    ],
    [
        'name' => 'Starlight',
        'tier' => 1,
        'ally_index' => 1, // ADE
        'colonies_count' => 3,
        'moons_count' => 2,
        'darkmatter' => 500000,
    ],
    [
        'name' => 'CyberCommander',
        'tier' => 1,
        'ally_index' => 2, // PIR
        'colonies_count' => 2,
        'moons_count' => 1,
        'darkmatter' => 450000,
    ],
    [
        'name' => 'Hyperion',
        'tier' => 1,
        'ally_index' => 3, // RIS
        'colonies_count' => 3,
        'moons_count' => 1,
        'darkmatter' => 400000,
    ],
    [
        'name' => 'NovaEmpress',
        'tier' => 1,
        'ally_index' => 0, // IMP
        'colonies_count' => 2,
        'moons_count' => 1,
        'darkmatter' => 350000,
    ],

    // TIER 2 : JOUEURS INTERMÉDIAIRES (Moyennes flottes, 1-2 colonies)
    [
        'name' => 'Astraea',
        'tier' => 2,
        'ally_index' => 1, // ADE
        'colonies_count' => 2,
        'moons_count' => 1,
        'darkmatter' => 200000,
    ],
    [
        'name' => 'Vortex',
        'tier' => 2,
        'ally_index' => 2, // PIR
        'colonies_count' => 1,
        'moons_count' => 1,
        'darkmatter' => 180000,
    ],
    [
        'name' => 'Apollo',
        'tier' => 2,
        'ally_index' => 3, // RIS
        'colonies_count' => 2,
        'moons_count' => 0,
        'darkmatter' => 150000,
    ],
    [
        'name' => 'OrionRider',
        'tier' => 2,
        'ally_index' => 0, // IMP
        'colonies_count' => 1,
        'moons_count' => 0,
        'darkmatter' => 120000,
    ],
    [
        'name' => 'SolarWind',
        'tier' => 2,
        'ally_index' => 1, // ADE
        'colonies_count' => 1,
        'moons_count' => 0,
        'darkmatter' => 100000,
    ],
    [
        'name' => 'NebulaSeeker',
        'tier' => 2,
        'ally_index' => 2, // PIR
        'colonies_count' => 1,
        'moons_count' => 0,
        'darkmatter' => 90000,
    ],
    [
        'name' => 'AsteroidMiner',
        'tier' => 2,
        'ally_index' => 3, // RIS
        'colonies_count' => 1,
        'moons_count' => 0,
        'darkmatter' => 80000,
    ],
    [
        'name' => 'Zeus',
        'tier' => 2,
        'ally_index' => 0, // IMP
        'colonies_count' => 1,
        'moons_count' => 0,
        'darkmatter' => 75000,
    ],

    // TIER 3 : DÉBUTANTS / ROOKIES (1 planète principale)
    [
        'name' => 'CosmicPirate',
        'tier' => 3,
        'ally_index' => 2, // PIR
        'colonies_count' => 0,
        'moons_count' => 0,
        'darkmatter' => 25000,
    ],
    [
        'name' => 'StarCadet',
        'tier' => 3,
        'ally_index' => 1, // ADE
        'colonies_count' => 0,
        'moons_count' => 0,
        'darkmatter' => 20000,
    ],
    [
        'name' => 'RookiePilot',
        'tier' => 3,
        'ally_index' => null, // Indépendant
        'colonies_count' => 0,
        'moons_count' => 0,
        'darkmatter' => 15000,
    ],
    [
        'name' => 'GalacticScout',
        'tier' => 3,
        'ally_index' => null,
        'colonies_count' => 0,
        'moons_count' => 0,
        'darkmatter' => 10000,
    ],
    [
        'name' => 'SpaceNoob',
        'tier' => 3,
        'ally_index' => null,
        'colonies_count' => 0,
        'moons_count' => 0,
        'darkmatter' => 10000,
    ],
    [
        'name' => 'Explorer99',
        'tier' => 3,
        'ally_index' => null,
        'colonies_count' => 0,
        'moons_count' => 0,
        'darkmatter' => 10000,
    ],
    [
        'name' => 'AstroBoy',
        'tier' => 3,
        'ally_index' => null,
        'colonies_count' => 0,
        'moons_count' => 0,
        'darkmatter' => 10000,
    ],
];

// Helper pour trouver des coordonnées libres
function getFreeCoords($universe, $maxGalaxy = 5, $maxSystem = 100) {
    do {
        $g = mt_rand(1, min(3, $maxGalaxy));
        $s = mt_rand(1, min(80, $maxSystem));
        $p = mt_rand(1, 12);
    } while (!PlayerUtil::isPositionFree($universe, $g, $s, $p, 1));

    return [$g, $s, $p];
}

// 3. Création des Alliances
logMessage("🚩 Création des alliances...", 'info', $isCli);
$createdAlliances = [];
foreach ($alliancesData as $idx => $allyData) {
    $sql = "INSERT INTO %%ALLIANCE%% SET
        ally_name = :name,
        ally_tag = :tag,
        ally_owner = 0,
        ally_register_time = :time,
        ally_description = :desc,
        ally_members = 0,
        ally_universe = :universe;";

    $db->insert($sql, [
        ':name' => $allyData['name'],
        ':tag' => $allyData['tag'],
        ':time' => TIMESTAMP,
        ':desc' => $allyData['desc'],
        ':universe' => $universe,
    ]);
    $createdAlliances[$idx] = $db->lastInsertId();
    logMessage("  Alliance '{$allyData['name']}' [{$allyData['tag']}] créée (ID: {$createdAlliances[$idx]}).", 'info', $isCli);
}

// 4. Création des Joueurs et des Planètes
logMessage("👥 Création des joueurs, planètes et lunes...", 'info', $isCli);
$createdPlayers = [];
$defaultPasswordHashed = PlayerUtil::cryptPassword('password123');

foreach ($playersConfig as $pConfig) {
    list($g, $s, $p) = getFreeCoords($universe, $config->max_galaxy, $config->max_system);
    $email = strtolower($pConfig['name']) . '@risistar.local';

    // Vérifier si le joueur existe déjà
    $existingUser = $db->selectSingle("SELECT id FROM %%USERS%% WHERE username = :name;", [':name' => $pConfig['name']]);
    $authLevel = isset($pConfig['authlevel']) ? $pConfig['authlevel'] : 0;
    if (!empty($existingUser)) {
        logMessage("  Le joueur '{$pConfig['name']}' existe déjà (ID: {$existingUser['id']}).", 'warn', $isCli);
        $userId = $existingUser['id'];
        $db->update("UPDATE %%USERS%% SET password = :password, authlevel = :authlevel WHERE id = :userId;", [
            ':password' => $defaultPasswordHashed,
            ':authlevel' => $authLevel,
            ':userId' => $userId,
        ]);
        $mainPlanet = $db->selectSingle("SELECT id, galaxy, system, planet FROM %%PLANETS%% WHERE id_owner = :userId AND planet_type = 1 ORDER BY id ASC LIMIT 1;", [':userId' => $userId]);
        $planetId = $mainPlanet['id'];
    } else {
        list($userId, $planetId) = PlayerUtil::createPlayer(
            $universe,
            $pConfig['name'],
            $defaultPasswordHashed,
            $email,
            'fr',
            $g,
            $s,
            $p,
            'Planète Principale',
            $authLevel
        );
        logMessage("  Joueur '{$pConfig['name']}' créé à {$g}:{$s}:{$p} (User ID: {$userId}, Planet ID: {$planetId}).", 'success', $isCli);
    }

    // Rattachement à une alliance
    $allyId = 0;
    if ($pConfig['ally_index'] !== null && isset($createdAlliances[$pConfig['ally_index']])) {
        $allyId = $createdAlliances[$pConfig['ally_index']];
        $db->update("UPDATE %%USERS%% SET ally_id = :allyId WHERE id = :userId;", [
            ':allyId' => $allyId,
            ':userId' => $userId,
        ]);
        // Définir comme propriétaire de l'alliance si premier membre
        $allyOwner = $db->selectSingle("SELECT ally_owner FROM %%ALLIANCE%% WHERE id = :allyId;", [':allyId' => $allyId], 'ally_owner');
        if ($allyOwner == 0) {
            $db->update("UPDATE %%ALLIANCE%% SET ally_owner = :ownerId WHERE id = :allyId;", [
                ':ownerId' => $userId,
                ':allyId' => $allyId,
            ]);
        }
    }

    // Configurer les technologies du joueur dans %%USERS%%
    $tier = $pConfig['tier'];
    $techLevels = [];
    if ($tier == 1) {
        $techLevels = [
            106 => 12, // Spy
            108 => 12, // Computer
            109 => 15, // Weapons
            110 => 14, // Shields
            111 => 15, // Armor
            113 => 14, // Energy
            114 => 10, // Hyperspace
            115 => 14, // Combustion
            117 => 12, // Impulse
            118 => 10, // Hyperspace drive
            120 => 14, // Laser
            121 => 10, // Ion
            122 => 10, // Plasma
            123 => 3,  // IRN
            124 => 15, // Astrophysics
            199 => 1,  // Graviton
        ];
    } elseif ($tier == 2) {
        $techLevels = [
            106 => 8,
            108 => 9,
            109 => 10,
            110 => 9,
            111 => 10,
            113 => 10,
            114 => 6,
            115 => 10,
            117 => 8,
            118 => 6,
            120 => 10,
            121 => 6,
            122 => 5,
            124 => 9,
        ];
    } else {
        $techLevels = [
            106 => 4,
            108 => 5,
            109 => 5,
            110 => 5,
            111 => 5,
            113 => 6,
            115 => 6,
            117 => 4,
            120 => 6,
            124 => 3,
        ];
    }

    $userUpdates = ['darkmatter = :darkmatter'];
    $userParams = [':darkmatter' => $pConfig['darkmatter'], ':userId' => $userId];
    foreach ($techLevels as $techId => $lvl) {
        if (isset($resource[$techId])) {
            $col = $resource[$techId];
            $userUpdates[] = "`{$col}` = :tech_{$techId}";
            $userParams[":tech_{$techId}"] = $lvl;
        }
    }
    $db->update("UPDATE %%USERS%% SET " . implode(', ', $userUpdates) . " WHERE id = :userId;", $userParams);

    // Planètes du joueur
    $playerPlanets = [];

    // Récupérer la planète principale créée
    $mainPlanetData = $db->selectSingle("SELECT id, galaxy, system, planet FROM %%PLANETS%% WHERE id = :planetId;", [':planetId' => $planetId]);
    $playerPlanets[] = [
        'id' => $planetId,
        'galaxy' => $mainPlanetData['galaxy'],
        'system' => $mainPlanetData['system'],
        'planet' => $mainPlanetData['planet'],
        'isHome' => true,
    ];

    // Créer des colonies supplémentaires selon le profil
    for ($c = 0; $c < $pConfig['colonies_count']; $c++) {
        list($cg, $cs, $cp) = getFreeCoords($universe, $config->max_galaxy, $config->max_system);
        try {
            $colonyId = PlayerUtil::createPlanet($cg, $cs, $cp, $universe, $userId, 'Colonie ' . ($c + 1), false, 0);
            $playerPlanets[] = [
                'id' => $colonyId,
                'galaxy' => $cg,
                'system' => $cs,
                'planet' => $cp,
                'isHome' => false,
            ];
            logMessage("    Colonie créée à {$cg}:{$cs}:{$cp} (Planet ID: {$colonyId}).", 'info', $isCli);
        } catch (Exception $e) {
            // Ignorer si la position est prise
        }
    }

    // Configurer chaque planète (Bâtiments, Ressources, Vaisseaux, Défenses)
    $moonsCreatedCount = 0;
    foreach ($playerPlanets as $pIdx => $pInfo) {
        $pId = $pInfo['id'];
        
        // Niveaux selon Tier
        if ($tier == 1) {
            $resources = ['metal' => 2000000, 'crystal' => 1000000, 'deuterium' => 500000];
            $elements = [
                // Bâtiments
                1 => 32, 2 => 28, 3 => 25, 4 => 28, 12 => 8, 14 => 10, 15 => 4, 21 => 12, 22 => 10, 23 => 8, 24 => 8, 31 => 12,
                // Vaisseaux
                202 => 800, 203 => 400, 204 => 3000, 205 => 1000, 206 => 500, 207 => 250, 208 => 5, 209 => 200, 210 => 150, 211 => 50, 212 => 100, 213 => 40, 214 => ($pInfo['isHome'] ? 2 : 0), 215 => 100, 219 => 5, 223 => 30000,
                // Défenses
                401 => 3000, 402 => 1500, 403 => 400, 404 => 100, 405 => 80, 406 => 30, 407 => 1, 408 => 1, 409 => 40, 410 => 15,
            ];
        } elseif ($tier == 2) {
            $resources = ['metal' => 500000, 'crystal' => 250000, 'deuterium' => 100000];
            $elements = [
                // Bâtiments
                1 => 24, 2 => 20, 3 => 18, 4 => 22, 14 => 6, 15 => 2, 21 => 8, 22 => 7, 23 => 6, 24 => 5, 31 => 8,
                // Vaisseaux
                202 => 200, 203 => 100, 204 => 600, 205 => 200, 206 => 100, 207 => 40, 208 => 2, 209 => 50, 210 => 50, 212 => 50, 215 => 20,
                // Défenses
                401 => 800, 402 => 400, 403 => 100, 404 => 25, 405 => 20, 406 => 5, 407 => 1, 408 => 1, 409 => 15,
            ];
        } else {
            $resources = ['metal' => 50000, 'crystal' => 25000, 'deuterium' => 10000];
            $elements = [
                // Bâtiments
                1 => 15, 2 => 12, 3 => 10, 4 => 14, 14 => 4, 21 => 4, 22 => 4, 23 => 3, 24 => 2, 31 => 4,
                // Vaisseaux
                202 => 30, 203 => 10, 204 => 80, 205 => 20, 209 => 10, 210 => 15, 212 => 20,
                // Défenses
                401 => 150, 402 => 60, 403 => 10, 407 => 1,
            ];
        }

        $planetUpdates = [
            '`metal` = :metal',
            '`crystal` = :crystal',
            '`deuterium` = :deuterium',
        ];
        $planetParams = [
            ':metal' => $resources['metal'],
            ':crystal' => $resources['crystal'],
            ':deuterium' => $resources['deuterium'],
            ':planetId' => $pId,
        ];

        foreach ($elements as $elemId => $val) {
            if (isset($resource[$elemId])) {
                $col = $resource[$elemId];
                $planetUpdates[] = "`{$col}` = :elem_{$elemId}";
                $planetParams[":elem_{$elemId}"] = $val;
            }
        }

        $db->update("UPDATE %%PLANETS%% SET " . implode(', ', $planetUpdates) . " WHERE id = :planetId;", $planetParams);

        // Créer une lune si configuré
        if ($moonsCreatedCount < $pConfig['moons_count']) {
            try {
                $moonId = PlayerUtil::createMoon($universe, $pInfo['galaxy'], $pInfo['system'], $pInfo['planet'], $userId, 20, 8944, 'Lune de ' . $pConfig['name']);
                if ($moonId !== false) {
                    $moonsCreatedCount++;
                    // Équiper la lune
                    $moonElements = [
                        41 => 4, // Base lunaire
                        42 => 3, // Phalange de capteur
                        43 => 1, // Porte de saut
                        204 => 500, 207 => 100, 213 => 20, 214 => 1, 210 => 50,
                        401 => 1000, 402 => 500, 406 => 20, 408 => 1,
                    ];
                    $moonUpdates = [];
                    $moonParams = [':moonId' => $moonId];
                    foreach ($moonElements as $elemId => $val) {
                        if (isset($resource[$elemId])) {
                            $col = $resource[$elemId];
                            $moonUpdates[] = "`{$col}` = :m_elem_{$elemId}";
                            $moonParams[":m_elem_{$elemId}"] = $val;
                        }
                    }
                    if (!empty($moonUpdates)) {
                        $db->update("UPDATE %%PLANETS%% SET " . implode(', ', $moonUpdates) . " WHERE id = :moonId;", $moonParams);
                    }
                    logMessage("    🌕 Lune créée sur {$pInfo['galaxy']}:{$pInfo['system']}:{$pInfo['planet']} (Moon ID: {$moonId}).", 'info');
                }
            } catch (Exception $e) {
                // Lune déjà existante ou erreur ignorée
            }
        }
    }

    $createdPlayers[] = [
        'id' => $userId,
        'name' => $pConfig['name'],
        'tier' => $tier,
        'planets' => $playerPlanets,
    ];
}

// Mettre à jour le nombre de membres par alliance
foreach ($createdAlliances as $aId) {
    $mCount = $db->selectSingle("SELECT COUNT(*) as count FROM %%USERS%% WHERE ally_id = :aId;", [':aId' => $aId], 'count');
    $db->update("UPDATE %%ALLIANCE%% SET ally_members = :mCount WHERE id = :aId;", [':mCount' => $mCount, ':aId' => $aId]);
}

// 5. Génération de Mouvements de Flotte en Vol (Missions vivantes)
logMessage("🛸 Lancement de flottes en vol (Missions en cours)...", 'info', $isCli);

// Récupérer toutes les planètes des joueurs créés
$allPlanets = $db->select("SELECT id, id_owner, galaxy, system, planet, planet_type FROM %%PLANETS%% WHERE id_owner > 0 AND planet_type = 1;");
$planetCount = count($allPlanets);

if ($planetCount >= 2) {
    $missions = [
        ['type' => 1, 'name' => 'Attaque'],
        ['type' => 3, 'name' => 'Transport'],
        ['type' => 4, 'name' => 'Stationnement'],
        ['type' => 6, 'name' => 'Espionnage'],
        ['type' => 8, 'name' => 'Recyclage'],
        ['type' => 15, 'name' => 'Expédition'],
    ];

    $flyingFleetCount = 0;
    for ($f = 0; $f < 14; $f++) {
        $startP = $allPlanets[$f % $planetCount];
        $targetP = $allPlanets[($f + 3) % $planetCount];

        if ($startP['id_owner'] == $targetP['id_owner'] && $f % 2 == 0) {
            $missionType = 4; // Stationnement
        } else {
            $missionType = [1, 3, 6, 8, 15][$f % 5];
        }

        $ships = [];
        if ($missionType == 6) {
            $ships = [210 => mt_rand(5, 30)]; // Sondes d'espionnage
        } elseif ($missionType == 3 || $missionType == 4) {
            $ships = [202 => mt_rand(20, 100), 203 => mt_rand(10, 50)];
        } elseif ($missionType == 8) {
            $ships = [209 => mt_rand(15, 60)];
        } elseif ($missionType == 15) {
            $ships = [202 => 30, 204 => 50, 206 => 10, 210 => 5];
        } else {
            // Attaque
            $ships = [204 => mt_rand(100, 500), 205 => mt_rand(30, 150), 206 => mt_rand(20, 80)];
        }

        $now = TIMESTAMP;
        $flightDuration = mt_rand(300, 2400); // 5 minutes à 40 minutes
        $startTime = $now + $flightDuration;
        $stayTime = $startTime + ($missionType == 15 ? 3600 : 0);
        $endTime = $stayTime + $flightDuration;

        $resources = [
            901 => ($missionType == 3 ? mt_rand(50000, 200000) : 0),
            902 => ($missionType == 3 ? mt_rand(30000, 100000) : 0),
            903 => ($missionType == 3 ? mt_rand(10000, 50000) : 0),
        ];

        try {
            FleetFunctions::sendFleet(
                $ships,
                $missionType,
                $startP['id_owner'],
                $startP['id'],
                $startP['galaxy'],
                $startP['system'],
                $startP['planet'],
                $startP['planet_type'],
                $targetP['id_owner'],
                $targetP['id'],
                $targetP['galaxy'],
                $targetP['system'],
                $targetP['planet'],
                $targetP['planet_type'],
                $resources,
                $startTime,
                $stayTime,
                $endTime,
                0
            );
            $flyingFleetCount++;
        } catch (Exception $e) {
            // Ignorer si la planète n'a pas assez de vaisseaux
        }
    }
    logMessage("  ✅ {$flyingFleetCount} flottes actives en vol créées.", 'success', $isCli);
}

// 5b. Flottes d'attaque imminentes pour l'Admin (impacts espacés de 5 secondes)
logMessage("⚔️ Création de flottes d'attaque imminentes pour l'Admin (impacts à +5s, +10s, +15s, +20s, +25s)...", 'info', $isCli);

$adminPlanet = $db->selectSingle("SELECT id, galaxy, system, planet, planet_type FROM %%PLANETS%% WHERE id_owner = 1 AND planet_type = 1 LIMIT 1;");
$targetPlanet = $db->selectSingle("SELECT id, id_owner, galaxy, system, planet, planet_type FROM %%PLANETS%% WHERE id_owner > 1 AND planet_type = 1 LIMIT 1;");

if (!empty($adminPlanet) && !empty($targetPlanet)) {
    $imminentAttacksCount = 0;
    $now = TIMESTAMP;

    $attackFleetsComposition = [
        1 => [204 => 300, 205 => 100, 206 => 50, 223 => 5000],
        2 => [206 => 120, 207 => 40, 223 => 5000],
        3 => [207 => 80, 213 => 15, 223 => 5000],
        4 => [214 => 2, 215 => 50, 223 => 5000],
        5 => [202 => 200, 203 => 100, 204 => 500, 207 => 100, 223 => 5000],
    ];

    foreach ($attackFleetsComposition as $index => $ships) {
        $impactDelay = $index * 5;
        $startTime   = $now + $impactDelay;
        $stayTime    = $startTime;
        $endTime     = $startTime + 300;

        try {
            FleetFunctions::sendFleet(
                $ships,
                1,
                1,
                $adminPlanet['id'],
                $adminPlanet['galaxy'],
                $adminPlanet['system'],
                $adminPlanet['planet'],
                $adminPlanet['planet_type'],
                $targetPlanet['id_owner'],
                $targetPlanet['id'],
                $targetPlanet['galaxy'],
                $targetPlanet['system'],
                $targetPlanet['planet'],
                $targetPlanet['planet_type'],
                [901 => 0, 902 => 0, 903 => 0],
                $startTime,
                $stayTime,
                $endTime,
                0
            );
            $imminentAttacksCount++;
            logMessage("    ⚔️ Vague {$index} lancée : Impact dans {$impactDelay}s sur [{$targetPlanet['galaxy']}:{$targetPlanet['system']}:{$targetPlanet['planet']}].", 'info', $isCli);
        } catch (Exception $e) {
            logMessage("    ⚠️ Erreur lors du lancement de la vague {$index} : " . $e->getMessage(), 'warn', $isCli);
        }
    }
    logMessage("  ✅ {$imminentAttacksCount} flottes d'attaque imminentes configurées avec succès !", 'success', $isCli);
}

// 6. Champs de Débris (TF)
logMessage("☄️ Création de champs de débris dans la galaxie...", 'info', $isCli);
$debrisPlanets = $db->select("SELECT id FROM %%PLANETS%% WHERE planet_type = 1 ORDER BY RAND() LIMIT 8;");
foreach ($debrisPlanets as $dp) {
    $db->update("UPDATE %%PLANETS%% SET der_metal = :m, der_crystal = :c WHERE id = :id;", [
        ':m' => mt_rand(50000, 800000),
        ':c' => mt_rand(30000, 500000),
        ':id' => $dp['id'],
    ]);
}
logMessage("  ✅ 8 champs de débris métalliques & cristallins générés.", 'success', $isCli);

// 7. Calcul & Recalcul des Statistiques Globale
logMessage("📊 Calcul des classements et mise à jour des statistiques...", 'info');
$statBuilder = new statbuilder();
$statResults = $statBuilder->MakeStats();
logMessage("  ✅ Statistiques calculées et enregistrées avec succès.", 'success');

// Vider le cache de vars / config
Cache::get()->flush('vars');

logMessage("🎉 **UNIVERS RISISTAR PEUPLÉ AVEC SUCCÈS !**", 'success', $isCli);

// Résumé d'information
if (!$isCli) {
    echo '<h2 style="color:#38bdf8; margin-top:25px;">Statistiques du Peuplement</h2>';
    echo '<div class="stats-grid">
        <div class="stat-card"><div class="stat-value">' . count($playersConfig) . '</div><div class="stat-label">Joueurs Créés</div></div>
        <div class="stat-card"><div class="stat-value">' . count($alliancesData) . '</div><div class="stat-label">Alliances</div></div>
        <div class="stat-card"><div class="stat-value">' . count($allPlanets) . '</div><div class="stat-label">Planètes</div></div>
        <div class="stat-card"><div class="stat-value">' . (isset($flyingFleetCount) ? $flyingFleetCount : 0) . '</div><div class="stat-label">Flottes en Vol</div></div>
    </div>';

    echo '<h3 style="color:#38bdf8;">Comptes Joueurs Générés (Mot de passe universel : <code>password123</code>)</h3>';
    echo '<table>
        <thead>
            <tr><th>Nom du Joueur</th><th>Rôle / Tier</th><th>Alliance</th><th>Planètes</th><th>Matière Noire</th></tr>
        </thead>
        <tbody>';
    foreach ($playersConfig as $p) {
        $allyName = ($p['ally_index'] !== null && isset($alliancesData[$p['ally_index']])) ? $alliancesData[$p['ally_index']]['name'] . ' [' . $alliancesData[$p['ally_index']]['tag'] . ']' : '<i>Aucune</i>';
        $tierName = ($p['tier'] == 1) ? '🥇 Top Empereur' : (($p['tier'] == 2) ? '🥈 Vétéran' : '🥉 Débutant');
        echo "<tr>
            <td><b>{$p['name']}</b></td>
            <td>{$tierName}</td>
            <td>{$allyName}</td>
            <td>1 Principale + {$p['colonies_count']} Colonies (" . ($p['moons_count'] > 0 ? "{$p['moons_count']} 🌕" : "Pas de lune") . ")</td>
            <td>" . number_format($p['darkmatter'], 0, ',', ' ') . " DM</td>
        </tr>";
    }
    echo '</tbody></table>';

    echo '<div style="margin-top: 30px; text-align: center;">';
    echo '<a href="index.php" class="btn" style="padding: 15px 30px; font-size: 16px;">🎮 Se Connecter au Jeu</a>';
    echo '</div>';
}

outputFooter($isCli);
