<?php

namespace App\Repositories\Criteria;

use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Bosnadev\Repositories\Criteria\Criteria;

class SearchByColumn extends Criteria
{
    protected $column = 'title';
    protected $term   = '';

    function __construct($term, $column)
    {
        $this->column = $column;
        $this->term   = $term;
    }

    /**
     * @param                                $model
     * @param Repository                     $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $query = $model->where($this->column, 'LIKE', $this->getTerm());

        return $query;
    }

    protected function getTerm()
    {
        return sprintf('%%%s%%', $this->term);
    }
}