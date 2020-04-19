<?php

namespace Tests\Api\Judger;

use App\Entities\Judger;
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
        $judger = $this->getValidJudger();

        if (! $judger) {
            $this->markTestIncomplete('No Valid Judger');

            return;
        }
        $response = $this->json('post', '/judge/api/heartbeat', [], ['Judge-Code' => $judger->code]);
        $response->assertStatus(200);
        $response->assertSee('');
    }

    /**
     * @return Judger
     */
    private function getValidJudger()
    {
        $judger = Judger::query()
                        ->where('status', Judger::ST_ACTIVITY)
                        ->orderBy('id', 'desc')
                        ->first();

        return $judger;
    }
}
