<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminTest extends TestCase
{
    public function testGuestIsRedirectedFromAdminHome()
    {
        $response = $this->get('/admin/home');

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors();
    }

    public function testNonAdminUserIsRedirectedFromAdminHome()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/admin/home');

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors();
    }

    public function testAdminCanAccessAdminHome()
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin/home');

        $response->assertSuccessful();
    }
}
