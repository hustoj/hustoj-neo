<?php

namespace App\Http\Controllers\Admin;

use App\Entities\User;
use App\Repositories\Criteria\Like;
use App\Repositories\Criteria\Where;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\MassAssignmentException;

class UserController extends DataController
{
    public function index()
    {
        if (request()->filled('id')) {
            $this->repository->pushCriteria(new Where('id', request('id')));
        }

        if (request()->filled('name')) {
            $this->repository->pushCriteria(new Like('username', request('name')));
        }

        if (request()->filled('email')) {
            $this->repository->pushCriteria(new Like('email', request('email')));
        }

        if (request()->filled('status')) {
            $this->repository->pushCriteria(new Where('status', request('status')));
        }

        /** @var Collection $models */
        $models = parent::index();
        $models->load('roles');

        foreach ($models as $model) {
            $model->roles_edit = $model->roles->map(function ($role) {
                return $role->id;
            });
            $model->addHidden('roles');
        }

        return $models;
    }

    public function update($id)
    {
        /** @var User $model */
        $model = $this->repository->findOrFail($id);

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

    protected function getRepository()
    {
        return UserRepository::class;
    }
}
