<?php

namespace Tests\Concerns;

use App\Entities\CompileInfo;
use App\Entities\Contest;
use App\Entities\Judger;
use App\Entities\Problem;
use App\Entities\RuntimeInfo;
use App\Entities\Solution;
use App\Entities\Topic;
use App\Entities\User;
use App\Status;
use Database\Seeders\RoleTableSeeder;

trait CreatesTestEntities
{
    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    protected function createVerifiedUser(array $attributes = []): User
    {
        return $this->createUser(array_merge(['email_verified_at' => now()], $attributes));
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

    protected function attachProblemToContest(Contest $contest, Problem $problem, int $order = 0, ?string $title = null): void
    {
        $contest->problems()->attach($problem->id, [
            'order' => $order,
            'title' => $title ?? $problem->title,
        ]);
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

    protected function attachCompileInfo(Solution $solution, string $content = 'compilation failed'): CompileInfo
    {
        return CompileInfo::query()->create([
            'solution_id' => $solution->id,
            'content' => $content,
        ]);
    }

    protected function attachRuntimeInfo(Solution $solution, string $content = 'segmentation fault'): RuntimeInfo
    {
        return RuntimeInfo::query()->create([
            'solution_id' => $solution->id,
            'content' => $content,
        ]);
    }

    protected function createContestSolution(User $user, Problem $problem, Contest $contest, array $attributes = []): Solution
    {
        return $this->createSolution($user, $problem, array_merge([
            'contest_id' => $contest->id,
        ], $attributes));
    }

    protected function createTopic(User $user, array $attributes = []): Topic
    {
        return Topic::query()->create(array_merge([
            'user_id' => $user->id,
            'title' => 'Topic '.fake()->unique()->word(),
            'content' => 'Topic body with enough detail for tests.',
            'contest_id' => 0,
            'problem_id' => null,
        ], $attributes));
    }
}
