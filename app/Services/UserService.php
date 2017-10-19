<?php

namespace App\Services;

use App\Repositories\Criteria\GroupBy;
use App\Repositories\Criteria\OrderBy;
use App\Repositories\Criteria\RawSelect;
use App\Repositories\Criteria\Where;
use App\Repositories\UserRepository;
use Carbon\Carbon;

class UserService
{
    /** @var UserRepository */
    private $repository;

    /**
     * UserService constructor.
     */
    public function __construct()
    {
        $this->repository = app(UserRepository::class);
    }

    /**
     * @param Carbon $from
     *
     * @return mixed
     */
    public function getUserStats($from)
    {
        $this->repository->clearCriteria();
        $rawSql = 'DATE_FORMAT(created_at,\'%Y-%m-%d\') as date, count(*) as number';
        $this->repository->pushCriteria(new RawSelect($rawSql));
        $this->repository->pushCriteria(new Where('created_at', $from->format('Y-m-d h:i:s'), '>'));
        $this->repository->pushCriteria(new GroupBy('date'));
        $this->repository->pushCriteria(new OrderBy('created_at', 'desc'));

        return $this->repository->all();
    }

    public function findByName($username)
    {
        return $this->repository->findBy('username', $username);
    }
}
