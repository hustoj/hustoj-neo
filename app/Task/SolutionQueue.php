<?php

namespace App\Task;

use App\Entities\Solution;
use App\Services\JudgeTaskToken;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\QueueManager;
use RuntimeException;

class SolutionQueue
{
    private Queue $queue;

    public function __construct()
    {
        /** @var QueueManager $manager */
        $manager = app(QueueManager::class);
        $this->queue = $manager->connection('judge');
    }

    public function add(Solution $solution)
    {
        $this->send($solution);
    }

    protected function send(Solution $solution)
    {
        if (! config('hustoj.services.judge.status')) {
            return;
        }

        app(JudgeTaskToken::class)->dispatch($solution->id, function (string $token) use ($solution) {
            if ($this->queue->pushRaw((new JudgeJob($solution, $token))->toJson()) === false) {
                throw new RuntimeException('Unable to push judge task.');
            }
        });
    }
}
