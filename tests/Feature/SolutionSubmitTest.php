<?php

namespace Tests\Feature;

use App\Entities\Solution;
use App\Status;
use App\Task\SolutionQueue;
use Tests\TestCase;

class SolutionSubmitTest extends TestCase
{
    public function testGuestIsRedirectedFromSolutionStore()
    {
        $problem = $this->createProblem();

        $response = $this->post('/solution/store', [
            'problem_id' => $problem->id,
            'language' => 1,
            'code' => 'int main() { return 0; }',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function testUnverifiedUserIsRedirectedFromSolutionStore()
    {
        $user = $this->createUser(['email_verified_at' => null]);
        $problem = $this->createProblem();

        $response = $this->actingAs($user)->post('/solution/store', [
            'problem_id' => $problem->id,
            'language' => 1,
            'code' => 'int main() { return 0; }',
        ]);

        $response->assertRedirect();
        $this->assertNotEquals(route('solution.index'), $response->headers->get('Location'));
    }

    public function testVerifiedUserCanSubmitSolutionAndPersistRecords()
    {
        $user = $this->createVerifiedUser();
        $problem = $this->createProblem();

        $this->mock(SolutionQueue::class, function ($mock) {
            $mock->shouldReceive('add')->once();
        });

        $response = $this->actingAs($user)->post('/solution/store', [
            'problem_id' => $problem->id,
            'language' => 1,
            'code' => "int main() {\n  return 0;\n}",
            'contest_id' => 0,
            'order' => 0,
        ]);

        $response->assertRedirect(route('solution.index'));

        $solution = Solution::query()->where('user_id', $user->id)->first();
        $this->assertNotNull($solution);
        $this->assertSame($problem->id, $solution->problem_id);
        $this->assertSame(Status::PENDING, $solution->result);
        $this->assertSame(strlen("int main() {\n  return 0;\n}"), $solution->code_length);

        $this->assertDatabaseHas('source_code', [
            'solution_id' => $solution->id,
            'code' => "int main() {\n  return 0;\n}",
        ]);
    }

    public function testSpecialJudgeProblemSubmitPageIsBlockedWhenDisabled()
    {
        config(['hustoj.special_judge_enabled' => false]);
        $user = $this->createVerifiedUser();
        $problem = $this->createProblem(['spj' => 1]);

        $response = $this->actingAs($user)->get('/problem/'.$problem->id.'/submit');

        $response->assertRedirect(route('problem.view', ['problem' => $problem->id]));
        $response->assertSessionHasErrors();
    }

    public function testSolutionIndexIsAccessible()
    {
        $response = $this->get('/status');

        $response->assertSuccessful();
    }

    public function testSolutionIndexFiltersByUsername()
    {
        $user = $this->createUser(['username' => 'filteruser']);
        $other = $this->createUser(['username' => 'otheruser']);
        $problem = $this->createProblem();
        $this->createSolution($user, $problem, ['result' => Status::ACCEPT]);
        $this->createSolution($other, $problem, ['result' => Status::WRONG_ANSWER]);

        $response = $this->get('/status?username=filteruser');

        $response->assertSuccessful()
                 ->assertSee('filteruser')
                 ->assertDontSee('otheruser');
    }
}
