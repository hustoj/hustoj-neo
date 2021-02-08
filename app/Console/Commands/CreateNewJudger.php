<?php

namespace App\Console\Commands;

use App\Exceptions\ApiException;
use App\Services\JudgerService;
use Illuminate\Console\Command;

class CreateNewJudger extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'judger:new {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create new judger';

    public function handle()
    {
        $name = $this->argument('name');
        if ($name) {
            try {
                $judger = app(JudgerService::class)->newJudger($name);
                $this->info("Create New Judger {$name}, Code: ".$judger->code);
            } catch (ApiException $e) {
                $this->error($e->getMessage());
            }
        }
    }
}
