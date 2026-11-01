<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserProfileTest extends TestCase
{
    public function testUserCanUpdateProfile()
    {
        $user = $this->createVerifiedUser(['nick' => 'Old Nick', 'email' => 'old@example.com']);

        $response = $this->from('/profile/')->actingAs($user)->post('/profile', [
            'nick' => 'New Nick',
            'email' => 'old@example.com',
        ]);

        $response->assertRedirect('/profile/');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nick' => 'New Nick',
        ]);
    }

    public function testChangingEmailClearsVerification()
    {
        $user = $this->createVerifiedUser(['email' => 'old@example.com']);

        $response = $this->from('/profile/')->actingAs($user)->post('/profile', [
            'nick' => $user->nick,
            'email' => 'new@example.com',
        ]);

        $response->assertRedirect('/profile/');
        $response->assertSessionHas('warning');

        $user->refresh();
        $this->assertSame('new@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function testUserCannotUpdateRankingAndStatusThroughProfile()
    {
        $user = $this->createUser([
            'submit' => 1,
            'solved' => 1,
            'status' => 0,
        ]);

        $response = $this->from('/profile/')->actingAs($user)->post('/profile', [
            'email' => $user->email,
            'nick' => 'new nick',
            'submit' => 999,
            'solved' => 999,
            'status' => 1,
        ]);

        $response->assertRedirect('/profile/');
        $user->refresh();
        $this->assertSame(1, $user->submit);
        $this->assertSame(1, $user->solved);
        $this->assertSame(0, $user->status);
        $this->assertSame('new nick', $user->nick);
    }

    public function testUserCanChangePassword()
    {
        $user = $this->createVerifiedUser();
        $user->password = app('hash')->make('12345678');
        $user->save();

        $response = $this->from('/profile/password')->actingAs($user)->post('/profile/password', [
            'password' => '12345678',
            'password_new' => '87654321',
            'password_new_confirmation' => '87654321',
        ]);

        $response->assertRedirect('/profile/password');
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(app('hash')->check('87654321', $user->password));
    }

    public function testPublicProfileShowsActiveUser()
    {
        $user = $this->createUser(['username' => 'publicuser', 'nick' => 'Public User']);

        $response = $this->get('/user/publicuser');

        $response->assertSuccessful()
                 ->assertSee('Public User');
    }

    public function testInactiveUserProfileIsNotFound()
    {
        $user = $this->createUser([
            'username' => 'hiddenuser',
            'status' => \App\Entities\User::ST_INACTIVE,
        ]);

        $response = $this->from('/')->get('/user/hiddenuser');

        $response->assertRedirect('/');
        $response->assertSessionHasErrors();
    }
}
