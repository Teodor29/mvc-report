<?php

namespace App\Service;

class GameService
{
    /**
     * @param array $playerHands array of player hands
     * @param array $dealerHand  dealer's hand
     *
     * @return array updated player hands with their status
     */
    public function endGame(array $playerHands, array $dealerHand): array
    {
        foreach ($playerHands as &$hand) {
            if ($hand['status'] === 'bust') {
                $hand['status'] = 'lose';
            } elseif ($hand['status'] === 'stand') {
                $hand['status'] = $this->compareScores($hand['score'], $dealerHand['score']);
            }
        }

        return $playerHands;
    }

    /**
     * @param int $playerScore player's score
     * @param int $dealerScore dealer's score
     *
     * @return string result of the comparison: "win", "lose", or "draw"
     */
    public function compareScores(int $playerScore, int $dealerScore): string
    {
        if ($dealerScore > 21 || $playerScore > $dealerScore) {
            return 'win';
        }

        if ($playerScore < $dealerScore) {
            return 'lose';
        }

        return 'draw';
    }

    /**
     * @param array $playerHands array of player hands
     * @param int   $balance     current balance
     *
     * @return int updated balance
     */
    public function calculateBalance(array $playerHands, int $balance): int
    {
        foreach ($playerHands as $hand) {
            if ($hand['status'] === 'win') {
                $balance += $hand['bet'] * 2;
            } elseif ($hand['status'] === 'draw') {
                $balance += $hand['bet'];
            }
        }

        return $balance;
    }

    /**
     * @param array $playerHands array of player hands
     *
     * @return int total winnings
     */
    public function calculateWinnings(array $playerHands): int
    {
        $winnings = 0;

        foreach ($playerHands as $hand) {
            if ($hand['status'] === 'win') {
                $winnings += $hand['bet'] * 2;
            } elseif ($hand['status'] === 'draw') {
                $winnings += $hand['bet'];
            }
        }

        return $winnings;
    }

    /**
     * @param array $playerHands array of player hands
     * @param int   $balance     current balance
     * @param array $bets        array of bets
     */
    public function placeBets(array &$playerHands, int &$balance, array $bets): void
    {
        foreach ($playerHands as $key => &$playerHand) {
            if (!isset($playerHand['bet'])) {
                $playerHand['bet'] = $bets[$key] ?? 0;
                $balance -= $playerHand['bet'];
            }
        }
    }
}
