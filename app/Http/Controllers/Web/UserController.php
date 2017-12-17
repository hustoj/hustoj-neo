<?php

namespace App\Http\Controllers\Web;

use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\EditRequest;
use App\Http\Requests\User\PasswordRequest;
use App\Repositories\Criteria\OrderBy;
use App\Repositories\Criteria\Where;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Support\MessageBag;

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
        $offset = (request('page', 1) - 1) * $per_page + 1;

        $this->repository->pushCriteria(new Where('status', User::ST_ACTIVE));
        $this->repository->pushCriteria(new OrderBy('solved', 'desc'));
        $this->repository->pushCriteria(new OrderBy('submit', 'asc'));

        $users = $this->repository->paginate($per_page);

        return view('web.user.index', ['users' => $users, 'offset' => $offset]);
    }

    public function show($username)
    {
        /** @var User $user */
        $user = (new UserService())->findByName($username);

        if (!$user->isActive()) {
            return back()->withErrors('User is not found!');
        }

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
            $user->update($request->all());

            return redirect()->back();
        }

        return redirect(route('home'));
    }

    public function editPassword()
    {
        $user = auth()->user();
        if ($user) {
            return view('web.user.password')->with('user', $user);
        }

        return redirect(route('home'));
    }

    public function password(PasswordRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();
        if ($user) {
            if (app('hash')->make($request->input('password')) === $user->password) {
                $user->password = app('hash')->make($request->input('password_new'));
                $user->save();

                return redirect()->back()->with('success', trans('user.message.password_change_success'));
            }
            $errors = new MessageBag(['password' => trans('user.message.password_not_match')]);

            return redirect()->back()->withErrors($errors);
        }
    }
}
