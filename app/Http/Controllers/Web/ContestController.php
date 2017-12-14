<?php

namespace App\Http\Controllers\Web;

use App\Entities\Contest;
use App\Http\Controllers\Controller;
use App\Repositories\ContestRepository;
use App\Services\ContestService;
use App\Services\StandingService;
use App\Services\TopicService;

class ContestController extends Controller
{
    /** @var ContestService */
    private $contestService;

    public function __construct(ContestService $service)
    {
        $this->contestService = $service;
    }

    public function index()
    {
        $contests = $this->contestService->paginate(request('per_page', 100));

        return view('web.contest.index', ['contests' => $contests]);
    }

    /**
     * @param Contest $contest
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($contest)
    {
        /** @var Contest $contest */
        $contest = app(ContestRepository::class)->find($contest);

        $problems = $contest->problems()->orderBy('order')->get();

        return view('web.contest.view', ['contest' => $contest, 'problems' => $problems]);
    }

    /**
     * @param Contest $contest
     * @param         $order
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function problem($contest, $order)
    {
        $contest = app(ContestRepository::class)->find($contest);

        $problem = $this->contestService->getContestProblemByOrder($contest, $order);

        return view('web.contest.problem', ['contest' => $contest, 'problem' => $problem]);
    }

    public function submit($contest)
    {
        $contest = app(ContestRepository::class)->find($contest);

        if (!auth()->user()) {
            return redirect(route('contest.view', $contest->id))->with('error', 'Login first');
        }
        $problem = $this->contestService->getContestProblemByOrder($contest, request('order'));

        return view('web.contest.submit', compact('contest', 'problem'));
    }

    public function status($contest)
    {
        $contest = app(ContestRepository::class)->find($contest);

        $solutions = $this->contestService->getSolutions($contest);
        $solutions->load('user');

        return view('web.contest.status', compact('contest', 'solutions'));
    }

    public function standing($contest)
    {
        $contest = app(ContestRepository::class)->find($contest);

        $standing = new StandingService($contest);

        return view('web.contest.standing', ['contest' => $contest, 'standing' => $standing->result()]);
    }

    public function clarify($contest)
    {
        $contest = app(ContestRepository::class)->find($contest);

        $topics = (new TopicService())->topicsForContest($contest->id);

        return view('web.contest.clarify', compact('contest', 'topics'));
    }
}
