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
        $solution->forceFill(['judge_token' => 'job-token'])->save();
        $timestamp = time();

        $payload = [
            'ts' => $timestamp,
            'solution_id' => $solution->id,
            'judge_token' => 'job-token',
            'status' => Status::ACCEPT,
            'time_cost' => 120,
            'memory_cost' => 2048,
        ];

        $response = $this->json(
            'post',
            '/judge/api/report',
            $payload,
            $this->judgerHeaders($judger, $timestamp, 'POST', '/judge/api/report', $payload)
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

    public function testReportStoresCompileInfoOnCompileError()
    {
        $judger = $this->createJudger();
        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem, ['result' => Status::COMPILE_ERROR]);
        $solution->forceFill(['judge_token' => 'compile-token'])->save();
        $timestamp = time();

        $payload = [
            'ts' => $timestamp,
            'solution_id' => $solution->id,
            'judge_token' => 'compile-token',
            'status' => Status::COMPILE_ERROR,
            'compile_info' => 'syntax error near line 1',
        ];

        $response = $this->json(
            'post',
            '/judge/api/report',
            $payload,
            $this->judgerHeaders($judger, $timestamp, 'POST', '/judge/api/report', $payload)
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('compile_info', [
            'solution_id' => $solution->id,
            'content' => 'syntax error near line 1',
        ]);
    }

    public function testReportStoresRuntimeInfoOnRuntimeError()
    {
        $judger = $this->createJudger();
        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem, ['result' => Status::RUNTIME_ERROR]);
        $solution->forceFill(['judge_token' => 'runtime-token'])->save();
        $timestamp = time();

        $payload = [
            'ts' => $timestamp,
            'solution_id' => $solution->id,
            'judge_token' => 'runtime-token',
            'status' => Status::RUNTIME_ERROR,
            'runtime_info' => 'SIGSEGV',
        ];

        $response = $this->json(
            'post',
            '/judge/api/report',
            $payload,
            $this->judgerHeaders($judger, $timestamp, 'POST', '/judge/api/report', $payload)
        );

        $response->assertStatus(200);
        $this->assertDatabaseHas('runtime_info', [
            'solution_id' => $solution->id,
            'content' => 'SIGSEGV',
        ]);
    }

    public function testReportReturnsSuccessForMissingSolution()
    {
        $judger = $this->createJudger();
        $timestamp = time();

        $payload = [
            'ts' => $timestamp,
            'solution_id' => 999999,
            'judge_token' => 'missing-token',
            'status' => Status::ACCEPT,
        ];

        $response = $this->json(
            'post',
            '/judge/api/report',
            $payload,
            $this->judgerHeaders($judger, $timestamp, 'POST', '/judge/api/report', $payload)
        );

        $response->assertStatus(200);
        $response->assertExactJson(['code' => 0]);
    }

    public function testReportRejectsWrongJudgeToken()
    {
        $judger = $this->createJudger();
        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem);
        $solution->forceFill(['judge_token' => 'expected-token'])->save();
        $timestamp = time();

        $payload = [
            'ts' => $timestamp,
            'solution_id' => $solution->id,
            'judge_token' => 'wrong-token',
            'status' => Status::ACCEPT,
        ];

        $response = $this->json(
            'post',
            '/judge/api/report',
            $payload,
            $this->judgerHeaders($judger, $timestamp, 'POST', '/judge/api/report', $payload)
        );

        $response->assertStatus(200);
        $response->assertExactJson([
            'code' => 403,
            'message' => 'judge token invalid',
        ]);
        $this->assertSame(Status::PENDING, $solution->fresh()->result);
    }

    public function testReportAllowsLegacyTaskWithoutJudgeToken()
    {
        $judger = $this->createJudger();
        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem);
        $timestamp = time();

        $payload = [
            'ts' => $timestamp,
            'solution_id' => $solution->id,
            'status' => Status::ACCEPT,
        ];

        $response = $this->json(
            'post',
            '/judge/api/report',
            $payload,
            $this->judgerHeaders($judger, $timestamp, 'POST', '/judge/api/report', $payload)
        );

        $response->assertStatus(200);
        $response->assertExactJson(['code' => 0]);
        $this->assertSame(Status::ACCEPT, $solution->fresh()->result);
    }
}
