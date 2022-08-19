<?php

namespace App\Task;

use App\Entities\Problem;
use App\Entities\Solution;
use Illuminate\Contracts\Support\Jsonable;

class JudgeJob implements Jsonable
{
    /** @var Solution */
    private Solution $solution;
    /** @var Problem */
    private Problem $problem;

    /**
     * @param  Solution  $solution
     */
    public function __construct(Solution $solution)
    {
        $this->solution = $solution;
        $this->problem = $solution->problem;
    }

    public function setProblem(Problem $problem): void
    {
        $this->problem = $problem;
    }

    public function setSolution(Solution $solution): void
    {
        $this->solution = $solution;
    }

    public function asQueueInfo(): array
    {
        return [
            'problem_id' => $this->problem->id,
            'time_limit' => $this->problem->time_limit,
            'memory_limit' => $this->problem->memory_limit,
            'is_special' => $this->problem->spj,

            'solution_id' => $this->solution->id,
            'code' => $this->solution->source->code,
            'language' => $this->solution->language,
        ];
    }

    public function toJson($options = 0): bool|string
    {
        return json_encode($this->asQueueInfo());
    }
}
