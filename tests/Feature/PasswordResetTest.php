<?php

namespace Tests\Feature;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{

    public function testGuestCanViewPasswordResetRequestForm()
    {
        $response = $this->get('/password/reset');

        $response->assertSuccessful();
    }

    public function testPasswordResetLinkCanBeRequested()
    {
        Notification::fake();

        $user = $this->createUser(['email' => 'reset@example.com']);

        $response = $this->post('/password/email', [
            'email' => 'reset@example.com',
        ]);

        $response->assertRedirect();
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function testPasswordResetLinkRequestRequiresValidEmail()
    {
        $response = $this->from('/password/reset')->post('/password/email', [
            'email' => 'not-an-email',
        ]);

        $response->assertRedirect('/password/reset');
        $response->assertSessionHasErrors('email');
    }

    public function testUserCanResetPasswordWithValidToken()
    {
        $user = $this->createUser([
            'email' => 'reset@example.com',
            'password' => app('hash')->make('oldpass12'),
        ]);
        $token = Password::broker()->createToken($user);

        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => 'reset@example.com',
            'password' => 'newpass12',
            'password_confirmation' => 'newpass12',
        ]);

        $response->assertRedirect('/');

        $user->refresh();
        $this->assertTrue(app('hash')->check('newpass12', $user->password));
    }
}
