<?php

namespace App\Observers;

use App\Entities\Solution;
use App\Entities\User;
use App\Services\SolutionService;

class UserSolutionCountObserver
{
    private SolutionService $solutionService;

    /**
     * @param  SolutionService  $solutionService
     */
    public function __construct(SolutionService $solutionService)
    {
        $this->solutionService = $solutionService;
    }

    public function created(Solution $solution)
    {
        /** @var User $user */
        $user = User::query()->find($solution->user_id);
        if ($user == null) {
            app('log')->error("user {$solution->user_id} of solution {$solution->id} not found!");

            return;
        }
        $user->submit = $this->solutionService->getUserSubmit($user);
        $user->save();
        app('log')->info("user {$user->id} add new submit");
    }

    public function updated(Solution $solution)
    {
        /** @var User $user */
        $user = User::query()->find($solution->user_id);
        if ($user == null) {
            app('log')->error("user {$solution->user_id} of solution {$solution->id} not found!");

            return;
        }
        if (! $solution->isAccepted()) {
            return;
        }
        $user->submit = $this->solutionService->getUserSubmit($user);
        $user->solved = $this->solutionService->getUserResolved($user);
        $user->save();
        app('log')->error("user {$solution->user_id} resolved a problem {$solution->problem_id}!");
    }
}
