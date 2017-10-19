<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repositories\Criteria\SearchByColumn;
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

    public function show($problem)
    {
        return view('web.problem.show', ['problem' => $problem]);
    }

    public function index()
    {
        if (request('text')) {
            $searchByColumn = new SearchByColumn(request('text'), request('area'));
            $this->repository->pushCriteria($searchByColumn);
        }

        $perPage = request('per_page', 100);
        $problems = $this->repository->paginate($perPage);

        return view('web.problem.index', ['problems' => $problems]);
    }

    public function summary($problem)
    {
        $summary = new SummaryService($problem);

        return view('web.problem.summary', ['summary' => $summary, 'perPage' => 50, 'problem' => $problem]);
    }
}
