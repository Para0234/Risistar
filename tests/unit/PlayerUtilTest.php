<?php

namespace Risistar\Tests\Unit;

use PlayerUtil;

class PlayerUtilTest extends UnitTestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!defined('UTF8_SUPPORT')) {
            define('UTF8_SUPPORT', true);
        }
        if (!defined('CRYPT_BLOWFISH')) {
            define('CRYPT_BLOWFISH', true);
        }

        self::bootConstants();
        require_once self::rootPath() . 'includes/classes/PlayerUtil.class.php';
    }

    public function testIsNameValid()
    {
        $this->assertTrue((bool) PlayerUtil::isNameValid('TestPlayer'));
        $this->assertTrue((bool) PlayerUtil::isNameValid('Player_123'));
        $this->assertTrue((bool) PlayerUtil::isNameValid('Lord Vador'));
        $this->assertFalse((bool) PlayerUtil::isNameValid('Player<script>'));
        $this->assertFalse((bool) PlayerUtil::isNameValid('User;DROP TABLE'));
    }

    public function testIsMailValid()
    {
        $this->assertTrue(PlayerUtil::isMailValid('player@test.com'));
        $this->assertTrue(PlayerUtil::isMailValid('admin.risistar@game.local'));
        $this->assertFalse(PlayerUtil::isMailValid('invalid-email-string'));
        $this->assertFalse(PlayerUtil::isMailValid('missing-at-domain.com'));
        $this->assertFalse(PlayerUtil::isMailValid('user@domain'));
    }

    public function testCryptPassword()
    {
        $password = 'SecretPassword123';
        $hashed = PlayerUtil::cryptPassword($password);

        $this->assertNotEmpty($hashed);
        $this->assertIsString($hashed);
        $this->assertEquals(60, strlen($hashed));
        $this->assertStringStartsWith('$2a$', $hashed);
    }
}
