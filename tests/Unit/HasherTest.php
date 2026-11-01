<?php

namespace Tests\Unit;

use App\Hustoj\Hashing\Hasher;
use Tests\TestCase;

class HasherTest extends TestCase
{
    private Hasher $hasher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hasher = app(Hasher::class);
    }

    public function testMakeAndCheckWithExplicitSalt()
    {
        $hash = $this->hasher->make('secret-pass', ['salt' => 'abcd']);

        $this->assertTrue($this->hasher->check('secret-pass', $hash));
        $this->assertFalse($this->hasher->check('wrong-pass', $hash));
    }

    public function testNeedsRehashAlwaysReturnsFalse()
    {
        $hash = $this->hasher->make('secret-pass', ['salt' => 'abcd']);

        $this->assertFalse($this->hasher->needsRehash($hash));
    }

    public function testInfoReportsHustojAlgorithm()
    {
        $hash = $this->hasher->make('secret-pass', ['salt' => 'abcd']);

        $this->assertSame([
            'algo' => 0,
            'algoName' => 'hustoj',
            'options' => [],
        ], $this->hasher->info($hash));
    }
}
