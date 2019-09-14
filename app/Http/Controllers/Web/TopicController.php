<?php

namespace App\Http\Controllers\Web;

use App\Entities\Topic;
use App\Entities\User;
use App\Http\Controllers\Controller;
use App\Repositories\Criteria\OrderBy;
use App\Repositories\Criteria\Where;
use App\Repositories\TopicRepository;

class TopicController extends Controller
{
    private $repository;

    public function __construct(TopicRepository $repository)
    {
        $this->repository = $repository;
    }

    public function reply($id)
    {
        /** @var Topic $topic */
        $topic = $this->repository->find($id);
        if ($topic) {
            $reply = [
                'user_id'  => app('auth')->guard()->id(),
                'topic_id' => $id,
                'content'  => request('content'),
            ];
            $topic->replies()->create($reply);
        }

        return redirect(route('topic.view', ['id' => $id]));
    }

    public function create()
    {
        if (auth()->user()) {
            return view('web.topic.create');
        }

        return redirect(route('topic.list'))->withErrors('You should login first');
    }

    public function store()
    {
        $data = [
            'user_id'    => app('auth')->guard()->id(),
            'title'      => request('title'),
            'content'    => request('content'),
            'contest_id' => request('contest_id', 0),
            'problem_id' => request('problem_id', 0),
        ];

        $data['user_id'] = request()->user()->id;
        $this->repository->create($data);

        if (request()->filled('contest_id')) {
            return redirect(route('contest.clarify', ['contest' => request('contest_id')]));
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

        return view('web.topic.list')->with('topics', $topics);
    }

    public function show($id)
    {
        $topic = $this->repository->find($id);

        return view('web.topic.view', ['topic' => $topic]);
    }
}
