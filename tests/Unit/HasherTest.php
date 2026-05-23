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

        // sha1 20 字节 + 16 字节随机 salt = 36 字节
        $this->assertSame(36, strlen(base64_decode($hash)));
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
        // Format B: 由于旧版 bug,PHP 版老用户密码以空 salt 存储
        $legacyHash = base64_encode(sha1(md5('secret-pass'), true));

        $this->assertTrue(
            $this->hasher->check('secret-pass', $legacyHash),
            'Format B(空 salt 哈希)必须仍可登录'
        );
        $this->assertFalse($this->hasher->check('wrong-pass', $legacyHash));
    }

    public function testLegacyPlainMd5HashRemainsCheckable()
    {
        // Format A: 原始 C++ HUSTOJ 遗留的纯 md5(pwd) 形式
        $legacyMd5 = md5('secret-pass');

        $this->assertSame(32, strlen($legacyMd5));
        $this->assertTrue(
            $this->hasher->check('secret-pass', $legacyMd5),
            'Format A(C++ HUSTOJ 纯 md5)必须仍可登录'
        );
        $this->assertFalse($this->hasher->check('wrong-pass', $legacyMd5));
    }

    public function testNeedsRehashReturnsTrueForLegacyEmptySaltHash()
    {
        $legacyHash = base64_encode(sha1(md5('secret-pass'), true));

        $this->assertTrue(
            $this->hasher->needsRehash($legacyHash),
            'Format B 应触发自动重新加密'
        );
    }

    public function testNeedsRehashReturnsTrueForLegacyMd5Hash()
    {
        $this->assertTrue(
            $this->hasher->needsRehash(md5('secret-pass')),
            'Format A 应触发自动重新加密'
        );
    }

    public function testNeedsRehashReturnsFalseForNewHash()
    {
        $newHash = $this->hasher->make('secret-pass');

        $this->assertFalse($this->hasher->needsRehash($newHash));
    }

    public function testNeedsRehashReturnsTrueForLegacyShortSaltHash()
    {
        // 旧版 4 字节弱 salt(sha1(mt_rand()) 截 4 字符)应被迁移到 16 字节随机 salt
        $legacyShortSaltHash = $this->hasher->make('secret-pass', ['salt' => 'abcd']);

        $this->assertTrue(
            $this->hasher->needsRehash($legacyShortSaltHash),
            '旧版 4 字节弱 salt 哈希应触发自动重新加密'
        );
    }

    public function testCheckHandlesEmptyOrNullHash()
    {
        $this->assertFalse($this->hasher->check('whatever', ''));
        $this->assertFalse($this->hasher->check('whatever', null));
    }

    public function testNeedsRehashHandlesEmptyOrNullHash()
    {
        $this->assertFalse($this->hasher->needsRehash(''));
        $this->assertFalse($this->hasher->needsRehash(null));
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
