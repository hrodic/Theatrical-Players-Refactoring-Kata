<?php

declare(strict_types=1);

namespace Theatrical;

use Error;
use NumberFormatter;

final class StatementPrinter
{
    public function print(Invoice $invoice, array $plays): string
    {
        return $this->renderPlainText($this->createStatement($invoice, $plays));
    }

    private function renderPlainText(Statement $statement): string
    {
        $result = "Statement for {$statement->customer}\n";

        foreach ($statement as $line) {
            $formattedAmount = $this->formatAsUSD($line->amount);
            $result .= "  {$line->playName}: {$formattedAmount} ({$line->audience} seats)\n";
        }

        $result .= "Amount owed is {$this->formatAsUSD($statement->totalAmount())}\n";
        $result .= "You earned {$statement->totalVolumeCredits()} credits";

        return $result;
    }

    private function createStatement(Invoice $invoice, array $plays): Statement
    {
        $lines = array_map(
            fn(Performance $performance): StatementLine =>
            $this->createStatementLine($performance, $plays),
            $invoice->performances,
        );

        return new Statement($invoice->customer, ...$lines);
    }

    private function createStatementLine(Performance $performance, array $plays): StatementLine
    {
        $play = $this->playForPerformance($performance, $plays);
        return new StatementLine(
            playName: $play->name,
            audience: $performance->audience,
            amount: $this->calculatePerformanceAmount($performance, $plays),
            volumeCredits: $this->calculateVolumeCredits($performance, $plays)
        );
    }

    private function calculatePerformanceAmount(Performance $performance, array $plays): int
    {
        $play = $this->playForPerformance($performance, $plays);

        return match ($play->type) {
            Play::TYPE_TRAGEDY => $this->calculateTragedyAmount($performance),
            Play::TYPE_COMEDY => $this->calculateComedyAmount($performance),
            default => $this->throwUnknownTypeError($play->type)
        };
    }

    private function calculateTragedyAmount(Performance $performance): int
    {
        return new TragedyCalculator($performance)->calculateAmount();
    }

    private function calculateComedyAmount(Performance $performance): int
    {
        return new ComedyCalculator($performance)->calculateAmount();
    }

    private function calculateVolumeCredits(Performance $performance, array $plays): int
    {
        $play = $this->playForPerformance($performance, $plays);

        return match ($play->type) {
            Play::TYPE_TRAGEDY => new TragedyCalculator($performance)->calculateVolumeCredits(),
            Play::TYPE_COMEDY => new ComedyCalculator($performance)->calculateVolumeCredits(),
            default => $this->throwUnknownTypeError($play->type)
        };
    }

    private function throwUnknownTypeError(string $type): never
    {
        throw new Error("Unknown type: {$type}");
    }

    private function playForPerformance(Performance $performance, array $plays): Play
    {
        return $plays[$performance->playId];
    }

    private function formatAsUSD(int $amountInCents): string
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);

        return (string) $formatter->formatCurrency($amountInCents / 100, 'USD');
    }
}
