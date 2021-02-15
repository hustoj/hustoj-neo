<?php

namespace App\Console\Commands;

use App\Entities\LoginLog;
use App\Entities\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class AddUserAccessTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:access-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'migrate user access time';

    private $total;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->total = 0;
        User::all()->chunk(500)->map(function (Collection $users) {
            $this->total = $this->total + $users->count();
            $users->load('logs');
            foreach ($users as $user) {
                /** @var User $user */
                if (count($user->logs) == 0) {
                    continue;
                }
                /** @var LoginLog $lastAccess */
                $lastAccess = $user->logs->first();
                $user->access_at = $lastAccess->created_at;
                $user->save();
            }
            $this->info("process {$this->total}");
        });
    }
}
