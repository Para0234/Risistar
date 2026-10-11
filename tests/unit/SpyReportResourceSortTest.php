<?php

namespace Risistar\Tests\Unit;

use SpyReportStore;

class SpyReportResourceSortTest extends UnitTestCase
{
	public static function setUpBeforeClass(): void
	{
		self::rootPath();
		require_once self::rootPath() . 'includes/classes/SpyReportStore.class.php';
	}

	public function testMetalEquivalentUsesMerchantRate(): void
	{
		$this->assertSame(240.0, SpyReportStore::metalEquivalent(array(
			901 => 100,
			902 => 50,
			903 => 10,
			911 => 99999,
		)));
	}

	public function testMetalEquivalentTreatsMissingStocksAsZero(): void
	{
		$this->assertSame(8.0, SpyReportStore::metalEquivalent(array(
			903 => 2,
		)));
	}

	public function testReportMetalEquivalentReadsResourceSectionOnly(): void
	{
		$payload = SpyReportStore::encodePayload(array(
			900 => array(901 => 10, 902 => 0, 903 => 1, 911 => 500),
			200 => array(202 => 30),
		));

		$this->assertSame(14.0, SpyReportStore::reportMetalEquivalent($payload));
		$this->assertSame(0.0, SpyReportStore::reportMetalEquivalent('{}'));
		$this->assertSame(0.0, SpyReportStore::reportMetalEquivalent('not-json'));
	}
}
