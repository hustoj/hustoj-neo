<?php

namespace Tests\Unit;

use App\Entities\Contest;
use Carbon\Carbon;
use Tests\TestCase;

class ContestModelTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function testIsOpenDuringContestWindow()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 12:00:00'));

        $contest = $this->makeContest(
            '2026-05-21 10:00:00',
            '2026-05-21 18:00:00'
        );

        $this->assertTrue($contest->isOpen());
        $this->assertFalse($contest->isEnd());
    }

    public function testIsEndAfterContestWindow()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 20:00:00'));

        $contest = $this->makeContest(
            '2026-05-21 10:00:00',
            '2026-05-21 18:00:00'
        );

        $this->assertFalse($contest->isOpen());
        $this->assertTrue($contest->isEnd());
        $this->assertSame('0', $contest->time_left());
    }

    public function testTimeLeftBeforeContestEnds()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 12:00:00'));

        $contest = $this->makeContest(
            '2026-05-21 10:00:00',
            '2026-05-21 14:00:00'
        );

        $this->assertSame('2 hours', $contest->time_left());
    }

    public function testIsNotOpenBeforeContestStarts()
    {
        Carbon::setTestNow(Carbon::parse('2026-05-21 08:00:00'));

        $contest = $this->makeContest(
            '2026-05-21 10:00:00',
            '2026-05-21 18:00:00'
        );

        $this->assertFalse($contest->isOpen());
        $this->assertFalse($contest->isEnd());
    }

    public function testIsPublicAndPrivateFlags()
    {
        $public = $this->createContest(['private' => Contest::PUBLIC]);
        $private = $this->createContest(['private' => Contest::PRIVATE]);

        $this->assertTrue($public->isPublic());
        $this->assertFalse($private->isPublic());
    }

    public function testIsAvailableOnlyForNormalStatus()
    {
        $visible = $this->createContest(['status' => Contest::ST_NORMAL]);
        $hidden = $this->createContest(['status' => Contest::ST_HIDE]);

        $this->assertTrue($visible->isAvailable());
        $this->assertFalse($hidden->isAvailable());
    }

    private function makeContest(string $start, string $end): Contest
    {
        return $this->createContest([
            'start_time' => Carbon::parse($start),
            'end_time' => Carbon::parse($end),
        ]);
    }
}
