<?php

namespace App\Tests;

use App\Card\Card;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    public function testCreateCard(): void
    {
        $deck = ['♠ 10', '♠ J', '♠ A', '♦ 5', '♦ 10', '♣ 6'];
        $card = new Card($deck, 1);
        $result = $card->drawCards();
        $this->assertEquals(['♠ 10'], $result['cards']);
        $this->assertCount(5, $result['deck']);

    }
}
