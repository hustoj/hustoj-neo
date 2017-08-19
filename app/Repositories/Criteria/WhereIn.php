<?php

namespace App\Repositories\Criteria;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Bosnadev\Repositories\Criteria\Criteria;

class WhereIn extends Criteria
{
    private $column;
    private $values;

    /**
     * WhereIn constructor.
     *
     * @param $column
     * @param $values
     */
    public function __construct($column, $values)
    {
        $this->column = $column;
        $this->values = $values;
    }

    /**
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->whereIn($this->column, $this->values);
    }
}