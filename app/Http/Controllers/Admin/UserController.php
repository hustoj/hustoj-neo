<?php

namespace App\Http\Controllers\Admin;

use App\Entities\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\MassAssignmentException;

class UserController extends DataController
{
    public function index()
    {
        $query = User::query();
        if (request()->filled('id')) {
            $query->where('id', request('id'));
        }

        if (request()->filled('name')) {
            $query->where('username', 'like', where_like(request('name')));
        }

        if (request()->filled('email')) {
            $query->where('email', 'like', where_like(request('email')));
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        /** @var Collection $models */
        $models = parent::paginate($query);
        $models->load('roles');

        foreach ($models as $model) {
            $model->roles_edit = $model->roles->map(function ($role) {
                return $role->id;
            });
            $model->makeHidden('roles');
        }

        return $models;
    }

    public function update($id)
    {
        /** @var User $model */
        $model = User::query()->findOrFail($id);

        try {
            $model->fill(request()->all());
            if (request('password')) {
                $model->password = app('hash')->make(request('password'));
            }
            $model->save();
        } catch (MassAssignmentException $e) {
            app('log')->error($e->getMessage());
        }

        $model->roles()->sync(request('roles_edit'));

        return $model;
    }

    protected function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return User::query();
    }
}
