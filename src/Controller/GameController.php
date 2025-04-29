<?php

namespace App\Controller;

use App\Card\Blackjack;
use App\Card\DeckOfCards;
use App\Service\BlackjackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    private BlackjackService $blackjackService;

    public function __construct(BlackjackService $blackjackService)
    {
        $this->blackjackService = $blackjackService;
    }

    #[Route('/blackjack', name: 'blackjack')]
    public function blackjack(
        SessionInterface $session
    ): Response {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $blackjack = new Blackjack($deck);

        $playerHand = [$blackjack->deal(), $blackjack->deal()];
        $dealerHand = [$blackjack->deal()];

        $playerHand = array_filter($playerHand, fn($card) => is_string($card));
        $dealerHand = array_filter($dealerHand, fn($card) => is_string($card));

        $playerScore = $blackjack->calculateScore($playerHand);
        $dealerScore = $blackjack->calculateScore($dealerHand);
        $end = $blackjack->checkGameEnd($playerScore, $dealerScore);

        $session->set('deck', $deck);
        $session->set('playerHand', $playerHand);
        $session->set('dealerHand', $dealerHand);
        $session->set('playerScore', $playerScore);
        $session->set('dealerScore', $dealerScore);

        return $this->render('blackjack.html.twig', [
            'playerHand' => $playerHand,
            'dealerHand' => $dealerHand,
            'playerScore' => $playerScore,
            'dealerScore' => $dealerScore,
            'end' => $end,
        ]);
    }

    #[Route('/hit', name: 'hit')]
    public function hit(SessionInterface $session): Response
    {
        $dealerScore = $session->get('dealerScore');
        if (!is_int($dealerScore)) {
            throw new \LogicException('Dealer score is not properly initialized.');
        }

        $playerHand = $session->get('playerHand', []);
        if (!is_array($playerHand)) {
            throw new \LogicException('Player hand is not properly initialized.');
        }

        $playerHand = $this->blackjackService->playerHit($playerHand);
        $playerScore = $this->blackjackService->calculateScore($playerHand);
        $end = $this->blackjackService->checkGameEnd($playerScore, $dealerScore);

        $session->set('playerHand', $playerHand);
        $session->set('playerScore', $playerScore);

        return $this->render('blackjack.html.twig', [
            'playerHand' => $playerHand,
            'dealerHand' => $session->get('dealerHand', []),
            'playerScore' => $playerScore,
            'dealerScore' => $dealerScore,
            'end' => $end,
        ]);
    }

    #[Route('/stand', name: 'stand')]
    public function stand(SessionInterface $session): Response
    {
        $playerHand = $session->get('playerHand', []);
        $playerScore = $session->get('playerScore');

        if (!is_int($playerScore)) {
            throw new \LogicException('Player score is not properly initialized.');
        }

        $dealerHand = $session->get('dealerHand', []);
        if (!is_array($dealerHand)) {
            throw new \LogicException('Dealer hand is not properly initialized.');
        }

        $dealerHand = $this->blackjackService->dealerPlay($dealerHand);
        $dealerScore = $this->blackjackService->calculateScore($dealerHand);
        $end = true;

        $session->set('dealerHand', $dealerHand);
        $session->set('dealerScore', $dealerScore);

        return $this->render('blackjack.html.twig', [
            'playerHand' => $playerHand,
            'dealerHand' => $dealerHand,
            'playerScore' => $playerScore,
            'dealerScore' => $dealerScore,
            'end' => $end,
        ]);
    }

    #[Route('/game/doc', name: 'doc')]
    public function doc(): Response
    {
        return $this->render('doc.html.twig');
    }
}
