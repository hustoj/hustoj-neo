<?php

namespace App\Repositories;

use App\Entities\LoginLog;

class LoginLogRepository extends Repository
{
    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return LoginLog::class;
    }
}
