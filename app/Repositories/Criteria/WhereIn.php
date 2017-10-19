<?php

namespace App\Repositories\Criteria;

use Czim\Repository\Contracts\BaseRepositoryInterface;
use Czim\Repository\Contracts\CriteriaInterface;

class WhereIn implements CriteriaInterface
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

    public function apply($model, BaseRepositoryInterface $repository)
    {
        return $model->whereIn($this->column, $this->values);
    }
}
