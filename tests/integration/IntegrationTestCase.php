<?php

namespace Risistar\Tests\Integration;

use Database;
use PHPUnit\Framework\TestCase;

/**
 * Base class for PHPUnit tests that use the isolated database (2moons_test).
 */
abstract class IntegrationTestCase extends TestCase
{
    protected static ?Database $db = null;
    protected static ?string $bootstrapError = null;

    protected static function rootPath(): string
    {
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__, 2) . '/');
        }

        return ROOT_PATH;
    }

    public static function setUpBeforeClass(): void
    {
        if (!defined('MODE')) {
            define('MODE', 'TEST');
        }
        if (!defined('DATABASE_VERSION')) {
            define('DATABASE_VERSION', 'OLD');
        }

        $probeError = self::probeDatabase();
        if ($probeError !== null) {
            self::$bootstrapError = $probeError;
            self::$db = null;
            return;
        }

        self::guardAgainstSilentExit(true);
        try {
            require_once self::rootPath() . 'includes/common.php';
            require_once self::rootPath() . 'includes/vars.php';
            self::ensureGameGlobals();

            self::$db = Database::get();
            self::guardAgainstSilentExit(false);
        } catch (\Throwable $e) {
            self::guardAgainstSilentExit(false);
            self::$bootstrapError = $e->getMessage();
            self::$db = null;
        }
    }

    /**
     * Check the test database before includes/common.php.
     *
     * common.php answers an unreachable database or a schema that is behind
     * DB_VERSION_REQUIRED with HTTP::redirectTo(), which exit()s. Under PHPUnit
     * that ended the process with status 0. Fail here so the suite can skip
     * or fail with the reason instead.
     *
     * @return string|null Error message, or null when the database looks usable.
     */
    private static function probeDatabase(): ?string
    {
        $root = self::rootPath();

        if (!is_file($root . 'includes/config.php') || filesize($root . 'includes/config.php') === 0) {
            return 'includes/config.php is missing or empty (includes/common.php would redirect to the installer)';
        }

        $configFile = defined('DATABASE_CONFIG_FILE') ? DATABASE_CONFIG_FILE : $root . 'includes/config.php';
        $database = [];
        require $configFile;

        try {
            $pdo = new \PDO(
                "mysql:host={$database['host']};port={$database['port']};dbname={$database['databasename']}",
                $database['user'],
                $database['userpw'],
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_TIMEOUT => 5,
                ]
            );
            $prefix = str_replace('`', '', (string) ($database['tableprefix'] ?? ''));
            $version = $pdo->query('SELECT dbVersion FROM `' . $prefix . 'system` LIMIT 1')->fetchColumn();
        } catch (\Throwable $e) {
            return sprintf(
                '%s (database "%s" on %s:%s; run scripts/reset_test_database.php to create it)',
                $e->getMessage(),
                $database['databasename'] ?? '?',
                $database['host'] ?? '?',
                $database['port'] ?? '?'
            );
        }

        $required = self::requiredSchemaVersion();
        $version = (int) $version;
        if ($required > 0 && $version < $required) {
            return sprintf(
                'schema version %d is older than required %d (run scripts/reset_test_database.php)',
                $version,
                $required
            );
        }

        return null;
    }

    private static function requiredSchemaVersion(): int
    {
        $source = file_get_contents(self::rootPath() . 'includes/dbtables.php');
        if ($source !== false && preg_match("/define\\('DB_VERSION_REQUIRED',\\s*(\\d+)\\)/", $source, $match) === 1) {
            return (int) $match[1];
        }

        return 0;
    }

    /**
     * If bootstrapping still calls exit() (for example a redirect added later),
     * fail the run with a non-zero status instead of exiting 0.
     *
     * Disable this only after common.php returns. A finally block runs before
     * shutdown when exit() is called, and would turn the guard off too early.
     */
    private static function guardAgainstSilentExit(bool $active): void
    {
        static $registered = false;
        static $isActive = false;

        $isActive = $active;
        if ($registered) {
            return;
        }
        $registered = true;

        register_shutdown_function(static function () use (&$isActive): void {
            if (!$isActive) {
                return;
            }
            fwrite(STDERR, PHP_EOL . 'Integration bootstrap aborted: includes/common.php called exit() '
                . '(database unreachable, missing tables, or schema upgrade required).' . PHP_EOL);
            exit(1);
        });
    }

    protected static function ensureGameGlobals(): void
    {
        global $resource;

        if (!isset($resource[204])) {
            $vars = \Cache::get()->getData('vars');
            foreach ($vars as $key => $value) {
                $GLOBALS[$key] = $value;
            }
        }
    }

    protected function requireDatabase(): void
    {
        if (self::$db === null) {
            $message = 'Database not available: ' . (self::$bootstrapError ?? 'unknown');
            if (getenv('RISISTAR_REQUIRE_TEST_DB')) {
                $this->fail($message);
            }
            $this->markTestSkipped($message);
        }
    }
}
