<?php

namespace Risistar\Tests\Unit;

class PhpSyntaxLintTest extends UnitTestCase
{
    public function testAllPhpFilesHaveValidSyntax()
    {
        $rootDir = self::rootPath();
        $directoriesToScan = [
            $rootDir . 'includes',
            $rootDir . 'tests',
            $rootDir . 'chat',
            $rootDir . 'install',
            $rootDir . 'language',
        ];

        $failedFiles = [];
        $scannedCount = 0;

        foreach ($directoriesToScan as $dir) {
            if (!is_dir($dir)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (!$file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $filePath = $file->getPathname();
                if (strpos($filePath, 'vendor') !== false || strpos($filePath, 'cache') !== false) {
                    continue;
                }

                $scannedCount++;
                $output = [];
                $returnCode = 0;
                exec(sprintf('php -l %s 2>&1', escapeshellarg($filePath)), $output, $returnCode);

                if ($returnCode !== 0) {
                    $failedFiles[$filePath] = implode("\n", $output);
                }
            }
        }

        $this->assertGreaterThan(0, $scannedCount, 'At least one PHP file should be scanned.');
        $this->assertEmpty(
            $failedFiles,
            sprintf('Syntax errors found in %d PHP files:\n%s', count($failedFiles), print_r($failedFiles, true))
        );
    }
}
