<?php

namespace App\Services;

use App\Entities\Contest;
use App\Entities\Permission;
use App\Entities\Problem;
use App\Repositories\ContestRepository;
use App\Repositories\Criteria\Where;
use App\Repositories\PermissionRepository;
use Carbon\Carbon;

class ContestService
{
    /** @var ContestRepository */
    private $repository;

    /**
     * @param $id
     *
     * @return Contest
     */
    public function getContest($id)
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * ContestService constructor.
     */
    public function __construct()
    {
        $this->repository = app(ContestRepository::class);
    }

    public static function permissionOfContest($contest)
    {
        return 'contest.'.$contest->id;
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
        $perms = $repository->findBy('name', $name);
        if ($perms->count()) {
            return $perms->first();
        }
        $perm = new Permission();
        $perm->name = $name;
        $perm->display_name = 'Contest .'.$contest->id;
        $perm->description = 'Privilege For Contest .'.$contest->id;
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

        // current is opening
        $now = Carbon::now();
        $this->repository->pushCriteria(new Where('start_time', $now, '<'));
        $this->repository->pushCriteria(new Where('end_time', $now, '>'));

        // should be public
        $this->repository->pushCriteria(new Where('private', Contest::PUBLIC));

        // should be normal
        $this->repository->pushCriteria(new Where('status', Contest::ST_NORMAL));

        return $this->repository->all();
    }
}
