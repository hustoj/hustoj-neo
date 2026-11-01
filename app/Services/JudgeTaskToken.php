<?php

namespace App\Services;

use Closure;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class JudgeTaskToken
{
    private const LOCK_TTL = 300;

    private const LOCK_WAIT = 5;

    private const DEFAULT_TTL = 604800;

    public function issue(int $solutionId): string
    {
        return $this->rotateToken($solutionId, fn (string $token) => $token);
    }

    public function dispatch(int $solutionId, Closure $dispatch): void
    {
        $this->rotateToken($solutionId, $dispatch);
    }

    public function consume(int $solutionId, string $provided, ?Closure $persist = null, bool $burn = true): bool
    {
        if ($provided === '') {
            return false;
        }

        return (bool) $this->lock($solutionId)->block($this->lockWait(), function () use ($solutionId, $provided, $persist, $burn) {
            $store = $this->store();
            $key = $this->key($solutionId);
            $expected = $store->get($key);
            if (! is_string($expected) || ! hash_equals($expected, $provided)) {
                return false;
            }

            // 持久化失败时保留 token，允许使用新的认证 nonce 重试。
            if ($persist !== null) {
                $persist();
            }

            if (! $burn) {
                return true;
            }

            return $store->forget($key);
        });
    }

    private function rotateToken(int $solutionId, Closure $callback): mixed
    {
        return $this->lock($solutionId)->block($this->lockWait(), function () use ($solutionId, $callback) {
            $store = $this->store();
            $key = $this->key($solutionId);
            $previous = $store->get($key);
            $previous = is_string($previous) && $previous !== '' ? $previous : null;

            $token = Str::random(64);
            if (! $store->put($key, $token, $this->ttl())) {
                throw new RuntimeException('Unable to store judge task token.');
            }

            try {
                return $callback($token);
            } catch (Throwable $exception) {
                if ($previous !== null) {
                    $store->put($key, $previous, $this->ttl());
                } else {
                    $store->forget($key);
                }

                throw $exception;
            }
        });
    }

    private function lock(int $solutionId): Lock
    {
        return $this->store()->lock($this->key($solutionId).':lock', self::LOCK_TTL);
    }

    private function store(): Repository
    {
        return Cache::store($this->cacheStore());
    }

    private function key(int $solutionId): string
    {
        return 'judger:task:'.$solutionId;
    }

    private function ttl(): int
    {
        $ttl = (int) config('hustoj.services.judge.task_token.ttl', self::DEFAULT_TTL);

        return $ttl > 0 ? $ttl : self::DEFAULT_TTL;
    }

    private function lockWait(): int
    {
        return max(0, (int) config('hustoj.services.judge.task_token.lock_wait', self::LOCK_WAIT));
    }

    private function cacheStore(): string
    {
        $store = config('hustoj.services.judge.task_token.cache_store');
        if (is_string($store) && $store !== '') {
            return $store;
        }

        return (string) config('hustoj.services.judge.auth.cache_store', 'redis');
    }
}
