<?php

namespace Tests\Feature;

use App\Entities\Contest;
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

    public function testShowUsesAuthorizeContestMiddleware()
    {
        $response = $this->get('/contest/999999');
        $response->assertNotFound();
    }

    public function testShowPublicContest()
    {
        $contest = $this->createContest(['title' => 'Public Spring Contest']);

        $response = $this->get('/contest/'.$contest->id);

        $response->assertSuccessful()
                 ->assertSee('Public Spring Contest');
    }

    public function testPrivateContestRedirectsGuest()
    {
        $contest = $this->createContest([
            'title' => 'Private Contest',
            'private' => Contest::PRIVATE,
        ]);

        $response = $this->get('/contest/'.$contest->id);

        $response->assertRedirect(route('contest.index'));
        $response->assertSessionHasErrors();
    }

    public function testEnrolledUserCanAccessPrivateContest()
    {
        $user = $this->createUser();
        $contest = $this->createContest([
            'title' => 'Private Enrolled Contest',
            'private' => Contest::PRIVATE,
        ]);
        $contest->users()->attach($user->id);

        $response = $this->actingAs($user)->get('/contest/'.$contest->id);

        $response->assertSuccessful()
                 ->assertSee('Private Enrolled Contest');
    }

    public function testHiddenContestIsNotAccessible()
    {
        $contest = $this->createContest([
            'title' => 'Hidden Contest',
            'status' => Contest::ST_HIDE,
        ]);

        $response = $this->from('/contest')->get('/contest/'.$contest->id);

        $response->assertRedirect('/contest');
        $response->assertSessionHasErrors();
    }
}
