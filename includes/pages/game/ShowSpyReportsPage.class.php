<?php

class ShowSpyReportsPage extends AbstractGamePage
{
	public static $requireModule = MODULE_MESSAGES;

	function __construct()
	{
		parent::__construct();
	}

	function show(): void
	{
		$this->renderTable($this->readFilters(), $this->readExtraIds());
	}

	function delete(): void
	{
		$reportId = HTTP::_GP('id', 0);
		$ownerId = $this->intelOwnerId();
		if ($reportId > 0) {
			SpyReportStore::deleteById($ownerId, $reportId);
		}

		$filters = $this->readFilters();
		$extraIds = $this->readExtraIds();
		$extraIds = array_values(array_filter($extraIds, static function ($id) use ($reportId) {
			return (int) $id !== (int) $reportId;
		}));
		if (AJAX_REQUEST) {
			$this->renderTable($filters, $extraIds);
			return;
		}
		$this->redirectTo($this->pageUrl($filters, $extraIds, $extraIds === array()));
	}

	function deleteAll(): void
	{
		global $USER;
		$ownerId = $this->intelOwnerId();
		if ($ownerId === (int) $USER['id']) {
			SpyReportStore::deleteByOwner($ownerId);
		}

		$filters = $this->readFilters();
		if (AJAX_REQUEST) {
			$this->renderTable($filters, array());
			return;
		}
		$this->redirectTo($this->pageUrl($filters, array(), true));
	}

	function deleteOld(): void
	{
		global $USER;
		$ownerId = $this->intelOwnerId();
		if ($ownerId === (int) $USER['id']) {
			SpyReportStore::deleteOlderThan($ownerId, TIMESTAMP - 86400);
		}

		$filters = $this->readFilters();
		$extraIds = $this->readExtraIds();
		if (AJAX_REQUEST) {
			$this->renderTable($filters, $extraIds);
			return;
		}
		$this->redirectTo($this->pageUrl($filters, $extraIds, $extraIds === array()));
	}

	/**
	 * @param array<string, mixed> $filters
	 * @param list<int> $extraIds
	 */
	private function renderTable(array $filters, array $extraIds): void
	{
		global $LNG, $USER, $reslist;

		$ownerId = $this->intelOwnerId();
		(new SpyReportMigrator())->migrateOwner($ownerId);
		$latest = SpyReportStore::listLatestByTarget($ownerId, $filters);
		$reports = $this->visibleReports($ownerId, $filters, $extraIds, $latest);
		$newIds = SpyReportStore::unviewedIds($ownerId);
		$newIdSet = array_fill_keys($newIds, true);

		if ($ownerId === (int) $USER['id']) {
			SpyReportStore::markAllViewed($ownerId);
			$this->assign(array(
				'spy_unseen' => 0,
			));
		}

		$history = array();
		foreach ($reports as $report) {
			$key = $this->targetKey($report);
			if (isset($history[$key])) {
				continue;
			}
			$history[$key] = SpyReportStore::listHistory(
				$ownerId,
				(int) $report['galaxy'],
				(int) $report['system'],
				(int) $report['planet'],
				(int) $report['planet_type']
			);
		}

		$classMap = SpyReportStore::classElementMap($reslist);
		$classOrder = array(900, 200, 400, 0, 100);
		$payloads = array();
		foreach ($reports as $index => $report) {
			$payloads[$index] = SpyReportStore::decodePayload((string) $report['payload']);
		}

		$sections = array();
		foreach ($classOrder as $classId) {
			$visible = false;
			foreach ($payloads as $payload) {
				if (array_key_exists($classId, $payload)) {
					$visible = true;
					break;
				}
			}
			if (!$visible) {
				continue;
			}

			$rows = array();
			$elementIds = isset($classMap[$classId]) ? $classMap[$classId] : array();
			foreach ($elementIds as $elementId) {
				$values = array();
				$allZero = true;
				$anyPresent = false;
				foreach ($payloads as $payload) {
					if (!array_key_exists($classId, $payload)) {
						$values[] = null;
						continue;
					}
					$anyPresent = true;
					if (array_key_exists($elementId, $payload[$classId])) {
						$amount = $payload[$classId][$elementId];
						$values[] = $amount;
						if ($amount != 0) {
							$allZero = false;
						}
					} else {
						$values[] = 0;
					}
				}
				if (!$anyPresent) {
					continue;
				}
				$rows[] = array(
					'elementID'	=> $elementId,
					'values'	=> $values,
					'isZero'	=> $allZero,
				);
			}

			$sections[] = array(
				'classID'	=> $classId,
				'rows'		=> $rows,
			);
		}

		$latestByTarget = array();
		foreach ($latest as $row) {
			$latestByTarget[$this->targetKey($row)] = (int) $row['report_id'];
		}

		$columns = array();
		$showSnapshotRow = false;
		foreach ($reports as $index => $report) {
			$key = $this->targetKey($report);
			$reportId = (int) $report['report_id'];
			$latestId = isset($latestByTarget[$key]) ? $latestByTarget[$key] : $reportId;
			$isExtra = $reportId !== $latestId;
			$simQuery = $this->simulatorQuery($payloads[$index]);
			$hideExtras = array_values(array_filter($extraIds, static function ($id) use ($reportId) {
				return (int) $id !== $reportId;
			}));
			$columnHistory = $this->formatHistory(
				isset($history[$key]) ? $history[$key] : array(),
				$reportId
			);
			if ($isExtra || count($columnHistory) > 1) {
				$showSnapshotRow = true;
			}
			$columns[] = array(
				'report_id'		=> $reportId,
				'latest_id'		=> $latestId,
				'target_key'	=> $key,
				'is_new'		=> isset($newIdSet[$reportId]),
				'is_extra'		=> $isExtra,
				'name'			=> $report['planet_name'],
				'username'		=> $report['target_username'],
				'galaxy'		=> (int) $report['galaxy'],
				'system'		=> (int) $report['system'],
				'planet'		=> (int) $report['planet'],
				'planet_type'	=> (int) $report['planet_type'],
				'time'			=> _date($LNG['php_tdformat'], (int) $report['report_time'], $USER['timezone']),
				'time_date'		=> _date($LNG['php_dateformat'], (int) $report['report_time'], $USER['timezone']),
				'time_clock'	=> _date($LNG['php_timeformat'], (int) $report['report_time'], $USER['timezone']),
				'attack_url'	=> 'game.php?page=fleetTable&galaxy=' . (int) $report['galaxy']
					. '&system=' . (int) $report['system']
					. '&planet=' . (int) $report['planet']
					. '&planettype=' . (int) $report['planet_type']
					. '&target_mission=1',
				'sim_url'		=> $simQuery !== ''
					? 'game.php?page=battleSimulator&' . $simQuery
					: '',
				'hide_url'		=> $this->pageUrl($filters, $hideExtras, true),
				'history'		=> $columnHistory,
			);
		}

		$isBattleSim = ENABLE_SIMULATOR_LINK == true && isModuleAvailable(MODULE_SIMULATOR);

		$this->assign(array(
			'colspan'		=> count($columns) + 1,
			'columns'		=> $columns,
			'sections'		=> $sections,
			'filters'		=> $filters,
			'extraParam'	=> implode(',', $extraIds),
			'queryBase'		=> $this->filterQuery($filters, array()),
			'isBattleSim'	=> $isBattleSim,
			'isOwnIntel'	=> $ownerId === (int) $USER['id'],
			'showSnapshotRow'	=> $showSnapshotRow,
			'newReportIds'	=> $newIds,
		));

		if (AJAX_REQUEST) {
			$this->display('page.spyReports.table.tpl');
			return;
		}

		$this->display('page.spyReports.default.tpl');
	}

