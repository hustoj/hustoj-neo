<?php

namespace App\Task;

use App\Entities\Solution;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Str;

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
        if (! $solution->judge_token) {
            $solution->forceFill(['judge_token' => Str::random(64)])->save();
        }
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
