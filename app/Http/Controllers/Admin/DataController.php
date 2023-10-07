<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\Model;

abstract class DataController extends Controller
{
    abstract protected function getQuery(): \Illuminate\Database\Eloquent\Builder;

    public function index()
    {
        $query = $this->getQuery();

        return $this->paginate($query);
    }

    protected function paginate(Builder $query)
    {
        $query->orderByDesc('id');

        return $query->paginate(request('per_page'));
    }

    public function store()
    {
        return $this->getQuery()->create(request()->all());
    }

    /**
     * @param  $id
     * @return mixed
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update($id)
    {
        if ($id instanceof Model) {
            $model = $id;
        } else {
            $model = $this->getQuery()->findOrFail($id);
        }

        try {
            $values = array_filter(request()->all(), function ($v) {
                return $v != null;
            });
            $model->fill($values);
            $model->save();
        } catch (MassAssignmentException $e) {
            logger()->error($e->getMessage());
        }

        return $model;
    }

    public function destroy($id)
    {
        if ($id instanceof Model) {
            $id->delete();
        } else {
            $model = $this->getQuery()->findOrFail($id);
            $model->delete();
        }
    }
}
