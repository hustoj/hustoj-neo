<?php

namespace Tests\Feature;

use App\Entities\User;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    public function testUserCanRegister()
    {
        $response = $this->post('/register', [
            'username' => 'newbie',
            'nick' => 'Newbie',
            'email' => 'newbie@example.com',
            'password' => 'password1',
            'password_confirmation' => 'password1',
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'username' => 'newbie',
            'email' => 'newbie@example.com',
            'status' => User::ST_ACTIVE,
        ]);
    }

    public function testRegistrationRequiresUniqueUsername()
    {
        $this->createUser(['username' => 'taken', 'email' => 'a@example.com']);

        $response = $this->from('/register')->post('/register', [
            'username' => 'taken',
            'nick' => 'Other',
            'email' => 'b@example.com',
            'password' => 'password1',
            'password_confirmation' => 'password1',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('username');
    }

    public function testUserCanLoginWithValidCredentials()
    {
        $user = $this->createUser([
            'username' => 'loginuser',
            'password' => app('hash')->make('password'),
        ]);

        $response = $this->post('/login', [
            'username' => 'loginuser',
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function testLoginFailsWithWrongPassword()
    {
        $this->createUser([
            'username' => 'loginuser',
            'password' => app('hash')->make('password'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'username' => 'loginuser',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function testLegacyMd5PasswordUserCanLoginAndIsRehashed()
    {
        // Format A: 模拟从 C++ HUSTOJ 迁移过来的用户(纯 md5 密码)
        $user = $this->createUser([
            'username' => 'legacy-md5',
            'password' => md5('password'),
        ]);
        $this->assertSame(32, strlen($user->password));

        $response = $this->post('/login', [
            'username' => 'legacy-md5',
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        // 登录成功后应自动 rehash 为 Format C(带 salt,base64 32 字符)
        $user->refresh();
        $this->assertNotSame(md5('password'), $user->password, '密码应已被自动升级');
        $this->assertFalse(app('hash')->needsRehash($user->password));
        $this->assertTrue(app('hash')->check('password', $user->password));
    }

    public function testLegacyEmptySaltPasswordUserCanLoginAndIsRehashed()
    {
        // Format B: 模拟 Hasher salt 丢失 bug 期间存的用户
        $legacyHash = base64_encode(sha1(md5('password'), true));
        $user = $this->createUser([
            'username' => 'legacy-emptysalt',
            'password' => $legacyHash,
        ]);

        $response = $this->post('/login', [
            'username' => 'legacy-emptysalt',
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        // 登录成功后应自动 rehash 为 Format C
        $user->refresh();
        $this->assertNotSame($legacyHash, $user->password, '密码应已被自动升级');
        $this->assertFalse(app('hash')->needsRehash($user->password));
        $this->assertTrue(app('hash')->check('password', $user->password));
    }

    public function testInactiveUserCannotLogin()
    {
        $this->createUser([
            'username' => 'inactive',
            'status' => User::ST_INACTIVE,
            'password' => app('hash')->make('password'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'username' => 'inactive',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
