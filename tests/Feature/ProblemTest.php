<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProblemTest extends TestCase
{
    public function testIndex()
    {
        $response = $this->get('/problemset');

        $response->assertSuccessful()
            ->assertStatus(200)
            ->assertSee('Ratio');
    }
}
