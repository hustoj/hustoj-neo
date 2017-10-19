<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Criteria\Like;
use App\Repositories\Criteria\Where;
use App\Repositories\PostRepository;

class ArticleController extends DataController
{
    public function index()
    {
        if (request('id')) {
            $this->repository->pushCriteria(new Where('id', request('id')));
        }
        if (request('title')) {
            $this->repository->pushCriteria(new Like('title', request('title')));
        }
        if (request('draft', -1) != -1) {
            $this->repository->pushCriteria(new Where('draft', request('draft')));
        }

        return parent::index();
    }

    public function store()
    {
        $attrs = request()->all();
        $attrs['user_id'] = request('user_id', request()->user()->id);

        return $this->repository->create($attrs);
    }

    protected function getRepository()
    {
        return PostRepository::class;
    }
}
