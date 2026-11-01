<?php

namespace Tests\Unit;

use App\Entities\LoginLog;
use App\Entities\Reply;
use App\Entities\Topic;
use App\Status;
use Carbon\Carbon;
use Tests\TestCase;

class ObserverAndListenerTest extends TestCase
{
    public function testSolutionCreatedObserverUpdatesUserSubmitCount()
    {
        $user = $this->createUser(['submit' => 0]);
        $problem = $this->createProblem();

        $this->createSolution($user, $problem);

        $user->refresh();
        $this->assertSame(1, $user->submit);
    }

    public function testSolutionAcceptedObserverUpdatesUserSolvedCount()
    {
        $user = $this->createUser(['submit' => 0, 'solved' => 0]);
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem, ['result' => Status::PENDING]);

        $solution->result = Status::ACCEPT;
        $solution->save();

        $user->refresh();
        $this->assertSame(1, $user->submit);
        $this->assertSame(1, $user->solved);
    }

    public function testUserDeletedObserverRemovesTopicsAndReplies()
    {
        $user = $this->createUser();
        $topic = $this->createTopic($user);
        Reply::query()->create([
            'user_id' => $user->id,
            'topic_id' => $topic->id,
            'content' => 'reply body',
        ]);

        $user->delete();

        $this->assertSame(0, Topic::query()->where('user_id', $user->id)->count());
        $this->assertSame(0, Reply::query()->where('user_id', $user->id)->count());
    }

    public function testLoginListenerRecordsSuccessfulLoginAndRefreshesAccessTime()
    {
        $user = $this->createUser([
            'username' => 'listeneruser',
            'password' => app('hash')->make('password'),
            'access_at' => Carbon::parse('2020-01-01 00:00:00'),
        ]);

        $this->post('/login', [
            'username' => 'listeneruser',
            'password' => 'password',
        ])->assertRedirect('/');

        $user->refresh();
        $this->assertDatabaseHas('logging', [
            'user_id' => $user->id,
            'status' => LoginLog::ST_OK,
        ]);
        $this->assertTrue($user->access_at->gt(Carbon::parse('2020-01-01 00:00:00')));
    }
}
