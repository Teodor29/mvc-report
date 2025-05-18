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

    /**
     * @param string[] $playerHand array of strings representing the player's hand
     *
     * @return string[] updated array of strings after the player hits
     */
    public function playerHit(array $playerHand): array
    {
        $playerHand[] = $this->blackjack->deal();

        return array_filter($playerHand, fn ($card) => null !== $card);
    }

    /**
     * @param string[] $dealerHand array of strings representing the dealer's hand
     *
     * @return string[] updated array of strings after the dealer plays
     */
    public function dealerPlay(array $dealerHand): array
    {
        $dealerScore = $this->blackjack->calculateScore($dealerHand);
        while ($dealerScore < 17) {
            $dealerHand[] = $this->blackjack->deal();
            $dealerHand = array_filter($dealerHand, fn ($card) => null !== $card);
            $dealerScore = $this->blackjack->calculateScore($dealerHand);
        }

        return $dealerHand;
    }

    /**
     * @param string[] $hand array of strings representing the hand
     *
     * @return int the score of the hand
     */
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
