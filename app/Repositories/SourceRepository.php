<?php

namespace App\Repositories;

use App\Entities\Source;

class SourceRepository extends Repository
{
    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return Source::class;
    }
}
