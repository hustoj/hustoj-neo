<?php

namespace App\Repositories\Criteria;

use Czim\Repository\Contracts\BaseRepositoryInterface;
use Czim\Repository\Contracts\CriteriaInterface;

class Limit implements CriteriaInterface
{
    private $limit;

    public function __construct($limit)
    {
        $this->limit = $limit;
    }

    public function apply($model, BaseRepositoryInterface $repository)
    {
        return $model->limit($this->limit);
    }
}
