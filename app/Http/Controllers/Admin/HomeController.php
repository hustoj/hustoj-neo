<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SolutionService;
use App\Services\UserService;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function dashboard()
    {
        return view('admin.app');
    }

    public function chart()
    {
        $result = [];

        $from = Carbon::create(null, null, null, 0, 0, 0);
        $from = $from->subDays(request('from'));

        $result['user'] = app(UserService::class)->getUserStats($from);
        $result['submission'] = app(SolutionService::class)->getSubmissionStats($from);

        return $result;
    }
}
