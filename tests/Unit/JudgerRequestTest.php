<?php

namespace Tests\Unit;

use App\Entities\Judger;
use App\Exceptions\Judger\JudgerCodeInvalid;
use App\Http\Requests\Judger\JudgerRequest;
use Tests\TestCase;

class JudgerRequestTest extends TestCase
{
    public function testValidateAcceptsMatchingToken()
    {
        $judger = $this->createJudger();
        $timestamp = 1_746_000_000;

        $request = $this->makeJudgerRequest($judger, $timestamp);

        $request->validate();

        $this->assertSame($judger->id, $request->getJudger()->id);
    }

    public function testValidateRejectsMissingJudger()
    {
        $this->expectException(JudgerCodeInvalid::class);

        $request = JudgerRequest::create('/judge/api/heartbeat', 'POST', ['ts' => time()]);
        $request->setContainer(app());

        $request->validate();
    }

    public function testValidateRejectsInvalidToken()
    {
        $this->expectException(JudgerCodeInvalid::class);

        $judger = $this->createJudger();
        $timestamp = time();

        $request = JudgerRequest::create('/judge/api/heartbeat', 'POST', ['ts' => $timestamp]);
        $request->headers->set('Judge-Id', (string) $judger->id);
        $request->headers->set('Token', 'invalid-token');
        $request->setContainer(app());

        $request->validate();
    }

    private function makeJudgerRequest(Judger $judger, int $timestamp): JudgerRequest
    {
        $request = JudgerRequest::create('/judge/api/heartbeat', 'POST', ['ts' => $timestamp]);
        $request->headers->set('Judge-Id', (string) $judger->id);
        $request->headers->set('Token', md5(sprintf('%s-%d', $judger->code, $timestamp)));
        $request->setContainer(app());

        return $request;
    }
}
