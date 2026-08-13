<?php

namespace Tests\Feature;

use App\Entities\Topic;
use App\Entities\Contest;
use Carbon\Carbon;
use Tests\TestCase;

class TopicTest extends TestCase
{
    public function testGuestCannotCreateTopic()
    {
        $response = $this->get('/topic/create');

        $response->assertRedirect(route('login'));
    }

    public function testVerifiedUserCanCreateTopic()
    {
        config(['hustoj.user.topic.verified_after' => 1]);
        $user = $this->createVerifiedUser([
            'email_verified_at' => Carbon::now()->subMinutes(30),
        ]);

        $response = $this->actingAs($user)->post('/topic/store', [
            'title' => 'Help on problem 1',
            'content' => 'I need help understanding the input format.',
        ]);

        $response->assertRedirect(route('topic.list'));

        $this->assertDatabaseHas('topics', [
            'user_id' => $user->id,
            'title' => 'Help on problem 1',
        ]);
    }

    public function testTopicStoreRequiresMinimumContentLength()
    {
        config(['hustoj.user.topic.verified_after' => 1]);
        $user = $this->createVerifiedUser([
            'email_verified_at' => Carbon::now()->subMinutes(30),
        ]);

        $response = $this->from('/topic/create')->actingAs($user)->post('/topic/store', [
            'title' => 'Too short',
            'content' => 'short',
        ]);

        $response->assertRedirect('/topic/create');
        $response->assertSessionHasErrors('content');
    }

    public function testUserVerifiedLongAgoCannotCreateTopicDuringCooldown()
    {
        config(['hustoj.user.topic.verified_after' => 24]);
        $user = $this->createVerifiedUser([
            'email_verified_at' => Carbon::now()->subDays(2),
        ]);

        $response = $this->actingAs($user)->post('/topic/store', [
            'title' => 'Blocked topic',
            'content' => 'This should fail because the topic cooldown window has passed.',
        ]);

        $response->assertRedirect(route('topic.list'));
        $response->assertSessionHasErrors();
        $this->assertSame(0, Topic::query()->count());
    }

    public function testGuestCannotViewPrivateContestTopic()
    {
        $owner = $this->createUser();
        $contest = $this->createContest(['private' => Contest::PRIVATE]);
        $topic = $this->createTopic($owner, ['contest_id' => $contest->id]);

        $response = $this->get('/topic/'.$topic->id);

        $response->assertRedirect(route('topic.list'));
        $response->assertSessionHasErrors();
    }

    public function testContestParticipantCanViewPrivateContestTopic()
    {
        $participant = $this->createUser();
        $contest = $this->createContest(['private' => Contest::PRIVATE]);
        $contest->users()->attach($participant->id);
        $topic = $this->createTopic($participant, ['contest_id' => $contest->id]);

        $response = $this->actingAs($participant)->get('/topic/'.$topic->id);

        $response->assertSuccessful();
    }

    public function testNonParticipantCannotCreatePrivateContestTopic()
    {
        config(['hustoj.user.topic.verified_after' => 1]);
        $user = $this->createVerifiedUser([
            'email_verified_at' => Carbon::now()->subMinutes(30),
        ]);
        $contest = $this->createContest(['private' => Contest::PRIVATE]);

        $response = $this->actingAs($user)->post('/topic/store', [
            'title' => 'Private clarify',
            'content' => 'This clarify should not be accepted for outsiders.',
            'contest_id' => $contest->id,
        ]);

        $response->assertRedirect(route('topic.list'));
        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('topics', [
            'user_id' => $user->id,
            'contest_id' => $contest->id,
        ]);
    }
}
