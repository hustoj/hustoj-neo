<?php

namespace Tests\Feature;

use App\Entities\Contest;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ContestTest extends TestCase
{
    public static function protectedContestRoutesProvider(): array
    {
        return [
            'contest view' => ['/%d', 'Private Contest'],
            'standing' => ['/%d/standing', 'Standing'],
            'status' => ['/%d/status', 'Status'],
            'clarify' => ['/%d/clarify', 'Clarify'],
        ];
    }
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

    public function testHiddenContestRejectsEnrolledUser()
    {
        $user = $this->createUser();
        $contest = $this->createContest([
            'title' => 'Hidden Enrolled Contest',
            'status' => Contest::ST_HIDE,
        ]);
        $contest->users()->attach($user->id);

        $response = $this->from('/contest')->actingAs($user)->get('/contest/'.$contest->id);

        $response->assertRedirect('/contest');
        $response->assertSessionHasErrors();
    }

    public function testAdminCanAccessPrivateContestWithoutEnrollment()
    {
        $admin = $this->createAdminUser();
        $contest = $this->createContest([
            'title' => 'Admin Private Contest',
            'private' => Contest::PRIVATE,
        ]);

        $response = $this->actingAs($admin)->get('/contest/'.$contest->id);

        $response->assertSuccessful()
                 ->assertSee('Admin Private Contest');
    }

    public function testLoggedInUserCannotAccessPrivateContestWithoutEnrollment()
    {
        $user = $this->createUser();
        $contest = $this->createContest([
            'title' => 'Blocked Private Contest',
            'private' => Contest::PRIVATE,
        ]);

        $response = $this->actingAs($user)->get('/contest/'.$contest->id);

        $response->assertRedirect(route('contest.index'));
        $response->assertSessionHasErrors();
    }

    public function testGuestCanAccessPublicContestProtectedRoutes()
    {
        $contest = $this->createContest([
            'title' => 'Guest Public Contest',
            'private' => Contest::PUBLIC,
        ]);

        $this->get('/contest/'.$contest->id)->assertSuccessful()->assertSee('Guest Public Contest');
        $this->get('/contest/'.$contest->id.'/standing')->assertSuccessful();
        $this->get('/contest/'.$contest->id.'/status')->assertSuccessful();
        $this->get('/contest/'.$contest->id.'/clarify')->assertSuccessful();
    }

    #[DataProvider('protectedContestRoutesProvider')]
    public function testPrivateContestProtectedRoutesRejectGuest(string $pathTemplate, string $seeText): void
    {
        $contest = $this->createContest([
            'title' => $seeText,
            'private' => Contest::PRIVATE,
        ]);

        $response = $this->get(sprintf('/contest'.$pathTemplate, $contest->id));

        $response->assertRedirect(route('contest.index'));
        $response->assertSessionHasErrors();
    }

    #[DataProvider('protectedContestRoutesProvider')]
    public function testEnrolledUserCanAccessPrivateContestProtectedRoutes(string $pathTemplate, string $seeText): void
    {
        $user = $this->createUser();
        $contest = $this->createContest([
            'title' => $seeText,
            'private' => Contest::PRIVATE,
        ]);
        $contest->users()->attach($user->id);

        $response = $this->actingAs($user)->get(sprintf('/contest'.$pathTemplate, $contest->id));

        $response->assertSuccessful();
    }

    public function testEnrolledUserCanViewContestProblemByOrder()
    {
        $user = $this->createUser();
        $contest = $this->createContest([
            'title' => 'Problem Order Contest',
            'private' => Contest::PRIVATE,
        ]);
        $contest->users()->attach($user->id);
        $problem = $this->createProblem(['title' => 'Problem A Body']);
        $this->attachProblemToContest($contest, $problem, 0);

        $response = $this->actingAs($user)->get('/contest/'.$contest->id.'/problem/A');

        $response->assertSuccessful()
                 ->assertSee('Problem A Body');
    }

    public function testGuestCannotAccessPrivateContestSubmitPage()
    {
        $contest = $this->createContest(['private' => Contest::PRIVATE]);

        $response = $this->get('/contest/'.$contest->id.'/submit');

        $response->assertRedirect(route('contest.index'));
        $response->assertSessionHasErrors();
    }

    public function testEnrolledUserCanOpenSubmitPageDuringContest()
    {
        $user = $this->createUser();
        $contest = $this->createContest([
            'private' => Contest::PRIVATE,
            'start_time' => now()->subHour(),
            'end_time' => now()->addHour(),
        ]);
        $contest->users()->attach($user->id);
        $problem = $this->createProblem();
        $this->attachProblemToContest($contest, $problem, 0);

        $response = $this->actingAs($user)->get('/contest/'.$contest->id.'/submit?order=A');

        $response->assertSuccessful();
    }
}
