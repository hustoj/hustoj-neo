<?php

namespace App\Services;

use App\Entities\Problem;
use App\Status;

class SummaryService
{
    public static $statusTypes = [
        Status::ACCEPT,
        Status::PRESENTATION_ERROR,
        Status::WRONG_ANSWER,
        Status::TIME_LIMIT,
        Status::MEMORY_LIMIT,
        Status::OUTPUT_LIMIT,
        Status::RUNTIME_ERROR,
        Status::COMPILE_ERROR,
    ];
    public $acceptedUser = 0;
    public $submitUser = 0;
    public $total = 0;
    public $statistics;
    private $problem;

    public function __construct(Problem $problem)
    {
        $this->problem = $problem;

        $this->process();
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

    public function getProblem()
    {
        return $this->problem;
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

    public function getResultCount()
    {
        return (new ProblemService())->getResultCount($this->problem->id);
    }

    public function bestSolutions()
    {
        return (new ProblemService())->bestSolutions($this->problem->id);
    }
}
