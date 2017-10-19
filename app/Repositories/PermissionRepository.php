<?php

namespace App\Repositories;

use App\Entities\Permission;

class PermissionRepository extends Repository
{
    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return Permission::class;
    }
}
