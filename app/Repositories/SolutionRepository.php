<?php

namespace App\Repositories;

use App\Entities\Solution;

class SolutionRepository extends Repository
{
    public function count($field)
    {
        $this->applyCriteria();

        return $this->model->count($field);
    }

    public function model()
    {
        return Solution::class;
    }
}