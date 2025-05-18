<?php

namespace App\Tests\Service;

use App\Service\PlayerService;
use PHPUnit\Framework\TestCase;

class PlayerServiceTest extends TestCase
{
    public function testInitPlayerHands(): void
    {
        $service = new PlayerService();
        $playerHands = $service->initPlayerHands(2);

        $this->assertCount(2, $playerHands);
        $this->assertArrayHasKey('hand', $playerHands[0]);
        $this->assertArrayHasKey('score', $playerHands[0]);
        $this->assertArrayHasKey('status', $playerHands[0]);
        $this->assertArrayHasKey('isActive', $playerHands[0]);
    }

    public function testPlayerHit(): void
    {
        $service = new PlayerService();
        $playerHands = [
            [
                'hand' => ['♠ 10', '♦ 5'], 
                'score' => 15, 
                'status' => 'playing', 
                'isActive' => true],
            [
                'hand' => ['♠ 8', '♦ 7'], 
                'score' => 15, 
                'status' => 'playing', 
                'isActive' => false
            ],
        ];
        $updatedHands = $service->playerHit($playerHands, 0);

        $this->assertCount(3, $updatedHands[0]['hand']);
        $this->assertNotEmpty($updatedHands[0]['hand'][2]);
    }

    public function testPlayerStand(): void
    {
        $service = new PlayerService();
        $playerHands = [
            [
                'hand' => ['♠ 10', '♦ 5'], 
                'score' => 15, 
                'status' => 'playing', 
                'isActive' => true
            ],
            [
                'hand' => ['♠ 8', '♦ 7'], 
                'score' => 15, 
                'status' => 'playing', 
                'isActive' => false
            ],
            
        ];
        $updatedHands = $service->playerStand($playerHands, 0);

        $this->assertEquals('stand', $updatedHands[0]['status']);
        $this->assertFalse($updatedHands[0]['isActive']);
    }

    public function testCheckHandStatus(): void
    {
        $service = new PlayerService();
        $playerHands = [
            [
                'hand' => ['♠ 10', '♦ A'], 
            ],
            [
                'hand' => ['♠ 10', '♦ 8', '♣ 3'], 
            ],
            [
                'hand' => ['♠ 10', '♦ 8'], 
            ],
        ];
        $status = $service->checkHandStatus($playerHands[0]['hand']);
        $status2 = $service->checkHandStatus($playerHands[1]['hand']);
        $status3 = $service->checkHandStatus($playerHands[2]['hand']);

        $this->assertEquals('blackjack', $status);
        $this->assertEquals('stand', $status2);
        $this->assertEquals('playing', $status3);
    }

    public function testCheckPlayerDone(): void
    {
        $service = new PlayerService();
        $playerHands = [
            [
                'status' => 'stand'
            ],
            [
                'status' => 'bust'
            ],
        ];
        $playerHands2 = [
            [
                'status' => 'playing'
            ],
        ];
        $isDone = $service->checkPlayerDone($playerHands);
        $isDone2 = $service->checkPlayerDone($playerHands2);

        $this->assertTrue($isDone);
        $this->assertFalse($isDone2);
    }

}