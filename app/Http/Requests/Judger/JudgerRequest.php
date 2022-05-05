<?php

namespace App\Http\Requests\Judger;

use App\Entities\Judger;
use App\Exceptions\Judger\JudgerCodeInvalid;
use App\Http\Requests\Request;

class JudgerRequest extends Request
{
    private $judger;

    public function validate()
    {
        $origin = sprintf('%s-%d', $this->getJudger()->code, $this->input('ts'));
        if ($this->getToken() != md5($origin)) {
            throw new JudgerCodeInvalid();
        }
    }

    public function getJudger()
    {
        if (! $this->judger) {
            $this->judger = Judger::query()->find($this->getJudgeId());
        }

        return $this->judger;
    }

    public function getJudgeId()
    {
        return $this->header('Judge-Id');
    }

    public function getToken()
    {
        return $this->header('Token');
    }
}
