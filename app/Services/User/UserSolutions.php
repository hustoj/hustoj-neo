<?php

namespace App\Services\User;

use App\Entities\Solution;
use App\Entities\User;
use App\Status;

class UserSolutions
{
    public function getResolvedProblems(User $user)
    {
        $query = Solution::query()
            ->where('user_id', $user->id)
            ->where('result', Status::ACCEPT)
            ->distinct('problem_id');

        return $query->get('problem_id')->map(function ($item) {
            return $item->problem_id;
        });
    }
}
