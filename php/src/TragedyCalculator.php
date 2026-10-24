<?php

declare(strict_types=1);

namespace Theatrical;

final readonly class TragedyCalculator implements PerformanceCalculator
{
    public function __construct(
        private Performance $performance
    ) {}

    public function calculateAmount(): int
    {
        $amount = 40000;
        if ($this->performance->audience > 30) {
            $amount += 1000 * ($this->performance->audience - 30);
        }

        return $amount;
    }

    public function calculateVolumeCredits(): int
    {
        return max($this->performance->audience - 30, 0);
    }
}
