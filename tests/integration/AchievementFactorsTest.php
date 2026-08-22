<?php

namespace Risistar\Tests\Integration;

class AchievementFactorsTest extends IntegrationTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once self::rootPath() . 'includes/classes/class.BuildFunctions.php';
    }

    public function testWarAchievementAddsCombatFactorsFromVars(): void
    {
        $this->requireDatabase();

        $factor = getFactors($this->userWith([
            'achievements_war3' => 1,
        ]));

        $this->assertEqualsWithDelta(0.10, $factor['Attack'], 0.00001);
        $this->assertEqualsWithDelta(0.10, $factor['Defensive'], 0.00001);
        $this->assertEqualsWithDelta(0.10, $factor['Shield'], 0.00001);
    }

    public function testTechAchievementAddsProductionFromVars(): void
    {
        $this->requireDatabase();

        $factor = getFactors($this->userWith([
            'achievements_tech1' => 1,
        ]));

        $this->assertEqualsWithDelta(0.01, $factor['Resource'], 0.00001);
        $this->assertEqualsWithDelta(0.0, $factor['ResearchTime'], 0.00001);
    }

    public function testTechAchievementsStackProductionBonuses(): void
    {
        $this->requireDatabase();

        $factor = getFactors($this->userWith([
            'achievements_tech1' => 1,
            'achievements_tech2' => 1,
            'achievements_tech3' => 1,
            'achievements_tech4' => 1,
        ]));

        $this->assertEqualsWithDelta(0.18, $factor['Resource'], 0.00001);
        $this->assertEqualsWithDelta(0.0, $factor['ResearchTime'], 0.00001);
    }

    /**
     * @param array<string, int> $flags
     * @return array<string, mixed>
     */
    private function userWith(array $flags): array
    {
        global $resource, $reslist;

        $user = ['id' => 1];
        foreach ($reslist['bonus'] as $elementID) {
            $user[$resource[$elementID]] = 0;
        }

        return array_merge($user, $flags);
    }
}
