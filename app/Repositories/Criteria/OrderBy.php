<?php

namespace App\Repositories\Criteria;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Bosnadev\Repositories\Criteria\Criteria;

class OrderBy extends Criteria
{
    private $field;
    private $order;

    /**
     * OrderBy constructor.
     *
     * @param $field
     * @param $order
     */
    public function __construct($field, $order = 'asc')
    {
        $this->field = $field;
        $this->order = $order;
    }

    /**
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->orderBy($this->field, $this->order);
    }
}