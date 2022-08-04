<?php

namespace App\Http\Requests\Solution;

use App\Http\Requests\Request;

class IndexRequest extends Request
{
    public function getUserName()
    {
        return $this->input('username');
    }

    public function getProblemId()
    {
        return $this->input('problem_id');
    }

    public function getLanguage()
    {
        $default = -1;

        $x = $this->input('language');
        if ($x == '') {
            return $default;
        }

        return $x;
    }

    public function getStatus()
    {
        return $this->input('status', -1);
    }

    public function rules()
    {
        return [];
    }

    public function authorize()
    {
        return true;
    }
}
