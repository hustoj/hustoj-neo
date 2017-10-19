<?php

namespace App\Http\Controllers\Admin;

use App\Entities\User;
use App\Repositories\Criteria\Where;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\MassAssignmentException;

class UserController extends DataController
{
    public function index()
    {
        if (request('id')) {
            $this->repository->pushCriteria(new Where('id', request('id')));
        }

        if (request('name')) {
            $this->repository->pushCriteria(new Where('username', request('name')));
        }

        if (request('email')) {
            $this->repository->pushCriteria(new Where('email', request('email')));
        }

        if (request('disable') >= 0) {
            $this->repository->pushCriteria(new Where('disable', request('disable')));
        }

        /** @var Collection $models */
        $models = parent::index();
        $models->load('lastAccess', 'roles');

        foreach ($models as $model) {
            $model->access = $model->lastAccess->first();
            $model->addHidden('lastAccess');

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
