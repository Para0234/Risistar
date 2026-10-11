<?php

class SpyReportStore
{
	/**
	 * @param array<string, mixed> $data
	 */
	public static function insert(array $data): int
	{
		$db = Database::get();
		$sql = 'INSERT INTO %%SPY_REPORTS%% SET
			message_id		= :messageId,
			owner_id		= :ownerId,
			universe		= :universe,
			report_time		= :reportTime,
			viewed			= :viewed,
			galaxy			= :galaxy,
			`system`		= :system,
			planet			= :planet,
			planet_type		= :planetType,
			planet_id		= :planetId,
			planet_name		= :planetName,
			target_user_id	= :targetUserId,
			target_username	= :targetUsername,
			target_chance	= :targetChance,
			spy_destroyed	= :spyDestroyed,
			payload			= :payload;';

		$db->insert($sql, array(
			':messageId'		=> $data['message_id'] ?? null,
			':ownerId'			=> (int) $data['owner_id'],
			':universe'			=> (int) $data['universe'],
			':reportTime'		=> (int) $data['report_time'],
			':viewed'			=> (int) ($data['viewed'] ?? 0),
			':galaxy'			=> (int) $data['galaxy'],
			':system'			=> (int) $data['system'],
			':planet'			=> (int) $data['planet'],
			':planetType'		=> (int) $data['planet_type'],
			':planetId'			=> $data['planet_id'] ?? null,
			':planetName'		=> (string) $data['planet_name'],
			':targetUserId'		=> $data['target_user_id'] ?? null,
			':targetUsername'	=> (string) ($data['target_username'] ?? ''),
			':targetChance'		=> (int) ($data['target_chance'] ?? 0),
			':spyDestroyed'		=> (int) ($data['spy_destroyed'] ?? 0),
			':payload'			=> self::encodePayload($data['payload'] ?? array()),
		));

		return (int) $db->lastInsertId();
	}

	/**
	 * @param array<int|string, array<int|string, int|float>> $payload
	 */
	public static function encodePayload(array $payload): string
	{
		$normalized = array();
		foreach ($payload as $classId => $elements) {
			if (!is_array($elements)) {
				continue;
			}
			$row = array();
			foreach ($elements as $elementId => $amount) {
				$row[(string) (int) $elementId] = is_numeric($amount) ? 0 + $amount : 0;
			}
			$normalized[(string) (int) $classId] = $row;
		}
		$json = json_encode($normalized);
		return $json === false ? '{}' : $json;
	}

	/**
	 * @return array<int, array<int, int|float>>
	 */
	public static function decodePayload(string $json): array
	{
		$decoded = json_decode($json, true);
		if (!is_array($decoded)) {
			return array();
		}
		$payload = array();
		foreach ($decoded as $classId => $elements) {
			if (!is_array($elements)) {
				continue;
			}
			$row = array();
			foreach ($elements as $elementId => $amount) {
				$row[(int) $elementId] = is_numeric($amount) ? 0 + $amount : 0;
			}
			$payload[(int) $classId] = $row;
		}
		return $payload;
	}

	/**
	 * Metal value of a resource stock at the merchant rate 4:2:1
	 * (1 crystal = 2 metal, 1 deuterium = 4 metal). Energy is ignored.
	 *
	 * @param array<int|string, int|float> $resources
	 */
	public static function metalEquivalent(array $resources): float
	{
		$metal = isset($resources[901]) ? (float) $resources[901] : 0.0;
		$crystal = isset($resources[902]) ? (float) $resources[902] : 0.0;
		$deuterium = isset($resources[903]) ? (float) $resources[903] : 0.0;
		return $metal + ($crystal * 2) + ($deuterium * 4);
	}

	public static function reportMetalEquivalent(string $payload): float
	{
		$decoded = self::decodePayload($payload);
		if (!isset($decoded[900]) || !is_array($decoded[900])) {
			return 0.0;
		}
		return self::metalEquivalent($decoded[900]);
	}

	public static function countUnviewed(int $ownerId): int
	{
		$sql = 'SELECT COUNT(*) as state FROM %%SPY_REPORTS%% WHERE owner_id = :ownerId AND viewed = 0;';
		return (int) Database::get()->selectSingle($sql, array(
			':ownerId' => $ownerId,
		), 'state');
	}

	/**
	 * @return list<int>
	 */
	public static function unviewedIds(int $ownerId): array
	{
		$sql = 'SELECT report_id FROM %%SPY_REPORTS%% WHERE owner_id = :ownerId AND viewed = 0;';
		$rows = Database::get()->select($sql, array(
			':ownerId' => $ownerId,
		));
		$ids = array();
		foreach ($rows as $row) {
			$ids[] = (int) $row['report_id'];
		}
		return $ids;
	}

