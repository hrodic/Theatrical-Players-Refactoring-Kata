<?php

declare(strict_types=1);

namespace Theatrical;

interface PerformanceCalculator
{
    public function calculateAmount(): int;

    public function calculateVolumeCredits(): int;
}
