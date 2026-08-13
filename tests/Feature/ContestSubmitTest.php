<?php

namespace Tests\Feature;

use App\Entities\Contest;
use App\Entities\Solution;
use App\Task\SolutionQueue;
use Carbon\Carbon;
use Tests\TestCase;

class ContestSubmitTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function testEndedContestSubmitRedirectsEnrolledUserToContestView()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-22 20:00:00'));

        $user = $this->createUser();
        $contest = $this->createContest([
            'private' => Contest::PRIVATE,
            'start_time' => Carbon::parse('2026-05-21 10:00:00'),
            'end_time' => Carbon::parse('2026-05-21 18:00:00'),
        ]);
        $contest->users()->attach($user->id);
        $problem = $this->createProblem();
        $this->attachProblemToContest($contest, $problem, 0);

        $response = $this->actingAs($user)->get('/contest/'.$contest->id.'/submit?order=A');

        $response->assertRedirect(route('contest.view', $contest->id));
        $response->assertSessionHasErrors();
    }

    public function testActiveContestSubmitRedirectsGuestToContestViewWithLoginError()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 12:00:00'));

        $contest = $this->createContest([
            'private' => Contest::PUBLIC,
            'start_time' => Carbon::parse('2026-05-21 10:00:00'),
            'end_time' => Carbon::parse('2026-05-21 18:00:00'),
        ]);
        $problem = $this->createProblem();
        $this->attachProblemToContest($contest, $problem, 0);

        $response = $this->get('/contest/'.$contest->id.'/submit?order=A');

        $response->assertRedirect(route('contest.view', $contest->id));
        $response->assertSessionHasErrors();
    }

    public function testEndedPublicContestSubmitRedirectsGuestBeforeLoginCheck()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-22 20:00:00'));

        $contest = $this->createContest([
            'private' => Contest::PUBLIC,
            'start_time' => Carbon::parse('2026-05-21 10:00:00'),
            'end_time' => Carbon::parse('2026-05-21 18:00:00'),
        ]);

        $response = $this->get('/contest/'.$contest->id.'/submit?order=A');

        $response->assertRedirect(route('contest.view', $contest->id));
        $response->assertSessionHasErrors();
    }

    public function testContestSubmissionPersistsContestContext()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 12:00:00'));

        $user = $this->createVerifiedUser();
        $contest = $this->createContest([
            'private' => Contest::PRIVATE,
            'start_time' => Carbon::parse('2026-05-21 10:00:00'),
            'end_time' => Carbon::parse('2026-05-21 18:00:00'),
        ]);
        $contest->users()->attach($user->id);
        $problem = $this->createProblem();
        $this->attachProblemToContest($contest, $problem, 1);

        $this->mock(SolutionQueue::class, function ($mock) {
            $mock->shouldReceive('add')->once();
        });

        $response = $this->actingAs($user)->post('/contest/'.$contest->id.'/submit', [
            'problem_id' => $problem->id,
            'order' => 'B',
            'language' => 1,
            'code' => 'int main() { return 0; }',
        ]);

        $response->assertRedirect(route('solution.index'));
        $solution = Solution::query()->where('user_id', $user->id)->first();
        $this->assertNotNull($solution);
        $this->assertSame($contest->id, $solution->contest_id);
        $this->assertSame(1, $solution->order);
        $this->assertSame($problem->id, $solution->problem_id);
    }

    public function testContestSubmissionRejectsProblemNotMatchingOrder()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 12:00:00'));

        $user = $this->createVerifiedUser();
        $contest = $this->createContest([
            'private' => Contest::PUBLIC,
            'start_time' => Carbon::parse('2026-05-21 10:00:00'),
            'end_time' => Carbon::parse('2026-05-21 18:00:00'),
        ]);
        $problem = $this->createProblem();
        $otherProblem = $this->createProblem();
        $this->attachProblemToContest($contest, $problem, 0);

        $response = $this->actingAs($user)->post('/contest/'.$contest->id.'/submit', [
            'problem_id' => $otherProblem->id,
            'order' => 'A',
            'language' => 1,
            'code' => 'int main() { return 0; }',
        ]);

        $response->assertRedirect(route('contest.view', $contest->id));
        $response->assertSessionHasErrors();
        $this->assertSame(0, Solution::query()->where('user_id', $user->id)->count());
    }
}
