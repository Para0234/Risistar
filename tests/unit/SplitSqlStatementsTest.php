<?php

namespace Risistar\Tests\Unit;

class SplitSqlStatementsTest extends UnitTestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once self::rootPath() . 'scripts/split_sql_statements.php';
    }

    public function testCommentHeaderDoesNotDropTheFollowingCreateTable(): void
    {
        $sql = <<<'SQL'
SET NAMES utf8mb4;

--
-- Structure de la table `uni1_aks`
--

CREATE TABLE `uni1_aks` (
  `id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB;

SQL;

        $statements = splitSqlStatements($sql);

        $this->assertCount(2, $statements);
        $this->assertStringContainsString('CREATE TABLE `uni1_aks`', $statements[1]);
    }

    public function testSemicolonInsideAStringIsNotASplit(): void
    {
        $sql = "INSERT INTO `uni1_config` (`moduls`) VALUES ('1;1;1;0');";

        $statements = splitSqlStatements($sql);

        $this->assertCount(1, $statements);
        $this->assertStringContainsString('1;1;1;0', $statements[0]);
    }

    public function testDoubledQuoteInsideAStringStaysInTheStatement(): void
    {
        $sql = "INSERT INTO `uni1_config` (`name`) VALUES ('L''empire; nord');";

        $statements = splitSqlStatements($sql);

        $this->assertCount(1, $statements);
        $this->assertStringContainsString("L''empire; nord", $statements[0]);
    }

    public function testInstallSqlKeepsEveryCreateAndTheSemicolonHeavyInsert(): void
    {
        $sql = str_replace(
            '%PREFIX%',
            'uni1_',
            (string) file_get_contents(self::rootPath() . 'install/install.sql')
        );
        $lf = str_replace("\r\n", "\n", $sql);
        $crlf = str_replace("\n", "\r\n", $lf);

        foreach ([$lf, $crlf] as $dump) {
            $statements = splitSqlStatements($dump);
            $creates = 0;
            $keepsModulList = false;
            foreach ($statements as $statement) {
                if (stripos($statement, 'CREATE TABLE') !== false) {
                    $creates++;
                }
                if (str_contains($statement, '1;1;1;1;1;1;1')) {
                    $keepsModulList = true;
                }
            }

            $this->assertSame(substr_count($dump, 'CREATE TABLE'), $creates);
            $this->assertTrue($keepsModulList);
        }
    }
}
