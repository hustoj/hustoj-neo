<?php

namespace App\Services;

use App\Entities\User;
use Carbon\Carbon;

class UserService
{
    /**
     * @param  Carbon  $from
     * @return mixed
     */
    public function getUserStats(Carbon $from)
    {
        $rawSql = 'DATE_FORMAT(created_at,\'%Y-%m-%d\') as date, count(*) as number';

        $query = User::query();
        $query->selectRaw($rawSql)
            ->where('created_at', '>', $from->format('Y-m-d h:i:s'))
            ->groupBy('date')
            ->orderByDesc('date');

        return $query->get();
    }

    public function findByName($username)
    {
        return User::query()->where('username', $username)->first();
    }
}
