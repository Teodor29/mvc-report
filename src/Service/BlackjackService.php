<?php

namespace App\Service;

use App\Card\Blackjack;
use App\Card\DeckOfCards;

class BlackjackService
{
    private Blackjack $blackjack;
    private DeckOfCards $deck;

    public function __construct()
    {
        $this->deck = new DeckOfCards();
        $this->deck->shuffle();
        $this->blackjack = new Blackjack($this->deck);
    }


    public function playerHit(array $playerHand): array
    {
        $playerHand[] = $this->blackjack->deal();
        return array_filter($playerHand, fn($card) => $card !== null);
    }

    public function dealerPlay(array $dealerHand): array
    {
        $dealerScore = $this->blackjack->calculateScore($dealerHand);
        while ($dealerScore < 17) {
            $dealerHand[] = $this->blackjack->deal();
            $dealerHand = array_filter($dealerHand, fn($card) => $card !== null);
            $dealerScore = $this->blackjack->calculateScore($dealerHand);
        }

        return $dealerHand;
    }

    public function calculateScore(array $hand): int
    {
        return $this->blackjack->calculateScore($hand);
    }

    public function checkGameEnd(int $playerScore, int $dealerScore): bool
    {
        return $this->blackjack->checkGameEnd($playerScore, $dealerScore);
    }

    public function getDeck(): DeckOfCards
    {
        return $this->deck;
    }
}
