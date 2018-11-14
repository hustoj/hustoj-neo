<?php

namespace App\Exceptions\Judger;

use App\Exceptions\HustException;

class JudgerCodeInvalid extends HustException
{
    protected $message = 'auth code invalid';
}
