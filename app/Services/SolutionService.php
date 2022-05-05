<?php

namespace App\Services;

use App\Entities\Solution;
use Carbon\Carbon;

class SolutionService
{

    /**
     * @param Carbon $from
     *
     * @return mixed
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
}
