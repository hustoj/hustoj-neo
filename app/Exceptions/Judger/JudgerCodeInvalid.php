<?php

namespace App\Exceptions\Judger;

use App\Exceptions\ApiException;

class JudgerCodeInvalid extends ApiException
{
    protected $message = 'auth code invalid';
}
