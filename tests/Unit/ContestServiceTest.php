<?php

namespace Tests\Unit;

use App\Entities\Contest;
use App\Exceptions\Contest\InvalidOrder;
use App\Services\ContestService;
use Carbon\Carbon;
use Tests\TestCase;

class ContestServiceTest extends TestCase
{
    private ContestService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ContestService::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function testOpeningContestReturnsOnlyPublicContestsInWindow()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 12:00:00'));

        $openPublic = $this->createContest([
            'title' => 'Open Public',
            'private' => Contest::PUBLIC,
            'start_time' => Carbon::parse('2026-05-21 10:00:00'),
            'end_time' => Carbon::parse('2026-05-21 18:00:00'),
        ]);
        $this->createContest([
            'title' => 'Open Private',
            'private' => Contest::PRIVATE,
            'start_time' => Carbon::parse('2026-05-21 10:00:00'),
            'end_time' => Carbon::parse('2026-05-21 18:00:00'),
        ]);
        $this->createContest([
            'title' => 'Ended Public',
            'private' => Contest::PUBLIC,
            'start_time' => Carbon::parse('2026-05-20 10:00:00'),
            'end_time' => Carbon::parse('2026-05-20 18:00:00'),
        ]);
        $this->createContest([
            'title' => 'Hidden Public',
            'private' => Contest::PUBLIC,
            'status' => Contest::ST_HIDE,
            'start_time' => Carbon::parse('2026-05-21 10:00:00'),
            'end_time' => Carbon::parse('2026-05-21 18:00:00'),
        ]);

        $opening = $this->service->openingContest();

        $this->assertCount(1, $opening);
        $this->assertTrue($opening->contains('id', $openPublic->id));
    }

    public function testGetProblemByOrderReturnsAttachedProblem()
    {
        $contest = $this->createContest();
        $problem = $this->createProblem(['title' => 'Contest Problem A']);
        $this->attachProblemToContest($contest, $problem, 0);

        $found = $this->service->getProblemByOrder($contest, 'A');

        $this->assertNotNull($found);
        $this->assertSame($problem->id, $found->id);
    }

    public function testGetProblemByOrderAcceptsLowercaseLetter()
    {
        $contest = $this->createContest();
        $problem = $this->createProblem();
        $this->attachProblemToContest($contest, $problem, 1);

        $found = $this->service->getProblemByOrder($contest, 'b');

        $this->assertSame($problem->id, $found->id);
    }

    public function testGetProblemByOrderThrowsForNonAlphaOrder()
    {
        $contest = $this->createContest();

        $this->expectException(InvalidOrder::class);

        $this->service->getProblemByOrder($contest, '1');
    }

    public function testGetProblemByOrderReturnsNullWhenOrderNotInContest()
    {
        $contest = $this->createContest();
        $problem = $this->createProblem();
        $this->attachProblemToContest($contest, $problem, 0);

        $found = $this->service->getProblemByOrder($contest, 'B');

        $this->assertNull($found);
    }
}
