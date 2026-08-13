<?php

namespace Tests\Feature;

use App\Entities\Problem;
use App\Entities\Contest;
use Tests\TestCase;

class AdminWriteTest extends TestCase
{
    private function problemPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Admin Created Problem',
            'description' => 'problem description',
            'input' => 'input format',
            'output' => 'output format',
            'sample_input' => '1',
            'sample_output' => '1',
            'time_limit' => 1000,
            'memory_limit' => 256,
            'status' => Problem::ST_NORMAL,
            'memo' => 'admin memo',
            'spj' => 0,
        ], $overrides);
    }

    public function testAdminCanCreateProblem()
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->postJson('/admin/problems', $this->problemPayload());

        $response->assertSuccessful();
        $this->assertDatabaseHas('problems', [
            'title' => 'Admin Created Problem',
        ]);
    }

    public function testAdminCanUpdateProblem()
    {
        $admin = $this->createAdminUser();
        $problem = $this->createProblem(['title' => 'Old Title']);

        $response = $this->actingAs($admin)->putJson('/admin/problems/'.$problem->id, [
            'title' => 'Updated Title',
        ]);

        $response->assertSuccessful();
        $this->assertDatabaseHas('problems', [
            'id' => $problem->id,
            'title' => 'Updated Title',
        ]);
    }

    public function testAdminCanDeleteProblem()
    {
        $admin = $this->createAdminUser();
        $problem = $this->createProblem(['title' => 'To Delete']);

        $response = $this->actingAs($admin)->deleteJson('/admin/problems/'.$problem->id);

        $response->assertSuccessful();
        $this->assertSoftDeleted('problems', ['id' => $problem->id]);
    }

    public function testNonAdminCannotCreateProblem()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson('/admin/problems', $this->problemPayload());

        $response->assertUnauthorized();
    }

    public function testAdminCanCreatePrivateContestWithProblemsAndUsers()
    {
        $admin = $this->createAdminUser();
        $user = $this->createUser();
        $problem = $this->createProblem(['title' => 'Contest Problem']);

        $response = $this->actingAs($admin)->postJson('/admin/contests', [
            'title' => 'Private Contest',
            'description' => 'contest description',
            'private' => Contest::PRIVATE,
            'status' => Contest::ST_NORMAL,
            'start_time' => now()->subHour()->toDateTimeString(),
            'end_time' => now()->addHour()->toDateTimeString(),
            'problem_list' => [$problem->id],
            'user_list' => [$user->id],
        ]);

        $response->assertSuccessful();

        $contest = Contest::query()->where('title', 'Private Contest')->first();
        $this->assertNotNull($contest);
        $this->assertDatabaseHas('contest_user', [
            'contest_id' => $contest->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('contest_problem', [
            'contest_id' => $contest->id,
            'problem_id' => $problem->id,
            'title' => 'Contest Problem',
            'order' => 0,
        ]);
    }
}
