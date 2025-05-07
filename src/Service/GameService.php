<?php

namespace App\Service;

class GameService
{
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
            }
        }

        return $balance;
    }
}
