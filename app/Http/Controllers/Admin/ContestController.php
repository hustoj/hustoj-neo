<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Contest;
use App\Services\Contest\ContestManager;
use Illuminate\Database\Eloquent\Model;

class ContestController extends DataController
{
    public function index()
    {
        $query = Contest::query();

        if (request()->filled('id')) {
            $query->where('id', request('id'));
        }
        if (request()->filled('title')) {
            $query->where('title', 'like', where_like(request('title')));
        }
        if (request()->filled('private')) {
            $query->where('private', request('private'));
        }

        return parent::paginate($query);
    }

    public function problems($contestId)
    {
        /** @var Contest $contest */
        $contest = Contest::query()->findOrFail($contestId);

        return $contest->problems;
    }

    public function users($contestId)
    {
        /** @var Contest $contest */
        $contest = Contest::query()->findOrFail($contestId);

        return $contest->users;
    }

    public function store()
    {
        app('db')->transaction(function () {
            $manager = new ContestManager();
            $attrs = request()->except(['start_time', 'end_time']);
            $contest = $manager->create($attrs, request('start_time'), request('end_time'));
            if (request()->has('problem_list')) {
                $manager->syncProblems($contest, request('problem_list', []));
            }
            if (request()->has('user_list')) {
                $manager->syncProblems($contest, request('user_list', []));
            }
        });
    }

    public function update($contest)
    {
        /* @var Contest $contest */
        if (! ($contest instanceof Model)) {
            $contest = Contest::query()->findOrFail($contest);
        }

        app('db')->transaction(function () use ($contest) {
            $manager = new ContestManager();
            $attrs = request()->except(['start_time', 'end_time']);
            $attrs = array_filter($attrs, function ($v) {
                return $v != null;
            });
            $contest = $manager->update($contest, $attrs, request('start_time'), request('end_time'));
            if (request()->has('problem_list')) {
                $manager->syncProblems($contest, request('problem_list', []));
            }
            if (request()->has('user_list')) {
                $manager->syncProblems($contest, request('user_list', []));
            }
        });
    }

    protected function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Contest::query();
    }
}
