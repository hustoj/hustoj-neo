<?php

namespace App\Repositories\Criteria;

use Czim\Repository\Contracts\BaseRepositoryInterface;
use Czim\Repository\Contracts\CriteriaInterface;

class OrderBy implements CriteriaInterface
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

    public function apply($model, BaseRepositoryInterface $repository)
    {
        return $model->orderBy($this->field, $this->order);
    }
}
