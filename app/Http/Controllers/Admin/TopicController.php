<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\TopicRepository;

class TopicController extends DataController
{
    protected function getRepository()
    {
        return TopicRepository::class;
    }
}
