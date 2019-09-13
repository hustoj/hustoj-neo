<?php

namespace App\Console\Commands;

use App\Console\Commands\Migrations\ArticleMigration;
use App\Console\Commands\Migrations\ContestMigration;
use App\Console\Commands\Migrations\LoginLogMigration;
use App\Console\Commands\Migrations\OptionMigration;
use App\Console\Commands\Migrations\ProblemMigration;
use App\Console\Commands\Migrations\SolutionMigration;
use App\Console\Commands\Migrations\TopicMigration;
use App\Console\Commands\Migrations\UserMigration;
use Illuminate\Console\Command;

class DatabaseMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'migrate from old hustoj';

    private $commands = [
        UserMigration::class,
        ProblemMigration::class,
        ContestMigration::class,
        SolutionMigration::class,
        TopicMigration::class,
        ArticleMigration::class,
        LoginLogMigration::class,
        OptionMigration::class,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach ($this->commands as $clz) {
            app($clz)->handle($this);
        }
    }
}
