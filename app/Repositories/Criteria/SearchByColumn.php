<?php

namespace App\Repositories\Criteria;

use Czim\Repository\Contracts\BaseRepositoryInterface;
use Czim\Repository\Contracts\CriteriaInterface;

class SearchByColumn implements CriteriaInterface
{
    protected $column = 'title';
    protected $term = '';

    public function __construct($term, $column)
    {
        $this->column = $column;
        $this->term = $term;
    }

    public function apply($model, BaseRepositoryInterface $repository)
    {
        $query = $model->where($this->column, 'LIKE', $this->getTerm());

        return $query;
    }

    protected function getTerm()
    {
        return sprintf('%%%s%%', $this->term);
    }
}
