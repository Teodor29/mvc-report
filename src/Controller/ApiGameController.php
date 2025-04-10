<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiGameController extends AbstractController
{
    #[Route('/api/game', name: 'api_game', methods: ['GET'])]
    public function apiGame(
        SessionInterface $session
    ): JsonResponse {
        $playerHand = $session->get('playerHand', []);
        $dealerHand = $session->get('dealerHand', []);
        $playerScore = $session->get('playerScore', 0);
        $dealerScore = $session->get('dealerScore', 0);
        $end = $session->get('end', false);

        $gameState = [
            'playerHand' => $playerHand,
            'dealerHand' => $dealerHand,
            'playerScore' => $playerScore,
            'dealerScore' => $dealerScore,
            'end' => $end,
        ];

        $response = new JsonResponse($gameState);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );

        return $response;
    }
}
