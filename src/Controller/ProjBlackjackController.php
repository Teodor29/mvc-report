<?php

namespace App\Controller;

use App\Card\DeckOfCards;
use App\Service\PlayerService;
use App\Service\DealerService;
use App\Service\GameService;
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
        $numberOfPlayers = $request->request->get('numberOfPlayers');

        $session->set('playerName', $playerName);
        $session->set('numberOfPlayers', $numberOfPlayers);

        $balance = $session->get('balance', 1000);

        $session->set('balance', $balance);

        $deck = new DeckOfCards();
        $deck->shuffle();

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

        if ($this->playerService->checkPlayerDone($playerHands)) {
            $dealerHand = $this->dealerService->dealerPlay($dealerHand);
            $playerHands = $this->gameService->endGame($playerHands, $dealerHand);
            $balance = $this->gameService->calculateBalance($playerHands, $balance);

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
    public function hit(
        int $playerIndex,
        SessionInterface $session
    ): Response {
        $playerHands = $session->get('playerHands', []);
        $deck = $session->get('deck');

        $playerHands = $this->playerService->playerHit($playerHands, $playerIndex);

        $session->set('playerHands', $playerHands);
        $session->set('deck', $deck);

        return $this->redirectToRoute('proj_blackjack');
    }

    #[Route('/proj/stand/{playerIndex}', name: 'proj_stand', methods: ['POST'])]
    public function stand(
        int $playerIndex,
        SessionInterface $session
    ): Response {
        $playerHands = $session->get('playerHands', []);
        $deck = $session->get('deck');

        $playerHands = $this->playerService->playerStand($playerHands, $playerIndex);

        $session->set('playerHands', $playerHands);
        $session->set('deck', $deck);

        return $this->redirectToRoute('proj_blackjack');
    }

    #[Route('/proj/reset-session', name: 'proj_reset_session')]
    public function resetSession(SessionInterface $session): Response
    {
        $session->clear();
        return $this->redirectToRoute('proj');
    }
}
