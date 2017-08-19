<?php

namespace App\Repositories\Criteria;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Bosnadev\Repositories\Criteria\Criteria;
use Illuminate\Database\Eloquent\Builder;

class Where extends Criteria
{
    private $field;
    private $op;
    private $value;

    /**
     * Where constructor.
     *
     * @param $field
     * @param $op
     * @param $value
     */
    public function __construct($field, $value, $op = '=')
    {
        $this->field = $field;
        $this->value = $value;
        $this->op    = $op;
    }


    /**
     * @param Builder             $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->where($this->field, $this->op, $this->value);
    }
}