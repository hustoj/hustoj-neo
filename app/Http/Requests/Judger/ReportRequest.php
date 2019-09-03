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
            'solution_id' => 'required|int',
            'status'      => 'required|int|between:2,12',
            'time_cost'   => 'int',
            'memory_cost' => 'int',
        ];
    }
}
