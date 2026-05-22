<?php

namespace Tests\Feature;

use App\Entities\Problem;
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

    public function testShowAvailableProblem()
    {
        $problem = $this->createProblem(['title' => 'A Plus B Problem']);

        $response = $this->get('/problem/'.$problem->id);

        $response->assertSuccessful()
                 ->assertSee('A Plus B Problem');
    }

    public function testHiddenProblemIsNotAccessible()
    {
        $problem = $this->createProblem([
            'title' => 'Hidden Problem',
            'status' => Problem::ST_HIDE,
        ]);

        $response = $this->from('/problemset')->get('/problem/'.$problem->id);

        $response->assertRedirect('/problemset');
        $response->assertSessionHasErrors();
    }
}
