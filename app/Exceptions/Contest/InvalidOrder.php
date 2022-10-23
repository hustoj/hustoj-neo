<?php

namespace App\Exceptions\Contest;

class InvalidOrder extends \App\Exceptions\WebException
{
    protected $message = 'Problem order is invalid';
}
