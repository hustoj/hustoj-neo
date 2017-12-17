<?php

namespace App\Http\Controllers\Web;

use App\Entities\Solution;
use App\Http\Controllers\Controller;
use App\Http\Requests\Solution\IndexRequest;
use App\Repositories\Criteria\OrderBy;
use App\Repositories\Criteria\Where;
use App\Repositories\SolutionRepository;
use App\Services\UserService;
use Czim\Repository\Criteria\Common\WithRelations;

class SolutionController extends Controller
{
    public function index(IndexRequest $request)
    {
        /** @var SolutionRepository $repository */
        $repository = app(SolutionRepository::class);

        if ($request->getUserName()) {
            $user = app(UserService::class)->findByName($request->getUserName());
            $repository->pushCriteria(new Where('user_id', $user->id));
        }

        if ($request->getProblemId()) {
            $repository->pushCriteria(new Where('problem_id', $request->getProblemId()));
        }

        if ($request->getLanguage() != -1) {
            $filter = new Where('language', $request->getLanguage());
            $repository->pushCriteria($filter);
        }

        if ($request->getStatus() != -1 && $request->getStatus()) {
            $filter = new Where('result', $request->getStatus());
            $repository->pushCriteria($filter);
        }

        $per_page = 100;
        $repository->pushCriteria(new WithRelations(['user']));
        $repository->pushCriteria(new OrderBy('id', 'desc'));
        $solutions = $repository->paginate($per_page);

        return view('web.solution.index')->with('solutions', $solutions);
    }

    public function create($problem)
    {
        if (!auth()->user()) {
            return redirect(route('problem.view', ['problem' => $problem->id]))->withErrors('Login first');
        }

        return view('web.problem.submit', ['problem' => $problem]);
    }

    public function store()
    {
        $data = [
            'user_id'    => app('auth')->guard()->id(),
            'problem_id' => request('problem_id', 0),
            'language'   => request('language'),
            'ip'         => request()->ip(),
            'order'      => request('order', 0),
            'contest_id' => request('contest_id', 0),
            'code'       => request('code', ''),
            'result'     => Solution::STATUS_PENDING,
        ];

        /** @var SolutionRepository $repository */
        $repository = app(SolutionRepository::class);
        $repository->create($data);

        return redirect(route('solution.index'));
    }

    public function source($solution)
    {
        return view('web.solution.source')->with('solution', $solution);
    }

    public function compileInfo($solution)
    {
        if (!$this->hasPrivilege($solution)) {
            return back()->withErrors('You cannot access this solution');
        }

        return view('web.solution.compile_info')->with('solution', $solution);
    }

    public function runtimeInfo($solution)
    {
        if (!$this->hasPrivilege($solution)) {
            return back()->withErrors('You cannot access this solution');
        }

        return view('web.solution.runtime_info')->with('solution', $solution);
    }

    /**
     * @param \App\Entities\Solution $solution
     *
     * @return bool
     */
    private function hasPrivilege($solution)
    {
        /** @var \App\Entities\User $currentUser */
        $currentUser = app('auth')->user();

        if ($currentUser->hasRole('admin')) {
            return true;
        }
        if ($solution->user_id === $currentUser->id) {
            return true;
        }

        return false;
    }
}
