<?php

namespace App\Card;

/**
 * Class for Blackjack game.
 * It can deal cards, calculate scores, and check if the game has ended.
 */
class Blackjack
{
    private DeckOfCards $deck;

    /**
     * Constructor for the Blackjack class.
     *
     * @param DeckOfCards $deck
     */
    public function __construct($deck)
    {
        $this->deck = $deck;
    }

    /**
     * Deals a card from the deck.
     *
     * @return string|null
     */
    public function deal()
    {
        return $this->deck->deal();
    }

    /**
     * Calculates the score of a hand in Blackjack.
     *
     * @param array<string> $hand
     *
     * @return int
     */
    public function calculateScore($hand)
    {
        $score = 0;
        $numAces = 0;

        foreach ($hand as $card) {
            $rank = explode(' ', $card)[1];
            if (in_array($rank, ['J', 'Q', 'K'])) {
                $score += 10;
            } elseif ('A' === $rank) {
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

    /**
     * Checks if the game of Blackjack has ended.
     *
     * @param int $playerScore
     * @param int $dealerScore
     *
     * @return bool
     */
    public function checkGameEnd($playerScore, $dealerScore)
    {
        return $playerScore > 20 || $dealerScore > 20 || $dealerScore > 16;
    }
}
