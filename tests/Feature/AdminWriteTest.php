<?php

namespace Tests\Feature;

use App\Entities\Problem;
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
}
