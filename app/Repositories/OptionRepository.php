<?php

namespace App\Repositories;

use App\Entities\Option;

class OptionRepository extends Repository
{
    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return Option::class;
    }
}
