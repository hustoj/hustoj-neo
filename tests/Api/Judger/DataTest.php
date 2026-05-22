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

        $this->mock(DataProvider::class, function ($mock) {
            $mock->shouldReceive('getData')
                ->once()
                ->andReturn([
                    ['input' => '1', 'output' => '1'],
                ]);
        });

        $response = $this->get(
            '/judge/api/data?pid='.$problem->id.'&ts='.$timestamp,
            $this->judgerHeaders($judger, $timestamp)
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

        $this->mock(DataProvider::class, function ($mock) {
            $mock->shouldReceive('getData')
                ->andThrow(new LogicException('Problem Data is not match!'));
        });

        $response = $this->get(
            '/judge/api/data?pid='.$problem->id.'&ts='.$timestamp,
            $this->judgerHeaders($judger, $timestamp)
        );

        $response->assertStatus(200);
        $payload = json_decode(gzdecode($response->getContent()), true);
        $this->assertSame('Problem Data is not match!', $payload['message']);
    }

    public function testDataRejectsInvalidJudger()
    {
        $problem = $this->createProblem();

        $response = $this->get('/judge/api/data?pid='.$problem->id);

        $response->assertStatus(200);
        $payload = json_decode(gzdecode($response->getContent()), true);
        $this->assertSame([
            'code' => 500,
            'message' => 'auth code invalid',
        ], $payload);
    }
}
