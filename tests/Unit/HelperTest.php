<?php

namespace Tests\Unit;

use Tests\TestCase;

class HelperTest extends TestCase
{
    public function testRatio()
    {
        $this->assertEquals('22.00%', show_ratio(22, 100));
        $this->assertEquals('0.00%', show_ratio(22, 0));
        $this->assertEquals('2.00%', show_ratio(2, 100));
        $this->assertEquals('20.00%', show_ratio(2, 10));
        $this->assertEquals('200.00%', show_ratio(20, 10));
        $this->assertEquals('100.00%', show_ratio(10, 10));
    }

    public function testIsAlpha()
    {
        $this->assertTrue(is_alpha('A'));
        $this->assertTrue(is_alpha('a'));
        $this->assertFalse(is_alpha('a1'));
    }

    public function testShowOrderAndOriginalOrder()
    {
        $this->assertSame('A', show_order(0));
        $this->assertSame('B', show_order(1));
        $this->assertSame(0, original_order('A'));
        $this->assertSame(1, original_order('b'));
    }

    public function testDisplayPenalizeTime()
    {
        $this->assertSame('0:20:00', display_penalize_time(20 * 60));
        $this->assertSame('1:05:07', display_penalize_time(3907));
    }

    public function testShowProblemIdForContestSolution()
    {
        $user = $this->createUser();
        $problem = $this->createProblem();
        $solution = $this->createSolution($user, $problem, [
            'contest_id' => 1,
            'order' => 2,
        ]);

        $this->assertSame('C', show_problem_id($solution));
    }
}
