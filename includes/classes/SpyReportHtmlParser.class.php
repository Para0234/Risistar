<?php

class SpyReportHtmlParser
{
	/**
	 * @return array<string, mixed>|null
	 */
	public function parse(string $html): ?array
	{
		if (strpos($html, 'spyRaport') === false) {
			return null;
		}

		$dom = new DOMDocument();
		$previous = libxml_use_internal_errors(true);
		$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
		libxml_clear_errors();
		libxml_use_internal_errors($previous);

		$xpath = new DOMXPath($dom);
		$report = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " spyRaport ")]')->item(0);
		if (!$report instanceof DOMElement) {
			return null;
		}

		$coords = $this->extractCoords($xpath, $report);
		if ($coords === null) {
			return null;
		}

		$sections = $this->extractSections($xpath, $report);
		$amounts = $this->extractSimAmounts($xpath, $report);
		$destroyed = $this->isDestroyed($report->textContent);

		return array(
			'galaxy'			=> $coords['galaxy'],
			'system'			=> $coords['system'],
			'planet'			=> $coords['planet'],
			'planet_type'		=> $coords['planet_type'],
			'planet_name'		=> $this->extractPlanetName($xpath, $report, $coords),
			'target_chance'		=> $destroyed ? 0 : $this->extractChance($report->textContent),
			'spy_destroyed'		=> $destroyed ? 1 : 0,
			'class_ids'			=> array_keys($sections),
			'amounts'			=> $amounts,
			'named_sections'	=> $sections,
		);
	}

	public function parseAmount(string $text): int
	{
		$cleaned = preg_replace('/[\s\x{00A0}.]/u', '', $text);
		$cleaned = str_replace(',', '.', (string) $cleaned);
		return (int) floor((float) $cleaned);
	}

	/**
	 * @return array{galaxy:int,system:int,planet:int,planet_type:int}|null
	 */
	private function extractCoords(DOMXPath $xpath, DOMElement $report): ?array
	{
		$links = $xpath->query('.//a[@href]', $report);
		foreach ($links as $link) {
			if (!$link instanceof DOMElement) {
				continue;
			}
			$href = html_entity_decode($link->getAttribute('href'), ENT_QUOTES, 'UTF-8');
			if (strpos($href, 'page=fleetTable') === false) {
				continue;
			}
			$query = array();
			parse_str((string) parse_url($href, PHP_URL_QUERY), $query);
			if (!isset($query['galaxy'], $query['system'], $query['planet'])) {
				continue;
			}
			return array(
				'galaxy'		=> (int) $query['galaxy'],
				'system'		=> (int) $query['system'],
				'planet'		=> (int) $query['planet'],
				'planet_type'	=> isset($query['planettype']) ? (int) $query['planettype'] : 1,
			);
		}

		foreach ($links as $link) {
			if (!$link instanceof DOMElement) {
				continue;
			}
			$href = html_entity_decode($link->getAttribute('href'), ENT_QUOTES, 'UTF-8');
			if (strpos($href, 'page=galaxy') === false) {
				continue;
			}
			$text = html_entity_decode($link->textContent, ENT_QUOTES, 'UTF-8');
			if (!preg_match('/\[(\d+):(\d+):(\d+)\]/', $text, $match)) {
				continue;
			}
			return array(
				'galaxy'		=> (int) $match[1],
				'system'		=> (int) $match[2],
				'planet'		=> (int) $match[3],
				'planet_type'	=> 1,
			);
		}

		return null;
	}

	private function extractPlanetName(DOMXPath $xpath, DOMElement $report, array $coords): string
	{
		$head = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " spyRaportHead ")]', $report)->item(0);
		$text = $head instanceof DOMElement
			? html_entity_decode($head->textContent, ENT_QUOTES, 'UTF-8')
			: '';
		$needle = '[' . $coords['galaxy'] . ':' . $coords['system'] . ':' . $coords['planet'] . ']';
		if (preg_match('/espionnage\s+(.+?)\s+' . preg_quote($needle, '/') . '/u', $text, $match)) {
			return trim($match[1]);
		}
		if (preg_match('/^(.+?)\s+' . preg_quote($needle, '/') . '/u', $text, $match)) {
			return trim($match[1]);
		}
		return '';
	}

	/**
	 * @return array<int, int>
	 */
	private function extractSimAmounts(DOMXPath $xpath, DOMElement $report): array
	{
		$amounts = array();
		$links = $xpath->query('.//a[@href]', $report);
		foreach ($links as $link) {
			if (!$link instanceof DOMElement) {
				continue;
			}
			$href = html_entity_decode($link->getAttribute('href'), ENT_QUOTES, 'UTF-8');
			if (strpos($href, 'page=battleSimulator') === false) {
				continue;
			}
			$query = array();
			parse_str((string) parse_url($href, PHP_URL_QUERY), $query);
			if (!isset($query['im']) || !is_array($query['im'])) {
				continue;
			}
			foreach ($query['im'] as $elementId => $amount) {
				$amounts[(int) $elementId] = (int) $amount;
			}
		}
		return $amounts;
	}

	/**
	 * @return array<int, list<array{0:string,1:int}>>
	 */
	private function extractSections(DOMXPath $xpath, DOMElement $report): array
	{
		$sections = array();
		$containers = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " spyRaportContainer ")]', $report);
		foreach ($containers as $container) {
			if (!$container instanceof DOMElement) {
				continue;
			}
			$classId = $this->containerClassId($xpath, $container);
			if ($classId === null) {
				continue;
			}
			$pairs = $this->extractItemPairs($xpath, $container);
			if (!$pairs) {
				$pairs = $this->extractRowPairs($xpath, $container);
			}
			$sections[$classId] = $pairs;
		}
		return $sections;
	}

	private function containerClassId(DOMXPath $xpath, DOMElement $container): ?int
	{
		$heads = $xpath->query('.//*[contains(@class, "spyRaportContainerHeadClass")]', $container);
		foreach ($heads as $head) {
			if (!$head instanceof DOMElement) {
				continue;
			}
			if (preg_match('/spyRaportContainerHeadClass(\d+)/', $head->getAttribute('class'), $match)) {
				return (int) $match[1];
			}
		}
		return null;
	}

	/**
	 * @return list<array{0:string,1:int}>
	 */
	private function extractItemPairs(DOMXPath $xpath, DOMElement $container): array
	{
		$pairs = array();
		$items = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " spyRaportItem ")]', $container);
		foreach ($items as $item) {
			if (!$item instanceof DOMElement) {
				continue;
			}
			$name = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " spyRaportName ")]', $item)->item(0);
			$amount = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " spyRaportAmount ")]', $item)->item(0);
			if (!$name instanceof DOMElement || !$amount instanceof DOMElement) {
				continue;
			}
			$pairs[] = array(
				trim(html_entity_decode($name->textContent, ENT_QUOTES, 'UTF-8')),
				$this->parseAmount($amount->textContent),
			);
		}
		return $pairs;
	}

	/**
	 * @return list<array{0:string,1:int}>
	 */
	private function extractRowPairs(DOMXPath $xpath, DOMElement $container): array
	{
		$pairs = array();
		$rows = $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " spyRaportContainerRow ")]', $container);
		foreach ($rows as $row) {
			if (!$row instanceof DOMElement) {
				continue;
			}
			$cells = array();
			foreach ($row->childNodes as $child) {
				if (!$child instanceof DOMElement) {
					continue;
				}
				$className = ' ' . $child->getAttribute('class') . ' ';
				if (strpos($className, ' spyRaportContainerCell ') === false && strpos($className, 'spyRaportContainerCell') === false) {
					continue;
				}
				$cells[] = trim(html_entity_decode($child->textContent, ENT_QUOTES, 'UTF-8'));
			}
			for ($i = 0; $i + 1 < count($cells); $i += 2) {
				$pairs[] = array($cells[$i], $this->parseAmount($cells[$i + 1]));
			}
		}
		return $pairs;
	}

	private function extractChance(string $text): int
	{
		if (preg_match('/:\s*(\d+)\s*%/u', $text, $match)) {
			return (int) $match[1];
		}
		return 0;
	}

	private function isDestroyed(string $text): bool
	{
		return stripos($text, 'détruites') !== false || stripos($text, 'destroyed') !== false;
	}
}
