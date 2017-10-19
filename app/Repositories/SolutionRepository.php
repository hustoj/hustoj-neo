<?php

namespace App\Repositories;

use App\Entities\Solution;

class SolutionRepository extends Repository
{
    public function model()
    {
        return Solution::class;
    }
}
