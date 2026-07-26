<?php

namespace Risistar\Tests;

use PHPUnit\Framework\TestCase;
use PlayerUtil;

class PlayerUtilTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__) . '/');
        }
        if (!defined('MODE')) {
            define('MODE', 'TEST');
        }
        if (!defined('UTF8_SUPPORT')) {
            define('UTF8_SUPPORT', true);
        }
        if (!defined('CRYPT_BLOWFISH')) {
            define('CRYPT_BLOWFISH', true);
        }

        require_once ROOT_PATH . 'includes/constants.php';
        require_once ROOT_PATH . 'includes/classes/PlayerUtil.class.php';
    }

    public function testIsNameValid()
    {
        // Valid usernames
        $this->assertTrue((bool) PlayerUtil::isNameValid('TestPlayer'));
        $this->assertTrue((bool) PlayerUtil::isNameValid('Player_123'));
        $this->assertTrue((bool) PlayerUtil::isNameValid('Lord Vador'));

        // Invalid usernames with injection/special characters
        $this->assertFalse((bool) PlayerUtil::isNameValid('Player<script>'));
        $this->assertFalse((bool) PlayerUtil::isNameValid('User;DROP TABLE'));
    }

    public function testIsMailValid()
    {
        // Valid email addresses
        $this->assertTrue(PlayerUtil::isMailValid('player@test.com'));
        $this->assertTrue(PlayerUtil::isMailValid('admin.risistar@game.local'));

        // Invalid email addresses
        $this->assertFalse(PlayerUtil::isMailValid('invalid-email-string'));
        $this->assertFalse(PlayerUtil::isMailValid('missing-at-domain.com'));
        $this->assertFalse(PlayerUtil::isMailValid('user@domain'));
    }

    public function testCryptPassword()
    {
        $password = 'SecretPassword123';
        $hashed = PlayerUtil::cryptPassword($password);

        $this->assertNotEmpty($hashed);
        $this->assertTrue(is_string($hashed));
        // Bcrypt hashes are 60 characters long
        $this->assertEquals(60, strlen($hashed));
        $this->assertStringStartsWith('$2a$', $hashed);
    }
}
