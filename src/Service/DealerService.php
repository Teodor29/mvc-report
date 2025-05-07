<?php

namespace App\Service;

use App\Card\Blackjack;
use App\Card\DeckOfCards;

class DealerService
{
    private Blackjack $blackjack;
    private DeckOfCards $deck;

    public function __construct()
    {
        $this->deck = new DeckOfCards();
        $this->deck->shuffle();
        $this->blackjack = new Blackjack($this->deck);
    }

    /**
     * Initialize the dealer's hand with one card
     *
     * @return array array containing the dealer's hand and score
     */
    public function initDealerHand(): array
    {
        $dealerHand = [
            $this->blackjack->deal(),
        ];
        $dealerScore = $this->blackjack->calculateScore($dealerHand);

        return [
            "hand" => $dealerHand,
            "score" => $dealerScore,
        ];
    }

    /**
     * @param string[] $dealerHand array of strings representing the dealer's hand
     *
     * @return string[] updated array of strings after the dealer plays
     */
    public function dealerPlay(array $dealerHand): array
    {
        $hand = $dealerHand['hand'];
        $score = $dealerHand['score'];

        while ($score < 17) {
            $hand[] = $this->blackjack->deal();
            $score = $this->blackjack->calculateScore($hand);
        }

        return [
            "hand" => $hand,
            "score" => $score,
        ];
    }
}
