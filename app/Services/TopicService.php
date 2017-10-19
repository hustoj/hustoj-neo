<?php

namespace App\Services;

use App\Repositories\Criteria\OrderBy;
use App\Repositories\Criteria\Where;
use App\Repositories\TopicRepository;

class TopicService
{
    /**
     * TopicService constructor.
     */
    public function __construct()
    {
        $this->repository = app(TopicRepository::class);
    }

    private $repository;

    public function topicsForContest($contestId)
    {
        $this->repository->pushCriteria(new Where('contest_id', $contestId));
        $this->repository->pushCriteria(new OrderBy('created_at', 'desc'));

        return $this->repository->paginate(request('per_page', 50));
    }
}
