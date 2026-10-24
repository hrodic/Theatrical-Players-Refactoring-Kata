<?php

declare(strict_types=1);

namespace Theatrical;

use Error;
use NumberFormatter;

final class StatementPrinter
{
    public function print(Invoice $invoice, array $plays): string
    {
        $result = "Statement for {$invoice->customer}\n";

        foreach ($invoice->performances as $performance) {
            $result .= $this->formatPerformanceLine($performance, $plays);
        }

        $result .= "Amount owed is {$this->formatAsUSD($this->totalAmount($invoice,$plays))}\n";
        $result .= "You earned {$this->totalVolumeCredits($invoice,$plays)} credits";

        return $result;
    }

    private function formatPerformanceLine(Performance $performance, array $plays): string
    {
        $play = $this->playForPerformance($performance, $plays);
        $amount = $this->calculatePerformanceAmount($performance, $plays);

        return "  {$play->name}: {$this->formatAsUSD($amount)} ({$performance->audience} seats)\n";
    }

    private function totalAmount(Invoice $invoice, array $plays): int
    {
        $total = 0;
        foreach ($invoice->performances as $performance) {
            $total += $this->calculatePerformanceAmount($performance, $plays);
        }

        return $total;
    }

    private function totalVolumeCredits(Invoice $invoice, array $plays): int
    {
        $credits = 0;
        foreach ($invoice->performances as $performance) {
            $credits += $this->calculateVolumeCredits($performance, $plays);
        }

        return $credits;
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
