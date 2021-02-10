<?php


namespace App\Http\Controllers\Admin\User;


use App\Http\Controllers\Admin\DataController;
use App\Repositories\Criteria\Where;
use App\Repositories\LoginLogRepository;

class LoggingController extends DataController
{
    public function index()
    {
        if (request()->filled('user_id')) {
            $this->repository->pushCriteria(new Where('user_id', request('user_id')));
        }

        return parent::index();
    }

    protected function getRepository()
    {
        return LoginLogRepository::class;
    }
}
