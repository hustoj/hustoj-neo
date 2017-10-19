<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Criteria\OrderBy;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\Model;

abstract class DataController extends Controller
{
    /** @var Repository */
    protected $repository;

    public function __construct()
    {
        $this->repository = app($this->getRepository());
    }

    public function index()
    {
        $this->repository->pushCriteria(new OrderBy('id', 'desc'));

        return $this->repository->paginate(request('per_page'));
    }

    public function store()
    {
        return $this->repository->create(request()->all());
    }

    /**
     * @param $id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @return mixed
     */
    public function update($id)
    {
        if ($id instanceof Model) {
            $model = $id;
        } else {
            $model = $this->repository->findOrFail($id);
        }

        try {
            $model->fill(request()->all());
            $model->save();
        } catch (MassAssignmentException $e) {
            app('log')->error($e->getMessage());
        }

        return $model;
    }

    public function destroy($id)
    {
        if ($id instanceof Model) {
            $id->delete();
        } else {
            $model = $this->repository->findOrFail($id);
            $model->delete();
        }
    }

    abstract protected function getRepository();
}
