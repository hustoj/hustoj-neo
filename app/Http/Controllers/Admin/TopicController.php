<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Topic;

class TopicController extends DataController
{
    protected function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Topic::query();
    }
}
