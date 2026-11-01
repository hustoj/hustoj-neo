<?php

namespace Tests\Unit;

use Tests\TestCase;

class CanViewCodeTest extends TestCase
{
    public function testGuestCannotViewCode()
    {
        $owner = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem);

        $this->assertFalse(can_view_code($solution));
    }

    public function testOwnerCanViewOwnSolutionCode()
    {
        $owner = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem);

        $this->actingAs($owner);

        $this->assertTrue(can_view_code($solution));
    }

    public function testOtherUserCannotViewSolutionCode()
    {
        $owner = $this->createUser();
        $other = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem);

        $this->actingAs($other);

        $this->assertFalse(can_view_code($solution));
    }

    public function testAdminCanViewAnySolutionCode()
    {
        $owner = $this->createUser();
        $admin = $this->createAdminUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($owner, $problem);

        $this->actingAs($admin);

        $this->assertTrue(can_view_code($solution));
    }
}
