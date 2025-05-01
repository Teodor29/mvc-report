<?php

namespace App\Controller;

use App\Card\Blackjack;
use App\Card\DeckOfCards;
use App\Service\BlackjackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProjBlackjackController extends AbstractController
{
    private BlackjackService $blackjackService;

    public function __construct(BlackjackService $blackjackService)
    {
        $this->blackjackService = $blackjackService;
    }

    #[Route('/proj', name: 'proj')]
    public function proj(): Response
    {
        return $this->render('proj.html.twig');
    }

    #[Route('/proj/blackjack', name: 'proj_blackjack')]
    public function blackjack(
        SessionInterface $session
    ): Response {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $blackjack = new Blackjack($deck);

        $playerHands = $this->blackjackService->initPlayerHands(3);
        $dealerHand = $this->blackjackService->initDealerHand();

        dump($dealerHand);

        foreach ($playerHands as $hand) {
            dump($hand);
        }

        return $this->render('proj_blackjack.html.twig', [
            'playerHands' => $playerHands,
            'dealerHand' => $dealerHand,
        ]);
    }
}
