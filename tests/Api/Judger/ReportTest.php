<?php

namespace Tests\Api\Judger;

use App\Status;
use Tests\TestCase;

class ReportTest extends TestCase
{
    public function testReportUpdatesSolution()
    {
        $judger = $this->createJudger();
        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem);
        $timestamp = time();

        $response = $this->json(
            'post',
            '/judge/api/report',
            [
                'ts' => $timestamp,
                'solution_id' => $solution->id,
                'status' => Status::ACCEPT,
                'time_cost' => 120,
                'memory_cost' => 2048,
            ],
            $this->judgerHeaders($judger, $timestamp)
        );

        $response->assertStatus(200);
        $response->assertExactJson(['code' => 0]);

        $solution->refresh();
        $this->assertSame(Status::ACCEPT, $solution->result);
        $this->assertSame(120, $solution->time_cost);
        $this->assertSame(2048, $solution->memory_cost);
    }

    public function testReportRejectsInvalidJudger()
    {
        $response = $this->json('post', '/judge/api/report', [
            'solution_id' => 1,
            'status' => Status::ACCEPT,
        ]);

        $response->assertStatus(200);
        $response->assertExactJson([
            'code' => 500,
            'message' => 'auth code invalid',
        ]);
    }
}
