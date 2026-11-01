<?php

namespace Tests\Unit;

use App\Services\JudgeTaskToken;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class JudgeTaskTokenTest extends TestCase
{
    public function testIssueThenConsumeSucceedsOnce()
    {
        $tokens = app(JudgeTaskToken::class);
        $token = $tokens->issue(42);

        $this->assertTrue($tokens->consume(42, $token));
        $this->assertFalse($tokens->consume(42, $token));
    }

    public function testIssueRotatesTokenAndInvalidatesThePreviousOne()
    {
        $tokens = app(JudgeTaskToken::class);
        $first = $tokens->issue(42);
        $second = $tokens->issue(42);

        $this->assertNotSame($first, $second);
        $this->assertFalse($tokens->consume(42, $first));
        $this->assertTrue($tokens->consume(42, $second));
    }

    public function testConsumeRejectsWrongTokenWithoutBurning()
    {
        $tokens = app(JudgeTaskToken::class);
        $token = $tokens->issue(7);

        $this->assertFalse($tokens->consume(7, 'nope'));
        $this->assertTrue($tokens->consume(7, $token));
    }

    public function testConsumeRejectsEmptyAndUnknownSolution()
    {
        $tokens = app(JudgeTaskToken::class);

        $this->assertFalse($tokens->consume(1, ''));
        $this->assertFalse($tokens->consume(1, 'ghost'));
    }

    public function testFailedPersistencePreservesTokenAndReleasesTheLock()
    {
        $tokens = app(JudgeTaskToken::class);
        $token = $tokens->issue(42);

        try {
            $tokens->consume(42, $token, function () {
                throw new RuntimeException('persistence failed');
            });
            $this->fail('Expected persistence failure to be propagated.');
        } catch (RuntimeException $exception) {
            $this->assertSame('persistence failed', $exception->getMessage());
        }

        $this->assertTrue($tokens->consume(42, $token));
        $this->assertFalse($tokens->consume(42, $token));
    }

    public function testInvalidTokenDoesNotInvokePersistence()
    {
        $tokens = app(JudgeTaskToken::class);
        $token = $tokens->issue(42);
        $persisted = false;

        $this->assertFalse($tokens->consume(42, 'wrong-token', function () use (&$persisted) {
            $persisted = true;
        }));

        $this->assertFalse($persisted);
        $this->assertTrue($tokens->consume(42, $token));
    }

    public function testConsumeKeepsTokenWhenBurnIsDisabled()
    {
        $tokens = app(JudgeTaskToken::class);
        $token = $tokens->issue(42);

        $this->assertTrue($tokens->consume(42, $token, null, false));
        $this->assertTrue($tokens->consume(42, $token));
    }

    public function testConsumeThrowsWhenLockCannotBeAcquired()
    {
        config(['hustoj.services.judge.task_token.lock_wait' => 0]);

        $tokens = app(JudgeTaskToken::class);
        $token = $tokens->issue(42);
        $lock = Cache::store('array')->lock('judger:task:42:lock', 30);
        $this->assertTrue($lock->get());

        try {
            $tokens->consume(42, $token);
            $this->fail('Expected lock timeout to be propagated.');
        } catch (LockTimeoutException) {
            $this->assertTrue(true);
        } finally {
            $lock->release();
        }

        $this->assertTrue($tokens->consume(42, $token));
    }

    public function testEmptyTtlFallsBackToDefaultInsteadOfOneSecond()
    {
        config(['hustoj.services.judge.task_token.ttl' => 0]);

        $tokens = app(JudgeTaskToken::class);
        $token = $tokens->issue(7);

        $this->travel(2)->seconds();

        $this->assertTrue($tokens->consume(7, $token, null, false));
    }

    public function testTokenLifecycleDoesNotQueryDatabase()
    {
        $connection = DB::connection();
        $connection->enableQueryLog();
        $connection->flushQueryLog();

        try {
            $tokens = app(JudgeTaskToken::class);
            $token = $tokens->issue(42);

            $this->assertTrue($tokens->consume(42, $token, null, false));
            $this->assertTrue($tokens->consume(42, $token));
            $this->assertFalse($tokens->consume(42, $token));
            $this->assertFalse($tokens->consume(7, 'unknown-token'));
            $this->assertSame([], $connection->getQueryLog());
        } finally {
            $connection->disableQueryLog();
        }
    }
}
