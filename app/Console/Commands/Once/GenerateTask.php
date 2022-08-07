<?php

namespace App\Console\Commands\Once;

use App\Entities\Solution;
use App\Task\SolutionQueue;
use Illuminate\Console\Command;

class GenerateTask extends Command
{
    protected $signature = 'task:make {id}';

    protected $description = 'Generate Task by Solution Id';

    public function handle()
    {
        $id = $this->argument('id');
        app()->singleton(SolutionQueue::class, function () {
            return new SolutionQueue();
        });
        if (strpos($id, '-') !== false) {
            [$start, $end] = explode('-', $id, 2);
            $start = intval($start);
            $end = intval($end);
            for ($i = $start; $i <= $end; $i++) {
                $this->addTask($i);
            }
        } else {
            $this->addTask($id);
        }
    }

    private function addTask($id)
    {
        $solution = Solution::query()->findOrFail($id);
        app(SolutionQueue::class)->add($solution);
    }
}
