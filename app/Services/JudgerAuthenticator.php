<?php

namespace App\Services;

use App\Entities\Judger;
use App\Exceptions\Judger\JudgerCodeInvalid;
use App\Http\Requests\Judger\JudgerRequest;
use Illuminate\Support\Facades\Cache;

class JudgerAuthenticator
{
    /**
     * @throws JudgerCodeInvalid
     */
    public function authenticate(JudgerRequest $request): Judger
    {
        $judger = $request->getJudger();
        if (! $judger || ! $this->timestampIsFresh($request)) {
            throw new JudgerCodeInvalid();
        }

        $tokenVersion = $request->getTokenVersion();
        if ($tokenVersion === '1') {
            $this->validateLegacyToken($request, $judger);

            return $judger;
        }

        if ($tokenVersion !== '2') {
            throw new JudgerCodeInvalid();
        }

        $this->validateV2Token($request, $judger);

        return $judger;
    }

    public static function signature(string $secret, string $method, string $path, array $payload, string $nonce): string
    {
        ksort($payload);

        $canonical = implode("\n", [
            strtoupper($method),
            '/'.ltrim($path, '/'),
            http_build_query($payload, '', '&', PHP_QUERY_RFC3986),
            $nonce,
        ]);

        return hash_hmac('sha256', $canonical, $secret);
    }

    private function timestampIsFresh(JudgerRequest $request): bool
    {
        $timestamp = $request->input('ts');
        if (! is_numeric($timestamp)) {
            return false;
        }

        return abs(time() - (int) $timestamp) <= $this->tokenTtl();
    }

    /**
     * @throws JudgerCodeInvalid
     */
    private function validateLegacyToken(JudgerRequest $request, Judger $judger): void
    {
        if (! config('hustoj.services.judge.auth.allow_legacy')) {
            throw new JudgerCodeInvalid();
        }

        $origin = sprintf('%s-%d', $judger->code, (int) $request->input('ts'));
        if (! hash_equals(md5($origin), (string) $request->getToken())) {
            throw new JudgerCodeInvalid();
        }
    }

    /**
     * @throws JudgerCodeInvalid
     */
    private function validateV2Token(JudgerRequest $request, Judger $judger): void
    {
        $nonce = (string) $request->getNonce();
        if ($nonce === '' || strlen($nonce) > 128) {
            throw new JudgerCodeInvalid();
        }

        $expected = self::signature(
            $judger->code,
            $request->method(),
            '/'.$request->path(),
            $request->all(),
            $nonce,
        );

        if (! hash_equals($expected, (string) $request->getToken())) {
            throw new JudgerCodeInvalid();
        }

        if (! Cache::store($this->nonceCacheStore())->add($this->nonceCacheKey($judger, $nonce), true, $this->tokenTtl())) {
            throw new JudgerCodeInvalid();
        }
    }

    private function nonceCacheKey(Judger $judger, string $nonce): string
    {
        return 'judger:nonce:'.$judger->id.':'.sha1($nonce);
    }

    private function tokenTtl(): int
    {
        return max(1, (int) config('hustoj.services.judge.auth.ttl', 300));
    }

    private function nonceCacheStore(): string
    {
        return (string) config('hustoj.services.judge.auth.cache_store', 'redis');
    }
}
