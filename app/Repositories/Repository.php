<?php

namespace App\Repositories;

use Bosnadev\Repositories\Eloquent\Repository as BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Repository
 *
 * @package App\Repositories
 *
 */
abstract class Repository extends BaseRepository
{
    /** @var Model|Builder */
    protected $model;

    /**
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    public function resetScope()
    {
        $this->model = $this->makeModel();
    }
}