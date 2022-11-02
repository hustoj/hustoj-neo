<?php

namespace Tests\Unit;

use Tests\TestCase;

class HelperTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
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
}
