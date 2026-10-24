<?php

declare(strict_types=1);

namespace Theatrical;

use NumberFormatter;

final class StatementPrinter
{
    public function __construct(
        private StatementFactory $statementFactory = new StatementFactory()
    ) {
    }

    public function print(Invoice $invoice, array $plays): string
    {
        $statement = $this->statementFactory->create($invoice, $plays);
        return $this->renderPlainText($statement);
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

    private function formatAsUSD(int $amountInCents): string
    {
        $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);

        return (string) $formatter->formatCurrency($amountInCents / 100, 'USD');
    }
}
