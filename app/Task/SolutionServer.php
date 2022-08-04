<?php

namespace App\Task;

use PhpAmqpLib\Exception\AMQPIOException;

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
        if (! config('hustoj.services.judge.status')) {
            return;
        }
        try {
            $queue = app(TaskQueue::class);
            $queue->add($this->task);
            $queue->done();
        } catch (AMQPIOException $e) {
            logger()->error('send to queue failed!', ['message' => $e->getMessage()]);
        }
    }
}
