<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Request;

class PasswordRequest extends Request
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'password'     => 'required',
            'password_new' => 'required|confirmed|alpha_num|digits_between:8,16',
        ];
    }
}
