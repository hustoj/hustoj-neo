<?php

namespace Tests\Feature;

use Tests\TestCase;

class ContestTest extends TestCase
{
    public function testIndex()
    {
        $response = $this->get('/contest');
        $response->assertSuccessful()
                 ->assertSee('Contest List')
                 ->assertStatus(200);
    }
}
