<?php

namespace App\Task;

class SolutionServer
{
    private $task;

    public function add($solution)
    {
        $task = app(Task::class);
        $task->setSolution($solution);
        $task->setProblem($solution->problem);

        $this->task = $task;

        return $this;
    }

    public function send()
    {
        if (config('hustoj.services.judge.status') == false) {
            return;
        }
        $queue = app(TaskQueue::class);
        $queue->add($this->task);
        $queue->done();
    }
}
