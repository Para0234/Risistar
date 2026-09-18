<?php

namespace Risistar\Tests\Unit;

use SpyReportHtmlParser;

class SpyReportHtmlParserTest extends UnitTestCase
{
	private SpyReportHtmlParser $parser;

	public static function setUpBeforeClass(): void
	{
		self::rootPath();
		require_once self::rootPath() . 'includes/classes/SpyReportHtmlParser.class.php';
	}

	protected function setUp(): void
	{
		$this->parser = new SpyReportHtmlParser();
	}

	public function testParseAmountStripsFrenchThousands(): void
	{
		$this->assertSame(21420000, $this->parser->parseAmount('21.420.000'));
		$this->assertSame(13092, $this->parser->parseAmount('13.092'));
		$this->assertSame(799, $this->parser->parseAmount('799'));
	}

	public function testParseClassicHtmlWithSimLink(): void
	{
		$parsed = $this->parser->parse($this->classicHtml());
		$this->assertNotNull($parsed);
		$this->assertSame(3, $parsed['galaxy']);
		$this->assertSame(16, $parsed['system']);
		$this->assertSame(6, $parsed['planet']);
		$this->assertSame(1, $parsed['planet_type']);
		$this->assertSame('Planète Principale', $parsed['planet_name']);
		$this->assertSame(0, $parsed['spy_destroyed']);
		$this->assertSame(0, $parsed['target_chance']);
		$this->assertContains(900, $parsed['class_ids']);
		$this->assertContains(200, $parsed['class_ids']);
		$this->assertSame(21420000, $parsed['amounts'][901]);
		$this->assertSame(799, $parsed['amounts'][202]);
		$this->assertSame('Métal', $parsed['named_sections'][900][0][0]);
		$this->assertSame(21420000, $parsed['named_sections'][900][0][1]);
	}

	public function testParseClassicHtmlWithoutSimLink(): void
	{
		$html = $this->classicHtml();
		$html = preg_replace('/<a href="game\.php\?page=battleSimulator[^"]*">Simuler<\/a>/', '', $html);
		$parsed = $this->parser->parse($html);
		$this->assertNotNull($parsed);
		$this->assertSame(array(), $parsed['amounts']);
		$this->assertSame('Métal', $parsed['named_sections'][900][0][0]);
		$this->assertSame(21420000, $parsed['named_sections'][900][0][1]);
		$this->assertSame(799, $parsed['named_sections'][200][0][1]);
	}

	public function testParseItemHtmlWithoutSimLink(): void
	{
		$parsed = $this->parser->parse($this->itemHtml());
		$this->assertNotNull($parsed);
		$this->assertSame(1, $parsed['galaxy']);
		$this->assertSame(2, $parsed['system']);
		$this->assertSame(3, $parsed['planet']);
		$this->assertSame(3, $parsed['planet_type']);
		$this->assertSame(array(), $parsed['amounts']);
		$this->assertSame(12, $parsed['named_sections'][100][0][1]);
		$this->assertSame('Technologie d\'Espionnage', $parsed['named_sections'][100][0][0]);
		$this->assertSame(12, $parsed['target_chance']);
	}

	public function testIgnoresActivityMessages(): void
	{
		$html = 'Une flotte hostile <a href="game.php?page=galaxy&amp;galaxy=1&amp;system=1">[1:1:1]</a>';
		$this->assertNull($this->parser->parse($html));
	}

	private function classicHtml(): string
	{
		return <<<'HTML'
<div class="spyRaport">
	<div class="spyRaportHead">
		<a href="game.php?page=galaxy&amp;galaxy=3&amp;system=16">Rapport d espionnage Planète Principale [3:16:6] sur 09. Sep 2026</a>
	</div>
	<div class="spyRaportContainer">
		<div class="spyRaportContainerHead spyRaportContainerHeadClass900">Ressources</div>
		<div class="spyRaportContainerRow clearfix">
			<div class="spyRaportContainerCell">Métal</div>
			<div class="spyRaportContainerCell">21.420.000</div>
			<div class="spyRaportContainerCell">Cristal</div>
			<div class="spyRaportContainerCell">6.360.000</div>
		</div>
	</div>
	<div class="spyRaportContainer">
		<div class="spyRaportContainerHead spyRaportContainerHeadClass200">Vaisseaux</div>
		<div class="spyRaportContainerRow clearfix">
			<div class="spyRaportContainerCell">Petit Transporteur</div>
			<div class="spyRaportContainerCell">799</div>
			<div class="spyRaportContainerCell">Grand Transporteur</div>
			<div class="spyRaportContainerCell">400</div>
		</div>
	</div>
	<div class="spyRaportFooter">
		<a href="game.php?page=fleetTable&amp;galaxy=3&amp;system=16&amp;planet=6&amp;planettype=1&amp;target_mission=1">Attaquer</a>
		<br>La probabilité de la destruction de la/des sonde(s) d'espionnage est :  0 %
		<br><a href="game.php?page=battleSimulator&amp;im[901]=21420000&amp;im[902]=6360000&amp;im[202]=799&amp;im[203]=400">Simuler</a>
	</div>
</div>
HTML;
	}

	private function itemHtml(): string
	{
		return <<<'HTML'
<div class="spyRaport">
	<div class="spyRaportHead">
		<a href="game.php?page=galaxy&amp;galaxy=1&amp;system=2">Rapport d espionnage Lune [1:2:3] sur 09. Sep 2026</a>
	</div>
	<div class="spyRaportContainer">
		<div class="spyRaportContainerHead spyRaportContainerHeadClass100">Recherche</div>
		<div class="spyRaportItem">
			<div class="spyRaportContainerCell spyRaportName">Technologie d'Espionnage</div>
			<div class="spyRaportContainerCell spyRaportAmount">12</div>
		</div>
	</div>
	<div class="spyRaportFooter">
		<a href="game.php?page=fleetTable&amp;galaxy=1&amp;system=2&amp;planet=3&amp;planettype=3&amp;target_mission=1">Attaquer</a>
		<br>La probabilité de la destruction de la/des sonde(s) d'espionnage est :  12 %
	</div>
</div>
HTML;
	}
}
