<?php

namespace App\Services\User;

use App\Entities\User;
use App\Repositories\Criteria\Distinct;
use App\Repositories\Criteria\Where;
use App\Repositories\SolutionRepository;
use App\Status;

class UserSolutions
{
    public function getResolvedProblems(User $user)
    {
        /** @var SolutionRepository $repository */
        $repository = app(SolutionRepository::class);
        $repository->pushCriteria(new Where('user_id', $user->id));
        $repository->pushCriteria(new Where('result', Status::ACCEPT));
        $repository->pushCriteria(new Distinct('problem_id'));

        return $repository->all('problem_id')->map(function ($item) {
            return $item->problem_id;
        });
    }
}
