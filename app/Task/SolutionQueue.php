<?php

namespace App\Task;

use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\QueueManager;

class SolutionQueue
{
    private Queue $queue;

    public function __construct()
    {
        /** @var QueueManager $manager */
        $manager = app(QueueManager::class);
        $this->queue = $manager->connection('judge');
    }

    public function add($solution)
    {
        $this->send(new JudgeJob($solution));
    }

    protected function send(JudgeJob $task)
    {
        if (! config('hustoj.services.judge.status')) {
            return;
        }
        $this->queue->pushRaw($task->toJson());
    }
}
