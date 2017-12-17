<?php

namespace App\Http\Controllers\Web;

use App\Entities\Problem;
use App\Http\Controllers\Controller;
use App\Repositories\Criteria\SearchByColumn;
use App\Repositories\Criteria\Where;
use App\Repositories\ProblemRepository;
use App\Services\SummaryService;

class ProblemController extends Controller
{
    private $repository;

    /**
     * ProblemController constructor.
     *
     * @param ProblemRepository $repository
     */
    public function __construct(ProblemRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param \App\Entities\Problem $problem
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($problem)
    {
        if (!$problem->isAvailable()) {
            return back()->withErrors('Problem is not found!');
        }

        return view('web.problem.show', ['problem' => $problem]);
    }

    public function index()
    {
        if (request('text')) {
            $searchByColumn = new SearchByColumn(request('text'), request('area'));
            $this->repository->pushCriteria($searchByColumn);
        }

        $this->repository->pushCriteria(new Where('status', Problem::ST_NORMAL));

        $perPage = request('per_page', 100);
        $problems = $this->repository->paginate($perPage);

        return view('web.problem.index', ['problems' => $problems]);
    }

    /**
     * @param Problem $problem
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function summary($problem)
    {
        if (!$problem->isAvailable()) {
            return back()->withErrors('Problem is not found!');
        }

        $summary = new SummaryService($problem);

        return view('web.problem.summary', ['summary' => $summary, 'perPage' => 50, 'problem' => $problem]);
    }
}
