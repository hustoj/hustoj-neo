<?php

namespace App\Services;

use App\Entities\Solution;
use App\Entities\User;
use App\Status;
use Carbon\Carbon;

class SolutionService
{
    /**
     * @param  Carbon  $from
     * @return Solution[]
     */
    public function getSubmissionStats($from)
    {
        $query = Solution::query();

        $rawSql = 'DATE_FORMAT(created_at,\'%Y-%m-%d\') as date, count(*) as number';
        $query->selectRaw($rawSql);
        $query->where('created_at', '>', $from->format('Y-m-d h:i:s'))
            ->groupBy('date')
            ->orderByDesc('date');

        return $query->get();
    }

    /**
     * @param  int|User  $user
     * @return int
     */
    public function getUserSubmit(User|int $user): int
    {
        if ($user instanceof User) {
            $userId = $user->id;
        } else {
            $userId = $user;
        }

        return Solution::query()->where('user_id', $userId)->count();
    }

    /**
     * @param  int|User  $user
     * @return int
     */
    public function getUserResolved(User|int $user): int
    {
        if ($user instanceof User) {
            $userId = $user->id;
        } else {
            $userId = $user;
        }

        return Solution::query()->where('user_id', $userId)
            ->where('result', Status::ACCEPT)
            ->count();
    }
}
