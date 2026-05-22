<?php

namespace App\Http\Controllers\Web;

use App\Entities\Contest;
use App\Entities\Problem;
use App\Entities\Solution;
use App\Entities\Topic;
use App\Entities\User;
use App\Exceptions\Contest\InvalidOrder;
use App\Http\Controllers\Controller;
use App\Services\ContestService;
use App\Services\Ranking;

class ContestController extends Controller
{
    private ContestService $contestService;

    public function __construct(ContestService $service)
    {
        $this->contestService = $service;
    }

    public function index()
    {
        $query = Contest::query()->where('status', Contest::ST_NORMAL);
        $query->orderByDesc('id');

        $contests = $query->paginate(request('per_page', 100));

        return view('web.contest.index', ['contests' => $contests]);
    }

    public function show($id)
    {
        /** @var Contest $contest */
        $contest = Contest::query()->findOrFail($id);

        $problems = $contest->problems;

        return view('web.contest.view', ['contest' => $contest, 'problems' => $problems]);
    }

    public function problem($id, $order)
    {
        /** @var Contest $contest */
        $contest = Contest::query()->findOrFail($id);

        try {
            $problem = $this->resolveContestProblem($contest, $order);
        } catch (InvalidOrder $exception) {
            return back()->withErrors($exception->getMessage());
        }

        if ($problem === null) {
            return back()->withErrors('Problem not found in contest!');
        }

        return view('web.contest.problem', ['contest' => $contest, 'problem' => $problem]);
    }

    public function submit($id)
    {
        /** @var Contest $contest */
        $contest = Contest::query()->findOrFail($id);

        if ($contest->isEnd()) {
            return redirect(route('contest.view', $contest->id))
                ->withErrors('Contest is End!');
        }

        if (! auth()->user()) {
            return redirect(route('contest.view', $contest->id))
                ->withErrors('Login first');
        }

        try {
            $problem = $this->resolveContestProblem($contest, request('order'));
        } catch (InvalidOrder $exception) {
            return redirect(route('contest.view', $contest->id))
                ->withErrors($exception->getMessage());
        }

        if ($problem === null) {
            return redirect(route('contest.view', $contest->id))
                ->withErrors('Problem not found in contest!');
        }

        return view('web.contest.submit', compact('contest', 'problem'));
    }

    /**
     * @throws InvalidOrder
     */
    private function resolveContestProblem(Contest $contest, $order): ?Problem
    {
        if ($order === null || $order === '') {
            throw new InvalidOrder();
        }

        return $this->contestService->getProblemByOrder($contest, $order);
    }

    public function status($id)
    {
        /** @var Contest $contest */
        $contest = Contest::query()->findOrFail($id);

        $query = Solution::query();

        if (request()->filled('username')) {
            /** @var User $user */
            $user = User::query()->where('username', request('username'))->first();
            if ($user == null) {
                $solutions = [];

                return view('web.contest.status', compact('contest', 'solutions'));
            }
            $query->where('user_id', $user->id);
        }

        if (request('problem_id')) {
            $order = ord(strtolower(request('problem_id'))) - ord('a');
            $query->where('order', $order);
        }

        if (request()->filled('language')) {
            $query->where('language', request('language'));
        }

        if (request()->filled('status')) {
            $query->where('result', request('status'));
        }

        $solutions = $query->where('contest_id', $contest->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->paginate(request('per_page', 50));

        return view('web.contest.status', compact('contest', 'solutions'));
    }

    public function standing($id)
    {
        /** @var Contest $contest */
        $contest = Contest::query()->findOrFail($id);

        $standing = new Ranking($contest);

        return view('web.contest.standing', [
            'contest' => $contest,
            'teams' => $standing->result(),
        ]);
    }

    public function clarify($id)
    {
        /** @var Contest $contest */
        $contest = Contest::query()->findOrFail($id);

        $topics = Topic::query()->where('contest_id', $id)
            ->orderByDesc('created_at')
            ->paginate(request('per_page', 50));

        return view('web.contest.clarify', compact('contest', 'topics'));
    }
}
