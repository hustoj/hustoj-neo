<?php

namespace App\Http\Controllers\Web;

use App\Entities\Problem;
use App\Http\Controllers\Controller;
use App\Services\SummaryService;

class ProblemController extends Controller
{
    public function show($id)
    {
        /** @var Problem $problem */
        $problem = Problem::query()->findOrFail($id);
        if (! $problem->isAvailable()) {
            return back()->withErrors('Problem is not found!');
        }

        return view('web.problem.show', ['problem' => $problem]);
    }

    public function index()
    {
        $query = Problem::query();
        if (request('text')) {
            $query->where(request('area'), 'like', where_like(request('text')));
        }
        $problems = $query->where('status', Problem::ST_NORMAL)
            ->orderBy('id')
            ->paginate(request('per_page', 100));

        return view('web.problem.index', ['problems' => $problems]);
    }

    public function summary($id)
    {
        /** @var Problem $problem */
        $problem = Problem::query()->findOrFail($id);
        if (! $problem->isAvailable()) {
            return back()->withErrors('Problem is not found!');
        }

        $summary = new SummaryService($problem);

        return view('web.problem.summary', ['summary' => $summary, 'perPage' => 50, 'problem' => $problem]);
    }
}
