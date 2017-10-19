<?php

namespace App\Services;

use App\Entities\Problem;
use App\Entities\Solution;

class SummaryService
{
    public $acceptedUser = 0;
    public $submitUser = 0;
    public $total = 0;

    public $statistics;

    private $problem;

    public static $statusTypes = [
        Solution::STATUS_AC,
        Solution::STATUS_PE,
        Solution::STATUS_WA,
        Solution::STATUS_TLE,
        Solution::STATUS_MLE,
        Solution::STATUS_OLE,
        Solution::STATUS_RE,
        Solution::STATUS_CE,
    ];

    public function getProblem()
    {
        return $this->problem;
    }

    public function __construct(Problem $problem)
    {
        $this->problem = $problem;

        $this->process();
    }

    public function accepted()
    {
        $service = new ProblemService();

        return $service->numberOfAcceptedUser($this->problem->id);
    }

    public function submit()
    {
        $service = new ProblemService();

        return $service->numberOfSubmitUser($this->problem->id);
    }

    private function process()
    {
        $service = new ProblemService();

        $this->statistics = [];
        $resultCount = $service->getResultCount($this->problem->id);
        foreach ($resultCount as $result) {
            $this->statistics[$result->result] = $result->user_count;
            $this->total += $result->user_count;
        }
    }

    public function getResultCount()
    {
        return (new ProblemService())->getResultCount($this->problem->id);
    }

    public function bestSolutions()
    {
        return (new ProblemService())->bestSolutions($this->problem->id);
    }
}
