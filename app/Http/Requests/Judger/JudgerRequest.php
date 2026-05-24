<?php

namespace App\Http\Requests\Judger;

use App\Entities\Judger;
use App\Http\Requests\Request;
use App\Services\JudgerAuthenticator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class JudgerRequest extends Request
{
    private $judger;

    public function getJudger()
    {
        if (! $this->judger) {
            $this->judger = Judger::query()
                ->where('status', Judger::ST_ACTIVITY)
                ->find($this->getJudgeId());
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

    public function getNonce()
    {
        return $this->header('Nonce');
    }

    public function getTokenVersion()
    {
        return $this->header('Token-Version', '1');
    }

    public function rules()
    {
        return [
            'ts' => 'required|int',
        ];
    }

    protected function passedValidation()
    {
        $this->judger = app(JudgerAuthenticator::class)->authenticate($this);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'code' => 500,
            'message' => 'auth code invalid',
        ]));
    }
}
