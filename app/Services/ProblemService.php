<?php

namespace App\Services;

use App\Entities\Solution;
use App\Status;

class ProblemService
{
    public function numberOfAcceptedUser($problemId)
    {
        $query = Solution::query();

        return $query->where('problem_id', $problemId)
            ->where('result', Status::ACCEPT)
            ->distinct('user_id')
            ->count();
    }

    public function numberOfSubmitUser($problemId)
    {
        $query = Solution::query();

        return $query->where('problem_id', $problemId)
            ->distinct('user_id')
            ->count();
    }

    /**
     * @param  $problemId
     * @return array
     */
    public function getResultCount($problemId)
    {
        $query = Solution::query();

        $query->getConnection()->statement('SET sql_mode = \'\'');

        return $query
            ->selectRaw('count(*) as user_count, result')
            ->where('problem_id', $problemId)
            ->groupBy('result')
            ->get();
    }

    public function bestSolutions($problemId, $perPage = 50)
    {
        $query = Solution::query();
        $query->getConnection()->statement('SET sql_mode = \'\'');
        $rawCount = 'count(*) as att';
        $rawGrade = 'min(10000000000000000000 + time_cost * 100000000000 + memory_cost * 100000) as score';
        $columns = ['id',
            'problem_id',
            'user_id',
            'language',
            'memory_cost',
            'time_cost',
            'created_at',
            $query->raw($rawCount),
            $query->raw($rawGrade),
        ];

        return $query
            ->select($columns)
            ->where('problem_id', $problemId)
            ->where('result', Status::ACCEPT)
            ->orderBy('time_cost')
            ->orderBy('score')
            ->orderBy('id')
            ->groupBy('user_id')
            ->paginate($perPage);
    }
}
