<?php

declare(strict_types=1);

namespace Theatrical;

use Error;

final readonly class StatementFactory
{
    public function create(Invoice $invoice, array $plays): Statement
    {
        $lines = array_map(
            fn (Performance $performance): StatementLine =>
            $this->createStatementLine($performance, $plays),
            $invoice->performances,
        );

        return new Statement($invoice->customer, ...$lines);
    }

    private function createStatementLine(Performance $performance, array $plays): StatementLine
    {
        $play = $this->playForPerformance($performance, $plays);
        $calculator = $this->createCalculator($performance, $play);
        return new StatementLine(
            playName: $play->name,
            audience: $performance->audience,
            amount: $calculator->calculateAmount(),
            volumeCredits: $calculator->calculateVolumeCredits()
        );
    }

    private function createCalculator(Performance $performance, Play $play): PerformanceCalculator
    {
        return match ($play->type) {
            Play::TYPE_TRAGEDY => new TragedyCalculator($performance),
            Play::TYPE_COMEDY => new ComedyCalculator($performance),
            default => throw new Error("Unknown type: {$play->type}"),
        };
    }

    private function playForPerformance(Performance $performance, array $plays): Play
    {
        return $plays[$performance->playId];
    }
}
