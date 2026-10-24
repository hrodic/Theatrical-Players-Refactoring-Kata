<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Theatrical\ComedyCalculator;
use Theatrical\Performance;
use Theatrical\Play;

final class ComedyCalculatorTest extends TestCase
{
    public function testCalculatesBaseAmountAndCreditsForAudienceAtOrBelowThreshold(): void
    {
        $calculator = new ComedyCalculator(
            new Performance('as-like', 20),
            new Play('As You Like It', Play::TYPE_COMEDY)
        );

        // 30,000 base + (300 * 20 attendee fee) = 36,000 cents
        self::assertSame(36000, $calculator->calculateAmount());

        // max(20 - 30, 0) + intdiv(20, 5) = 0 + 4 = 4 credits
        self::assertSame(4, $calculator->calculateVolumeCredits());
    }

    public function testCalculatesAmountAndCreditsWithAudienceOverThreshold(): void
    {
        $calculator = new ComedyCalculator(
            new Performance('as-like', 40),
            new Play('As You Like It', Play::TYPE_COMEDY)
        );

        // 30,000 base + 10,000 flat surcharge + (500 * 20 seats over 20) + (300 * 40 seats)
        // = 30,000 + 20,000 + 12,000 = 62,000 cents
        self::assertSame(62000, $calculator->calculateAmount());

        // max(40 - 30, 0) + intdiv(40, 5) = 10 + 8 = 18 credits
        self::assertSame(18, $calculator->calculateVolumeCredits());
    }
}
