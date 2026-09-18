<?php

namespace Risistar\Tests\Unit;

use ShowRaportPage;

class CombatReportLabelTest extends UnitTestCase
{
	public static function setUpBeforeClass(): void
	{
		self::rootPath();
		require_once self::rootPath() . 'includes/pages/game/AbstractGamePage.class.php';
		require_once self::rootPath() . 'includes/pages/game/ShowRaportPage.class.php';
	}

	protected function setUp(): void
	{
		$GLOBALS['reslist'] = array(
			'defense' => array(401, 409),
			'missile' => array(502, 503),
		);
	}

	public function testFleetShipsKeepAttackLabels(): void
	{
		$this->assertTrue(ShowRaportPage::isAttackerCombatLabel(204));
		$this->assertTrue(ShowRaportPage::isAttackerCombatLabel(223));
	}

	public function testDefensesAndMissilesHideAttackLabels(): void
	{
		$this->assertFalse(ShowRaportPage::isAttackerCombatLabel(401));
		$this->assertFalse(ShowRaportPage::isAttackerCombatLabel(409));
		$this->assertFalse(ShowRaportPage::isAttackerCombatLabel(502));
	}

	public function testOrbitalStationsHideAttackLabels(): void
	{
		$this->assertFalse(ShowRaportPage::isAttackerCombatLabel(212));
		$this->assertFalse(ShowRaportPage::isAttackerCombatLabel(227));
		$this->assertFalse(ShowRaportPage::isAttackerCombatLabel(231));
	}
}
