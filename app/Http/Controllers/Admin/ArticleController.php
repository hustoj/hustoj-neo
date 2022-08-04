<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Post;

class ArticleController extends DataController
{
    public function index()
    {
        $query = Post::query();
        if (request()->filled('id')) {
            $query->where('id', request('id'));
        }
        if (request()->filled('title')) {
            $query->where('title', 'like', where_like(request('title')));
        }
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        return parent::paginate($query);
    }

    public function store()
    {
        $attrs = request()->all();
        $attrs['user_id'] = request('user_id', request()->user()->id);

        return $this->getQuery()->create($attrs);
    }

    protected function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Post::query();
    }
}
