<?php

namespace App\Card;

/**
 * The Blackjack class provides functionality to play a game of Blackjack.
 */
class Blackjack
{
    private $deck;

    public function __construct($deck)
    {
        $this->deck = $deck;
    }

    /**
     * Deals a card from the deck.
     *
     * @return string the card dealt
     */
    public function deal()
    {
        return $this->deck->deal();
    }

    public function calculateScore($hand)
    {
        $score = 0;
        $numAces = 0;

        foreach ($hand as $card) {
            $rank = explode(' ', $card)[1];
            if (in_array($rank, ['Knekt', 'Drottning', 'Kung'])) {
                $score += 10;
            } elseif ('Ess' === $rank) {
                $score += 11;
                ++$numAces;
            } else {
                $score += intval($rank);
            }
        }

        while ($score > 21 && $numAces > 0) {
            $score -= 10;
            --$numAces;
        }

        return $score;
    }

    public function checkGameEnd($playerScore, $dealerScore)
    {
        return $playerScore > 20 || $dealerScore > 20 || $dealerScore > 16;
    }
}
