<?php

namespace App\Repositories\Criteria;

use Czim\Repository\Contracts\BaseRepositoryInterface;
use Czim\Repository\Contracts\CriteriaInterface;

class Like implements CriteriaInterface
{
    public $column;
    public $term;

    public function __construct($column, $term)
    {
        $this->column = $column;
        $this->term = $term;
    }

    public function apply($model, BaseRepositoryInterface $repository)
    {
        $term = sprintf('%%%s%%', $this->term);

        return $model->where($this->column, 'LIKE', $term);
    }
}
