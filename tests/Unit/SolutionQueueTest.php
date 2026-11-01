<?php

namespace Tests\Unit;

use App\Task\JudgeJob;
use App\Task\SolutionQueue;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\QueueManager;
use Mockery;
use Tests\TestCase;

class SolutionQueueTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testAddPushesJsonPayloadToJudgeConnection()
    {
        config(['hustoj.services.judge.status' => true]);

        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem);
        $solution->source()->create(['code' => 'int main() { return 0; }']);
        $solution->load(['problem', 'source']);

        $queue = Mockery::mock(Queue::class);
        $queue->shouldReceive('pushRaw')
            ->once()
            ->with(Mockery::on(function (string $payload) use ($solution) {
                $data = json_decode($payload, true);

                return $data['solution_id'] === $solution->id
                    && $data['code'] === 'int main() { return 0; }';
            }));

        $manager = Mockery::mock(QueueManager::class);
        $manager->shouldReceive('connection')->with('judge')->andReturn($queue);
        $this->app->instance(QueueManager::class, $manager);

        (new SolutionQueue())->add($solution);

        $this->addToAssertionCount(1);
    }

    public function testAddSkipsPushWhenJudgeServiceDisabled()
    {
        config(['hustoj.services.judge.status' => false]);

        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem);
        $solution->source()->create(['code' => 'void']);

        $queue = Mockery::mock(Queue::class);
        $queue->shouldNotReceive('pushRaw');

        $manager = Mockery::mock(QueueManager::class);
        $manager->shouldReceive('connection')->with('judge')->andReturn($queue);
        $this->app->instance(QueueManager::class, $manager);

        (new SolutionQueue())->add($solution);

        $this->addToAssertionCount(1);
    }
}
