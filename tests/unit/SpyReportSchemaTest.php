<?php

namespace Risistar\Tests\Unit;

class SpyReportSchemaTest extends UnitTestCase
{
    public function testFreshInstallIsAtTheRequiredSchemaVersion(): void
    {
        $install = file_get_contents(self::rootPath() . 'install/install.sql');
        $this->assertSame(1, preg_match('/INSERT INTO `%PREFIX%system` \(`dbVersion`\) VALUES\s*\((\d+)\);/', $install, $match));

        $this->assertSame($this->requiredVersion(), (int) $match[1]);
    }

    public function testSpyReportsTableIsTheSameOnInstallAndUpgrade(): void
    {
        $install = $this->spyReportsTable('install/install.sql');
        $migration = $this->spyReportsTable('install/migrations/migration_9.sql');

        $this->assertNotSame('', $install);
        $this->assertSame($install, $migration);
    }

    public function testUpgradeKeepsAnExistingSpyReportsTable(): void
    {
        $migration = file_get_contents(self::rootPath() . 'install/migrations/migration_9.sql');

        $this->assertMatchesRegularExpression('/CREATE TABLE IF NOT EXISTS `?%PREFIX%spy_reports`? \(/', $migration);
    }

    /**
     * MySQL 8 reserves SYSTEM: an unquoted column name is a syntax error (see #46).
     * A name after a table alias (sr.system) is always an identifier and may stay bare.
     */
    public function testSpyReportQueriesQuoteTheSystemColumn(): void
    {
        $source = file_get_contents(self::rootPath() . 'includes/classes/SpyReportStore.class.php');
        preg_match_all("/(?<![.`$:'\\w])system(?![\\w`'])/", $source, $matches);

        $this->assertSame([], $matches[0]);
    }

    private function requiredVersion(): int
    {
        $source = file_get_contents(self::rootPath() . 'includes/dbtables.php');
        $this->assertSame(1, preg_match("/define\\('DB_VERSION_REQUIRED',\\s*(\\d+)\\)/", $source, $match));
        return (int) $match[1];
    }

    private function spyReportsTable(string $file): string
    {
        $sql = file_get_contents(self::rootPath() . $file);
        if (preg_match('/CREATE TABLE (?:IF NOT EXISTS )?`?%PREFIX%spy_reports`? \((.*?)\) ENGINE[^;]*;/s', $sql, $match) !== 1) {
            return '';
        }
        return preg_replace('/\s+/', ' ', trim($match[1]));
    }
}
