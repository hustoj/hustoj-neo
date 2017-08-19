<?php

namespace App\Repositories\Criteria;

use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Criteria\Criteria;

class Like extends Criteria
{
    public $column;
    public $term;

    public function __construct($column, $term)
    {
        $this->column = $column;
        $this->term = $term;
    }

    /**
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        $term = sprintf('%%%s%%', $this->term);
        return $model->where($this->column, 'LIKE', $term);
    }
}