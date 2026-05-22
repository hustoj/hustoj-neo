<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminResourceTest extends TestCase
{
    public static function adminResourcePathsProvider(): array
    {
        return [
            'problems index' => ['/admin/problems'],
            'contests index' => ['/admin/contests'],
            'users index' => ['/admin/users'],
            'roles index' => ['/admin/roles'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminResourcePathsProvider')]
    public function testGuestIsRedirectedFromAdminResources(string $path): void
    {
        $response = $this->get($path);

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminResourcePathsProvider')]
    public function testNonAdminIsRedirectedFromAdminResources(string $path): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get($path);

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('adminResourcePathsProvider')]
    public function testAdminCanAccessAdminResources(string $path): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get($path);

        $response->assertSuccessful();
    }
}
