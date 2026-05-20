<?php

namespace Tests\Unit;

use App\Models\Student;
use App\Services\TeamDistributionService;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * A.2 — TeamDistributionService Unit Tests (U-10 … U-16)
 *
 * generateBalanced() and generateFixed() are pure functions — no DB.
 * getUnassigned() / preview() DB interaction verified via source inspection.
 */
class TeamDistributionServiceTest extends TestCase
{
    private TeamDistributionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TeamDistributionService();
    }

    /** Build a collection of N fake Student stubs with sequential IDs. */
    private function makeStudents(int $count): Collection
    {
        $students = [];
        for ($i = 1; $i <= $count; $i++) {
            $s = new Student();
            $s->id = $i;
            $students[] = $s;
        }
        return collect($students);
    }

    // U-10: 6 students, size 3 → exactly 2 groups of 3
    public function test_U10_generateBalanced_6students_size3_returns_2_groups_of_3(): void
    {
        $groups = $this->service->generateBalanced($this->makeStudents(6), 3);

        $this->assertCount(2, $groups, 'ceil(6/3)=2 teams');
        $this->assertCount(3, $groups[0]);
        $this->assertCount(3, $groups[1]);
    }

    // U-11: 7 students, size 3 → 3 groups, total = 7
    public function test_U11_generateBalanced_7students_size3_returns_3_groups(): void
    {
        $groups = $this->service->generateBalanced($this->makeStudents(7), 3);

        $this->assertCount((int) ceil(7 / 3), $groups, 'ceil(7/3)=3 teams');
        $this->assertEquals(7, array_sum(array_map('count', $groups)), 'All 7 distributed');
    }

    // U-12: generateFixed() 7 students, size 3 → groups=[3,3], remaining=[1]
    public function test_U12_generateFixed_7students_size3_two_full_one_remainder(): void
    {
        $result = $this->service->generateFixed($this->makeStudents(7), 3);

        $this->assertArrayHasKey('groups',    $result);
        $this->assertArrayHasKey('remaining', $result);
        $this->assertCount(2, $result['groups'],    '2 full groups of 3');
        $this->assertCount(3, $result['groups'][0]);
        $this->assertCount(3, $result['groups'][1]);
        $this->assertCount(1, $result['remaining'], '1 leftover student');
    }

    // U-13: generateFixed() exact multiple → no remainder group
    public function test_U13_generateFixed_exact_multiple_no_remainder(): void
    {
        $result = $this->service->generateFixed($this->makeStudents(6), 3);

        $this->assertCount(2, $result['groups']);
        $this->assertEmpty($result['remaining'], 'No remainder for exact multiple');
    }

    // U-14: getUnassigned() queries scoped by level_id AND academic_year_id
    public function test_U14_getUnassigned_scoped_by_level_and_year(): void
    {
        $src = file_get_contents(app_path('Services/TeamDistributionService.php'));

        $this->assertStringContainsString("where('level_id', \$level->id)", $src,
            'U-14: getUnassigned() must scope by level_id');
        $this->assertStringContainsString("where('academic_year_id', \$year->id)", $src,
            'U-14: getUnassigned() must scope by academic_year_id');
        $this->assertStringContainsString('whereDoesntHave', $src,
            'Must exclude already-assigned students');
    }

    // U-15: SoftDeletes on Student means getUnassigned() auto-excludes soft-deleted rows
    public function test_U15_getUnassigned_excludes_soft_deleted(): void
    {
        $src = file_get_contents(app_path('Models/Student.php'));
        $this->assertStringContainsString('SoftDeletes', $src,
            'Student model must use SoftDeletes so Eloquent auto-filters deleted rows');
    }

    // U-16: preview() stores session but does NOT write to teams table
    public function test_U16_preview_uses_session_not_db(): void
    {
        $src = file_get_contents(app_path('Services/TeamDistributionService.php'));

        $this->assertStringContainsString("session([", $src,
            'preview() must store data in session');
        $this->assertStringContainsString("'distribution.groups'", $src);

        // Extract just the preview() method body (approx 60 lines after the declaration)
        $start  = strpos($src, 'public function preview(');
        $body   = substr($src, $start, 1500);

        $this->assertStringNotContainsString('Team::create', $body,
            'preview() must NOT call Team::create');
        $this->assertStringNotContainsString("->insert(", $body,
            'preview() must NOT insert rows directly');
    }

    // Extra: 22 students, size 4 → 6 balanced teams, all 22 distributed
    public function test_balanced_22students_size4_six_teams(): void
    {
        $groups = $this->service->generateBalanced($this->makeStudents(22), 4);

        $this->assertCount(6, $groups);
        $this->assertEquals(22, array_sum(array_map('count', $groups)));
    }

    // Extra: empty input returns empty array
    public function test_generateBalanced_empty_returns_empty(): void
    {
        $this->assertEmpty($this->service->generateBalanced(collect([]), 3));
    }
}
