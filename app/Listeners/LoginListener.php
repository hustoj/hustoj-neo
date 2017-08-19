<?php

namespace App\Listeners;

use App\Entities\Log;
use App\Entities\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;

class LoginListener
{
    public function handle($event)
    {
        if (!$event->user) {
            return;
        }

        if($event instanceof Failed) {
            $this->loggingFailed($event->user);
        }

        if($event instanceof Login) {
            $this->loggingOk($event->user);
        }
    }

    private function loggingFailed($user)
    {
        $logging = $this->createLog($user);
        $logging->status = Log::ST_FAILED;
        $logging->save();
    }

    private function loggingOk($user)
    {
        $logging = $this->createLog($user);
        $logging->status = Log::ST_OK;
        $logging->save();
    }

    /**
     * @param User $user
     *
     * @return Log
     */
    private function createLog($user)
    {
        $logging = new Log();
        $logging->user_id = $user->id;
        $logging->ip = request()->getClientIp();
        return $logging;
    }
}