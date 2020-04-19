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

    public function testProfile()
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/');
    }
}
