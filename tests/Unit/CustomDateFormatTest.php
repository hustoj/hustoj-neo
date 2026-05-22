<?php

namespace Tests\Unit;

use App\Entities\CustomDateFormat;
use Carbon\Carbon;
use DateTimeInterface;
use PHPUnit\Framework\TestCase as BaseTestCase;

class CustomDateFormatTest extends BaseTestCase
{
    public function testSerializeDateUsesLegacyFormat()
    {
        $formatter = new class
        {
            use CustomDateFormat;

            protected $dateFormat = null;

            public function format(DateTimeInterface $date): string
            {
                return $this->serializeDate($date);
            }
        };

        $formatted = $formatter->format(Carbon::parse('2026-05-21 10:00:00'));

        $this->assertSame('2026-05-21 10:00:00', $formatted);
    }
}
