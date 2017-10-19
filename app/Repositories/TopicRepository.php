<?php

namespace App\Repositories;

use App\Entities\Topic;

class TopicRepository extends Repository
{
    public function model()
    {
        return Topic::class;
    }
}
