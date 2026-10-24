<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Theatrical\Play;

final class PlayTest extends TestCase
{
    public function testPlayToString(): void
    {
        // arrange
        $sut = new Play('Rock day', 'Rock');
        // act
        $result = (string) $sut;
        // assert
        $this->assertSame('Rock day : Rock', $result);
    }
}
