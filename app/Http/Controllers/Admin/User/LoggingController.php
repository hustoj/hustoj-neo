<?php

namespace App\Http\Controllers\Admin\User;

use App\Entities\LoginLog;
use App\Http\Controllers\Admin\DataController;

class LoggingController extends DataController
{
    public function index()
    {
        $query = LoginLog::query();
        if (request()->filled('user_id')) {
            $query->where('user_id', request('user_id'));
        }

        return parent::paginate($query);
    }

    protected function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return LoginLog::query();
    }
}
