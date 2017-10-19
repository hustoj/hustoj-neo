<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Criteria\Where;
use App\Repositories\RoleRepository;

class RoleController extends DataController
{
    public function index()
    {
        if (request('name')) {
            $this->repository->pushCriteria(new Where('name', request('name')));
        }

        return parent::index();
    }

    protected function getRepository()
    {
        return RoleRepository::class;
    }
}
