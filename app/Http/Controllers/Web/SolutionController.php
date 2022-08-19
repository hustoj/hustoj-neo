<?php

namespace App\Http\Controllers\Web;

use App\Entities\Problem;
use App\Entities\Solution;
use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Solution\IndexRequest;
use App\Services\UserService;
use App\Status;
use App\Task\SolutionQueue;

class SolutionController extends Controller
{
    public function index(IndexRequest $request)
    {
        $query = Solution::query();

        if ($request->getUserName()) {
            $user = app(UserService::class)->findByName($request->getUserName());
            if ($user != null) {
                $query->where('user_id', $user->id);
            }
        }

        if ($request->getProblemId()) {
            $query->where('problem_id', $request->getProblemId());
        }

        if ($request->getLanguage() != -1) {
            $query->where('language', $request->getLanguage());
        }

        if ($request->getStatus() != -1 && $request->getStatus()) {
            $query->where('result', $request->getStatus());
        }

        $solutions = $query->with(['user'])
            ->orderByDesc('id')
            ->paginate(request('per_page', 100));

        return view('web.solution.index')->with('solutions', $solutions);
    }

    public function create($id)
    {
        /** @var User $user */
        $user = auth()->user();
        if (! $user) {
            return redirect(route('problem.view', ['problem' => $id]))->withErrors(__('Login first'));
        }

        /** @var Problem $problem */
        $problem = Problem::query()->findOrFail($id);

        if (! config('hustoj.special_judge_enabled') && $problem->isSpecialJudge()) {
            return redirect(route('problem.view', ['problem' => $id]))
                ->withErrors(__('solution.alert.special_judge_disabled'));
        }

        return view('web.problem.submit', ['problem' => $problem]);
    }

    public function store()
    {
        $data = [
            'user_id' => app('auth')->guard()->id(),
            'problem_id' => request('problem_id', 0),
            'language' => request('language'),
            'ip' => request()->ip(),
            'order' => request('order', 0),
            'contest_id' => request('contest_id', 0),
            'code_length' => strlen(request('code')),
            'result' => Status::PENDING,
        ];

        /** @var Solution $solution */
        $solution = Solution::query()->create($data);
        $solution->source()->create([
            'code' => request('code', ''),
        ]);

        app(SolutionQueue::class)->add($solution);

        return redirect(route('solution.index'));
    }

    public function source($id)
    {
        /** @var Solution $solution */
        $solution = Solution::query()->findOrFail($id);
        if (! can_view_code($solution)) {
            return redirect(route('solution.index'))->withErrors(__('You have no permission access solution source'));
        }

        return view('web.solution.source')->with('solution', $solution);
    }

    public function compileInfo($id)
    {
        /** @var Solution $solution */
        $solution = Solution::query()->findOrFail($id);
        if (! can_view_code($solution)) {
            return back()->withErrors(__('You cannot access this solution'));
        }

        return view('web.solution.compile_info')->with('solution', $solution);
    }

    public function runtimeInfo($id)
    {
        /** @var Solution $solution */
        $solution = Solution::query()->findOrFail($id);
        if (! can_view_code($solution)) {
            return back()->withErrors(__('You cannot access this solution'));
        }

        return view('web.solution.runtime_info')->with('solution', $solution);
    }
}
