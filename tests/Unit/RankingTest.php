<?php

namespace Tests\Unit;

use App\Services\Ranking;
use App\Status;
use Carbon\Carbon;
use Tests\TestCase;

class RankingTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function testRankingSortsByAcceptCountThenPenalty()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 12:00:00'));

        $contest = $this->createContest([
            'start_time' => Carbon::parse('2026-05-21 10:00:00'),
            'end_time' => Carbon::parse('2026-05-21 18:00:00'),
        ]);
        $problemA = $this->createProblem();
        $problemB = $this->createProblem();
        $this->attachProblemToContest($contest, $problemA, 0);
        $this->attachProblemToContest($contest, $problemB, 1);
        $contest->load('problems');

        $fast = $this->createUser(['username' => 'fast_team']);
        $slow = $this->createUser(['username' => 'slow_team']);

        $this->createContestSolution($fast, $problemA, $contest, [
            'order' => 0,
            'result' => Status::ACCEPT,
            'created_at' => Carbon::parse('2026-05-21 10:30:00'),
        ]);
        $this->createContestSolution($fast, $problemB, $contest, [
            'order' => 1,
            'result' => Status::ACCEPT,
            'created_at' => Carbon::parse('2026-05-21 10:45:00'),
        ]);

        $this->createContestSolution($slow, $problemA, $contest, [
            'order' => 0,
            'result' => Status::ACCEPT,
            'created_at' => Carbon::parse('2026-05-21 10:30:00'),
        ]);

        $teams = (new Ranking($contest))->result();

        $this->assertCount(2, $teams);
        $this->assertSame('fast_team', $teams[0]->user()->username);
        $this->assertSame(2, $teams[0]->numberOfAccept());
        $this->assertSame(1, $teams[1]->numberOfAccept());
    }

    public function testRankingIgnoresSubmissionsOutsideContestWindow()
    {
        $contest = $this->createContest([
            'start_time' => Carbon::parse('2026-05-21 10:00:00'),
            'end_time' => Carbon::parse('2026-05-21 12:00:00'),
        ]);
        $problem = $this->createProblem();
        $this->attachProblemToContest($contest, $problem, 0);
        $contest->load('problems');

        $user = $this->createUser();
        $this->createContestSolution($user, $problem, $contest, [
            'order' => 0,
            'result' => Status::ACCEPT,
            'created_at' => Carbon::parse('2026-05-21 13:00:00'),
        ]);

        $teams = (new Ranking($contest))->result();

        $this->assertCount(0, $teams);
    }
}
