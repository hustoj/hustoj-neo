<?php

namespace Tests\Unit;

use App\Exceptions\WebException;
use App\Services\Topic\Validator\UserValidator;
use Carbon\Carbon;
use Tests\TestCase;

class TopicUserValidatorTest extends TestCase
{
    private UserValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new UserValidator();
    }

    public function testUnverifiedUserHasFutureCooldownDate()
    {
        $user = $this->createUser(['email_verified_at' => null]);

        $this->assertFalse($this->validator->isUserColdDown($user));
        $this->assertTrue($this->validator->getColdDownDate($user)->isFuture());
    }

    public function testRecentlyVerifiedUserCanPost()
    {
        config(['hustoj.user.topic.verified_after' => 1]);
        $user = $this->createUser([
            'email_verified_at' => Carbon::now()->subMinutes(30),
        ]);

        $this->assertFalse($this->validator->isUserColdDown($user));
    }

    public function testValidateThrowsForUserInCooldown()
    {
        config(['hustoj.user.topic.verified_after' => 24]);
        $user = $this->createUser([
            'email_verified_at' => Carbon::now()->subDays(2),
        ]);

        $this->expectException(WebException::class);

        $this->validator->validate($user);
    }
}
