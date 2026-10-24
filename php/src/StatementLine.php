<?php

declare(strict_types=1);

namespace Theatrical;

final readonly class StatementLine
{
    public function __construct(
        public string $playName,
        public int $audience,
        public int $amount,
        public int $volumeCredits
    ) {
    }
}
