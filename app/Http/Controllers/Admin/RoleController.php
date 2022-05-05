<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Role;

class RoleController extends DataController
{
    public function index()
    {
        $query = Role::query();
        if (request()->filled('name')) {
            $query->where('name', request('name'));
        }

        return parent::paginate($query);
    }

    protected function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Role::query();
    }
}
