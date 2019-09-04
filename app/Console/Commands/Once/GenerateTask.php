<?php

namespace App\Console\Commands\Once;

use App\Entities\Solution;
use App\Task\SolutionServer;
use Illuminate\Console\Command;

class GenerateTask extends Command
{
    protected $signature = 'task:make {id}';

    protected $description = 'Generate Task by Solution Id';

    public function handle()
    {
        $id = $this->argument('id');
        $solution = Solution::query()->findOrFail($id);
        app(SolutionServer::class)->add($solution)->send();
    }
}
