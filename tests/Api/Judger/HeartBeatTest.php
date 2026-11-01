<?php

namespace Tests\Api\Judger;

use Tests\TestCase;

class HeartBeatTest extends TestCase
{
    public function testInvalidJudger()
    {
        $response = $this->json('post', '/judge/api/heartbeat', [], ['Judge-Code' => 'dddd']);
        $response->assertStatus(200);
        $response->assertExactJson([
            'code' => 500,
            'message' => 'auth code invalid',
        ]);
    }

    public function testValidJudger()
    {
        $judger = $this->createJudger();
        $timestamp = time();

        $response = $this->json(
            'post',
            '/judge/api/heartbeat',
            ['ts' => $timestamp],
            $this->judgerHeaders($judger, $timestamp)
        );

        $response->assertStatus(200);
        $response->assertSee('');
    }
}
