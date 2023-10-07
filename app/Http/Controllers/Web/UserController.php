<?php

namespace App\Http\Controllers\Web;

use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\EditRequest;
use App\Http\Requests\User\PasswordRequest;
use App\Services\User\UserSolutions;
use App\Services\UserService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\MessageBag;

class UserController extends Controller
{
    public function index()
    {
        $per_page = 100;
        $offset = (Paginator::resolveCurrentPage() - 1) * $per_page + 1;

        $query = User::query();
        $query->where('status', User::ST_ACTIVE)
            ->orderByDesc('solved')
            ->orderBy('submit');

        $users = $query->paginate($per_page);

//        $this->repository->pushCriteria(new Where('status', User::ST_ACTIVE));
//        $this->repository->pushCriteria(new OrderBy('solved', 'desc'));
//        $this->repository->pushCriteria(new OrderBy('submit', 'asc'));
//
//        $users = $this->repository->paginate($per_page);

        return view('web.user.index', ['users' => $users, 'offset' => $offset]);
    }

    public function show($username)
    {
        /** @var User $user */
        $user = app(UserService::class)->findByName($username);
        if (! $user) {
            // user may not exist
            return redirect(route('home'))->withErrors('user is not exist!');
        }

        if (! $user->isActive()) {
            return back()->withErrors('User is not found!');
        }

        $problems = app(UserSolutions::class)->getResolvedProblems($user);

        return view('web.user.view')->with('user', $user)
            ->with('problems', $problems);
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
            $emailChanged = $user->isDirty('email');
            if ($emailChanged) {
                $user->email_verified_at = null;
            }
            $user->save();
            if ($emailChanged) {
                $user->sendEmailVerificationNotification();

                return redirect()->back()->with('warning', __('Email has been modified, should verify again'));
            }

            return redirect()->back()->with('success', __('Profile Update Success!'));
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
        $user = $request->user();

        if (app('hash')->make($request->input('password')) === $user->password) {
            $user->password = app('hash')->make($request->input('password_new'));
            $user->save();

            return redirect()->back()->with('success', trans('user.message.password_change_success'));
        }
        $errors = new MessageBag(['password' => trans('user.message.password_not_match')]);

        return redirect()->back()->withErrors($errors);
    }
}
