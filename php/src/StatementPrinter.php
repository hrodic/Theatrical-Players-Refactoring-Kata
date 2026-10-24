<?php

declare(strict_types=1);

namespace Theatrical;

use Error;
use NumberFormatter;

class StatementPrinter
{
    /**
     * @param array<string, Play> $plays
     */
    public function print(Invoice $invoice, array $plays): string
    {
        $totalAmount = 0;
        $volumeCredits = 0;

        $result = "Statement for {$invoice->customer}\n";

        foreach ($invoice->performances as $performance) {
            // lookup the play for this performance
            $play = $plays[$performance->playId];

            // amount for this order
            $performanceAmount = $this->calculatePerformanceAmount($performance, $play);

            $volumeCredits += $this->calculateVolumeCredits($performance, $play);
            // formatting logic: print line for this order

            $result .= "  {$play->name}: {$this->formatAsUSD($performanceAmount)} ";
            $result .= "({$performance->audience} seats)\n";

            $totalAmount += $performanceAmount;
        }

        $result .= "Amount owed is {$this->formatAsUSD($totalAmount)}\n";
        $result .= "You earned {$volumeCredits} credits";
        return $result;
    }

    private function calculateVolumeCredits(Performance $performance, Play $play): int
    {
        $volumeCredits = max($performance->audience - 30, 0);
        if ($play->type === 'comedy') {
            $volumeCredits += intdiv($performance->audience, 5);
        }
        return $volumeCredits;
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
        $amount += 300 * $performance->audience;
        return $amount;
    }

    private function calculatePerformanceAmount(Performance $performance, Play $play): int
    {
        switch ($play->type) {
            case 'tragedy':
                return $this->calculateTragedyAmount($performance);

            case 'comedy':
                return $this->calculateComedyAmount($performance);

            default:
                throw new Error("Unknown type: {$play->type}");
        }
    }

    private function formatAsUSD(int $amount): string
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount / 100, 'USD');
    }
}
