<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;

class EditRequest extends Request
{
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'sometimes|confirmed'
        ];
    }
}