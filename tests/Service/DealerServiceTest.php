<?php

namespace App\Tests\Service;

use App\Service\DealerService;
use PHPUnit\Framework\TestCase;

class DealerServiceTest extends TestCase
{
    public function testInitDealerHand(): void
    {
        $service = new DealerService();
        $dealerHand = $service->initDealerHand();

        $this->assertCount(1, $dealerHand['hand']);
        $this->assertGreaterThanOrEqual(0, $dealerHand['score']);
    }

    public function testDealerPlay(): void
    {
        $service = new DealerService();
        $dealerHand = [
            'hand' => ['♠ 10'], 
            'score' => 10];
        $updatedHand = $service->dealerPlay($dealerHand);

        $this->assertGreaterThanOrEqual(17, $updatedHand['score']);
    }
}