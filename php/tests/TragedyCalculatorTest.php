<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Theatrical\Performance;
use Theatrical\Play;
use Theatrical\TragedyCalculator;

final class TragedyCalculatorTest extends TestCase
{
    public function testCalculatesBaseAmountForAudienceAtOrBelowThreshold(): void
    {
        $calculator = new TragedyCalculator(
            new Performance('hamlet', 30),
            new Play('Hamlet', Play::TYPE_TRAGEDY)
        );

        self::assertSame(40000, $calculator->calculateAmount());
        self::assertSame(0, $calculator->calculateVolumeCredits());
    }

    public function testCalculatesAmountAndCreditsWithAudienceOverThreshold(): void
    {
        $calculator = new TragedyCalculator(
            new Performance('hamlet', 55),
            new Play('Hamlet', Play::TYPE_TRAGEDY)
        );

        // 40000 + 1000 * (55 - 30) = 65000
        self::assertSame(65000, $calculator->calculateAmount());
        // 55 - 30 = 25
        self::assertSame(25, $calculator->calculateVolumeCredits());
    }
}
