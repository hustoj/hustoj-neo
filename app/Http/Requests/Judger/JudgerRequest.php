<?php

namespace App\Http\Requests\Judger;

use App\Entities\Judger;
use App\Exceptions\Judger\JudgerCodeInvalid;
use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class JudgerRequest extends Request
{
    private $judger;

    public function validate()
    {
        $judger = $this->getJudger();
        if (! $judger) {
            throw new JudgerCodeInvalid();
        }

        $origin = sprintf('%s-%d', $judger->code, $this->input('ts'));
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

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'code' => 500,
            'message' => $validator->errors()->first(),
        ]));
    }
}
