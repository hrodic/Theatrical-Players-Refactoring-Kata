<?php

declare(strict_types=1);

namespace Theatrical;

final readonly class ComedyCalculator implements PerformanceCalculator
{
    public function __construct(
        private Performance $performance
    ) {}

    public function calculateAmount(): int
    {
        $amount = 30000;
        if ($this->performance->audience > 20) {
            $amount += 10000 + 500 * ($this->performance->audience - 20);
        }

        return $amount + 300 * $this->performance->audience;
    }

    public function calculateVolumeCredits(): int
    {
        return max($this->performance->audience - 30, 0) + intdiv($this->performance->audience, 5);
    }
}
