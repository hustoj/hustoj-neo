<?php

namespace App\Listeners;

use App\Entities\LoginLog;
use App\Entities\User;
use App\Services\LoginLogService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;

class LoginListener
{
    public function handle($event)
    {
        if (!$event->user) {
            // no such user in system, don't record it
            return;
        }

        $password = app('hash')->make($event->credentials['password']);
        if($event instanceof Failed) {
            $this->loggingFailed($event->user, $password);
        }

        if($event instanceof Login) {
            $this->loggingOk($event->user, $password);
        }

        $this->cleanUserRecentLog($event->user);
    }

    private function loggingFailed($user, $password)
    {
        $logging = $this->createLog($user);
        $logging->status = LoginLog::ST_FAILED;
        $logging->password = $password;
        $logging->save();
    }

    private function loggingOk($user, $password)
    {
        $logging = $this->createLog($user);
        $logging->status = LoginLog::ST_OK;
        $logging->password = $password;
        $logging->save();
    }

    /**
     * @param User $user
     *
     * @return LoginLog
     */
    private function createLog($user)
    {
        $logging = new LoginLog();
        $logging->user_id = $user->id;
        $logging->ip = request()->getClientIp();
        return $logging;
    }

    private function cleanUserRecentLog($user)
    {
        app(LoginLogService::class)->cleanUserLog($user);
    }
}