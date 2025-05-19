<?php

namespace App\Tests\Service;

use App\Service\GameService;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase
{
    public function testEndGame(): void
    {
        $service = new GameService();
        $playerHands = [
            [
                'score' => 15,
                'status' => 'stand',
            ],
            [
                'score' => 19,
                'status' => 'stand',
            ],
            [
                'score' => 22,
                'status' => 'bust',
            ],
            [
                'score' => 18,
                'status' => 'stand',
            ],

        ];
        $dealerHand = [
            'score' => 18
        ];
        $updatedHands = $service->endGame($playerHands, $dealerHand);

        $this->assertEquals('lose', $updatedHands[0]['status']);
        $this->assertEquals('win', $updatedHands[1]['status']);
        $this->assertEquals('lose', $updatedHands[2]['status']);
        $this->assertEquals('draw', $updatedHands[3]['status']);
    }

    public function testCompareScores(): void
    {
        $service = new GameService();
        $result = $service->compareScores(21, 20);

        $this->assertEquals('win', $result);
    }

    public function testCalculateBalance(): void
    {
        $service = new GameService();
        $playerHands = [
            [
                'status' => 'win',
                'bet' => 10,
            ],
            [
                'status' => 'lose',
                'bet' => 10,
            ],
            [
                'status' => 'draw',
                'bet' => 10,
            ],
        ];

        $balance = 100;
        $updatedBalance = $service->calculateBalance($playerHands, $balance);
        $this->assertEquals(130, $updatedBalance);

    }

    public function testCalculateWinnings(): void
    {
        $service = new GameService();
        $playerHands = [
            [
                'status' => 'win',
                'bet' => 10,
            ],
            [
                'status' => 'lose',
                'bet' => 10,
            ],
            [
                'status' => 'draw',
                'bet' => 10,
            ],
        ];
        $winnings = $service->calculateWinnings($playerHands);
        $this->assertEquals(30, $winnings);
    }

    public function testPlaceBets(): void
    {
        $service = new GameService();
        $playerHands = [
            [],
            [],
        ];

        $bets = [
            0 => 10,
            1 => 20,
        ];

        $balance = 100;

        $service->placeBets($playerHands, $balance, $bets);

        $this->assertEquals(10, $playerHands[0]['bet']);
        $this->assertEquals(20, $playerHands[1]['bet']);
        $this->assertEquals(70, $balance);
    }
}
