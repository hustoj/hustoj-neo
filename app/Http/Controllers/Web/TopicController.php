<?php

namespace App\Http\Controllers\Web;

use App\Entities\Topic;
use App\Entities\User;
use App\Exceptions\WebException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Topic\ReplyStoreRequest;
use App\Http\Requests\Topic\StoreRequest;
use App\Repositories\Criteria\OrderBy;
use App\Repositories\Criteria\Where;
use App\Repositories\TopicRepository;
use App\Services\Topic\Validator\UserValidator;

class TopicController extends Controller
{
    private $repository;

    public function __construct(TopicRepository $repository)
    {
        $this->repository = $repository;
    }

    public function reply($id, ReplyStoreRequest $request)
    {
        /** @var User $user */
        $user = auth()->user();
        try {
            app(UserValidator::class)->validate($user);
        } catch (WebException $exception) {
            return redirect(route('topic.list'))->withErrors($exception->getMessage());
        }

        /** @var Topic $topic */
        $topic = $this->repository->find($id);
        if ($topic) {
            $reply = [
                'user_id' => app('auth')->guard()->id(),
                'topic_id' => $id,
                'content' => $request->getBody(),
            ];
            $topic->replies()->create($reply);
        }

        return redirect(route('topic.view', ['id' => $id]));
    }

    public function create()
    {
        /** @var User $user */
        $user = auth()->user();
        try {
            app(UserValidator::class)->validate($user);
        } catch (WebException $exception) {
            return redirect(route('topic.list'))->withErrors($exception->getMessage());
        }
        return view('web.topic.create');
    }

    public function store(StoreRequest $request)
    {
        /** @var User $user */
        $user = $request->user();
        try {
            app(UserValidator::class)->validate($user);
        } catch (WebException $exception) {
            return redirect(route('topic.list'))->withErrors($exception->getMessage());
        }

        $data = [
            'user_id' => $user->id,
            'title' => $request->getTitle(),
            'content' => $request->getBody(),
            'contest_id' => $request->getContestId(),
            'problem_id' => $request->getProblemId(),
        ];

        $this->repository->create($data);

        if ($request->filled('contest_id')) {
            return redirect(route('contest.clarify', ['contest' => $request->getContestId()]));
        }

        return redirect(route('topic.list'));
    }

    public function index()
    {
        if (request()->filled('uid')) {
            /** @var User $user */
            $user = User::query()->where('username', request()->input('uid'))->first();
            if ($user) {
                $this->repository->pushCriteria(new Where('user_id', $user->id));
            }
        }
        if (request()->filled('pid')) {
            $this->repository->pushCriteria(new Where('problem_id', request('pid')));
        }
        $this->repository->pushCriteria(new OrderBy('id', 'desc'));
        // 获取非比赛的clarity
        $this->repository->pushCriteria(new Where('contest_id', 1, '<'));
        $topics = $this->repository->paginate(request('per_page', 50));
        $topics->load("user");

        return view('web.topic.list')->with('topics', $topics);
    }

    public function show($id)
    {
        $topic = $this->repository->findOrFail($id);
        /** @var User $user */
        $user = auth()->user();

        $isUserCanReply = false;
        if ($user) {
            $isUserCanReply = app(UserValidator::class)->isUserColdDown($user);
        }

        return view('web.topic.view', [
            'topic' => $topic,
            'isUserCanReply' => $isUserCanReply
        ]);
    }
}
