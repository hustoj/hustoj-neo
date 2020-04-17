<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;

class EditRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'nick' => 'required',
        ];
    }
}
