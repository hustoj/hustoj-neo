<?php

namespace Tests\Unit;

use App\Services\ProblemService;
use App\Services\SummaryService;
use App\Status;
use Tests\TestCase;

class ProblemServiceTest extends TestCase
{
    private ProblemService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProblemService();
    }

    public function testNumberOfAcceptedUserCountsDistinctUsers()
    {
        $problem = $this->createProblem();
        $first = $this->createUser();
        $second = $this->createUser();

        $this->createSolution($first, $problem, ['result' => Status::ACCEPT]);
        $this->createSolution($first, $problem, ['result' => Status::ACCEPT]);
        $this->createSolution($second, $problem, ['result' => Status::ACCEPT]);
        $this->createSolution($second, $problem, ['result' => Status::WRONG_ANSWER]);

        $this->assertSame(2, $this->service->numberOfAcceptedUser($problem->id));
        $this->assertSame(2, $this->service->numberOfSubmitUser($problem->id));
    }

    public function testGetResultCountGroupsByResult()
    {
        $problem = $this->createProblem();
        $user = $this->createUser();

        $this->createSolution($user, $problem, ['result' => Status::ACCEPT]);
        $this->createSolution($user, $problem, ['result' => Status::WRONG_ANSWER]);

        $counts = collect($this->service->getResultCount($problem->id))
            ->pluck('user_count', 'result')
            ->all();

        $this->assertSame(1, $counts[Status::ACCEPT]);
        $this->assertSame(1, $counts[Status::WRONG_ANSWER]);
    }
}
