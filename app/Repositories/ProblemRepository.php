<?php

namespace App\Repositories;

use App\Entities\Problem;

class ProblemRepository extends Repository
{
    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return Problem::class;
    }
}
