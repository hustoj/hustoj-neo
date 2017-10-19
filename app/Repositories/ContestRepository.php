<?php

namespace App\Repositories;

use App\Entities\Contest;

class ContestRepository extends Repository
{
    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return Contest::class;
    }
}
