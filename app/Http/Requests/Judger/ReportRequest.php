<?php

namespace App\Http\Requests\Judger;

class ReportRequest extends JudgerRequest
{
    public function getSolutionId()
    {
        return $this->input('solution_id');
    }

    public function rules()
    {
        return [
            'ts'          => 'required|int',
            'solution_id' => 'required|int',
            'judge_token' => 'sometimes|string',
            'status'      => 'required|int|between:2,12',
            'time_cost'   => 'int',
            'memory_cost' => 'int',
        ];
    }

    public function getJudgeToken(): string
    {
        return (string) $this->input('judge_token');
    }
}
