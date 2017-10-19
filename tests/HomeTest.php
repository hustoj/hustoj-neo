<?php

class HomeTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testHome()
    {
        $response = $this->get('/');
        $response->assertStatus(200)
                 ->assertSee('Welcome to HUSTOJ!')
                 ->assertSuccessful();
    }
}
