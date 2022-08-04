<?php

namespace App\Services\Contest;

use App\Entities\Contest;
use Carbon\Carbon;

class ContestManager
{
    /**
     * @param  array  $attrs
     * @param  string  $startAt
     * @param  string  $endAt
     * @return \App\Entities\Contest
     */
    public function create($attrs, $startAt, $endAt)
    {
        $model = new Contest();

        return $this->update($model, $attrs, $startAt, $endAt);
    }

    /**
     * @param  Contest  $contest
     * @param  array  $attrs
     * @param  string  $startAt
     * @param  string  $endAt
     * @return \App\Entities\Contest
     */
    public function update($contest, $attrs, $startAt, $endAt)
    {
        $contest->fill($attrs);
        $contest->start_time = Carbon::parse($startAt);
        $contest->end_time = Carbon::parse($endAt);

        $contest->save();

        return $contest;
    }

    /**
     * @param  Contest  $contest
     * @param  array  $problemIds
     */
    public function syncProblems($contest, $problemIds)
    {
        $relations = [];
        $index = 0;
        foreach ($problemIds as $id) {
            $relations[$id] = ['order' => $index];
            $index++;
        }
        $contest->problems()->sync($relations);
    }

    /**
     * @param  Contest  $contest
     * @param  array  $userIds
     */
    public function syncUser($contest, $userIds)
    {
        $contest->users()->sync($userIds);
    }
}
