<?php

namespace App\Repositories;

use App\Entities\Role;

class RoleRepository extends Repository
{
    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return Role::class;
    }
}
