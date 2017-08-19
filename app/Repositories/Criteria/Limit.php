<?php

namespace App\Repositories\Criteria;

use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Criteria\Criteria;

class Limit extends Criteria
{
    private $limit;

    public function __construct($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->limit($this->limit);
    }
}