	public static function markAllViewed(int $ownerId): void
	{
		$sql = 'UPDATE %%SPY_REPORTS%% SET viewed = 1 WHERE owner_id = :ownerId AND viewed = 0;';
		Database::get()->update($sql, array(
			':ownerId' => $ownerId,
		));
	}

	public static function deleteById(int $ownerId, int $reportId): bool
	{
		self::deleteReports($ownerId, 'AND report_id = :reportId', array(
			':reportId' => $reportId,
		));
		return true;
	}

	public static function deleteByOwner(int $ownerId): void
	{
		self::deleteReports($ownerId, '', array());
	}

	public static function deleteOlderThan(int $ownerId, int $beforeTime): void
	{
		self::deleteReports($ownerId, 'AND report_time < :beforeTime', array(
			':beforeTime' => $beforeTime,
		));
	}

	public static function existsForMessage(int $messageId): bool
	{
		$sql = 'SELECT report_id FROM %%SPY_REPORTS%% WHERE message_id = :messageId;';
		$row = Database::get()->selectSingle($sql, array(
			':messageId' => $messageId,
		));
		return !empty($row);
	}

	/**
	 * @param array<int, int> $reportIds
	 * @return list<array<string, mixed>>
	 */
	public static function findByIds(int $ownerId, array $reportIds): array
	{
		$ids = array();
		foreach ($reportIds as $id) {
			$id = (int) $id;
			if ($id > 0) {
				$ids[$id] = $id;
			}
		}
		if (!$ids) {
			return array();
		}
		$in = implode(',', $ids);
		$sql = 'SELECT * FROM %%SPY_REPORTS%% WHERE owner_id = :ownerId AND report_id IN (' . $in . ');';
		return Database::get()->select($sql, array(
			':ownerId' => $ownerId,
		));
	}

	/**
	 * @param array<string, mixed> $filters
	 * @return list<array<string, mixed>>
	 */
	public static function listLatestByTarget(int $ownerId, array $filters = array()): array
	{
		$where = array('owner_id = :ownerId');
		$params = array(':ownerId' => $ownerId);

		if (!empty($filters['galaxy'])) {
			$where[] = 'galaxy = :galaxy';
			$params[':galaxy'] = (int) $filters['galaxy'];
		}
		if (!empty($filters['planet_type'])) {
			$where[] = 'planet_type = :planetType';
			$params[':planetType'] = (int) $filters['planet_type'];
		}
		if (!empty($filters['from'])) {
			$where[] = 'report_time >= :fromTime';
			$params[':fromTime'] = (int) $filters['from'];
		}
		if (!empty($filters['to'])) {
			$where[] = 'report_time <= :toTime';
			$params[':toTime'] = (int) $filters['to'];
		}
		if (!empty($filters['player'])) {
			$where[] = 'target_username LIKE :player';
			$params[':player'] = '%' . $filters['player'] . '%';
		}

		$whereSql = implode(' AND ', $where);
		$orderSql = self::orderSql(isset($filters['sort']) ? (string) $filters['sort'] : 'time', isset($filters['order']) ? (string) $filters['order'] : 'desc');

		$sql = 'SELECT r.* FROM %%SPY_REPORTS%% r
			INNER JOIN (
				SELECT sr.galaxy, sr.system, sr.planet, sr.planet_type, MAX(sr.report_id) AS report_id
				FROM %%SPY_REPORTS%% sr
				INNER JOIN (
					SELECT galaxy, `system`, planet, planet_type, MAX(report_time) AS report_time
					FROM %%SPY_REPORTS%%
					WHERE ' . $whereSql . '
					GROUP BY galaxy, `system`, planet, planet_type
				) latest ON latest.galaxy = sr.galaxy
					AND latest.system = sr.system
					AND latest.planet = sr.planet
					AND latest.planet_type = sr.planet_type
					AND latest.report_time = sr.report_time
				WHERE sr.owner_id = ' . (int) $ownerId . '
				GROUP BY sr.galaxy, sr.system, sr.planet, sr.planet_type
			) pick ON pick.report_id = r.report_id
			ORDER BY ' . $orderSql . ';';

		return Database::get()->select($sql, $params);
	}

