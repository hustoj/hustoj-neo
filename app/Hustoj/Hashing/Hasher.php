<?php

namespace App\Hustoj\Hashing;

use Illuminate\Contracts\Hashing\Hasher as HashingContract;
use Illuminate\Support\Arr;

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
        // TODO: Implement check() method.
        if (md5($value) == $hashedValue) {
            // generate new style password
        }

        $origin_hash = base64_decode($hashedValue);
        $salt = substr($origin_hash, 20);

        $hashed_password = $this->make($value, ['salt' => $salt]);

        return $hashed_password == $hashedValue;
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
        $salt = Arr::get($options, 'salt');
        if (! $salt) {
            $this->generateSalt();
        }

        $hashed_password = sha1(md5($value).$salt, true);

        return base64_encode($hashed_password.$salt);
    }

    protected function generateSalt()
    {
        $salt = sha1(mt_rand());

        return substr($salt, 0, 4);
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
        //todo: rehash old password to new password
        return false;
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
