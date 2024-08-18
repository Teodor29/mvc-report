<?php

namespace App\Tests;

use App\Card\DeckOfCards;
use App\Card\Blackjack;
use PHPUnit\Framework\TestCase;

class BlackjackTest extends TestCase
{

    public function testCreateBlackjack()
    {
        $deck = ['♠ 10', '♠ Knekt', '♠ Ess', '♦ 5', '♦ 10', '♣ 6'];
        $deckOfCards = new DeckOfCards($deck);
        $blackjack = new Blackjack($deckOfCards);
        $this->assertInstanceOf(Blackjack::class, $blackjack);
    }

    public function testDealCard()
    {
        $deck = ['♠ 10', '♠ Knekt', '♠ Ess', '♦ 5', '♦ 10', '♣ 6'];
        $deckOfCards = new DeckOfCards($deck);
        $blackjack = new Blackjack($deckOfCards);
        $card = $blackjack->deal();
        $this->assertEquals('♠ 10', $card);
    }

    public function testCalculateScore()
    {
        $deck = ['♠ 10', '♠ Knekt', '♠ Ess', '♦ 5', '♦ 10', '♣ 6'];
        $blackjack = new Blackjack($deck);
        $score = $blackjack->calculateScore(['♠ 10', '♠ Knekt', '♠ Ess']);
        $this->assertEquals(21, $score);

    }

    public function testCheckGameEnd()
    {
        $deck = ['♠ 10', '♠ Knekt', '♠ Ess', '♦ 5', '♦ 10', '♣ 6'];
        $blackjack = new Blackjack($deck);
        $gameEnd = $blackjack->checkGameEnd(21, 20);
        $this->assertTrue($gameEnd);

    }
}