	/**
	 * @return list<array<string, mixed>>
	 */
	public static function listHistory(int $ownerId, int $galaxy, int $system, int $planet, int $planetType): array
	{
		$sql = 'SELECT report_id, report_time FROM %%SPY_REPORTS%%
			WHERE owner_id = :ownerId AND galaxy = :galaxy AND `system` = :system AND planet = :planet AND planet_type = :planetType
			ORDER BY report_time DESC, report_id DESC;';
		return Database::get()->select($sql, array(
			':ownerId'		=> $ownerId,
			':galaxy'		=> $galaxy,
			':system'		=> $system,
			':planet'		=> $planet,
			':planetType'	=> $planetType,
		));
	}

	/**
	 * @param array<int, int> $amounts
	 * @param array<int, int> $classIds
	 * @return array<int, array<int, int|float>>
	 */
	public static function groupAmountsByClass(array $amounts, array $classIds, array $reslist): array
	{
		$map = self::classElementMap($reslist);
		$payload = array();
		foreach ($classIds as $classId) {
			$classId = (int) $classId;
			$payload[$classId] = array();
			if (!isset($map[$classId])) {
				continue;
			}
			foreach ($map[$classId] as $elementId) {
				if (array_key_exists($elementId, $amounts)) {
					$payload[$classId][$elementId] = $amounts[$elementId];
				}
			}
		}
		if ($classIds) {
			return $payload;
		}
		foreach ($map as $classId => $elementIds) {
			$section = array();
			foreach ($elementIds as $elementId) {
				if (array_key_exists($elementId, $amounts)) {
					$section[$elementId] = $amounts[$elementId];
				}
			}
			if ($section) {
				$payload[$classId] = $section;
			}
		}
		return $payload;
	}

	/**
	 * @return array<int, list<int>>
	 */
	public static function classElementMap(array $reslist): array
	{
		$defense = isset($reslist['defense']) ? $reslist['defense'] : array();
		$missile = isset($reslist['missile']) ? $reslist['missile'] : array();
		return array(
			900	=> array_merge(
				isset($reslist['resstype'][1]) ? $reslist['resstype'][1] : array(),
				isset($reslist['resstype'][2]) ? $reslist['resstype'][2] : array()
			),
			200	=> isset($reslist['fleet']) ? $reslist['fleet'] : array(),
			400	=> array_merge($defense, $missile),
			0	=> isset($reslist['build']) ? $reslist['build'] : array(),
			100	=> isset($reslist['tech']) ? $reslist['tech'] : array(),
		);
	}

	private static function orderSql(string $sort, string $order): string
	{
		$direction = strtolower($order) === 'asc' ? 'ASC' : 'DESC';
		switch ($sort) {
			case 'coords':
				return 'r.galaxy ' . $direction . ', r.system ' . $direction . ', r.planet ' . $direction . ', r.planet_type ' . $direction;
			default:
				return 'r.report_time ' . $direction . ', r.report_id ' . $direction;
		}
	}

	/**
	 * @param array<string, int> $extraParams
	 */
	private static function deleteReports(int $ownerId, string $extraWhere, array $extraParams): void
	{
		self::markLinkedMessagesDeleted($ownerId, $extraWhere, $extraParams);
		$sql = 'DELETE FROM %%SPY_REPORTS%% WHERE owner_id = :ownerId ' . $extraWhere . ';';
		Database::get()->delete($sql, array_merge($extraParams, array(
			':ownerId' => $ownerId,
		)));
	}

	/**
	 * Drop the backing inbox row so SpyReportMigrator cannot re-import a deleted report.
	 *
	 * @param array<string, int> $extraParams
	 */
	private static function markLinkedMessagesDeleted(int $ownerId, string $extraWhere, array $extraParams): void
	{
		$sql = 'SELECT message_id FROM %%SPY_REPORTS%%
			WHERE owner_id = :ownerId AND message_id IS NOT NULL ' . $extraWhere . ';';
		$rows = Database::get()->select($sql, array_merge($extraParams, array(
			':ownerId' => $ownerId,
		)));
		$ids = array();
		foreach ($rows as $row) {
			$id = (int) $row['message_id'];
			if ($id > 0) {
				$ids[$id] = $id;
			}
		}
		if (!$ids) {
			return;
		}

		Database::get()->update(
			'UPDATE %%MESSAGES%% SET message_deleted = :deletedAt
			WHERE message_owner = :ownerId AND message_id IN (' . implode(',', $ids) . ');',
			array(
				':deletedAt'	=> defined('TIMESTAMP') ? TIMESTAMP : time(),
				':ownerId'		=> $ownerId,
			)
		);
	}
}
