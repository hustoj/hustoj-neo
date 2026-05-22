<?php

namespace Tests\Unit;

use App\Entities\Contest;
use Database\Seeders\RoleTableSeeder;
use Tests\TestCase;

class CanAttendTest extends TestCase
{
    public function testGuestCannotAttendPrivateContest()
    {
        $contest = $this->createContest(['private' => Contest::PRIVATE]);

        $this->assertFalse(can_attend($contest));
    }

    public function testEnrolledUserCanAttendPrivateContest()
    {
        $user = $this->createUser();
        $contest = $this->createContest(['private' => Contest::PRIVATE]);
        $contest->users()->attach($user->id);

        $this->actingAs($user);

        $this->assertTrue(can_attend($contest));
    }

    public function testAdminCanAttendPrivateContestWithoutEnrollment()
    {
        $this->seed(RoleTableSeeder::class);
        $admin = $this->createUser();
        $admin->addRole('admin');
        $contest = $this->createContest(['private' => Contest::PRIVATE]);

        $this->actingAs($admin->fresh());

        $this->assertTrue(can_attend($contest));
    }

    public function testRegularUserCannotAttendPrivateContestWithoutEnrollment()
    {
        $user = $this->createUser();
        $contest = $this->createContest(['private' => Contest::PRIVATE]);

        $this->actingAs($user);

        $this->assertFalse(can_attend($contest));
    }
}
