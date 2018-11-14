<?php

namespace App\Http\Requests\Judger;

use App\Http\Requests\Request;

class JudgerRequest extends Request
{
    public function judgeCode()
    {
        return $this->header('Judge-Code');
    }
}
