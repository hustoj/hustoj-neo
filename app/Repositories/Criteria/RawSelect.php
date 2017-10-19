<?php

namespace App\Repositories\Criteria;

use Czim\Repository\Contracts\BaseRepositoryInterface;
use Czim\Repository\Contracts\CriteriaInterface;

class RawSelect implements CriteriaInterface
{
    private $rawSql;

    /**
     * RawSelect constructor.
     *
     * @param $rawSql
     */
    public function __construct($rawSql)
    {
        $this->rawSql = $rawSql;
    }

    public function apply($model, BaseRepositoryInterface $repository)
    {
        return $model->select(app('db')->raw($this->rawSql));
    }
}
