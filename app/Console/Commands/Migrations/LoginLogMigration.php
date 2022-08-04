<?php

namespace App\Console\Commands\Migrations;

use App\Entities\LoginLog;
use App\Entities\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class LoginLogMigration extends Migration
{
    /**
     * @param  Command  $command
     */
    public function handle($command)
    {
        $command->info('Migrating Login Logs...');
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $this->processUser($user);
            }
        });
        $command->info('Migrating Login Logs Done');
    }

    private function processUser($user)
    {
        $logs = $this->getUserRecentLogs($user, 90);
        foreach ($logs as $log) {
            $newLog = new LoginLog();
            $newLog->user_id = $user->id;
            $newLog->ip = $log->ip;
            $newLog->password = $log->password;
            $newLog->status = LoginLog::ST_OK;
            $newLog->created_at = $log->time;
            $newLog->save();
        }
    }

    /**
     * @param  User  $user
     */
    private function getUserRecentLogs($user, $days)
    {
        $now = Carbon::now()->subDays($days)->setTime(0, 0);
        $logs = $this->getTable()->where('user_id', $user->username)
                     ->where('time', $now, '>')
                     ->orderBy('time', 'asc')
                     ->get();

        if ($logs->count() < 10) {
            $logs = $this->getTable()->where('user_id', $user->username)
                         ->limit(10)
                         ->orderBy('time', 'desc')
                         ->get();
            $logs = $logs->reverse();
        }

        return $logs;
    }

    private function getTable()
    {
        return $this->getConnection()->table('loginlog');
    }
}
