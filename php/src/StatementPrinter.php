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

            $performanceAmount = $this->calculatePerformanceAmount($performance, $play);

            // credit rules ---
            // add volume credits
            $volumeCredits += max($performance->audience - 30, 0);
            // add extra credit for every ten comedy attendees
            if ($play->type === 'comedy') {
                $volumeCredits += floor($performance->audience / 5);
            }
            // end credit rules ---

            // formatting logic: print line for this order
            $result .= "  {$play->name}: {$this->formatAsUSD($performanceAmount)} ";
            $result .= "({$performance->audience} seats)\n";

            $totalAmount += $performanceAmount;
        }

        $result .= "Amount owed is {$this->formatAsUSD($totalAmount)}\n";
        $result .= "You earned {$volumeCredits} credits";
        return $result;
    }

    private function calculatePerformanceAmount(Performance $performance, Play $play): int
    {
        switch ($play->type) {
            case 'tragedy':
                $amount = 40000;
                if ($performance->audience > 30) {
                    $amount += 1000 * ($performance->audience - 30);
                }
                break;

            case 'comedy':
                $amount = 30000;
                if ($performance->audience > 20) {
                    $amount += 10000 + 500 * ($performance->audience - 20);
                }
                $amount += 300 * $performance->audience;
                break;

            default:
                throw new Error("Unknown type: {$play->type}");
        }

        return $amount;
    }

    private function formatAsUSD(int $amount): string
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount / 100, 'USD');
    }
}
