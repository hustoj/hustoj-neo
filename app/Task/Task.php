<?php

namespace App\Task;

use App\Entities\Problem;
use App\Entities\Solution;

class Task
{
    /** @var Solution */
    private $solution;
    /** @var Problem */
    private $problem;

    public function setProblem(Problem $problem)
    {
        $this->problem = $problem;
    }

    public function setSolution(Solution $solution)
    {
        $this->solution = $solution;
    }

    public function asQueueInfo()
    {
        return [
            'problem_id'   => $this->problem->id,
            'time_limit'   => $this->problem->time_limit,
            'memory_limit' => $this->problem->memory_limit,
            'is_special'   => $this->problem->spj,

            'solution_id' => $this->solution->id,
            'code'        => $this->solution->source->code,
            'language'    => intval($this->solution->language),
        ];
    }
}
