<?php

namespace Tests\Unit;

use App\Entities\Team;
use App\Status;
use Carbon\Carbon;
use Tests\TestCase;

class TeamTest extends TestCase
{
    public function testAcceptIncrementsSolvedCountOncePerProblem()
    {
        $contest = $this->createContest();
        $problem = $this->createProblem();
        $this->attachProblemToContest($contest, $problem, 0);
        $contest->load('problems');

        $user = $this->createUser();
        $team = new Team($contest);
        $team->setUser($user);

        $first = $this->createContestSolution($user, $problem, $contest, [
            'order' => 0,
            'result' => Status::ACCEPT,
            'created_at' => Carbon::parse('2026-05-21 11:00:00'),
        ]);
        $later = $this->createContestSolution($user, $problem, $contest, [
            'order' => 0,
            'result' => Status::ACCEPT,
            'created_at' => Carbon::parse('2026-05-21 12:00:00'),
        ]);

        $team->addSolution($first);
        $team->addSolution($later);

        $this->assertSame(1, $team->numberOfAccept());
        $this->assertTrue($team->isProblemAccept(0));
    }

    public function testWrongAnswerBeforeAcceptAddsPenalty()
    {
        $contest = $this->createContest([
            'start_time' => Carbon::parse('2026-05-21 10:00:00'),
            'end_time' => Carbon::parse('2026-05-21 18:00:00'),
        ]);
        $problem = $this->createProblem();
        $this->attachProblemToContest($contest, $problem, 0);
        $contest->load('problems');

        $user = $this->createUser();
        $team = new Team($contest);
        $team->setUser($user);

        $wa = $this->createContestSolution($user, $problem, $contest, [
            'order' => 0,
            'result' => Status::WRONG_ANSWER,
            'created_at' => Carbon::parse('2026-05-21 11:00:00'),
        ]);
        $ac = $this->createContestSolution($user, $problem, $contest, [
            'order' => 0,
            'result' => Status::ACCEPT,
            'created_at' => Carbon::parse('2026-05-21 11:30:00'),
        ]);

        $team->addSolution($wa);
        $team->addSolution($ac);

        $this->assertSame(1, $team->getProblemWACount(0));
        $this->assertSame(20 * 60, $team->getPenaltyOfProblem(0));
    }
}
