<?php

namespace App\Controller;

use App\Service\DealerService;
use App\Service\GameService;
use App\Service\PlayerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProjBlackjackController extends AbstractController
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

    #[Route('/proj', name: 'proj')]
    public function proj(): Response
    {
        return $this->render('project/index.html.twig');
    }

    #[Route('/proj/about', name: 'proj_about')]
    public function about(): Response
    {
        return $this->render('project/proj_about.html.twig');
    }

    #[Route('/proj/bet', name: 'proj_bet', methods: ['POST'])]
    public function bet(
        Request $request,
        SessionInterface $session
    ): Response {
        $playerName = $request->request->get('playerName');
        if ($request->request->get('numberOfPlayers')) {
            $numberOfPlayers = (int) $request->request->get('numberOfPlayers');
        } else {
            $numberOfPlayers = $session->get('numberOfPlayers', 1);
        }
        $session->set('playerName', $playerName);
        $session->set('numberOfPlayers', $numberOfPlayers);

        $balance = $session->get('balance', 1000);

        $session->set('balance', $balance);

        if (!is_int($numberOfPlayers)) {
            $numberOfPlayers = 1;
        }

        $playerHands = $this->playerService->initPlayerHands($numberOfPlayers);
        $dealerHand = $this->dealerService->initDealerHand();

        $session->set('playerHands', $playerHands);
        $session->set('dealerHand', $dealerHand);
        $session->set('balance', $balance);

        return $this->render('project/proj_bet.html.twig', [
            'playerHands' => $playerHands,
            'dealerHand' => $dealerHand,
            'playerName' => $playerName,
            'balance' => $balance,
        ]);
    }

    #[Route('/proj/blackjack', name: 'proj_blackjack', methods: ['GET', 'POST'])]
    public function blackjack(
        Request $request,
        SessionInterface $session
    ): Response {
        $playerName = $session->get('playerName');

        $playerHands = $session->get('playerHands', []);

        $dealerHand = $session->get('dealerHand', []);
        $balance = $session->get('balance');

        if (!is_array($playerHands)) {
            $playerHands = [];
        }
        if (!is_array($dealerHand)) {
            $dealerHand = [];
        }

        if ($this->playerService->checkPlayerDone($playerHands)) {
            $dealerHand = $this->dealerService->dealerPlay($dealerHand);
            $playerHands = $this->gameService->endGame($playerHands, $dealerHand);
            $winnings = $this->gameService->calculateWinnings($playerHands);
            $balance += $winnings;

            $session->set('dealerHand', $dealerHand);
            $session->set('playerHands', $playerHands);
            $session->set('balance', $balance);
            $session->set('winnings', $winnings);
            $session->set('roundFinished', true);
        } else {
            $session->set('roundFinished', false);
        }

        if ($request->isMethod('POST')) {
            $bets = [];
            foreach ($playerHands as $key => $playerHand) {
                $bets[$key] = (int) $request->request->get('bet' . $key, 0);
            }

            $this->gameService->placeBets($playerHands, $balance, $bets);
            $session->set('playerHands', $playerHands);
            $session->set('balance', $balance);
        }

        return $this->render('project/proj_blackjack.html.twig', [
            'playerHands' => $playerHands,
            'dealerHand' => $dealerHand,
            'playerName' => $playerName,
            'balance' => $balance,
            'roundFinished' => $session->get('roundFinished', false),
            'winnings' => $session->get('winnings', 0),
        ]);
    }

    #[Route('/proj/hit/{playerIndex}', name: 'proj_hit', methods: ['POST'])]
    public function hit(
        int $playerIndex,
        SessionInterface $session
    ): Response {
        $playerHands = $session->get('playerHands', []);

        if (!is_array($playerHands)) {
            $playerHands = [];
        }

        $playerHands = $this->playerService->playerHit($playerHands, $playerIndex);

        $session->set('playerHands', $playerHands);

        return $this->redirectToRoute('proj_blackjack');
    }

    #[Route('/proj/stand/{playerIndex}', name: 'proj_stand', methods: ['POST'])]
    public function stand(
        int $playerIndex,
        SessionInterface $session
    ): Response {
        $playerHands = $session->get('playerHands', []);

        if (!is_array($playerHands)) {
            $playerHands = [];
        }

        $playerHands = $this->playerService->playerStand($playerHands, $playerIndex);

        $session->set('playerHands', $playerHands);

        return $this->redirectToRoute('proj_blackjack');
    }

    #[Route('/proj/reset-session', name: 'proj_reset_session')]
    public function resetSession(SessionInterface $session): Response
    {
        $session->clear();

        return $this->redirectToRoute('proj');
    }
}
