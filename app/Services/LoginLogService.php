<?php

namespace App\Services;

use App\Entities\LoginLog;
use App\Entities\User;
use Carbon\Carbon;

class LoginLogService
{
    public function getUserRecentRecord(User $user)
    {
        $query = LoginLog::query();

        return $query->where('user_id', $user->id)
            ->limit(19)
            ->orderByDesc('id')
            ->get();
    }

    public function getUserRecordsNumber($user): int
    {
        return LoginLog::query()->where('user_id', $user->id)->count();
    }

    public function cleanUserLog(User $user)
    {
        $query = LoginLog::query();
        $query->where('user_id', $user->id);
        $total = $query->count();

        $loginItemLimit = config('hustoj.log.login.limit');
        if ($total > $loginItemLimit) {
            logger()->info("user login log is more than $loginItemLimit");
            $theDay = Carbon::now()->subDays(90)->setTime(0, 0);
            $query->where('created_at', '<', $theDay);
            $query->delete();
        }
    }
}
