<?php

class SpyReportMigrator
{
	private SpyReportHtmlParser $parser;

	public function __construct(?SpyReportHtmlParser $parser = null)
	{
		$this->parser = $parser ?? new SpyReportHtmlParser();
	}

	public function migrateOwner(int $ownerId, int $limit = 100): int
	{
		$sql = 'SELECT m.message_id, m.message_time, m.message_text, m.message_universe, u.lang
			FROM %%MESSAGES%% m
			INNER JOIN %%USERS%% u ON u.id = m.message_owner
			LEFT JOIN %%SPY_REPORTS%% r ON r.message_id = m.message_id
			WHERE m.message_owner = :ownerId
				AND m.message_type = 0
				AND m.message_deleted IS NULL
				AND m.message_text LIKE :needle
				AND r.report_id IS NULL
			ORDER BY m.message_time ASC
			LIMIT ' . (int) $limit . ';';

		$rows = Database::get()->select($sql, array(
			':ownerId'	=> $ownerId,
			':needle'	=> '%spyRaport%',
		));

		return $this->migrateRows($rows, $ownerId);
	}

	public function migrateAll(int $limit = 100): int
	{
		$sql = 'SELECT m.message_id, m.message_owner, m.message_time, m.message_text, m.message_universe, u.lang
			FROM %%MESSAGES%% m
			INNER JOIN %%USERS%% u ON u.id = m.message_owner
			LEFT JOIN %%SPY_REPORTS%% r ON r.message_id = m.message_id
			WHERE m.message_type = 0
				AND m.message_deleted IS NULL
				AND m.message_text LIKE :needle
				AND r.report_id IS NULL
			ORDER BY m.message_time ASC
			LIMIT ' . (int) $limit . ';';

		$rows = Database::get()->select($sql, array(
			':needle' => '%spyRaport%',
		));

		$count = 0;
		foreach ($rows as $row) {
			$count += $this->migrateRows(array($row), (int) $row['message_owner']);
		}
		return $count;
	}

	/**
	 * @param list<array<string, mixed>> $rows
	 */
	private function migrateRows(array $rows, int $ownerId): int
	{
		global $reslist;

		$count = 0;
		foreach ($rows as $row) {
			$parsed = $this->parser->parse((string) $row['message_text']);
			if ($parsed === null) {
				error_log('[SPY MIGRATE] unparsable message_id=' . $row['message_id']);
				continue;
			}

			$amounts = $parsed['amounts'];
			if (!$amounts) {
				$amounts = $this->amountsFromNames($parsed['named_sections'], (string) $row['lang']);
			}

			$payload = SpyReportStore::groupAmountsByClass(
				$amounts,
				$parsed['class_ids'],
				is_array($reslist) ? $reslist : array()
			);
			foreach ($parsed['named_sections'] as $classId => $pairs) {
				$classId = (int) $classId;
				if (!isset($payload[$classId])) {
					$payload[$classId] = array();
				}
			}

			try {
				SpyReportStore::insert(array(
					'message_id'		=> (int) $row['message_id'],
					'owner_id'			=> $ownerId,
					'universe'			=> (int) $row['message_universe'],
					'report_time'		=> (int) $row['message_time'],
					'viewed'			=> 1,
					'galaxy'			=> $parsed['galaxy'],
					'system'			=> $parsed['system'],
					'planet'			=> $parsed['planet'],
					'planet_type'		=> $parsed['planet_type'],
					'planet_name'		=> $parsed['planet_name'],
					'target_chance'		=> $parsed['target_chance'],
					'spy_destroyed'		=> $parsed['spy_destroyed'],
					'payload'			=> $payload,
				));
				$count++;
			} catch (\Throwable $e) {
				error_log('[SPY MIGRATE] insert failed message_id=' . $row['message_id'] . ' ' . $e->getMessage());
			}
		}

		return $count;
	}

	/**
	 * @param array<int, list<array{0:string,1:int}>> $namedSections
	 * @return array<int, int>
	 */
	private function amountsFromNames(array $namedSections, string $lang): array
	{
		$map = $this->techNameMap($lang);
		$amounts = array();
		foreach ($namedSections as $pairs) {
			foreach ($pairs as $pair) {
				$key = $this->normalizeName($pair[0]);
				if (!isset($map[$key])) {
					continue;
				}
				$amounts[$map[$key]] = $pair[1];
			}
		}
		return $amounts;
	}

	/**
	 * @return array<string, int>
	 */
	private function techNameMap(string $lang): array
	{
		static $cache = array();
		if (isset($cache[$lang])) {
			return $cache[$lang];
		}

		$LNG = new Language($lang);
		$LNG->includeData(array('L18N', 'INGAME', 'TECH', 'CUSTOM'));
		$map = array();
		if (isset($LNG['tech']) && is_array($LNG['tech'])) {
			foreach ($LNG['tech'] as $id => $name) {
				$map[$this->normalizeName((string) $name)] = (int) $id;
			}
		}
		$cache[$lang] = $map;
		return $map;
	}

	private function normalizeName(string $name): string
	{
		$name = html_entity_decode($name, ENT_QUOTES, 'UTF-8');
		$name = preg_replace('/\s+/u', ' ', $name);
		return mb_strtolower(trim((string) $name));
	}
}
