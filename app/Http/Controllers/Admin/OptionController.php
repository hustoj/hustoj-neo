<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Option;

class OptionController extends DataController
{
    public function index()
    {
        $query = Option::query();

        if (request()->filled('id')) {
            $query->where('id', request('id'));
//            $this->repository->pushCriteria(new Where('id', request('id')));
        }

        if (request()->filled('key')) {
            $query->where('key', request('key'));
//            $this->repository->pushCriteria(new Where('key', request('key')));
        }

        if (request()->filled('category')) {
            $query->where('category', request('category'));
//            $this->repository->pushCriteria(new Where('category', request('category')));
        }

        return parent::paginate($query);
    }

    protected function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Option::query();
    }
}
