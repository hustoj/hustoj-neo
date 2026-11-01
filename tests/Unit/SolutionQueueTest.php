<?php

namespace Tests\Unit;

use App\Services\JudgeTaskToken;
use App\Task\SolutionQueue;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Cache;
use Mockery;
use RuntimeException;
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

        $pushed = null;
        $queue = Mockery::mock(Queue::class);
        $queue->shouldReceive('pushRaw')
            ->once()
            ->with(Mockery::on(function (string $payload) use ($solution, &$pushed) {
                $pushed = json_decode($payload, true);

                return $pushed['solution_id'] === $solution->id
                    && $pushed['code'] === 'int main() { return 0; }'
                    && is_string($pushed['judge_token'] ?? null)
                    && strlen($pushed['judge_token']) === 64;
            }));

        $manager = Mockery::mock(QueueManager::class);
        $manager->shouldReceive('connection')->with('judge')->andReturn($queue);
        $this->app->instance(QueueManager::class, $manager);

        (new SolutionQueue())->add($solution);

        $this->assertIsArray($pushed);
        $this->assertFalse(app(JudgeTaskToken::class)->consume($solution->id, 'wrong-token'));
        $this->assertTrue(app(JudgeTaskToken::class)->consume($solution->id, $pushed['judge_token']));
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

        $this->assertFalse(app(JudgeTaskToken::class)->consume($solution->id, 'any-token'));
    }

    public function testRepeatedAddRotatesTaskTokenAndInvalidatesThePreviousOne()
    {
        config(['hustoj.services.judge.status' => true]);

        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem);
        $solution->source()->create(['code' => 'void']);

        $payloads = [];
        $queue = Mockery::mock(Queue::class);
        $queue->shouldReceive('pushRaw')
            ->twice()
            ->with(Mockery::on(function (string $payload) use (&$payloads) {
                $payloads[] = json_decode($payload, true);

                return true;
            }));

        $manager = Mockery::mock(QueueManager::class);
        $manager->shouldReceive('connection')->with('judge')->andReturn($queue);
        $this->app->instance(QueueManager::class, $manager);

        $solutionQueue = new SolutionQueue();
        $solutionQueue->add($solution);
        $solutionQueue->add($solution);

        $this->assertCount(2, $payloads);
        $this->assertNotSame($payloads[0]['judge_token'], $payloads[1]['judge_token']);
        $this->assertFalse(app(JudgeTaskToken::class)->consume($solution->id, $payloads[0]['judge_token']));
        $this->assertTrue(app(JudgeTaskToken::class)->consume($solution->id, $payloads[1]['judge_token']));
    }

    public function testFailedPushDoesNotLeaveUsableTaskToken()
    {
        config(['hustoj.services.judge.status' => true]);

        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem);
        $solution->source()->create(['code' => 'void']);

        $token = null;
        $queue = Mockery::mock(Queue::class);
        $queue->shouldReceive('pushRaw')
            ->once()
            ->with(Mockery::on(function (string $payload) use (&$token) {
                $token = json_decode($payload, true)['judge_token'];

                return true;
            }))
            ->andThrow(new RuntimeException('queue unavailable'));

        $manager = Mockery::mock(QueueManager::class);
        $manager->shouldReceive('connection')->with('judge')->andReturn($queue);
        $this->app->instance(QueueManager::class, $manager);

        try {
            (new SolutionQueue())->add($solution);
            $this->fail('Expected queue failure to be propagated.');
        } catch (RuntimeException $exception) {
            $this->assertSame('queue unavailable', $exception->getMessage());
        }

        $this->assertIsString($token);
        $this->assertFalse(app(JudgeTaskToken::class)->consume($solution->id, $token));
    }

    public function testFailedRepeatedPushKeepsThePreviouslyQueuedTaskValid()
    {
        config(['hustoj.services.judge.status' => true]);

        $solution = $this->createSolution($this->createUser(), $this->createProblem());
        $solution->source()->create(['code' => 'void']);

        $payloads = [];
        $queue = Mockery::mock(Queue::class);
        $queue->shouldReceive('pushRaw')->twice()->andReturnUsing(function (string $payload) use (&$payloads) {
            $payloads[] = json_decode($payload, true);
            if (count($payloads) === 2) {
                throw new RuntimeException('queue unavailable');
            }

            return 'first-task';
        });

        $manager = Mockery::mock(QueueManager::class);
        $manager->shouldReceive('connection')->with('judge')->andReturn($queue);
        $this->app->instance(QueueManager::class, $manager);

        $solutionQueue = new SolutionQueue();
        $solutionQueue->add($solution);
        try {
            $solutionQueue->add($solution);
            $this->fail('Expected queue failure to be propagated.');
        } catch (RuntimeException $exception) {
            $this->assertSame('queue unavailable', $exception->getMessage());
        }

        $this->assertCount(2, $payloads);
        $this->assertNotSame($payloads[0]['judge_token'], $payloads[1]['judge_token']);
        $this->assertFalse(app(JudgeTaskToken::class)->consume($solution->id, $payloads[1]['judge_token']));
        $this->assertTrue(app(JudgeTaskToken::class)->consume($solution->id, $payloads[0]['judge_token']));
    }

    public function testAddDoesNotPushWhenTokenCannotBeStored()
    {
        config(['hustoj.services.judge.status' => true]);

        $solution = $this->createSolution($this->createUser(), $this->createProblem());
        $store = Mockery::mock(Repository::class, [new ArrayStore()])->makePartial();
        $store->shouldReceive('put')->once()->andReturnFalse();
        Cache::shouldReceive('store')->andReturn($store);

        $queue = Mockery::mock(Queue::class);
        $queue->shouldNotReceive('pushRaw');
        $manager = Mockery::mock(QueueManager::class);
        $manager->shouldReceive('connection')->with('judge')->andReturn($queue);
        $this->app->instance(QueueManager::class, $manager);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to store judge task token.');

        (new SolutionQueue())->add($solution);
    }

    public function testFailedPushReturningFalseDoesNotLeaveUsableTaskToken()
    {
        config(['hustoj.services.judge.status' => true]);

        $solution = $this->createSolution($this->createUser(), $this->createProblem());
        $solution->source()->create(['code' => 'void']);
        $token = null;

        $queue = Mockery::mock(Queue::class);
        $queue->shouldReceive('pushRaw')->once()->andReturnUsing(function (string $payload) use (&$token) {
            $token = json_decode($payload, true)['judge_token'];

            return false;
        });
        $manager = Mockery::mock(QueueManager::class);
        $manager->shouldReceive('connection')->with('judge')->andReturn($queue);
        $this->app->instance(QueueManager::class, $manager);

        try {
            (new SolutionQueue())->add($solution);
            $this->fail('Expected queue failure to be propagated.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Unable to push judge task.', $exception->getMessage());
        }

        $this->assertIsString($token);
        $this->assertFalse(app(JudgeTaskToken::class)->consume($solution->id, $token));
    }
}
