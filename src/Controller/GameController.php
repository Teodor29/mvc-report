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
        Request $request,
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
        Request $request,
        SessionInterface $session
    ): Response {
        /** @var DeckOfCards $deck */
        $deck = $session->get('deck');
        $playerHand = $session->get('playerHand', []);
        dump($playerHand);

        $blackjack = new Blackjack($deck);
        /** @var array<string> $playerHand */
        $playerHand[] = $blackjack->deal();
        dump($playerHand);

        $playerScore = $blackjack->calculateScore($playerHand);
        /** @var int $dealerScore */
        $dealerScore = $session->get('dealerScore');
        $end = $blackjack->checkGameEnd($playerScore, $dealerScore);

        $session->set('deck', $deck);
        $session->set('playerHand', $playerHand);
        $session->set('playerScore', $playerScore);

        return $this->render('blackjack.html.twig', [
            'playerHand' => $playerHand,
            'dealerHand' => $session->get('dealerHand'),
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
        /** @var DeckOfCards $deck */
        $deck = $session->get('deck');
        /** @var array<string> $dealerHand */
        $dealerHand = $session->get('dealerHand', []);
        $blackjack = new Blackjack($deck);
        $dealerScore = $blackjack->calculateScore($dealerHand);

        while ($dealerScore < 17) {
            /** @var array<string> $dealerHand */
            $dealerHand[] = $blackjack->deal();
            dump($dealerHand);
            $dealerScore = $blackjack->calculateScore($dealerHand);
        }

        $playerHand = $session->get('playerHand');
        $playerScore = $session->get('playerScore');
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
