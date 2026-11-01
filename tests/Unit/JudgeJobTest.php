<?php

namespace Tests\Unit;

use App\Entities\Problem;
use App\Status;
use App\Task\JudgeJob;
use Tests\TestCase;

class JudgeJobTest extends TestCase
{
    public function testToJsonIncludesQueuePayloadFields()
    {
        $user = $this->createUser();
        $problem = $this->createProblem([
            'time_limit' => 2000,
            'memory_limit' => 512,
            'spj' => 1,
        ]);
        $solution = $this->createSolution($user, $problem, [
            'language' => 2,
            'result' => Status::PENDING,
        ]);
        $solution->source()->create(['code' => 'print(1)']);

        $job = new JudgeJob($solution->load(['problem', 'source']));
        $payload = json_decode($job->toJson(), true);

        $this->assertSame($problem->id, $payload['problem_id']);
        $this->assertSame(2000, $payload['time_limit']);
        $this->assertSame(512, $payload['memory_limit']);
        $this->assertSame(1, $payload['is_special']);
        $this->assertSame($solution->id, $payload['solution_id']);
        $this->assertSame('print(1)', $payload['code']);
        $this->assertSame(2, $payload['language']);
    }

    public function testAsQueueInfoUsesUpdatedProblemWhenSet()
    {
        $user = $this->createUser();
        $problem = $this->createProblem(['time_limit' => 1000]);
        $other = $this->createProblem(['time_limit' => 3000]);
        $solution = $this->createSolution($user, $problem);
        $solution->source()->create(['code' => 'x']);

        $job = new JudgeJob($solution->load(['problem', 'source']));
        $job->setProblem(Problem::query()->find($other->id));

        $this->assertSame(3000, $job->asQueueInfo()['time_limit']);
    }
}
