<?php

namespace App\Service;

use App\Card\Blackjack;
use App\Card\DeckOfCards;

class ProjBlackjackService
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
        $playerHands[$playerIndex]['hand'] = array_filter($playerHands[$playerIndex]['hand'], fn($card) => null !== $card);
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
     * @param string[] $dealerHand array of strings representing the dealer's hand
     *
     * @return string[] updated array of strings after the dealer plays
     */
    public function dealerPlay(array $dealerHand): array
    {

        while ($dealerHand['score'] < 17) {
            $dealerHand['hand'][] = $this->blackjack->deal();
            $dealerHand['hand'] = array_filter($dealerHand['hand'], fn($card) => null !== $card);
            $dealerHand['score'] = $this->blackjack->calculateScore($dealerHand['hand']);
        }
        
        $dealerHand['status'] = $this->checkHandStatus($dealerHand['hand']);
        return $dealerHand;
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
     * @param string[] $hand array of strings representing the hand
     *
     * @return int the score of the hand
     */
    public function calculateScore(array $hand): int
    {
        return $this->blackjack->calculateScore($hand);
    }

    public function getDeck(): DeckOfCards
    {
        return $this->deck;
    }

    public function checkPlayerDone(array $playerHands): bool
    {
        foreach ($playerHands as $hand) {
            if ($hand['status'] === "playing") {
                return false;
            }
        }
        return true;
    }

    /**
     * Calculate the player's balance based on the hands and their status
     *
     * @param array $playerHands array of player hands
     * @param int $balance current balance
     *
     * @return int updated balance
     */
    public function calculateBalance(array $playerHands, int $balance): int
    {
        foreach ($playerHands as $hand) {
            if ($hand['status'] === "win") {
                $balance += $hand['bet'] * 2;
            } elseif ($hand['status'] === "draw") {
                $balance += $hand['bet'];
            } elseif ($hand['status'] === "lose") {
                $balance -= $hand['bet'];
            }
        }

        return $balance;
    }

    /**
     * End the game and determine the outcome for each player hand
     *
     * @param array $playerHands array of player hands
     * @param array $dealerHand dealer's hand
     *
     * @return array updated player hands with their status
     */
    public function endGame(array $playerHands, array $dealerHand): array
    {
        foreach ($playerHands as &$hand) {
            if ($hand['status'] === "bust") {
                $hand['status'] = "lose";
            } elseif ($hand['status'] === "stand") {
                if ($dealerHand['score'] > 21 || $hand['score'] > $dealerHand['score']) {
                    $hand['status'] = "win";
                } elseif ($hand['score'] < $dealerHand['score']) {
                    $hand['status'] = "lose";
                } else {
                    $hand['status'] = "draw";
                }
            }
        }

        return $playerHands;
    }
}
