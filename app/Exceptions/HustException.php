<?php

namespace App\Exceptions;

use Exception;

class HustException extends Exception
{
    public function render()
    {
        return [
            'code'    => 500,
            'message' => $this->message,
        ];
    }
}
