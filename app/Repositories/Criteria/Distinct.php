<?php

namespace App\Repositories\Criteria;

use Czim\Repository\Contracts\BaseRepositoryInterface;
use Czim\Repository\Contracts\CriteriaInterface;

class Distinct implements CriteriaInterface
{
    private $field;

    public function __construct($field = 'id')
    {
        $this->field = $field;
    }

    public function apply($model, BaseRepositoryInterface $repository)
    {
        return $model->distinct($this->field);
    }
}
