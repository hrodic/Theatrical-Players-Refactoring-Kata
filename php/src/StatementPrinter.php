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
            'tragedy' => $this->calculateTragedyAmount($performance),
            'comedy' => $this->calculateComedyAmount($performance),
            default => throw new Error("Unknown type: {$play->type}"),
        };
    }

    private function calculateTragedyAmount(Performance $performance): int
    {
        $amount = 40000;
        if ($performance->audience > 30) {
            $amount += 1000 * ($performance->audience - 30);
        }

        return $amount;
    }

    private function calculateComedyAmount(Performance $performance): int
    {
        $amount = 30000;
        if ($performance->audience > 20) {
            $amount += 10000 + 500 * ($performance->audience - 20);
        }

        return $amount + 300 * $performance->audience;
    }

    private function calculateVolumeCredits(Performance $performance, array $plays): int
    {
        $play = $this->playForPerformance($performance, $plays);
        $credits = max($performance->audience - 30, 0);

        if ($play->type === 'comedy') {
            $credits += intdiv($performance->audience, 5);
        }

        return $credits;
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
