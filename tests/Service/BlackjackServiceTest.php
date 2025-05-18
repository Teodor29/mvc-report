<?php

namespace App\Tests\Service;

use App\Service\BlackjackService;
use PHPUnit\Framework\TestCase;

class BlackjackServiceTest extends TestCase
{
    public function testPlayerHit(): void
    {
        $service = new BlackjackService();
        $playerHand = ['♠ 10', '♦ 5'];
        $updatedHand = $service->playerHit($playerHand);

        $this->assertCount(3, $updatedHand);
        $this->assertNotEmpty($updatedHand[2]);
    }

    public function testDealerPlay(): void
    {
        $service = new BlackjackService();
        $dealerHand = ['♠ 10', '♦ 6'];
        $updatedHand = $service->dealerPlay($dealerHand);

        $this->assertGreaterThanOrEqual(2, count($updatedHand));
    }

    public function testCalculateScore(): void
    {
        $service = new BlackjackService();
        $playerHand = ['♠ 10', '♦ 5'];
        $score = $service->calculateScore($playerHand);

        $this->assertEquals(15, $score);
    }

    public function testCheckGameEnd(): void
    {
        $service = new BlackjackService();
        $gameEnd = $service->checkGameEnd(21, 20);

        $this->assertTrue($gameEnd);
    }

    public function testGetDeck(): void
    {
        $service = new BlackjackService();
        $deck = $service->getDeck()->getDeck();

        $this->assertContains('♠ A', $deck);
        $this->assertContains('♦ 10', $deck);
        $this->assertContains('♣ 6', $deck);
    }
}
