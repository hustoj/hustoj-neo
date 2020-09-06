<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Contest;
use App\Repositories\ContestRepository;
use App\Repositories\Criteria\Like;
use App\Repositories\Criteria\Where;
use App\Services\Contest\ContestManager;
use Illuminate\Database\Eloquent\Model;

class ContestController extends DataController
{
    public function index()
    {
        if (request()->filled('id')) {
            $this->repository->pushCriteria(new Where('id', request('id')));
        }
        if (request()->filled('title')) {
            $this->repository->pushCriteria(new Like('title', request('title')));
        }
        if (request()->filled('private')) {
            $this->repository->pushCriteria(new Where('private', request('private')));
        }

        return parent::index();
    }

    public function problems($contestId)
    {
        /** @var Contest $contest */
        $contest = $this->repository->findOrFail($contestId);

        return $contest->problems;
    }

    public function users($contestId)
    {
        /** @var Contest $contest */
        $contest = $this->repository->findOrFail($contestId);

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
        if (!($contest instanceof Model)) {
            $contest = $this->repository->findOrFail($contest);
        }

        app('db')->transaction(function () use ($contest) {
            $manager = new ContestManager();
            $attrs = request()->except(['start_time', 'end_time']);
            $contest = $manager->update($contest, $attrs, request('start_time'), request('end_time'));
            if (request()->has('problem_list')) {
                $manager->syncProblems($contest, request('problem_list', []));
            }
            if (request()->has('user_list')) {
                $manager->syncProblems($contest, request('user_list', []));
            }
        });
    }

    protected function getRepository()
    {
        return ContestRepository::class;
    }
}