	/**
	 * @param array<string, mixed> $filters
	 * @param list<int> $extraIds
	 * @return list<array<string, mixed>>
	 */
	private function visibleReports(int $ownerId, array $filters, array $extraIds, array $latest): array
	{
		$byTarget = array();
		foreach ($latest as $row) {
			$byTarget[$this->targetKey($row)] = $row;
		}

		if ($extraIds) {
			foreach (SpyReportStore::findByIds($ownerId, $extraIds) as $row) {
				$byTarget[$this->targetKey($row)] = $row;
			}
		}

		$reports = array_values($byTarget);
		$sort = $filters['sort'];
		$direction = $filters['order'] === 'asc' ? 1 : -1;
		$origin = $this->currentOrigin();
		usort($reports, static function (array $a, array $b) use ($sort, $direction, $origin) {
			if ($sort === 'coords') {
				$cmp = (self::reportDistance($origin, $a) <=> self::reportDistance($origin, $b))
					?: ((int) $a['planet_type'] <=> (int) $b['planet_type']);
			} elseif ($sort === 'name') {
				$cmp = strcasecmp((string) $a['planet_name'], (string) $b['planet_name']);
			} else {
				$cmp = ((int) $a['report_time'] <=> (int) $b['report_time'])
					?: ((int) $a['report_id'] <=> (int) $b['report_id']);
			}
			return $cmp * $direction;
		});

		return $reports;
	}

	/**
	 * @return array<string, mixed>
	 */
	private function readFilters(): array
	{
		$from = HTTP::_GP('from', '', true);
		$to = HTTP::_GP('to', '', true);
		$sort = HTTP::_GP('sort', 'time');
		if ($sort !== 'coords' && $sort !== 'name') {
			$sort = 'time';
		}
		$defaultOrder = $sort === 'coords' ? 'asc' : 'desc';
		$userFilter = 0;
		global $USER;
		if ((int) $USER['authlevel'] >= AUTH_ADM) {
			$userFilter = HTTP::_GP('user', 0);
		}
		return array(
			'sort'			=> $sort,
			'order'			=> HTTP::_GP('order', $defaultOrder),
			'galaxy'		=> HTTP::_GP('galaxy', 0),
			'planet_type'	=> HTTP::_GP('planet_type', 0),
			'from'			=> $from !== '' ? strtotime($from . ' 00:00:00') : 0,
			'to'			=> $to !== '' ? strtotime($to . ' 23:59:59') : 0,
			'from_value'	=> $from,
			'to_value'		=> $to,
			'player'		=> HTTP::_GP('player', '', true),
			'user'			=> $userFilter,
		);
	}

