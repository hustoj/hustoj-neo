<?php

namespace Tests\Unit;

use App\Hustoj\Hashing\Hasher;
use Tests\TestCase;

class HashServiceProviderTest extends TestCase
{
    public function testHashServiceResolvesLegacyHasher()
    {
        $hash = app('hash');

        $this->assertInstanceOf(Hasher::class, $hash);
        $this->assertInstanceOf(Hasher::class, app('hash.driver'));
        $this->assertSame($hash, app('hash.driver'));

        $first = $hash->make('secret');
        $second = $hash->make('secret');

        // 修复 salt 丢失 bug 后,两次 make 应产生不同 hash(各自随机 salt)
        $this->assertNotSame($first, $second);
        $this->assertTrue($hash->check('secret', $first));
        $this->assertTrue($hash->check('secret', $second));
    }
}
