<?php

namespace App\Http\Requests\Topic;

use App\Http\Requests\Request;

class StoreRequest extends Request
{
    public function getTitle()
    {
        return $this->input('title');
    }

    public function getBody()
    {
        return $this->input('content');
    }

    public function getContestId()
    {
        return $this->input('contest_id', 0);
    }

    public function getProblemId()
    {
        return $this->input('problem_id', 0);
    }

    public function rules()
    {
        return [
            'title' => 'required',
            'content' => 'required|min:10',
            'contest_id' => 'sometimes|numeric',
        ];
    }
}
