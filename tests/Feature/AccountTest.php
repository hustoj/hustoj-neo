<?php

namespace Tests\Feature;

use Tests\TestCase;

class AccountTest extends TestCase
{
    public function testLoginPage()
    {
        $response = $this->get('/login');
        $response->assertSuccessful()
                 ->assertStatus(200)
                 ->assertSee('Name')
                 ->assertSee('Password')
                 ->assertSee('_token');
    }

    public function testGuestIsRedirectedFromProfile()
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/');
    }

    public function testAuthenticatedUserCanViewProfile()
    {
        $user = $this->createUser(['nick' => 'Tester']);

        $response = $this->actingAs($user)->get('/profile/');

        $response->assertSuccessful()
                 ->assertSee('Tester');
    }

    public function testAuthenticatedUserIsRedirectedFromLogin()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/home');
    }
}
