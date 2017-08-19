<?php

namespace App\Repositories\Criteria;

use Czim\Repository\Contracts\BaseRepositoryInterface;
use Czim\Repository\Contracts\CriteriaInterface;

class WithRelation implements CriteriaInterface
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

    public function apply($model, BaseRepositoryInterface $repository)
    {
        return $model->with($this->relations);
    }
}