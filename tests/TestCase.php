<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Concerns\CreatesTestEntities;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use CreatesTestEntities;
    use RefreshDatabase;
}
