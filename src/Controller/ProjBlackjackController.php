<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use App\Service\ProjBlackjackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProjBlackjackController extends AbstractController
{
    private ProjBlackjackService $blackjackService;

    public function __construct(ProjBlackjackService $blackjackService)
    {
        $this->blackjackService = $blackjackService;
    }

    // General Routes
    #[Route('/proj', name: 'proj')]
    public function showProjectHome(): Response
    {
        return $this->render('project/index.html.twig');
    }

    #[Route('/proj/about', name: 'proj_about')]
    public function showAboutPage(): Response
    {
        return $this->render('project/proj_about.html.twig');
    }

    // Game Setup
    #[Route('/proj/bet', name: 'proj_bet', methods: ['POST'])]
    public function placeBet(Request $request, SessionInterface $session): Response
    {
        $playerName = $request->request->get('playerName');
        $numberOfPlayers = $request->request->get('numberOfPlayers');

        $session->set('playerName', $playerName);
        $session->set('numberOfPlayers', $numberOfPlayers);

        $balance = $session->get('balance', 1000);
        $session->set('balance', $balance);

        $deck = new DeckOfCards();
        $deck->shuffle();

        $playerHands = $this->blackjackService->initPlayerHands($numberOfPlayers);
        $dealerHand = $this->blackjackService->initDealerHand();

        $session->set('deck', $deck);
        $session->set('playerHands', $playerHands);
        $session->set('dealerHand', $dealerHand);

        return $this->render('project/proj_bet.html.twig', [
            'playerHands' => $playerHands,
            'dealerHand' => $dealerHand,
            'playerName' => $playerName,
            'balance' => $balance,
        ]);
    }

    #[Route('/proj/reset-session', name: 'proj_reset_session')]
    public function resetSession(SessionInterface $session): Response
    {
        $session->clear();
        return $this->redirectToRoute('proj');
    }

    // Game Actions
    #[Route('/proj/blackjack', name: 'proj_blackjack', methods: ['GET', 'POST'])]
    public function playBlackjack(Request $request, SessionInterface $session): Response
    {
        $playerName = $session->get('playerName');
        $playerHands = $session->get('playerHands', []);
        $dealerHand = $session->get('dealerHand', []);
        $balance = $session->get('balance');

        if ($this->blackjackService->checkPlayerDone($playerHands)) {
            $dealerHand = $this->blackjackService->dealerPlay($dealerHand);
            $playerHands = $this->blackjackService->endGame($playerHands, $dealerHand);
            $balance = $this->blackjackService->calculateBalance($playerHands, $balance);

            $session->set('dealerHand', $dealerHand);
            $session->set('playerHands', $playerHands);
            $session->set('balance', $balance);
        }

        if ($request->isMethod('POST')) {
            foreach ($playerHands as $key => &$playerHand) {
                if (!isset($playerHand['bet'])) {
                    $playerHand['bet'] = $request->request->get('bet' . $key, 0);
                    $balance -= $playerHand['bet'];
                }
            }

            $session->set('playerHands', $playerHands);
            $session->set('balance', $balance);
        }

        return $this->render('project/proj_blackjack.html.twig', [
            'playerHands' => $playerHands,
            'dealerHand' => $dealerHand,
            'playerName' => $playerName,
            'balance' => $balance,
        ]);
    }

    #[Route('/proj/hit/{playerIndex}', name: 'proj_hit', methods: ['POST'])]
    public function hit(int $playerIndex, SessionInterface $session): Response
    {
        $playerHands = $session->get('playerHands', []);
        $deck = $session->get('deck');

        $playerHands = $this->blackjackService->playerHit($playerHands, $playerIndex);

        $session->set('playerHands', $playerHands);
        $session->set('deck', $deck);

        return $this->redirectToRoute('proj_blackjack');
    }

    #[Route('/proj/stand/{playerIndex}', name: 'proj_stand', methods: ['POST'])]
    public function stand(int $playerIndex, SessionInterface $session): Response
    {
        $playerHands = $session->get('playerHands', []);
        $deck = $session->get('deck');

        $playerHands = $this->blackjackService->playerStand($playerHands, $playerIndex);

        $session->set('playerHands', $playerHands);
        $session->set('deck', $deck);

        return $this->redirectToRoute('proj_blackjack');
    }
}
