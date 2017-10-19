<?php

namespace App\Http\Controllers\Web;

use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\EditRequest;
use App\Repositories\Criteria\OrderBy;
use App\Repositories\UserRepository;
use App\Services\UserService;

class UserController extends Controller
{
    private $repository;

    /**
     * UserController constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $per_page = 100;
        $offset   = (request('page', 1) - 1) * $per_page + 1;
        $this->repository->pushCriteria(new OrderBy('solved', 'desc'));
        $this->repository->pushCriteria(new OrderBy('submit', 'asc'));
        $users = $this->repository->paginate($per_page);

        return view('web.user.index', ['users' => $users, 'offset' => $offset]);
    }

    public function show($username)
    {
        $user = (new UserService())->findByName($username);
        if ($user) {
            return view('web.user.view')->with('user', $user);
        }

        return redirect(route('home'));
    }

    public function profile()
    {
        $user = auth()->user();
        if ($user) {
            return view('web.user.edit')->with('user', $user);
        }

        return redirect(route('home'));
    }

    public function edit(EditRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user) {
            $user->fill($request->all());
            if ($request->input('password')) {
                $user->password = app('hash')->make($request->input('password'));
                session()->flash('success', 'Password Change Success!');
            } else {
                session()->flash('success', 'Profile Saved!');
            }
            $user->save();
        }

        return redirect()->back();
    }
}
