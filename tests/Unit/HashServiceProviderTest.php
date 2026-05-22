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

        $this->assertSame($first, $second);
        $this->assertTrue($hash->check('secret', $first));
    }
}
