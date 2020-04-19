<?php

namespace App\Http\Controllers\Web;

use App\Entities\Contest;
use App\Http\Controllers\Controller;
use App\Repositories\ContestRepository;
use App\Repositories\Criteria\OrderBy;
use App\Repositories\Criteria\Where;
use App\Repositories\SolutionRepository;
use App\Services\ContestService;
use App\Services\Ranking;
use App\Services\TopicService;
use App\Services\UserService;
use Czim\Repository\Criteria\Common\WithRelations;

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
        /** @var ContestRepository $repository */
        $repository = app(ContestRepository::class);
        $repository->clearCriteria();
        $repository->pushCriteria(new Where('status', Contest::ST_NORMAL));
        $repository->pushCriteria(new OrderBy('id', 'desc'));

        $contests = $repository->paginate(request('per_page', 100));

        return view('web.contest.index', ['contests' => $contests]);
    }

    public function show($id)
    {
        /** @var Contest $contest */
        $contest = app(ContestRepository::class)->findOrFail($id);

        $problems = $contest->problems;

        return view('web.contest.view', ['contest' => $contest, 'problems' => $problems]);
    }

    public function problem($id, $order)
    {
        /** @var Contest $contest */
        $contest = app(ContestRepository::class)->findOrFail($id);

        $problem = $this->contestService->getProblemByOrder($contest, $order);
        if ($problem === null) {
            return back()->withErrors('Problem not found in contest!');
        }

        return view('web.contest.problem', ['contest' => $contest, 'problem' => $problem]);
    }

    public function submit($id)
    {
        /** @var Contest $contest */
        $contest = app(ContestRepository::class)->findOrFail($id);

        if ($contest->isEnd()) {
            return redirect(route('contest.view', $contest->id))
                ->withErrors('Contest is End!');
        }

        if (! auth()->user()) {
            return redirect(route('contest.view', $contest->id))
                ->withErrors('Login first');
        }

        $problem = $this->contestService->getProblemByOrder($contest, request('order'));

        return view('web.contest.submit', compact('contest', 'problem'));
    }

    public function status($id)
    {
        $contest = $this->contestService->getContest($id);
        /** @var SolutionRepository $repository */
        $repository = app(SolutionRepository::class);

        if (request('username')) {
            $user = app(UserService::class)->findByName(request('username'));
            $repository->pushCriteria(new Where('user_id', $user->id));
        }

        if (request('problem_id')) {
            $order = ord(strtolower(request('problem_id'))) - ord('a');
            $repository->pushCriteria(new Where('order', $order));
        }

        if (request()->filled('language')) {
            $filter = new Where('language', request('language'));
            $repository->pushCriteria($filter);
        }

        if (request()->filled('status')) {
            $filter = new Where('result', request('status'));
            $repository->pushCriteria($filter);
        }

        $repository->pushCriteria(new Where('contest_id', $contest->id));

        $repository->pushCriteria(new WithRelations(['user']));
        $repository->pushCriteria(new OrderBy('created_at', 'desc'));
        $solutions = $repository->paginate(request('per_page', 50));

        return view('web.contest.status', compact('contest', 'solutions'));
    }

    public function standing($id)
    {
        /** @var Contest $contest */
        $contest = app(ContestRepository::class)->findOrFail($id);

        $standing = new Ranking($contest);

        return view('web.contest.standing', [
            'contest' => $contest,
            'teams' => $standing->result(),
        ]);
    }

    public function clarify($id)
    {
        /** @var Contest $contest */
        $contest = app(ContestRepository::class)->findOrFail($id);

        $topics = (new TopicService())->topicsForContest($contest->id);

        return view('web.contest.clarify', compact('contest', 'topics'));
    }
}
