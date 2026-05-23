<?php

namespace App\Hustoj\Hashing;

use Illuminate\Contracts\Hashing\Hasher as HashingContract;

class Hasher implements HashingContract
{
    /**
     * Check the given plain value against a hash.
     *
     * @param  string  $value
     * @param  string  $hashedValue
     * @param  array  $options
     * @return bool
     */
    public function check($value, $hashedValue, array $options = [])
    {
        if ($hashedValue === null || $hashedValue === '') {
            return false;
        }

        // Format A: 历史 C++ HUSTOJ 遗留的纯 md5(pwd) hex 字符串
        // needsRehash 会把它升级到 Format C
        if ($this->isLegacyMd5($hashedValue)) {
            return hash_equals($hashedValue, md5($value));
        }

        // Format B/C: base64(sha1(md5(pwd).salt) . salt),salt 段可能为空(老 bug 数据)
        $origin_hash = base64_decode($hashedValue, true);
        if ($origin_hash === false) {
            return false;
        }
        $salt = substr($origin_hash, 20);

        $hashed_password = $this->make($value, ['salt' => $salt]);

        return hash_equals($hashed_password, $hashedValue);
    }

    /**
     * Hash the given value.
     *
     * @param  string  $value
     * @param  array  $options
     * @return string
     */
    public function make($value, array $options = [])
    {
        // 注意:必须用 array_key_exists 而非 Arr::get/isset,
        // 否则会与 check() 传入空 salt 的兼容路径冲突,导致老用户登录失败。
        $salt = array_key_exists('salt', $options)
            ? $options['salt']
            : $this->generateSalt();

        $hashed_password = sha1(md5($value).$salt, true);

        return base64_encode($hashed_password.$salt);
    }

    protected function generateSalt()
    {
        // 使用密码学安全随机源生成 16 字节(128 bit)二进制 salt。
        // check() 通过 substr($origin_hash, 20) 按字节读取尾部 salt,长度任意可变;
        // needsRehash 用 < 36 判断,16 字节 salt 解码后总长 36 字节,不会被误判为需要重哈希。
        return random_bytes(16);
    }

    /**
     * Check if the given hash has been hashed using the given options.
     *
     * @param  string  $hashedValue
     * @param  array  $options
     * @return bool
     */
    public function needsRehash($hashedValue, array $options = [])
    {
        if ($hashedValue === null || $hashedValue === '') {
            return false;
        }

        // Format A: 纯 md5,必须迁移
        if ($this->isLegacyMd5($hashedValue)) {
            return true;
        }

        // Format B(空 salt,解码 20 字节)与旧版 Format C(4 字节弱 salt,解码 24 字节)
        // 都视为需要迁移到新的 16 字节随机 salt(解码 36 字节)。
        return strlen((string) base64_decode($hashedValue, true)) < 36;
    }

    /**
     * Format A: 32 字符的纯小写 hex(md5 输出形态)。
     *
     * 注意: 32 字符 hex 也是合法的 base64 编码,但 Format C 的 base64
     * 输出会包含 sha1 二进制的随机字节,几乎不可能全部落在 0-9a-f 范围,
     * 用 ctype_xdigit 区分足够稳健。
     */
    private function isLegacyMd5(string $hashedValue): bool
    {
        return strlen($hashedValue) === 32 && ctype_xdigit($hashedValue) && strtolower($hashedValue) === $hashedValue;
    }

    /**
     * Get information about the given hashed value.
     *
     * @param  string  $hashedValue
     * @return array
     */
    public function info($hashedValue)
    {
        return [
            'algo'     => 0,
            'algoName' => 'hustoj',
            'options'  => [],
        ];
    }

    /**
     * detect password is old md5 password.
     *
     * @param  string  $password
     * @return bool
     */
    protected function isDeprecated($password)
    {
        $len = strlen($password);
        for ($pos = 0; $pos < $len; $pos++) {
            $char = $password[$pos];
            if ($this->isCharactorInHexScope($char)) {
                continue;
            }
            break;
        }

        return false;
    }

    /**
     * detect charactor is 0-9a-z.
     *
     * @param  $char
     * @return bool
     */
    private function isCharactorInHexScope($char)
    {
        $char = strtolower($char);
        if (ctype_digit($char)) {
            return true;
        }
        if ($char >= 'a' && $char <= 'z') {
            return true;
        }

        return false;
    }
}
