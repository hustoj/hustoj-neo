<?php

namespace App\Http\Requests\Judger;

use App\Exceptions\Judger\JudgerCodeInvalid;
use App\Http\Requests\Request;
use App\Services\JudgerService;

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
        if (!$this->judger) {
            $this->judger = app(JudgerService::class)->find($this->getJudgeId());
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
