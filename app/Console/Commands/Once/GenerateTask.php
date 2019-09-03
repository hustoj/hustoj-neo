<?php

namespace App\Console\Commands\Once;

use App\Entities\Solution;
use App\Task\Task;
use App\Task\TaskQueue;
use Illuminate\Console\Command;

class GenerateTask extends Command
{
    protected $signature = 'task:make {id}';

    protected $description = 'Generate Task by Solution Id';

    public function handle()
    {
        $solution = $this->getSolution();
        $task = $this->generateTask($solution);

        $this->send($task);
    }

    /**
     * @return Solution
     */
    private function getSolution()
    {
        $id = $this->argument('id');

        return Solution::query()->findOrFail($id);
    }

    private function generateTask(Solution $solution)
    {
        $task = app(Task::class);
        $task->setSolution($solution);
        $task->setProblem($solution->problem);

        return $task;
    }

    private function send($task)
    {
        $queue = app(TaskQueue::class);
        $queue->add($task);
        $queue->close();
    }
}
