<?php

namespace App\Http\Requests\Solution;

use App\Http\Requests\Request;

class StoreRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'problem_id' => 'required|integer',
            'language' => 'required|integer',
            'code' => 'required|string',
        ];
    }

    public function getProblemId(): int
    {
        return (int) $this->input('problem_id');
    }

    public function getLanguage(): int
    {
        return (int) $this->input('language');
    }

    public function getCode(): string
    {
        return (string) $this->input('code');
    }
}
