<?php

namespace Tests\Concerns;

use App\Entities\Contest;
use App\Entities\Judger;
use App\Entities\Problem;
use App\Entities\Solution;
use App\Entities\User;
use App\Status;
use Database\Seeders\RoleTableSeeder;

trait CreatesTestEntities
{
    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    protected function createAdminUser(array $attributes = []): User
    {
        $this->seed(RoleTableSeeder::class);

        $user = $this->createUser($attributes);
        $user->addRole('admin');

        return $user->fresh();
    }

    protected function createJudger(array $attributes = []): Judger
    {
        return Judger::factory()->create($attributes);
    }

    protected function judgerHeaders(Judger $judger, ?int $timestamp = null): array
    {
        $timestamp ??= time();

        return [
            'Judge-Id' => (string) $judger->id,
            'Token' => md5(sprintf('%s-%d', $judger->code, $timestamp)),
        ];
    }

    protected function createContest(array $attributes = []): Contest
    {
        return Contest::query()->create(array_merge([
            'title' => 'Contest '.fake()->unique()->word(),
            'description' => 'contest description',
            'start_time' => now()->subDay(),
            'end_time' => now()->addDay(),
            'status' => Contest::ST_NORMAL,
            'private' => Contest::PUBLIC,
        ], $attributes));
    }

    protected function createProblem(array $attributes = []): Problem
    {
        return Problem::query()->create(array_merge([
            'title' => 'Problem '.fake()->unique()->word(),
            'description' => 'problem description',
            'input' => 'input format',
            'output' => 'output format',
            'sample_input' => '1',
            'sample_output' => '1',
            'memo' => '',
            'time_limit' => 1000,
            'memory_limit' => 256,
            'status' => Problem::ST_NORMAL,
        ], $attributes));
    }

    protected function createSolution(User $user, Problem $problem, array $attributes = []): Solution
    {
        return Solution::query()->create(array_merge([
            'problem_id' => $problem->id,
            'user_id' => $user->id,
            'order' => 0,
            'language' => 1,
            'code_length' => 100,
            'ip' => '127.0.0.1',
            'result' => Status::PENDING,
        ], $attributes));
    }
}
