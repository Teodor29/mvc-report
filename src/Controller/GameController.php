<?php

namespace App\Controller;

use App\Card\Blackjack;
use App\Card\DeckOfCards;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    #[Route('/game', name: 'game')]
    public function game(): Response
    {
        return $this->render('game.html.twig');
    }

    #[Route('/blackjack', name: 'blackjack')]
    public function blackjack(
        SessionInterface $session
    ): Response {
        $deck = new DeckOfCards();
        $deck->shuffle();
        $blackjack = new Blackjack($deck);

        /** @var array<string> $playerHand */
        $playerHand = [$blackjack->deal(), $blackjack->deal()];
        /** @var array<string> $dealerHand */
        $dealerHand = [$blackjack->deal()];

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
    public function hit(
        SessionInterface $session
    ): Response {
        /** @var DeckOfCards|null $deck */
        $deck = $session->get('deck');
        if (!$deck instanceof DeckOfCards) {
            throw new \LogicException('Deck is not properly initialized.');
        }

        /** @var array<string>|null $playerHand */
        $playerHand = $session->get('playerHand');
        if (!is_array($playerHand)) {
            $playerHand = [];
        }

        $blackjack = new Blackjack($deck);
        $playerHand[] = $blackjack->deal();

        $playerHand = array_filter($playerHand, fn($card) => null !== $card);

        $playerScore = $blackjack->calculateScore($playerHand);
        /** @var int|null $dealerScore */
        $dealerScore = $session->get('dealerScore');
        if (!is_int($dealerScore)) {
            throw new \LogicException('Dealer score is not properly initialized.');
        }

        $end = $blackjack->checkGameEnd($playerScore, $dealerScore);

        $session->set('deck', $deck);
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
    public function stand(
        Request $request,
        SessionInterface $session
    ): Response {
        /** @var DeckOfCards|null $deck */
        $deck = $session->get('deck');
        if (!$deck instanceof DeckOfCards) {
            throw new \LogicException('Deck is not properly initialized.');
        }

        /** @var array<string>|null $dealerHand */
        $dealerHand = $session->get('dealerHand');
        if (!is_array($dealerHand)) {
            $dealerHand = [];
        }

        $blackjack = new Blackjack($deck);
        $dealerHand = array_filter($dealerHand, fn($card) => null !== $card);

        $dealerScore = $blackjack->calculateScore($dealerHand);

        while ($dealerScore < 17) {
            $dealerHand[] = $blackjack->deal();
            $dealerHand = array_filter($dealerHand, fn($card) => null !== $card);
            $dealerScore = $blackjack->calculateScore($dealerHand);
        }

        /** @var array<string>|null $playerHand */
        $playerHand = $session->get('playerHand');
        if (!is_array($playerHand)) {
            $playerHand = [];
        }

        /** @var int|null $playerScore */
        $playerScore = $session->get('playerScore');
        if (!is_int($playerScore)) {
            throw new \LogicException('Player score is not properly initialized.');
        }

        $end = true;

        $session->set('deck', $deck);
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
