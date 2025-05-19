<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use App\Service\DealerService;
use App\Service\GameService;
use App\Service\PlayerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiProjBlackjackController extends AbstractController
{
    private PlayerService $playerService;
    private DealerService $dealerService;
    private GameService $gameService;

    public function __construct(
        PlayerService $playerService,
        DealerService $dealerService,
        GameService $gameService
    ) {
        $this->playerService = $playerService;
        $this->dealerService = $dealerService;
        $this->gameService = $gameService;
    }

    #[Route('/proj/api', name: 'proj_api', methods: ['GET'])]
    public function apiBlackjack(): Response
    {
        return $this->render('project/proj_api.html.twig');
    }

    #[Route('/proj/api/init/{numberOfHands}', name: 'proj_api_init', methods: ['GET', 'POST'])]
    public function startGame(
        int $numberOfHands,
        SessionInterface $session
    ): JsonResponse {
        $playerHands = $this->playerService->initPlayerHands($numberOfHands);
        $dealerHand = $this->dealerService->initDealerHand();

        $session->set('playerHands', $playerHands);
        $session->set('dealerHand', $dealerHand);

        return $this->json([
            'playerHands' => $playerHands,
            'dealerHand' => $dealerHand,
        ], 200, [], ['json_encode_options' => JSON_PRETTY_PRINT]);
    }

    #[Route('/proj/api/game', name: 'proj_api_game', methods: ['GET'])]
    public function gameState(
        SessionInterface $session
    ): JsonResponse {
        $playerHands = $session->get('playerHands', []);
        $dealerHand = $session->get('dealerHand', []);

        return $this->json([
            'playerHands' => $playerHands,
            'dealerHand' => $dealerHand,
        ], 200, [], ['json_encode_options' => JSON_PRETTY_PRINT]);
    }

    #[Route('/proj/api/hit/{playerIndex}', name: 'proj_api_hit', methods: ['GET', 'POST'])]
    public function playerHit(
        int $playerIndex,
        SessionInterface $session
    ): JsonResponse {
        $playerHands = $session->get('playerHands', []);
        $dealerHand = $session->get('dealerHand', []);

        if (!is_array($playerHands)) {
            $playerHands = [];
        }

        $playerHands = $this->playerService->playerHit($playerHands, $playerIndex);

        $session->set('playerHands', $playerHands);

        return $this->json([
            'playerHands' => $playerHands,
            'dealerHand' => $dealerHand,
        ], 200, [], ['json_encode_options' => JSON_PRETTY_PRINT]);
    }

    #[Route('/proj/api/stand/{playerIndex}', name: 'proj_api_stand', methods: ['GET', 'POST'])]
    public function playerStand(
        int $playerIndex,
        SessionInterface $session
    ): JsonResponse {
        $playerHands = $session->get('playerHands', []);
        $dealerHand = $session->get('dealerHand', []);

        if (!is_array($playerHands)) {
            $playerHands = [];
        }

        $playerHands = $this->playerService->playerStand($playerHands, $playerIndex);

        $session->set('playerHands', $playerHands);

        return $this->json([
            'playerHands' => $playerHands,
            'dealerHand' => $dealerHand,
        ], 200, [], ['json_encode_options' => JSON_PRETTY_PRINT]);
    }

    #[Route('/proj/api/dealer-play', name: 'proj_api_dealer_play', methods: ['GET'])]
    public function deal(
        SessionInterface $session
    ): JsonResponse {
        $playerHands = $session->get('playerHands', []);
        $dealerHand = $session->get('dealerHand', []);

        if (!is_array($playerHands)) {
            $playerHands = [];
        }
        if (!is_array($dealerHand)) {
            $dealerHand = [];
        }

        $dealerHand = $this->dealerService->dealerPlay($dealerHand);
        $playerHands = $this->gameService->endGame($playerHands, $dealerHand);

        $session->set('dealerHand', $dealerHand);
        $session->set('playerHands', $playerHands);

        return $this->json([
            'playerHands' => $playerHands,
            'dealerHand' => $dealerHand,
        ], 200, [], ['json_encode_options' => JSON_PRETTY_PRINT]);
    }
}
