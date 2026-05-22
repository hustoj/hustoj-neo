<?php

namespace Tests\Unit;

use App\Services\SummaryService;
use App\Status;
use Tests\TestCase;

class SummaryServiceTest extends TestCase
{
    public function testSummaryAggregatesResultStatistics()
    {
        $problem = $this->createProblem();
        $user = $this->createUser();

        $this->createSolution($user, $problem, ['result' => Status::ACCEPT]);
        $this->createSolution($user, $problem, ['result' => Status::WRONG_ANSWER]);
        $this->createSolution($this->createUser(), $problem, ['result' => Status::ACCEPT]);

        $summary = new SummaryService($problem);

        $this->assertSame($problem->id, $summary->getProblem()->id);
        $this->assertSame(2, $summary->accepted());
        $this->assertSame(2, $summary->submit());
        $this->assertSame(1, $summary->statistics[Status::WRONG_ANSWER]);
        $this->assertSame(3, $summary->total);
    }
}
