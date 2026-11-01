<?php

namespace Tests\Api\Judger;

use App\Entities\Solution;
use App\Services\JudgeTaskToken;
use App\Status;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use RuntimeException;
use Tests\TestCase;

class ReportTest extends TestCase
{
    public function testReportUpdatesSolution()
    {
        $judger = $this->createJudger();
        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem);
        $token = app(JudgeTaskToken::class)->issue($solution->id);
        $timestamp = time();

        $payload = [
            'ts' => $timestamp,
            'solution_id' => $solution->id,
            'judge_token' => $token,
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
        $this->assertFalse(app(JudgeTaskToken::class)->consume($solution->id, $token));
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
        $token = app(JudgeTaskToken::class)->issue($solution->id);
        $timestamp = time();

        $payload = [
            'ts' => $timestamp,
            'solution_id' => $solution->id,
            'judge_token' => $token,
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
        $token = app(JudgeTaskToken::class)->issue($solution->id);
        $timestamp = time();

        $payload = [
            'ts' => $timestamp,
            'solution_id' => $solution->id,
            'judge_token' => $token,
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

    public function testReportRejectsMissingSolutionWithoutIssuedToken()
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
        $response->assertExactJson([
            'code' => 403,
            'message' => 'judge token invalid',
        ]);
    }

    public function testReportRejectsWrongJudgeToken()
    {
        $judger = $this->createJudger();
        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem);
        app(JudgeTaskToken::class)->issue($solution->id);
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

    public function testReportRejectsMissingJudgeToken()
    {
        $judger = $this->createJudger();
        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem);
        app(JudgeTaskToken::class)->issue($solution->id);
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
        $response->assertExactJson([
            'code' => 403,
            'message' => 'judge token invalid',
        ]);
        $this->assertSame(Status::PENDING, $solution->fresh()->result);
    }

    public function testReportRejectsReplayAfterSuccess()
    {
        $judger = $this->createJudger();
        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem);
        $token = app(JudgeTaskToken::class)->issue($solution->id);
        $timestamp = time();

        $payload = [
            'ts' => $timestamp,
            'solution_id' => $solution->id,
            'judge_token' => $token,
            'status' => Status::ACCEPT,
        ];
        $headers = $this->judgerHeaders($judger, $timestamp, 'POST', '/judge/api/report', $payload);

        $this->json('post', '/judge/api/report', $payload, $headers)
            ->assertExactJson(['code' => 0]);

        $replayTimestamp = $timestamp + 1;
        $replayPayload = array_merge($payload, ['ts' => $replayTimestamp, 'status' => Status::WRONG_ANSWER]);
        $replayHeaders = $this->judgerHeaders($judger, $replayTimestamp, 'POST', '/judge/api/report', $replayPayload);

        $this->json('post', '/judge/api/report', $replayPayload, $replayHeaders)
            ->assertExactJson([
                'code' => 403,
                'message' => 'judge token invalid',
            ]);

        $this->assertSame(Status::ACCEPT, $solution->fresh()->result);
    }

    public function testReportCanBeRetriedWhenSavingTheResultFails()
    {
        $judger = $this->createJudger();
        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem);
        $token = app(JudgeTaskToken::class)->issue($solution->id);
        $failed = false;

        Solution::updating(function () use (&$failed) {
            if (! $failed) {
                $failed = true;
                throw new RuntimeException('database unavailable');
            }
        });

        $timestamp = time();
        $payload = [
            'ts' => $timestamp,
            'solution_id' => $solution->id,
            'judge_token' => $token,
            'status' => Status::ACCEPT,
        ];
        $headers = $this->judgerHeaders($judger, $timestamp, 'POST', '/judge/api/report', $payload);

        $this->withoutExceptionHandling();
        try {
            $this->json('post', '/judge/api/report', $payload, $headers);
            $this->fail('Expected the first report to fail while saving the solution.');
        } catch (RuntimeException $exception) {
            $this->assertSame('database unavailable', $exception->getMessage());
        } finally {
            Event::forget('eloquent.updating: '.Solution::class);
        }

        $this->assertTrue($failed);
        $this->assertSame(Status::PENDING, $solution->fresh()->result);

        $retryTimestamp = $timestamp + 1;
        $retryPayload = array_merge($payload, ['ts' => $retryTimestamp]);
        $retryHeaders = $this->judgerHeaders(
            $judger,
            $retryTimestamp,
            'POST',
            '/judge/api/report',
            $retryPayload
        );
        $this->assertNotSame($headers['Nonce'], $retryHeaders['Nonce']);

        $this->json('post', '/judge/api/report', $retryPayload, $retryHeaders)
            ->assertStatus(200)
            ->assertExactJson(['code' => 0]);
        $this->assertSame(Status::ACCEPT, $solution->fresh()->result);
        $this->assertFalse(app(JudgeTaskToken::class)->consume($solution->id, $token));
    }

    public function testReportKeepsTokenAfterCompilingStatus()
    {
        $judger = $this->createJudger();
        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem);
        $token = app(JudgeTaskToken::class)->issue($solution->id);
        $timestamp = time();

        $compiling = [
            'ts' => $timestamp,
            'solution_id' => $solution->id,
            'judge_token' => $token,
            'status' => Status::COMPILING,
        ];
        $this->json(
            'post',
            '/judge/api/report',
            $compiling,
            $this->judgerHeaders($judger, $timestamp, 'POST', '/judge/api/report', $compiling)
        )->assertExactJson(['code' => 0]);
        $this->assertSame(Status::COMPILING, $solution->fresh()->result);

        $acceptTimestamp = $timestamp + 1;
        $accept = [
            'ts' => $acceptTimestamp,
            'solution_id' => $solution->id,
            'judge_token' => $token,
            'status' => Status::ACCEPT,
        ];
        $this->json(
            'post',
            '/judge/api/report',
            $accept,
            $this->judgerHeaders($judger, $acceptTimestamp, 'POST', '/judge/api/report', $accept)
        )->assertExactJson(['code' => 0]);
        $this->assertSame(Status::ACCEPT, $solution->fresh()->result);
        $this->assertFalse(app(JudgeTaskToken::class)->consume($solution->id, $token));
    }

    public function testReportReturnsBusyWhenTokenLockTimesOut()
    {
        config(['hustoj.services.judge.task_token.lock_wait' => 0]);

        $judger = $this->createJudger();
        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem);
        $token = app(JudgeTaskToken::class)->issue($solution->id);
        $lock = Cache::store('array')->lock('judger:task:'.$solution->id.':lock', 30);
        $this->assertTrue($lock->get());

        $timestamp = time();
        $payload = [
            'ts' => $timestamp,
            'solution_id' => $solution->id,
            'judge_token' => $token,
            'status' => Status::ACCEPT,
        ];

        try {
            $this->json(
                'post',
                '/judge/api/report',
                $payload,
                $this->judgerHeaders($judger, $timestamp, 'POST', '/judge/api/report', $payload)
            )->assertStatus(503)->assertExactJson([
                'code' => 503,
                'message' => 'judge token busy',
            ]);
            $this->assertSame(Status::PENDING, $solution->fresh()->result);
        } finally {
            $lock->release();
        }
    }
}
