<?php

namespace Risistar\Tests\Unit;

use BuildFunctions;

class GetFactorsTest extends UnitTestCase
{
    private const TECH1_ID = 1115;
    private const TECH2_ID = 1116;
    private const WAR3_ID = 1131;
    private const TIME3_ID = 1138;

    public static function setUpBeforeClass(): void
    {
        self::bootConstants();
        require_once self::rootPath() . 'includes/classes/ArrayUtil.class.php';
        require_once self::rootPath() . 'includes/classes/class.BuildFunctions.php';
        require_once self::rootPath() . 'includes/GeneralFunctions.php';
    }

    protected function setUp(): void
    {
        $GLOBALS['reslist'] = [
            'bonus' => [self::TECH1_ID, self::TECH2_ID, self::WAR3_ID, self::TIME3_ID],
            'dmfunc' => [],
        ];
        $GLOBALS['resource'] = [
            self::TECH1_ID => 'achievements_tech1',
            self::TECH2_ID => 'achievements_tech2',
            self::WAR3_ID => 'achievements_war3',
            self::TIME3_ID => 'achievements_time3',
        ];
        $GLOBALS['pricelist'] = [
            self::TECH1_ID => ['bonus' => self::bonus(['Resource' => 0.01])],
            self::TECH2_ID => ['bonus' => self::bonus(['Resource' => 0.02])],
            self::WAR3_ID => ['bonus' => self::bonus([
                'Attack' => 0.10,
                'Defensive' => 0.10,
                'Shield' => 0.10,
            ])],
            self::TIME3_ID => ['bonus' => self::bonus(['ResearchTime' => -0.05])],
        ];
    }

    public function testUnlockedWarAchievementAddsCombatFactors(): void
    {
        $factor = getFactors($this->userWith([
            'achievements_war3' => 1,
        ]));

        $this->assertEqualsWithDelta(0.10, $factor['Attack'], 0.00001);
        $this->assertEqualsWithDelta(0.10, $factor['Defensive'], 0.00001);
        $this->assertEqualsWithDelta(0.10, $factor['Shield'], 0.00001);
        $this->assertEqualsWithDelta(0.0, $factor['Resource'], 0.00001);
    }

    public function testTechAchievementAddsProductionFactor(): void
    {
        $factor = getFactors($this->userWith([
            'achievements_tech1' => 1,
        ]));

        $this->assertEqualsWithDelta(0.01, $factor['Resource'], 0.00001);
        $this->assertEqualsWithDelta(0.0, $factor['ResearchTime'], 0.00001);
    }

    public function testTechAchievementsStackProductionBonuses(): void
    {
        $factor = getFactors($this->userWith([
            'achievements_tech1' => 1,
            'achievements_tech2' => 1,
        ]));

        $this->assertEqualsWithDelta(0.03, $factor['Resource'], 0.00001);
    }

    public function testTimeAchievementShortensResearchTime(): void
    {
        $factor = getFactors($this->userWith([
            'achievements_time3' => 1,
        ]));

        $this->assertEqualsWithDelta(-0.05, $factor['ResearchTime'], 0.00001);
    }

    public function testLockedAchievementDoesNotChangeFactors(): void
    {
        $factor = getFactors($this->userWith([]));

        $this->assertEqualsWithDelta(0.0, $factor['Attack'], 0.00001);
        $this->assertEqualsWithDelta(0.0, $factor['Resource'], 0.00001);
        $this->assertEqualsWithDelta(0.0, $factor['ResearchTime'], 0.00001);
    }

    /**
     * @param array<string, int> $flags
     * @return array<string, int>
     */
    private function userWith(array $flags): array
    {
        return array_merge([
            'id' => 1,
            'achievements_tech1' => 0,
            'achievements_tech2' => 0,
            'achievements_war3' => 0,
            'achievements_time3' => 0,
        ], $flags);
    }

    /**
     * @param array<string, float> $overrides
     * @return array<string, array{0: float, 1: int}>
     */
    private static function bonus(array $overrides): array
    {
        $bonus = [];
        foreach (BuildFunctions::getBonusList() as $key) {
            $bonus[$key] = [0, 0];
        }
        foreach ($overrides as $key => $value) {
            $bonus[$key] = [$value, 0];
        }

        return $bonus;
    }
}
