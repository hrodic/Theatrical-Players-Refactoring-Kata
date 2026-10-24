<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Theatrical\Statement;
use Theatrical\StatementLine;

final class StatementTest extends TestCase
{
    public function testStatementHasZeroTotalsWhenNoPerformances(): void
    {
        $sut = new Statement('Customer');
        self::assertSame(0, $sut->totalAmount());
        self::assertSame(0, $sut->totalVolumeCredits());
        self::assertCount(0, $sut);
    }

    public function testStatementPublicMethods(): void
    {
        $customer = 'Customer';
        $sut = new Statement($customer, new StatementLine('fakePlay', 1, 10, 2));
        self::assertSame($customer, $sut->customer);
        self::assertSame(10, $sut->totalAmount());
        self::assertSame(2, $sut->totalVolumeCredits());
        self::assertCount(1, $sut);
    }

    public function testCalculateTotalAmountAndCreditsAcrossMultipleLines(): void
    {
        $line1 = new StatementLine('play1', 10, 5000, 25);
        $line2 = new StatementLine('play2', 200, 20000, 40);

        $sut = new Statement(
            'Fake customer',
            $line1,
            $line2
        );
        self::assertSame(25000, $sut->totalAmount());
        self::assertSame(65, $sut->totalVolumeCredits());
        self::assertCount(2, $sut);
        self::assertSame([$line1, $line2], iterator_to_array($sut));
    }
}
