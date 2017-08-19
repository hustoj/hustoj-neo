<?php

namespace App\Repositories\Criteria;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Bosnadev\Repositories\Criteria\Criteria;

class GroupBy extends Criteria
{
    private $field;

    /**
     * GroupBy constructor.
     *
     * @param $field
     */
    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->groupBy($this->field);
    }
}