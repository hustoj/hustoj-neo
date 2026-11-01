<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    public function testUnverifiedUserSeesVerificationNotice()
    {
        $user = $this->createUser(['email_verified_at' => null]);

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertSuccessful();
    }

    public function testVerifiedUserIsRedirectedFromVerificationNotice()
    {
        $user = $this->createVerifiedUser();

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertRedirect('/');
    }

    public function testVerifiedUserCanVerifyEmailViaSignedUrl()
    {
        $user = $this->createUser(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/');
        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }

    public function testGuestCannotAccessVerificationNotice()
    {
        $response = $this->get('/email/verify');

        $response->assertRedirect(route('login'));
    }
}
