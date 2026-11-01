<?php

namespace Tests\Feature;

use App\Status;
use Tests\TestCase;

class SolutionPermissionTest extends TestCase
{
    public function testGuestIsRedirectedFromSolutionSource()
    {
        $owner = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem);

        $response = $this->get('/solution/'.$solution->id.'/source');

        $response->assertRedirect(route('login'));
    }

    public function testOwnerCanViewSolutionSource()
    {
        $owner = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem);
        $solution->source()->create(['code' => 'int main() { return 0; }']);

        $response = $this->actingAs($owner)->get('/solution/'.$solution->id.'/source');

        $response->assertSuccessful();
    }

    public function testOtherUserCannotViewSolutionSource()
    {
        $owner = $this->createUser();
        $other = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem);
        $solution->source()->create(['code' => 'secret code']);

        $response = $this->actingAs($other)->get('/solution/'.$solution->id.'/source');

        $response->assertRedirect(route('solution.index'));
        $response->assertSessionHasErrors();
    }

    public function testAdminCanViewOtherUsersSolutionSource()
    {
        $owner = $this->createUser();
        $admin = $this->createAdminUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem);
        $solution->source()->create(['code' => 'admin readable']);

        $response = $this->actingAs($admin)->get('/solution/'.$solution->id.'/source');

        $response->assertSuccessful();
    }

    public function testGuestIsRedirectedFromCompileInfo()
    {
        $owner = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem, ['result' => Status::COMPILE_ERROR]);
        $this->attachCompileInfo($solution, 'expected ; before }');

        $response = $this->get('/solution/'.$solution->id.'/compileinfo');

        $response->assertRedirect(route('login'));
    }

    public function testOwnerCanViewCompileInfo()
    {
        $owner = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem, ['result' => Status::COMPILE_ERROR]);
        $this->attachCompileInfo($solution, 'expected ; before }');

        $response = $this->actingAs($owner)->get('/solution/'.$solution->id.'/compileinfo');

        $response->assertSuccessful()
                 ->assertSee('expected ; before }', false);
    }

    public function testOtherUserCannotViewCompileInfo()
    {
        $owner = $this->createUser();
        $other = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem, ['result' => Status::COMPILE_ERROR]);
        $this->attachCompileInfo($solution, 'secret compile log');

        $response = $this->actingAs($other)->get('/solution/'.$solution->id.'/compileinfo');

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function testAdminCanViewOtherUsersCompileInfo()
    {
        $owner = $this->createUser();
        $admin = $this->createAdminUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem, ['result' => Status::COMPILE_ERROR]);
        $this->attachCompileInfo($solution, 'admin compile log');

        $response = $this->actingAs($admin)->get('/solution/'.$solution->id.'/compileinfo');

        $response->assertSuccessful()
                 ->assertSee('admin compile log', false);
    }

    public function testOwnerCanViewRuntimeInfo()
    {
        $owner = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem, ['result' => Status::RUNTIME_ERROR]);
        $this->attachRuntimeInfo($solution, 'SIGSEGV on addr 0x0');

        $response = $this->actingAs($owner)->get('/solution/'.$solution->id.'/runtimeinfo');

        $response->assertSuccessful()
                 ->assertSee('SIGSEGV on addr 0x0', false);
    }

    public function testOtherUserCannotViewRuntimeInfo()
    {
        $owner = $this->createUser();
        $other = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem, ['result' => Status::RUNTIME_ERROR]);
        $this->attachRuntimeInfo($solution, 'secret runtime log');

        $response = $this->actingAs($other)->get('/solution/'.$solution->id.'/runtimeinfo');

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function testAdminCanViewOtherUsersRuntimeInfo()
    {
        $owner = $this->createUser();
        $admin = $this->createAdminUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem, ['result' => Status::RUNTIME_ERROR]);
        $this->attachRuntimeInfo($solution, 'admin runtime log');

        $response = $this->actingAs($admin)->get('/solution/'.$solution->id.'/runtimeinfo');

        $response->assertSuccessful()
                 ->assertSee('admin runtime log', false);
    }
}
