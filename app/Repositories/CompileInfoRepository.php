<?php

namespace App\Repositories;

use App\Entities\CompileInfo;

class CompileInfoRepository extends Repository
{
    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return CompileInfo::class;
    }
}
