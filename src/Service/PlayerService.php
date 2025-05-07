<?php

namespace App\Service;

use App\Card\Blackjack;
use App\Card\DeckOfCards;

class PlayerService
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
     * @param int $numberOfHands number of hands to initialize
     *
     * @return array array of hands with their scores
     */
    public function initPlayerHands(int $numberOfHands): array
    {
        $hands = [];
        for ($i = 0; $i < $numberOfHands; ++$i) {
            $hand = [
                $this->blackjack->deal(),
                $this->blackjack->deal(),
            ];
            $score = $this->blackjack->calculateScore($hand);

            $hands[] = [
                "hand" => $hand,
                "score" => $score,
                "status" => "playing",
                "isActive" => ($i === 0),
            ];
        }

        return $hands;
    }

    /**
     * @param array $playerHands array of player hands
     * @param int $playerIndex index of the player who hit
     *
     * @return array updated array of player hands
     */
    public function playerHit(array $playerHands, int $playerIndex): array
    {
        $playerHands[$playerIndex]['hand'][] = $this->blackjack->deal();
        $playerHands[$playerIndex]['score'] = $this->blackjack->calculateScore($playerHands[$playerIndex]['hand']);
        $playerHands[$playerIndex]['status'] = $this->checkHandStatus($playerHands[$playerIndex]['hand']);

        if ($playerHands[$playerIndex]['status'] === "bust" || $playerHands[$playerIndex]['status'] === "stand") {
            $playerHands[$playerIndex]['isActive'] = false;
            if (isset($playerHands[$playerIndex + 1])) {
                $playerHands[$playerIndex + 1]['isActive'] = true;
            }
        }

        return $playerHands;
    }

    /**
     * @param array $playerHands array of player hands
     * @param int $playerIndex index of the player who stood
     *
     * @return array updated array of player hands
     */
    public function playerStand(array $playerHands, int $playerIndex): array
    {
        $playerHands[$playerIndex]['status'] = "stand";
        $playerHands[$playerIndex]['isActive'] = false;

        if (isset($playerHands[$playerIndex + 1])) {
            $playerHands[$playerIndex + 1]['isActive'] = true;
        }

        return $playerHands;
    }

    /**
     * Check the status of a hand (e.g., blackjack, bust, or playing)
     *
     * @param array $hand the player's hand
     *
     * @return string the status of the hand (e.g., "blackjack", "bust", "playing", "stand")
     */
    public function checkHandStatus(array $hand): string
    {
        $score = $this->blackjack->calculateScore($hand);

        if (count($hand) === 2 && $score === 21) {
            return "blackjack";
        } elseif ($score > 21) {
            return "bust";
        } elseif ($score === 21) {
            return "stand";
        }

        return "playing";
    }

    /**
     * Check if all players have finished their turns
     *
     * @param array $playerHands array of player hands
     *
     * @return bool true if all players are done, false otherwise
     */
    public function checkPlayerDone(array $playerHands): bool
    {
        foreach ($playerHands as $hand) {
            if ($hand['status'] === "playing") {
                return false;
            }
        }
        return true;
    }
}