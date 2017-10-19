<?php

namespace App\Repositories\Criteria;

use Czim\Repository\Contracts\BaseRepositoryInterface;
use Czim\Repository\Contracts\CriteriaInterface;

class GroupBy implements CriteriaInterface
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

    public function apply($model, BaseRepositoryInterface $repository)
    {
        return $model->groupBy($this->field);
    }
}
