<?php

namespace App\Http\Requests\Judger;

class DataRequest extends JudgerRequest
{
    public function getProblemId()
    {
        return $this->input('pid');
    }

    public function rules()
    {
        return [
            'pid' => 'required|int',
            'ts'  => 'required|int',
        ];
    }
}
