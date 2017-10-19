<?php

namespace App\Services;

use App\Entities\Solution;
use App\Repositories\Criteria\BestSolution;
use App\Repositories\Criteria\Distinct;
use App\Repositories\Criteria\GroupBy;
use App\Repositories\Criteria\OrderBy;
use App\Repositories\Criteria\RawSelect;
use App\Repositories\Criteria\Where;
use App\Repositories\SolutionRepository;

class ProblemService
{
    public function numberOfAcceptedUser($problemId)
    {
        /** @var SolutionRepository $repository */
        $repository = app(SolutionRepository::class);
        $repository->pushCriteria(new Where('problem_id', $problemId));
        $repository->pushCriteria(new Where('result', Solution::STATUS_AC));
        $repository->pushCriteria(new Distinct('user_id'));

        return $repository->count('user_id');
    }

    public function numberOfSubmitUser($problemId)
    {
        /** @var SolutionRepository $repository */
        $repository = app(SolutionRepository::class);
        $repository->pushCriteria(new Where('problem_id', $problemId));
        $repository->pushCriteria(new Distinct('user_id'));

        return $repository->count('user_id');
    }

    /**
     * @param $problemId
     *
     * @return array
     */
    public function getResultCount($problemId)
    {
        /** @var SolutionRepository $repository */
        $repository = app(SolutionRepository::class);
        $repository->pushCriteria(new Where('problem_id', $problemId));
        $repository->pushCriteria(new RawSelect('count(*) as user_count, result'));
        $repository->pushCriteria(new GroupBy('result'));

        app('db')->connection()->statement('SET sql_mode = \'\'');

        return $repository->all();
    }

    public function bestSolutions($problemId, $perPage = 50)
    {
        /** @var SolutionRepository $repository */
        $repository = app(SolutionRepository::class);

        $repository->pushCriteria(new BestSolution());
        $repository->pushCriteria(new Where('problem_id', $problemId));
        $repository->pushCriteria(new Where('result', Solution::STATUS_AC));
        $repository->pushCriteria(new OrderBy('time_cost', 'asc'));
        $repository->pushCriteria(new OrderBy('score', 'asc'));
        $repository->pushCriteria(new OrderBy('id', 'asc'));
        $repository->pushCriteria(new GroupBy('user_id'));

        return $repository->paginate($perPage);
    }
}
