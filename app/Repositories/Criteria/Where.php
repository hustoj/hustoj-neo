<?php

namespace App\Repositories\Criteria;

use Czim\Repository\Contracts\BaseRepositoryInterface;
use Czim\Repository\Contracts\CriteriaInterface;

class Where implements CriteriaInterface
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
        $this->op = $op;
    }

    public function apply($model, BaseRepositoryInterface $repository)
    {
        return $model->where($this->field, $this->op, $this->value);
    }
}
