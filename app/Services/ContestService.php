<?php

namespace App\Services;

use App\Entities\Contest;
use App\Entities\Permission;
use App\Entities\Problem;
use App\Entities\Solution;
use App\Repositories\ContestRepository;
use App\Repositories\Criteria\OrderBy;
use App\Repositories\Criteria\Where;
use App\Repositories\PermissionRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ContestService
{
    /** @var ContestRepository */
    private $repository;

    /**
     * ContestService constructor.
     */
    public function __construct()
    {
        $this->repository = app(ContestRepository::class);
    }

    public static function permissionOfContest($contest)
    {
        return 'contest.' . $contest->id;
    }

    /**
     * @param $contest
     *
     * @return Permission
     */
    public function getContestPermission($contest)
    {
        $name = self::permissionOfContest($contest);
        /** @var PermissionRepository $repository */
        $repository = app(PermissionRepository::class);
        $perms      = $repository->findBy('name', $name);
        if ($perms->count()) {
            return $perms->first();
        }
        $perm               = new Permission();
        $perm->name         = $name;
        $perm->display_name = 'Contest .' . $contest->id;
        $perm->description  = 'Privilege For Contest .' . $contest->id;
        $perm->save();

        return $perm;
    }

    /**
     * @param Contest $contest
     * @param         $order
     *
     * @return Problem
     */
    public function getContestProblemByOrder($contest, $order)
    {
        return $contest->problems()->wherePivot('order', '=', original_order($order))->first();
    }

    public function openingContest()
    {
        $this->repository->clearCriteria();

        $now = Carbon::now();
        $this->repository->pushCriteria(new Where('start_time', $now, '<'));
        $this->repository->pushCriteria(new Where('end_time', $now, '>'));

        return $this->repository->all();
    }

    public function paginate($per_page)
    {
        $this->repository->pushCriteria(new OrderBy('id', 'desc'));

        return $this->repository->paginate($per_page);
    }

    /**
     * @param $contest
     *
     * @return Solution[]|Collection
     */
    public function getSolutions($contest)
    {
        return app(SolutionService::class)->forContest($contest);
    }
}