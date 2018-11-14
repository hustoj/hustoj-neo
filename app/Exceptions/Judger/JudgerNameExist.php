<?php

namespace App\Exceptions\Judger;

use App\Exceptions\HustException;

class JudgerNameExist extends HustException
{
    protected $message = 'this name is exist!';
}
