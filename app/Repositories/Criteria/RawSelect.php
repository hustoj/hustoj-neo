<?php

namespace App\Repositories\Criteria;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Contracts\RepositoryInterface as Repository;
use Bosnadev\Repositories\Criteria\Criteria;

class RawSelect extends Criteria
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

    /**
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository)
    {
        return $model->select(\DB::raw($this->rawSql));
    }
}