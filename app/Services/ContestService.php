<?php

namespace App\Services;

use App\Entities\Contest;
use App\Entities\Permission;
use App\Entities\Problem;
use App\Exceptions\Contest\InvalidOrder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class ContestService
{
    public function getContestPermission(Contest $contest)
    {
        $name = contest_permission($contest);
        $perm = Permission::query()->where('name', $name)->first();

        if ($perm) {
            return $perm;
        }

        $perm = new Permission();
        $perm->name = $name;
        $perm->display_name = 'Contest '.$contest->id;
        $perm->description = 'Privilege For Contest '.$contest->id;
        $perm->save();

        return $perm;
    }

    /**
     * @throws InvalidOrder
     */
    public function getProblemByOrder(Contest $contest, $order): Problem
    {
        $order = strtoupper($order);
        if (! is_alpha($order)) {
            throw new InvalidOrder();
        }

        return $contest->problems()
            ->wherePivot('order', '=', original_order($order))
            ->first();
    }

    /**
     * @return Contest[]|Collection
     */
    public function openingContest()
    {
        // current is opening
        $now = Carbon::now();

        $query = Contest::query();
        $query->where('start_time', '<', $now)
            ->where('end_time', '>', $now)
            ->where('private', Contest::PUBLIC)
            ->where('status', Contest::ST_NORMAL);

        return $query->get();
    }
}
