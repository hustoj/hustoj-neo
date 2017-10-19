<?php

namespace App\Services;

use App\Repositories\Criteria\Limit;
use App\Repositories\Criteria\OrderBy;
use App\Repositories\Criteria\Where;
use App\Repositories\LoginLogRepository;
use Carbon\Carbon;

class LoginLogService
{
    /** @var LoginLogRepository */
    private $repository;

    public function __construct()
    {
        $this->repository = app(LoginLogRepository::class);
    }

    public function getUserRecentRecord($user)
    {
        $this->repository->clearCriteria();
        $this->repository->pushCriteria(new Where('user_id', $user->id));
        $this->repository->pushCriteria(new Limit(10));
        $this->repository->pushCriteria(new OrderBy('id', 'desc'));

        return $this->repository->all();
    }

    public function getUserRecordsNumber($user)
    {
        $this->repository->clearCriteria();
        $this->repository->pushCriteria(new Where('user_id', $user->id));

        return $this->repository->count();
    }

    public function cleanUserLog($user)
    {
        $this->repository->clearCriteria();
        $this->repository->pushCriteria(new Where('user_id', $user->id));
        $total = $this->repository->count();

        if ($total > 10) {
            $theDay = Carbon::now()->subDays(90)->setTime(0, 0);
            $this->repository->pushCriteria(new Where('created_at', $theDay, '<'));
            $this->repository->query()->delete();
        }
    }
}
