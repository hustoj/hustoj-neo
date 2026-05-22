<?php

namespace Tests\Feature;

use App\Entities\Contest;
use Carbon\Carbon;
use Tests\TestCase;

class ContestProblemTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function testMissingProblemOrderRedirectsBackWithErrors()
    {
        $user = $this->createUser();
        $contest = $this->createContest(['private' => Contest::PUBLIC]);
        $problem = $this->createProblem(['title' => 'Only Problem A']);
        $this->attachProblemToContest($contest, $problem, 0);

        $response = $this->from('/contest/'.$contest->id)
            ->actingAs($user)
            ->get('/contest/'.$contest->id.'/problem/B');

        $response->assertRedirect('/contest/'.$contest->id);
        $response->assertSessionHasErrors();
    }

    public function testInvalidProblemOrderRedirectsBackWithErrors()
    {
        $user = $this->createUser();
        $contest = $this->createContest(['private' => Contest::PUBLIC]);

        $response = $this->from('/contest/'.$contest->id)
            ->actingAs($user)
            ->get('/contest/'.$contest->id.'/problem/1');

        $response->assertRedirect('/contest/'.$contest->id);
        $response->assertSessionHasErrors();
    }

    public function testSubmitMissingOrderRedirectsToContestViewWithErrors()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 12:00:00'));

        $user = $this->createUser();
        $contest = $this->createContest([
            'private' => Contest::PUBLIC,
            'start_time' => Carbon::parse('2026-05-21 10:00:00'),
            'end_time' => Carbon::parse('2026-05-21 18:00:00'),
        ]);

        $response = $this->actingAs($user)->get('/contest/'.$contest->id.'/submit');

        $response->assertRedirect(route('contest.view', $contest->id));
        $response->assertSessionHasErrors();
    }

    public function testSubmitInvalidOrderRedirectsToContestViewWithErrors()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 12:00:00'));

        $user = $this->createUser();
        $contest = $this->createContest([
            'private' => Contest::PUBLIC,
            'start_time' => Carbon::parse('2026-05-21 10:00:00'),
            'end_time' => Carbon::parse('2026-05-21 18:00:00'),
        ]);

        $response = $this->actingAs($user)->get('/contest/'.$contest->id.'/submit?order=1');

        $response->assertRedirect(route('contest.view', $contest->id));
        $response->assertSessionHasErrors();
    }

    public function testSubmitUnknownProblemOrderRedirectsToContestViewWithErrors()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 12:00:00'));

        $user = $this->createUser();
        $contest = $this->createContest([
            'private' => Contest::PUBLIC,
            'start_time' => Carbon::parse('2026-05-21 10:00:00'),
            'end_time' => Carbon::parse('2026-05-21 18:00:00'),
        ]);
        $problem = $this->createProblem();
        $this->attachProblemToContest($contest, $problem, 0);

        $response = $this->actingAs($user)->get('/contest/'.$contest->id.'/submit?order=B');

        $response->assertRedirect(route('contest.view', $contest->id));
        $response->assertSessionHasErrors();
    }
}
