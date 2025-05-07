<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiProjBlackjackController extends AbstractController
{
    #[Route('/proj/api', name: 'api_blackjack', methods: ['GET'])]
    public function apiGame(
        SessionInterface $session
    ): JsonResponse {
        $playerHands = $session->get('playerHands', []);
        $dealerHand = $session->get('dealerHand', []);

        $gameState = [
            'playerHands' => $playerHands,
            'dealerHand' => $dealerHand,
        ];

        $response = new JsonResponse($gameState);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );

        return $response;
    }
}
