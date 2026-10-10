<?php

namespace Risistar\Tests\Unit;

use ReflectionClass;
use ReflectionMethod;
use ShowSpyReportsPage;

/**
 * session_id() can only be set before any output, so each test runs on its own.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class SpyReportsDeleteTokenTest extends UnitTestCase
{
    private const SESSION_ID = 'spyReportsTestSession0123456789';

    private array $server = [];
    private array $post = [];

    public static function setUpBeforeClass(): void
    {
        self::bootConstants();
        require_once self::rootPath() . 'includes/pages/game/AbstractGamePage.class.php';
        require_once self::rootPath() . 'includes/pages/game/ShowSpyReportsPage.class.php';
    }

    protected function setUp(): void
    {
        $this->server = $_SERVER;
        $this->post = $_POST;
        session_id(self::SESSION_ID);
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->server;
        $_POST = $this->post;
    }

    public function testPostWithTheSessionTokenMayDelete(): void
    {
        $this->request('POST', $this->token());

        $this->assertTrue($this->confirmed());
    }

    public function testLinkCannotDelete(): void
    {
        // A GET is what a link or an image on another site sends, even with a copied token.
        $this->request('GET', $this->token());

        $this->assertFalse($this->confirmed());
    }

    public function testPostWithoutTheTokenCannotDelete(): void
    {
        $this->request('POST', null);

        $this->assertFalse($this->confirmed());
    }

    public function testPostWithATokenOfAnotherSessionCannotDelete(): void
    {
        $this->request('POST', $this->token());
        session_id('anotherSession9876543210abcdef');

        $this->assertFalse($this->confirmed());
    }

    public function testTokenDoesNotShowTheSessionId(): void
    {
        $this->assertStringNotContainsString(self::SESSION_ID, $this->token());
        $this->assertSame(64, strlen($this->token()));
    }

    private function request(string $method, ?string $token): void
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_POST = $token === null ? [] : ['token' => $token];
    }

    private function token(): string
    {
        $method = new ReflectionMethod(ShowSpyReportsPage::class, 'deleteToken');
        $method->setAccessible(true);
        return $method->invoke(null);
    }

    private function confirmed(): bool
    {
        $page = (new ReflectionClass(ShowSpyReportsPage::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(ShowSpyReportsPage::class, 'isConfirmedPost');
        $method->setAccessible(true);
        return $method->invoke($page);
    }
}
