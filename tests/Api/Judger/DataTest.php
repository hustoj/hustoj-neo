<?php

namespace Tests\Api\Judger;

use App\Services\DataProvider;
use LogicException;
use Tests\TestCase;

class DataTest extends TestCase
{
    public function testDataReturnsProblemTestCasesForAuthorizedJudger()
    {
        $judger = $this->createJudger();
        $problem = $this->createProblem();
        $timestamp = time();

        $this->mock(DataProvider::class, function ($mock) use ($problem) {
            $mock->shouldReceive('getData')
                ->once()
                ->with($problem->id)
                ->andReturn([
                    ['input' => '1', 'output' => '1'],
                ]);
        });

        $response = $this->get(
            '/judge/api/data?pid='.$problem->id.'&ts='.$timestamp,
            $this->judgerHeaders($judger, $timestamp, 'GET', '/judge/api/data', ['pid' => $problem->id])
        );

        $response->assertStatus(200);
        $response->assertHeader('Content-Encoding', 'gzip');
        $response->assertHeader('Content-Type', 'application/x-download');

        $decoded = json_decode(gzdecode($response->getContent()), true);
        $this->assertSame([['input' => '1', 'output' => '1']], $decoded);
    }

    public function testDataReturnsMessageWhenProviderThrows()
    {
        $judger = $this->createJudger();
        $problem = $this->createProblem();
        $timestamp = time();

        $this->mock(DataProvider::class, function ($mock) use ($problem) {
            $mock->shouldReceive('getData')
                ->with($problem->id)
                ->andThrow(new LogicException('Problem Data is not match!'));
        });

        $response = $this->get(
            '/judge/api/data?pid='.$problem->id.'&ts='.$timestamp,
            $this->judgerHeaders($judger, $timestamp, 'GET', '/judge/api/data', ['pid' => $problem->id])
        );

        $response->assertStatus(200);
        $payload = json_decode(gzdecode($response->getContent()), true);
        $this->assertSame('Problem Data is not match!', $payload['message']);
    }

    public function testDataRejectsInvalidJudger()
    {
        $problem = $this->createProblem();
        $timestamp = time();

        $response = $this->get('/judge/api/data?pid='.$problem->id.'&ts='.$timestamp);

        $response->assertStatus(200);
        $payload = json_decode(gzdecode($response->getContent()), true);
        $this->assertSame([
            'code' => 500,
            'message' => 'auth code invalid',
        ], $payload);
    }

    public function testDataReturnsApiErrorWhenProblemIdIsMissing()
    {
        $judger = $this->createJudger();
        $timestamp = time();

        $this->mock(DataProvider::class, function ($mock) {
            $mock->shouldNotReceive('getData');
        });

        $response = $this->get(
            '/judge/api/data?ts='.$timestamp,
            $this->judgerHeaders($judger, $timestamp, 'GET', '/judge/api/data')
        );

        $response->assertStatus(200);

        $payload = json_decode(gzdecode($response->getContent()), true);
        $this->assertSame(500, $payload['code']);
        $this->assertSame('auth code invalid', $payload['message']);
    }
}
