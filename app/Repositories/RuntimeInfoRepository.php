<?php

namespace App\Repositories;

use App\Entities\RuntimeInfo;

class RuntimeInfoRepository extends Repository
{
    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return RuntimeInfo::class;
    }
}
