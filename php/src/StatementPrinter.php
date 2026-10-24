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
