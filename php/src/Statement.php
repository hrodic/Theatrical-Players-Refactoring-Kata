<?php

declare(strict_types=1);

namespace Theatrical;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, StatementLine>
 */
final readonly class Statement implements IteratorAggregate, Countable
{
    /**
     * @var list<StatementLine>
     */
    public array $lines;

    /**
     * PHP enforces that every argument after $customer is an instance of StatementLine at runtime.
     */
    public function __construct(
        public string $customer,
        StatementLine ...$lines,
    ) {
        $this->lines = array_values($lines);
    }

    public function totalAmount(): int
    {
        return array_reduce(
            $this->lines,
            fn (int $total, StatementLine $line): int => $total + $line->amount,
            0,
        );
    }

    public function totalVolumeCredits(): int
    {
        return array_reduce(
            $this->lines,
            fn (int $total, StatementLine $line): int => $total + $line->volumeCredits,
            0,
        );
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->lines);
    }

    public function count(): int
    {
        return count($this->lines);
    }
}
