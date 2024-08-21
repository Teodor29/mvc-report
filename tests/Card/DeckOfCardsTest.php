<?php

namespace App\Tests;

use App\Card\DeckOfCards;
use PHPUnit\Framework\TestCase;

class DeckOfCardsTest extends TestCase
{
    public function testShuffleDeck(): void
    {
        $deck = new DeckOfCards();
        $originalDeck = $deck->getDeck();
        $deck->shuffle();
        $shuffledDeck = $deck->getDeck();
        $this->assertNotEquals($originalDeck, $shuffledDeck);
    }

    public function testDealCard(): void
    {
        $deck = new DeckOfCards();
        $card = $deck->deal();
        $this->assertNotNull($card);
        $this->assertIsString($card);
    }
}
