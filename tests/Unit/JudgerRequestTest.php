<?php

namespace Tests\Unit;

use App\Entities\Judger;
use App\Exceptions\Judger\JudgerCodeInvalid;
use App\Http\Requests\Judger\JudgerRequest;
use App\Services\JudgerAuthenticator;
use Tests\TestCase;

class JudgerRequestTest extends TestCase
{
    public function testValidateAcceptsMatchingToken()
    {
        $judger = $this->createJudger();
        $timestamp = time();

        $request = $this->makeJudgerRequest($judger, $timestamp);

        $request->validateResolved();

        $this->assertSame($judger->id, $request->getJudger()->id);
    }

    public function testValidateRejectsMissingJudger()
    {
        $this->expectException(JudgerCodeInvalid::class);

        $request = JudgerRequest::create('/judge/api/heartbeat', 'POST', ['ts' => time()]);
        $request->setContainer(app());

        $request->validateResolved();
    }

    public function testValidateRejectsInvalidToken()
    {
        $this->expectException(JudgerCodeInvalid::class);

        $judger = $this->createJudger();
        $timestamp = time();

        $request = JudgerRequest::create('/judge/api/heartbeat', 'POST', ['ts' => $timestamp]);
        $request->headers->set('Judge-Id', (string) $judger->id);
        $request->headers->set('Token-Version', '2');
        $request->headers->set('Nonce', 'nonce');
        $request->headers->set('Token', 'invalid-token');
        $request->setContainer(app());

        $request->validateResolved();
    }

    public function testValidateRejectsMissingNonce()
    {
        $this->expectException(JudgerCodeInvalid::class);

        $judger = $this->createJudger();
        $timestamp = time();

        $request = JudgerRequest::create('/judge/api/heartbeat', 'POST', ['ts' => $timestamp]);
        $request->headers->set('Judge-Id', (string) $judger->id);
        $request->headers->set('Token-Version', '2');
        $request->headers->set('Token', 'invalid-token');
        $request->setContainer(app());

        $request->validateResolved();
    }

    public function testValidateRejectsUnknownTokenVersion()
    {
        $this->expectException(JudgerCodeInvalid::class);

        $judger = $this->createJudger();
        $timestamp = time();
        $request = $this->makeJudgerRequest($judger, $timestamp);
        $request->headers->set('Token-Version', '3');

        $request->validateResolved();
    }

    public function testValidateRejectsReplayNonce()
    {
        $this->expectException(JudgerCodeInvalid::class);

        $judger = $this->createJudger();
        $timestamp = time();
        $nonce = 'same-nonce';

        $this->makeJudgerRequest($judger, $timestamp, $nonce)->validateResolved();
        $this->makeJudgerRequest($judger, $timestamp, $nonce)->validateResolved();
    }

    public function testValidateRejectsExpiredTimestamp()
    {
        $this->expectException(JudgerCodeInvalid::class);

        $judger = $this->createJudger();
        $timestamp = time() - 301;

        $this->makeJudgerRequest($judger, $timestamp)->validateResolved();
    }

    public function testValidateRejectsInactiveJudger()
    {
        $this->expectException(JudgerCodeInvalid::class);

        $judger = $this->createJudger(['status' => Judger::ST_DEACTIVATE]);
        $timestamp = time();

        $this->makeJudgerRequest($judger, $timestamp)->validateResolved();
    }

    public function testValidateRejectsLegacyTokenByDefault()
    {
        $this->expectException(JudgerCodeInvalid::class);

        $judger = $this->createJudger();
        $timestamp = time();

        $request = JudgerRequest::create('/judge/api/heartbeat', 'POST', ['ts' => $timestamp]);
        $request->headers->set('Judge-Id', (string) $judger->id);
        $request->headers->set('Token', md5(sprintf('%s-%d', $judger->code, $timestamp)));
        $request->setContainer(app());

        $request->validateResolved();
    }

    public function testValidateAcceptsLegacyTokenWhenEnabled()
    {
        config(['hustoj.services.judge.auth.allow_legacy' => true]);

        $judger = $this->createJudger();
        $timestamp = time();

        $request = JudgerRequest::create('/judge/api/heartbeat', 'POST', ['ts' => $timestamp]);
        $request->headers->set('Judge-Id', (string) $judger->id);
        $request->headers->set('Token', md5(sprintf('%s-%d', $judger->code, $timestamp)));
        $request->setContainer(app());

        $request->validateResolved();

        $this->assertSame($judger->id, $request->getJudger()->id);
    }

    public function testSignatureUsesProtocolV2CanonicalRequest()
    {
        $this->assertSame(
            '9b333e653709be9d4917de94242035596594ca2a5669a44cda6b58df816a8646',
            JudgerAuthenticator::signature('secret', 'POST', '/judge/api/report', [
                'ts' => 1000,
                'solution_id' => 42,
                'status' => 4,
            ], 'nonce-1')
        );
    }

    private function makeJudgerRequest(Judger $judger, int $timestamp, string $nonce = 'nonce'): JudgerRequest
    {
        $payload = ['ts' => $timestamp];
        $request = JudgerRequest::create('/judge/api/heartbeat', 'POST', $payload);
        $request->headers->set('Judge-Id', (string) $judger->id);
        $request->headers->set('Token-Version', '2');
        $request->headers->set('Nonce', $nonce);
        $request->headers->set(
            'Token',
            JudgerAuthenticator::signature($judger->code, 'POST', '/judge/api/heartbeat', $payload, $nonce)
        );
        $request->setContainer(app());

        return $request;
    }
}
