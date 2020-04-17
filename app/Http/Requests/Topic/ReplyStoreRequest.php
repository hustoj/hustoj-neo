<?php

namespace App\Http\Requests\Topic;

use App\Http\Requests\Request;

class ReplyStoreRequest extends Request
{
    public function getBody()
    {
        return $this->input('content');
    }

    public function rules()
    {
        return [
            'content' => 'required|min:10',
        ];
    }
}
