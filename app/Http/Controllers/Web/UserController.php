<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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

        return view('web.user.view')->with('user', $user);
    }

    public function profile()
    {
        $user = auth()->user();

        return view('web.user.edit')->with('user', $user);
    }

    public function edit()
    {
        $user = auth()->user();
        $user->fill(request()->all());
        if (request('password') && (request('password') == request('password_confirmation'))) {

        }
        $user->save();

        return redirect()->back();
    }
}