	/**
	 * @return list<int>
	 */
	private function readExtraIds(): array
	{
		$raw = HTTP::_GP('extra', '', true);
		if ($raw === '') {
			return array();
		}
		$ids = array();
		foreach (explode(',', $raw) as $id) {
			$id = (int) $id;
			if ($id > 0) {
				$ids[$id] = $id;
			}
		}
		return array_values($ids);
	}

	/**
	 * @param array<string, mixed> $filters
	 * @param list<int> $extraIds
	 */
	private function filterQuery(array $filters, array $extraIds): string
	{
		$params = array();
		if ($filters['sort'] !== 'time') {
			$params['sort'] = $filters['sort'];
		}
		$defaultOrder = $filters['sort'] === 'coords' ? 'asc' : 'desc';
		if ($filters['order'] !== $defaultOrder) {
			$params['order'] = $filters['order'];
		}
		if (!empty($filters['galaxy'])) {
			$params['galaxy'] = (int) $filters['galaxy'];
		}
		if (!empty($filters['planet_type'])) {
			$params['planet_type'] = (int) $filters['planet_type'];
		}
		if (!empty($filters['from_value'])) {
			$params['from'] = $filters['from_value'];
		}
		if (!empty($filters['to_value'])) {
			$params['to'] = $filters['to_value'];
		}
		if ($filters['player'] !== '') {
			$params['player'] = $filters['player'];
		}
		if (!empty($filters['user'])) {
			$params['user'] = (int) $filters['user'];
		}
		if ($extraIds) {
			$params['extra'] = implode(',', $extraIds);
		}
		return http_build_query($params);
	}

	/**
	 * @param array<string, mixed> $filters
	 * @param list<int> $extraIds
	 */
	private function pageUrl(array $filters, array $extraIds, bool $forceExtra = false): string
	{
		$query = $this->filterQuery($filters, $extraIds);
		if ($forceExtra && $extraIds === array() && strpos($query, 'extra=') === false) {
			$query .= ($query !== '' ? '&' : '') . 'extra=';
		}
		return 'game.php?page=spyReports' . ($query !== '' ? '&' . $query : '');
	}

	private function intelOwnerId(): int
	{
		global $USER;
		$self = (int) $USER['id'];
		if ((int) $USER['authlevel'] < AUTH_ADM) {
			return $self;
		}
		$other = HTTP::_GP('user', 0);
		return $other > 0 ? $other : $self;
	}

	/**
	 * @param list<array<string, mixed>> $rows
	 * @return list<array{id:int,label:string,selected:bool}>
	 */
	private function formatHistory(array $rows, int $currentId): array
	{
		global $LNG, $USER;
		$out = array();
		foreach ($rows as $row) {
			$id = (int) $row['report_id'];
			$out[] = array(
				'id'		=> $id,
				'label'		=> _date($LNG['php_tdformat'], (int) $row['report_time'], $USER['timezone']),
				'selected'	=> $id === $currentId,
			);
		}
		return $out;
	}

	private function targetKey(array $report): string
	{
		return $report['galaxy'] . ':' . $report['system'] . ':' . $report['planet'] . ':' . $report['planet_type'];
	}

	/**
	 * @return array{0:int,1:int,2:int}
	 */
	private function currentOrigin(): array
	{
		global $PLANET;
		if (!is_array($PLANET) || !isset($PLANET['galaxy'], $PLANET['system'], $PLANET['planet'])) {
			return array(1, 1, 1);
		}
		return array((int) $PLANET['galaxy'], (int) $PLANET['system'], (int) $PLANET['planet']);
	}

	/**
	 * @param array{0:int,1:int,2:int} $origin
	 * @param array<string, mixed> $report
	 */
	private static function reportDistance(array $origin, array $report): int
	{
		return (int) FleetFunctions::GetTargetDistance($origin, array(
			(int) $report['galaxy'],
			(int) $report['system'],
			(int) $report['planet'],
		));
	}

	/**
	 * @param array<int, array<int, int|float>> $payload
	 */
	private function simulatorQuery(array $payload): string
	{
		$parts = array();
		foreach ($payload as $elements) {
			foreach ($elements as $elementId => $amount) {
				$parts[] = 'im[' . (int) $elementId . ']=' . (int) $amount;
			}
		}
		return implode('&', $parts);
	}
}
