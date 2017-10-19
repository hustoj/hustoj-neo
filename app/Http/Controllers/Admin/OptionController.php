<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Criteria\Where;
use App\Repositories\OptionRepository;

class OptionController extends DataController
{
    public function index()
    {
        if (request()->has('id')) {
            $this->repository->pushCriteria(new Where('id', request('id')));
        }

        if (request()->has('key')) {
            $this->repository->pushCriteria(new Where('key', request('key')));
        }

        if (request()->has('category')) {
            $this->repository->pushCriteria(new Where('category', request('category')));
        }

        return parent::index();
    }

    protected function getRepository()
    {
        return OptionRepository::class;
    }
}
