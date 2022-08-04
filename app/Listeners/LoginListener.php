<?php

namespace App\Listeners;

use App\Entities\LoginLog;
use App\Entities\User;
use App\Services\LoginLogService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Carbon;

class LoginListener
{
    public function handle($event)
    {
        if (! $event->user) {
            // no such user in system, don't record it
            $information = [
                'name' => $event->credentials['username'],
                'ip'   => request()->getClientIp(),
                'ua'   => request()->userAgent(),
            ];
            info('user is not existed:', $information);

            return;
        }

        if ($event instanceof Failed) {
            $password = app('hash')->make($event->credentials['password']);
            $this->loggingFailed($event->user, $password);
        }

        if ($event instanceof Login) {
            $password = '*'; // if login ok, then no credentials
            $this->loggingOk($event->user, $password);
            $this->refreshAccessTime($event->user);
        }

        $this->cleanUserRecentLog($event->user);
    }

    private function refreshAccessTime(User $user)
    {
        $user->access_at = Carbon::now();
        $user->save();
    }

    private function loggingFailed($user, $password)
    {
        $logging = $this->createLog($user);
        $logging->status = LoginLog::ST_FAILED;
        $logging->password = $password;
        $logging->save();
    }

    /**
     * @param  User  $user
     * @return LoginLog
     */
    private function createLog($user)
    {
        $logging = new LoginLog();
        $logging->user_id = $user->id;
        $logging->ip = request()->getClientIp();

        return $logging;
    }

    private function loggingOk($user, $password)
    {
        $logging = $this->createLog($user);
        $logging->status = LoginLog::ST_OK;
        $logging->password = $password;
        $logging->save();
    }

    private function cleanUserRecentLog($user)
    {
        app(LoginLogService::class)->cleanUserLog($user);
    }
}
