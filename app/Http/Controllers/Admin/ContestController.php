<?php

namespace App\Http\Controllers\Admin;

use App\Entities\Contest;
use App\Repositories\ContestRepository;
use App\Repositories\Criteria\Like;
use App\Repositories\Criteria\Where;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ContestController extends DataController
{
    public function index()
    {
        if (request('id')) {
            $this->repository->pushCriteria(new Where('id', request('id')));
        }
        if (request('title')) {
            $this->repository->pushCriteria(new Like('title', request('title')));
        }
        if (request('private', -1) != -1) {
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
        $model = new Contest();

        $this->updateOrSave($model);
    }

    public function update($id)
    {
        /* @var Contest $model */
        if ($id instanceof Model) {
            $model = $id;
        } else {
            $model = $this->repository->findOrFail($id);
        }

        $this->updateOrSave($model);
    }

    /**
     * @param Contest $model ;
     */
    protected function updateOrSave($model)
    {
        app('db')->transaction(function () use ($model) {
            $attrs = request()->except(['start_time', 'end_time']);
            $model->fill($attrs);
            $model->start_time = Carbon::parse(request('start_time'));
            $model->end_time = Carbon::parse(request('end_time'));
            $model->save();

            if (request()->has('problem_list')) {
                $problemIds = request('problem_list', []);
                $relations = [];
                $index = 0;
                foreach ($problemIds as $id) {
                    $relations[$id] = ['order' => $index];
                    $index++;
                }
                $model->problems()->sync($relations);
            }

            if (request()->has('user_list')) {
                $userIds = request('user_list', []);
                $model->users()->sync($userIds);
            }
        });
    }

    protected function getRepository()
    {
        return ContestRepository::class;
    }
}
