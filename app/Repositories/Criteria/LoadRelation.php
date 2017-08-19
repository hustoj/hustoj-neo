<?php

namespace App\Repositories\Criteria;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Bosnadev\Repositories\Criteria\Criteria;

class LoadRelation extends Criteria
{
    private $relations;

    /**
     * LoadRelation constructor.
     *
     * @param $relations
     */
    public function __construct($relations)
    {
        $this->relations = $relations;
    }


    /**
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->with($this->relations);
    }
}