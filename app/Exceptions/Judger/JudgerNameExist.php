<?php

namespace App\Exceptions\Judger;

use App\Exceptions\ApiException;

class JudgerNameExist extends ApiException
{
    protected $message = 'this name is exist!';
}
