<?php

namespace App\Repositories;

use App\Entities\Judger;

class JudgerRepository extends Repository
{
    /**
     * Returns specified model class name.
     *
     * @return string
     */
    public function model()
    {
        return Judger::class;
    }
}
