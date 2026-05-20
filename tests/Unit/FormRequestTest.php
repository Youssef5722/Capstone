<?php

namespace Tests\Unit;

use App\Http\Requests\DistributeTeamsRequest;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\StoreTeamRequestRequest;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * A.4 — Form Request Validation Rules (U-26 … U-33)
 *
 * Uses Validator::make() directly against each FormRequest's rules()
 * array so no HTTP cycle is needed.
 *
 * U-27 needs RefreshDatabase because StoreTeamRequest has an
 * `exists:students,id` rule that hits the DB even in unit style tests.
 * The others are purely rule-structure assertions.
 */
class FormRequestTest extends TestCase
{
    use DatabaseMigrations; // needed only for U-27 (exists:students,id)

    // ── StoreTeamRequest ──────────────────────────────────────────────────────

    // U-26: name nullable, members + leader present → rule structure correct
    public function test_U26_StoreTeamRequest_null_name_is_nullable(): void
    {
        $rules = (new StoreTeamRequest())->rules();

        $this->assertContains('nullable', $rules['name'],
            'U-26: name field must be nullable');
        $this->assertContains('required', $rules['leader_id'],
            'U-26: leader_id must be required');
        $this->assertContains('required', $rules['student_ids'],
            'U-26: student_ids must be required');
    }

    // U-27: empty members array → Fails (min:1 on student_ids)
    public function test_U27_StoreTeamRequest_empty_members_fails(): void
    {
        $v = Validator::make(
            ['name' => 'Alpha', 'leader_id' => 1, 'student_ids' => []],
            (new StoreTeamRequest())->rules()
        );

        $this->assertTrue($v->fails(), 'U-27: Empty student_ids must fail validation');
        $this->assertArrayHasKey('student_ids', $v->errors()->toArray());
    }

    // U-28: leader_id must reference students table (exists rule present)
    public function test_U28_StoreTeamRequest_leader_exists_rule_present(): void
    {
        $rules = (new StoreTeamRequest())->rules();

        $this->assertContains('exists:students,id', $rules['leader_id'],
            'U-28: leader_id must have exists:students,id rule');
    }

    // ── StoreTeamRequestRequest ───────────────────────────────────────────────

    /**
     * Run the after-validation custom hook the same way Laravel does internally.
     * We instantiate the FormRequest, manually configure the validator,
     * then call the protected withValidator() via reflection.
     */
    private function runTeamRequestValidation(array $data): \Illuminate\Validation\Validator
    {
        $formRequest = new StoreTeamRequestRequest();
        // Populate the request data so $this->input() works inside withValidator
        $formRequest->merge($data);

        $v = Validator::make($data, $formRequest->rules());

        // Invoke the protected withValidator hook via reflection
        $ref = new \ReflectionMethod($formRequest, 'withValidator');
        $ref->setAccessible(true);
        $ref->invoke($formRequest, $v);

        $v->passes(); // trigger evaluation
        return $v;
    }

    // U-29: both fields null → Fails (custom after-rule)
    public function test_U29_StoreTeamRequestRequest_both_null_fails(): void
    {
        $v = $this->runTeamRequestValidation([
            'requested_name'  => null,
            'project_idea_id' => null,
        ]);

        $this->assertTrue($v->fails(), 'U-29: Both null must fail');
        $this->assertArrayHasKey('requested_name', $v->errors()->toArray(),
            'U-29: Error must be keyed on requested_name');
    }

    // U-30: name present, project null → Passes custom rule
    public function test_U30_StoreTeamRequestRequest_name_only_passes(): void
    {
        $v = $this->runTeamRequestValidation([
            'requested_name'  => 'New Name',
            'project_idea_id' => null,
        ]);

        $this->assertFalse($v->fails(), 'U-30: Name-only request must pass');
    }

    // U-31: name null, project_idea_id nullable rule present → structure verified
    public function test_U31_StoreTeamRequestRequest_project_only_rule_structure(): void
    {
        $rules = (new StoreTeamRequestRequest())->rules();

        $this->assertContains('nullable', $rules['requested_name'],
            'U-31: requested_name is nullable — allows name-less request');
        $this->assertContains('nullable', $rules['project_idea_id'],
            'U-31: project_idea_id is nullable — allows no-project request');
    }

    // ── DistributeTeamsRequest ────────────────────────────────────────────────

    // U-32: mode not in [balanced, fixed] → Fails
    public function test_U32_DistributeTeamsRequest_invalid_mode_fails(): void
    {
        $v = Validator::make(
            ['mode' => 'random', 'team_size' => 3],
            (new DistributeTeamsRequest())->rules()
        );

        $this->assertTrue($v->fails(), 'U-32: Invalid mode must fail');
        $this->assertArrayHasKey('mode', $v->errors()->toArray());
    }

    // U-33: size < 2 → Fails (min:2)
    public function test_U33_DistributeTeamsRequest_size_below_2_fails(): void
    {
        $v = Validator::make(
            ['mode' => 'balanced', 'team_size' => 1],
            (new DistributeTeamsRequest())->rules()
        );

        $this->assertTrue($v->fails(), 'U-33: Team size < 2 must fail');
        $this->assertArrayHasKey('team_size', $v->errors()->toArray());
    }

    // Extra: valid balanced + size 3 passes
    public function test_distribute_valid_input_passes(): void
    {
        $v = Validator::make(
            ['mode' => 'balanced', 'team_size' => 3],
            (new DistributeTeamsRequest())->rules()
        );
        $this->assertFalse($v->fails());
    }

    // Extra: valid fixed mode passes
    public function test_distribute_fixed_mode_passes(): void
    {
        $v = Validator::make(
            ['mode' => 'fixed', 'team_size' => 4],
            (new DistributeTeamsRequest())->rules()
        );
        $this->assertFalse($v->fails());
    }
}
