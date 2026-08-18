<?php

namespace Risistar\Tests\Unit;

use PHPUnit\Framework\TestCase;

abstract class UnitTestCase extends TestCase
{
    protected static function rootPath(): string
    {
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__, 2) . '/');
        }

        return ROOT_PATH;
    }

    protected static function bootConstants(): void
    {
        require_once self::rootPath() . 'includes/constants.php';
    }
}
