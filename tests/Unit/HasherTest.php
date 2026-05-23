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

    public function testMakeWithoutSaltGeneratesSaltedHash()
    {
        $hash = $this->hasher->make('secret-pass');

        // sha1 20 字节 + 4 字节 salt = 24 字节 → base64 32 字符
        $this->assertSame(24, strlen(base64_decode($hash)));
        $this->assertTrue($this->hasher->check('secret-pass', $hash));
        $this->assertFalse($this->hasher->check('wrong-pass', $hash));
    }

    public function testMakeWithoutSaltProducesDifferentHashEachCall()
    {
        $hash1 = $this->hasher->make('secret-pass');
        $hash2 = $this->hasher->make('secret-pass');

        $this->assertNotSame($hash1, $hash2, '随机 salt 应使每次 hash 结果不同');
    }

    public function testLegacyEmptySaltHashRemainsCheckable()
    {
        // 模拟历史数据:由于旧版 bug,所有老用户密码以空 salt 存储
        $legacyHash = base64_encode(sha1(md5('secret-pass'), true));

        $this->assertTrue(
            $this->hasher->check('secret-pass', $legacyHash),
            '老用户(空 salt 哈希)必须仍可登录'
        );
        $this->assertFalse($this->hasher->check('wrong-pass', $legacyHash));
    }

    public function testNeedsRehashReturnsTrueForLegacyHash()
    {
        $legacyHash = base64_encode(sha1(md5('secret-pass'), true));

        $this->assertTrue(
            $this->hasher->needsRehash($legacyHash),
            '老格式 hash 应触发自动重新加密'
        );
    }

    public function testNeedsRehashReturnsFalseForNewHash()
    {
        $newHash = $this->hasher->make('secret-pass');

        $this->assertFalse($this->hasher->needsRehash($newHash));
        $this->assertFalse(
            $this->hasher->needsRehash($this->hasher->make('secret-pass', ['salt' => 'abcd']))
        );
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
