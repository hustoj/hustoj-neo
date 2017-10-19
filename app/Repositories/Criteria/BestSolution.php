<?php

namespace App\Repositories\Criteria;

use Czim\Repository\Contracts\BaseRepositoryInterface;
use Czim\Repository\Contracts\CriteriaInterface;

class BestSolution implements CriteriaInterface
{
    public function apply($model, BaseRepositoryInterface $repository)
    {
        app('db')->connection()->statement('SET sql_mode = \'\'');
        $rawCount = 'count(*) as att';
        $rawGrade = 'min(10000000000000000000 + time_cost * 100000000000 + memory_cost * 100000) as score';

        return $model->select(
            'id',
            'problem_id',
            'user_id',
            'language',
            'memory_cost',
            'time_cost',
            'created_at',
            app('db')->raw($rawCount),
            app('db')->raw($rawGrade)
        );
    }
}
