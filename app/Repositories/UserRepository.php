<?php

namespace App\Repositories;

use App\Entities\User;

class UserRepository extends Repository
{
    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return User::class;
    }
}
