<?php

namespace Tests\Feature;

use App\Entities\Problem;
use App\Status;
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

    public function testSummaryPageShowsProblemStatistics()
    {
        $problem = $this->createProblem(['title' => 'Summary Problem']);
        $user = $this->createUser();
        $this->createSolution($user, $problem, ['result' => Status::ACCEPT]);

        $response = $this->get('/problem/'.$problem->id.'/summary');

        $response->assertSuccessful()
                 ->assertSee('Summary Problem');
    }

    public function testHiddenProblemSummaryIsNotAccessible()
    {
        $problem = $this->createProblem([
            'title' => 'Hidden Summary Problem',
            'status' => Problem::ST_HIDE,
        ]);

        $response = $this->from('/problemset')->get('/problem/'.$problem->id.'/summary');

        $response->assertRedirect('/problemset');
        $response->assertSessionHasErrors();
    }
}
