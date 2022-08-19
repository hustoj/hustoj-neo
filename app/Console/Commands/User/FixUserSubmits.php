<?php

namespace App\Console\Commands\User;

use App\Entities\Solution;
use App\Entities\User;
use App\Services\SolutionService;
use Illuminate\Console\Command;

class FixUserSubmits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:user:submits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fix user submits';

    private SolutionService $solutionService;

    /**
     * @param  SolutionService  $solutionService
     */
    public function __construct(SolutionService $solutionService)
    {
        parent::__construct();
        $this->solutionService = $solutionService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $allUserIds = Solution::query()->select('user_id')->distinct()->get();
        $allUserIds->map(function ($solution) {
            /** @var User $user */
            $user = User::query()->find($solution->user_id);
            if ($user == null) {
                app('log')->error("user {$solution->user_id} not found!");

                return;
            }
            $submit = $this->solutionService->getUserSubmit($user);
            $resolved = $this->solutionService->getUserResolved($user);

            if ($user->submit != $submit) {
                app('log')->info("user {$user->id} submit({$user->submit}) is not match {$submit}");
                $user->submit = $submit;
            }

            if ($user->solved != $resolved) {
                app('log')->info("user {$user->id} solved ({$user->solved}) is not match {$resolved}");
                $user->solved = $resolved;
            }
            $user->save();
        });

        return 0;
    }
}
