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
